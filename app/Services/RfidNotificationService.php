<?php

namespace App\Services;

use App\Jobs\SendAttendanceTelegramNotification;
use App\Models\Attendance;
use App\Models\AttendanceNotificationDelivery;
use App\Models\Notification;
use App\Models\TelegramBot;
use App\Models\TelegramConnection;
use App\Models\TelegramTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Throwable;

class RfidNotificationService
{
    public function record(Attendance $attendance): void
    {
        $student = $attendance->student;
        $date = $attendance->attendance_date->format('d-m-Y');
        $time = $attendance->check_in_at->format('H:i:s');
        $status = $attendance->status === 'late' ? 'Terlambat' : 'Hadir';
        $body = "Kehadiran {$student->full_name} ({$student->class_name}) tercatat melalui RFID pada {$date} pukul {$time} WIB. Status: {$status}.";
        Notification::create([
            'student_id' => $student->id, 'attendance_id' => $attendance->id,
            'type' => 'success', 'title' => 'Kehadiran RFID tercatat', 'body' => $body,
        ]);

        $settings = DB::table('rfid_notification_settings')->where('id', 1)->first();
        $template = TelegramTemplate::find($settings?->telegram_template_id);
        $bot = TelegramBot::find($settings?->telegram_bot_id);
        $enabled = $template && $template->is_active && $template->report_type === 'attendance' && $bot?->is_active;
        $message = $enabled ? strtr($template->body, [
            '{{nama_siswa}}' => $student->full_name,
            '{{kelas}}' => $student->class_name,
            '{{tanggal}}' => $date,
            '{{jam_masuk}}' => $time.' WIB',
            '{{status}}' => $status,
            '{{catatan}}' => 'Kehadiran tercatat melalui RFID.',
            '{{guru}}' => $attendance->recorder?->full_name ?: $attendance->recorder?->name ?: '-',
            '{{link_detail}}' => route('portal.kehadiran', ['student_id' => $student->id]),
            '{{nama_sekolah}}' => config('school.name', config('app.name')),
        ]) : $body;

        foreach ($student->parents()->where('role', 'parent')->where('is_active', true)->get() as $parent) {
            $connected = $enabled && TelegramConnection::where('user_id', $parent->id)
                ->where('telegram_bot_id', $bot->id)->whereNotNull('chat_id')->where('chat_id', '!=', '')->exists();
            $delivery = AttendanceNotificationDelivery::create([
                'attendance_id' => $attendance->id, 'parent_id' => $parent->id,
                'telegram_bot_id' => $enabled ? $bot->id : null,
                'message' => $message, 'status' => $connected ? 'pending' : 'skipped',
                'reason' => $connected ? null : ($enabled ? 'not_connected' : 'not_configured'),
            ]);
            if ($connected) {
                DB::afterCommit(function () use ($delivery) {
                    try {
                        $this->enqueue($delivery->id);
                    } catch (Throwable) {
                        // Keep the durable pending row for scheduler recovery, without failing the scan.
                        Log::warning('RFID notification queue unavailable', ['delivery_id' => $delivery->id]);
                    }
                });
            }
        }
    }

    public function enqueue(int $deliveryId): void
    {
        DB::transaction(function () use ($deliveryId) {
            $delivery = AttendanceNotificationDelivery::lockForUpdate()->find($deliveryId);
            if (! $delivery || $delivery->status !== 'pending') {
                return;
            }
            // Use the same database for the outbox row and queue insertion, even when DB_QUEUE_CONNECTION differs.
            $connection = config('database.default');
            config(['queue.connections.rfid_notifications' => [
                'driver' => 'database', 'connection' => $connection,
                'table' => 'jobs', 'queue' => 'rfid-notifications', 'retry_after' => 90, 'after_commit' => false,
            ]]);
            Queue::connection('rfid_notifications')->push(new SendAttendanceTelegramNotification($delivery->id), '', 'rfid-notifications');
            $delivery->update(['status' => 'queued']);
        });
    }
}
