<?php

namespace Tests\Feature;

use App\Jobs\SendAttendanceTelegramNotification;
use App\Models\Attendance;
use App\Models\AttendanceNotificationDelivery;
use App\Models\Student;
use App\Models\TelegramBot;
use App\Models\TelegramConnection;
use App\Models\TelegramTemplate;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RfidNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $parent;

    private Student $student;

    private TelegramBot $bot;

    private TelegramTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        config(['rfid.token' => 'reader-secret', 'rfid.recorded_by_user_id' => null]);
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->parent = User::factory()->create(['role' => 'parent', 'is_active' => true]);
        $this->student = Student::create(['nisn' => '1234567890', 'full_name' => 'Budi RFID',
            'class_name' => 'X RPL', 'program_name' => 'RPL', 'status' => 'active', 'rfid_uid' => '12:AB:34:CD']);
        $this->student->parents()->attach($this->parent->id);
        $this->bot = TelegramBot::create(['name' => 'Bot Absensi', 'mode' => 'bot_api', 'token' => 'fake-token', 'is_active' => true]);
        $this->template = TelegramTemplate::create(['telegram_bot_id' => $this->bot->id, 'name' => 'Absensi masuk',
            'report_type' => 'attendance', 'is_active' => true,
            'body' => '{{nama_siswa}} | {{kelas}} | {{tanggal}} | {{jam_masuk}} | {{status}} | {{link_detail}}']);
        DB::table('rfid_notification_settings')->where('id', 1)->update(['telegram_template_id' => $this->template->id, 'telegram_bot_id' => $this->bot->id]);
    }

    private function scan(): void
    {
        $this->postJson('/api/rfid/scan', ['uid' => '12:AB:34:CD'], ['X-RFID-Token' => 'reader-secret'])->assertOk();
    }

    private function connect(): void
    {
        TelegramConnection::create(['user_id' => $this->parent->id, 'telegram_bot_id' => $this->bot->id, 'chat_id' => '12345']);
    }

    public function test_disconnected_parent_gets_app_notification_without_telegram_and_repeated_scan_is_deduplicated(): void
    {
        $this->scan();
        $this->scan();
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseCount('attendance_notification_deliveries', 1);
        $this->assertDatabaseHas('attendance_notification_deliveries', ['parent_id' => $this->parent->id, 'status' => 'skipped', 'reason' => 'not_connected']);
        $this->actingAs($this->parent)->get('/portal/notifikasi')->assertOk()->assertSee('Kehadiran RFID tercatat');
        $this->getJson('/notifications/feed?after=0')->assertOk()->assertJsonCount(1, 'new')->assertJsonPath('new.0.title', 'Kehadiran RFID tercatat');
        $this->artisan('rfid:dispatch-notifications')->assertSuccessful();
        $this->assertDatabaseCount('jobs', 0);
        Http::assertNothingSent();
    }

    public function test_connected_parent_is_queued_once_and_job_sends_rendered_message_once(): void
    {
        $this->connect();
        $this->scan();
        $delivery = AttendanceNotificationDelivery::first();
        $this->assertSame('queued', $delivery->status);
        $this->assertStringContainsString('Budi RFID | X RPL', $delivery->message);
        $this->assertStringNotContainsString('{{', $delivery->message);
        $this->artisan('rfid:dispatch-notifications')->assertSuccessful();
        $this->artisan('rfid:dispatch-notifications')->assertSuccessful();
        $this->assertDatabaseCount('jobs', 1);
        $this->assertSame('rfid-notifications', DB::table('jobs')->value('queue'));
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true, 'result' => ['message_id' => 1]])]);
        $job = new SendAttendanceTelegramNotification($delivery->id);
        $job->handle(app(TelegramService::class));
        $job->handle(app(TelegramService::class));
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request['chat_id'] === '12345' && $request['text'] === $delivery->message);
        $this->assertSame('sent', $delivery->fresh()->status);
        $this->assertSame(1, $delivery->fresh()->attempts);
        $this->assertNotNull($delivery->fresh()->sent_at);
    }

    public function test_failures_keep_app_notification_and_support_retry_without_duplicate_app_messages(): void
    {
        $this->connect();
        $this->scan();
        $delivery = AttendanceNotificationDelivery::first();
        Http::fake(['api.telegram.org/*' => Http::sequence()->push(['ok' => false])->push(['ok' => true])]);
        $job = new SendAttendanceTelegramNotification($delivery->id);
        try {
            $job->handle(app(TelegramService::class));
            $this->fail('Expected Telegram failure');
        } catch (\RuntimeException $exception) {
            $this->assertSame('retrying', $delivery->fresh()->status);
            $job->failed($exception);
        }
        $this->assertSame('failed', $delivery->fresh()->status);
        $this->assertDatabaseCount('notifications', 1);
        $this->actingAs($this->admin)->post('/admin/absensi-rfid/notifikasi/'.$delivery->id.'/retry')->assertRedirect();
        $this->assertSame('queued', $delivery->fresh()->status);
        $this->post('/admin/absensi-rfid/notifikasi/'.$delivery->id.'/retry')->assertStatus(409);
        $job->handle(app(TelegramService::class));
        $this->assertSame('sent', $delivery->fresh()->status);
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_connection_and_parent_relationship_are_rechecked_before_sending(): void
    {
        $this->connect();
        $this->scan();
        $delivery = AttendanceNotificationDelivery::first();
        $this->student->parents()->detach($this->parent->id);
        (new SendAttendanceTelegramNotification($delivery->id))->handle(app(TelegramService::class));
        $this->assertSame('skipped', $delivery->fresh()->status);
        Http::assertNothingSent();
    }

    public function test_each_parent_has_separate_delivery_and_feed_cannot_leak_other_students(): void
    {
        $this->connect();
        $otherParent = User::factory()->create(['role' => 'parent', 'is_active' => true]);
        $this->student->parents()->attach($otherParent->id);
        $this->scan();
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseCount('attendance_notification_deliveries', 2);
        $this->assertDatabaseHas('attendance_notification_deliveries', ['parent_id' => $otherParent->id, 'status' => 'skipped']);
        $stranger = User::factory()->create(['role' => 'parent']);
        $this->actingAs($stranger)->getJson('/notifications/feed?after=0')->assertOk()->assertJsonCount(0, 'new')->assertJsonCount(0, 'recent');
        $studentUser = User::factory()->create(['role' => 'student']);
        $this->student->update(['user_id' => $studentUser->id]);
        $this->actingAs($studentUser)->getJson('/notifications/feed?after=0')->assertOk()->assertJsonCount(1, 'new');
        $this->get('/siswa/dashboard')->assertOk()->assertSee('live-notification-widget');
        $this->actingAs($this->admin)->getJson('/notifications/feed?after=0')->assertForbidden();
    }

    public function test_manual_attendance_and_registration_do_not_trigger_notifications(): void
    {
        Attendance::create(['student_id' => $this->student->id, 'attendance_date' => today(), 'status' => 'sick', 'recorded_by' => $this->admin->id]);
        $this->scan();
        $this->assertDatabaseCount('notifications', 0);
        $this->actingAs($this->admin)->postJson('/admin/absensi-rfid/start', ['student_id' => $this->student->id])->assertOk();
        $this->scan();
        $this->assertDatabaseCount('notifications', 0);
        $this->assertDatabaseCount('attendance_notification_deliveries', 0);
    }

    public function test_admin_can_choose_active_attendance_template_and_others_cannot_change_settings(): void
    {
        $this->actingAs($this->admin)->get('/admin/absensi-rfid/notifikasi')->assertOk()->assertSee('Bot Absensi');
        $grade = TelegramTemplate::create(['telegram_bot_id' => $this->bot->id, 'name' => 'Nilai', 'report_type' => 'grade', 'body' => 'Nilai', 'is_active' => true]);
        $this->postJson('/admin/absensi-rfid/notifikasi', ['telegram_template_id' => $grade->id])->assertUnprocessable();
        $this->post('/admin/absensi-rfid/notifikasi', ['telegram_template_id' => null])->assertRedirect();
        $this->scan();
        $this->assertDatabaseHas('attendance_notification_deliveries', ['status' => 'skipped', 'reason' => 'not_configured']);
        $this->actingAs($this->parent)->postJson('/admin/absensi-rfid/notifikasi', ['telegram_template_id' => $this->template->id])->assertForbidden();
    }

    public function test_bot_can_be_selected_independently_of_template_and_inactive_bot_is_rejected(): void
    {
        $this->template->update(['telegram_bot_id' => null]);
        $this->actingAs($this->admin)->post('/admin/absensi-rfid/notifikasi', [
            'telegram_template_id' => $this->template->id, 'telegram_bot_id' => $this->bot->id,
        ])->assertRedirect();
        $this->connect();
        $this->scan();
        $this->assertDatabaseHas('attendance_notification_deliveries', ['telegram_bot_id' => $this->bot->id, 'status' => 'queued']);
        $this->bot->update(['is_active' => false]);
        $this->postJson('/admin/absensi-rfid/notifikasi', [
            'telegram_template_id' => $this->template->id, 'telegram_bot_id' => $this->bot->id,
        ])->assertUnprocessable();
    }

    public function test_queue_failure_does_not_fail_scan_and_pending_delivery_is_recovered(): void
    {
        $this->connect();
        $queue = Queue::getFacadeRoot();
        Queue::shouldReceive('connection')->once()->with('rfid_notifications')
            ->andThrow(new \RuntimeException('Queue temporarily unavailable'));
        $this->scan();
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('attendance_notification_deliveries', ['status' => 'pending']);
        $this->assertDatabaseCount('jobs', 0);
        Queue::swap($queue);
        $this->artisan('rfid:dispatch-notifications')->assertSuccessful();
        $this->assertDatabaseHas('attendance_notification_deliveries', ['status' => 'queued']);
        $this->assertDatabaseCount('jobs', 1);
    }

    public function test_rollback_keeps_attendance_notification_and_delivery_atomic(): void
    {
        $this->connect();
        DB::beginTransaction();
        $this->scan();
        DB::rollBack();
        $this->assertDatabaseCount('attendance', 0);
        $this->assertDatabaseCount('notifications', 0);
        $this->assertDatabaseCount('attendance_notification_deliveries', 0);
    }
}
