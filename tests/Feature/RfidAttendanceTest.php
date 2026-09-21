<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RfidAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        config(['rfid.token' => 'test-reader-secret', 'rfid.recorded_by_user_id' => null]);
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->student = Student::create(['nisn' => '1234567890', 'nis' => '20260001',
            'full_name' => 'Siswa RFID', 'class_name' => 'X RPL', 'program_name' => 'RPL', 'status' => 'active']);
    }

    private function scan(string $uid = '12:AB:34:CD')
    {
        return $this->postJson('/api/rfid/scan', ['uid' => $uid], ['X-RFID-Token' => 'test-reader-secret']);
    }

    private function start(): string
    {
        return $this->actingAs($this->admin)->postJson('/admin/absensi-rfid/start', ['student_id' => $this->student->id])
            ->assertOk()->json('session');
    }

    public function test_registration_captures_first_card_and_requires_confirmation(): void
    {
        $session = $this->start();
        $this->scan()->assertOk()->assertJsonPath('mode', 'registration');
        $this->scan('AA:BB:CC:DD')->assertOk();
        $this->assertNull($this->student->fresh()->rfid_uid);
        $this->assertDatabaseCount('attendance', 0);
        $this->getJson('/admin/absensi-rfid/state')->assertJsonPath('uid', '12:AB:34:CD');
        $this->postJson('/admin/absensi-rfid/finish', ['session' => $session, 'confirm' => true, 'uid' => '12:AB:34:CD'])->assertUnprocessable();
        $this->postJson('/admin/absensi-rfid/finish', ['session' => $session, 'confirm' => true, 'approved' => true, 'uid' => '12:AB:34:CD'])->assertOk();
        $this->assertSame('12:AB:34:CD', $this->student->fresh()->rfid_uid);
        $this->scan()->assertOk()->assertJsonPath('mode', 'attendance');
    }

    public function test_registered_card_records_once_and_keeps_first_timestamp(): void
    {
        $this->student->update(['rfid_uid' => '12:AB:34:CD']);
        $this->scan('12:ab:34:cd')->assertOk();
        $first = Attendance::first()->check_in_at->toDateTimeString();
        $this->travel(10)->seconds();
        $this->scan()->assertOk()->assertJsonPath('mode', 'already_recorded');
        $this->assertDatabaseCount('attendance', 1);
        $this->assertSame($first, Attendance::first()->check_in_at->toDateTimeString());
        $this->assertDatabaseHas('attendance', ['source' => 'rfid', 'status' => 'present', 'recorded_by' => $this->admin->id]);
        $this->travel(1)->days();
        $this->scan()->assertOk();
        $this->assertDatabaseCount('attendance', 2);
    }

    public function test_manual_attendance_is_preserved(): void
    {
        $this->student->update(['rfid_uid' => '12:AB:34:CD']);
        Attendance::create(['student_id' => $this->student->id, 'attendance_date' => today(), 'status' => 'sick', 'note' => 'Izin dokter', 'recorded_by' => $this->admin->id]);
        $this->scan()->assertOk()->assertJsonPath('mode', 'already_recorded');
        $this->assertDatabaseHas('attendance', ['status' => 'sick', 'note' => 'Izin dokter', 'check_in_at' => null]);
    }

    public function test_unknown_inactive_invalid_cards_and_invalid_token_are_rejected(): void
    {
        $this->postJson('/api/rfid/scan', ['uid' => '12:AB:34:CD'])->assertUnauthorized();
        $this->scan()->assertNotFound();
        $this->scan('1:ZZ')->assertUnprocessable();
        $this->student->update(['rfid_uid' => '12:AB:34:CD', 'status' => 'inactive']);
        $this->scan()->assertNotFound();
        $this->assertDatabaseCount('attendance', 0);
    }

    public function test_session_is_exclusive_owned_and_expires(): void
    {
        $session = $this->start();
        $other = User::factory()->create(['role' => 'principal']);
        $this->actingAs($other)->postJson('/admin/absensi-rfid/start', ['student_id' => $this->student->id])->assertConflict();
        $this->getJson('/admin/absensi-rfid/state')->assertJsonPath('mine', false)->assertJsonPath('session', null);
        $this->postJson('/admin/absensi-rfid/finish', ['session' => $session, 'confirm' => false])->assertConflict();
        $this->travel(121)->seconds();
        $this->actingAs($this->admin)->postJson('/admin/absensi-rfid/finish', ['session' => $session, 'confirm' => true, 'approved' => true, 'uid' => '12:AB:34:CD'])->assertConflict();
        $this->postJson('/admin/absensi-rfid/start', ['student_id' => $this->student->id])->assertOk();
    }

    public function test_duplicate_uid_is_rejected_and_card_can_be_unlinked(): void
    {
        $owner = Student::create(['nisn' => '9999999999', 'full_name' => 'Pemilik Kartu', 'class_name' => 'X RPL', 'program_name' => 'RPL', 'status' => 'active', 'rfid_uid' => '12:AB:34:CD']);
        $session = $this->start();
        $this->scan()->assertOk();
        $payload = ['session' => $session, 'confirm' => true, 'approved' => true, 'uid' => '12:AB:34:CD'];
        $this->postJson('/admin/absensi-rfid/finish', $payload)->assertUnprocessable();
        $this->deleteJson('/admin/absensi-rfid/cards/'.$owner->id, ['uid' => '12:AB:34:CD', 'approved' => true])->assertOk();
        $this->postJson('/admin/absensi-rfid/finish', $payload)->assertOk();
        $this->assertNull($owner->fresh()->rfid_uid);
    }

    public function test_cancel_and_replacement(): void
    {
        $session = $this->start();
        $this->postJson('/admin/absensi-rfid/finish', ['session' => $session, 'confirm' => false])->assertOk();
        $this->student->update(['rfid_uid' => 'AA:BB:CC:DD']);
        $session = $this->start();
        $this->scan()->assertOk();
        $this->postJson('/admin/absensi-rfid/finish', ['session' => $session, 'confirm' => true, 'approved' => true, 'uid' => '12:AB:34:CD'])->assertOk();
        $this->assertSame('12:AB:34:CD', $this->student->fresh()->rfid_uid);
        $this->scan('AA:BB:CC:DD')->assertNotFound();
    }

    public function test_pages_filters_summary_and_role_access(): void
    {
        $this->student->update(['rfid_uid' => '12:AB:34:CD']);
        $this->scan()->assertOk();
        $this->actingAs($this->admin)->get('/admin/absensi-rfid')->assertOk()->assertSee('Siswa RFID')
            ->assertViewHas('summary', fn ($s) => $s === ['active' => 1, 'recorded' => 1, 'present' => 1, 'unrecorded' => 0]);
        $this->get('/admin/absensi-rfid?class=OTHER')->assertOk()->assertSee('Belum Ada Absensi')
            ->assertViewHas('summary', fn ($s) => $s === ['active' => 0, 'recorded' => 0, 'present' => 0, 'unrecorded' => 0]);
        $this->get('/admin/absensi-rfid?tab=registration&search=20260001')->assertOk()->assertSee('Siswa RFID');
        $this->actingAs(User::factory()->create(['role' => 'principal']))->get('/admin/absensi-rfid')->assertOk();
        $this->actingAs(User::factory()->create(['role' => 'teacher']))->getJson('/admin/absensi-rfid/state')->assertForbidden();
        $this->postJson('/admin/absensi-rfid/start', ['student_id' => $this->student->id])->assertForbidden();
    }

    public function test_configuration_failures_do_not_create_attendance(): void
    {
        $this->student->update(['rfid_uid' => '12:AB:34:CD']);
        config(['rfid.token' => '']);
        $this->scan()->assertUnauthorized();
        config(['rfid.token' => 'test-reader-secret', 'rfid.recorded_by_user_id' => 999999]);
        $this->scan()->assertStatus(503);
        $this->assertDatabaseCount('attendance', 0);
    }

    public function test_expired_session_returns_to_attendance_and_valid_long_uids_work(): void
    {
        $this->student->update(['rfid_uid' => '01:02:03:04:05:06:07']);
        $this->start();
        $this->travel(121)->seconds();
        $this->scan('01:02:03:04:05:06:07')->assertOk()->assertJsonPath('mode', 'attendance');
        $this->assertDatabaseCount('attendance', 1);
        $this->student->update(['rfid_uid' => '01:02:03:04:05:06:07:08:09:0A']);
        $this->scan('01:02:03:04:05:06:07:08:09:0a')->assertOk()->assertJsonPath('mode', 'already_recorded');
    }
}
