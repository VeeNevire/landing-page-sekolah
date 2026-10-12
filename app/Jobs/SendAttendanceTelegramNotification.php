<?php

namespace App\Jobs;

use App\Models\AttendanceNotificationDelivery;
use App\Models\TelegramConnection;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class SendAttendanceTelegramNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public array $backoff = [10, 60];

    public function __construct(public int $deliveryId) {}

    public function handle(TelegramService $telegram): void
    {
        $error = DB::transaction(function () use ($telegram) {
            $delivery = AttendanceNotificationDelivery::lockForUpdate()->find($this->deliveryId);
            if (! $delivery || in_array($delivery->status, ['sent', 'skipped', 'failed'], true)) {
                return null;
            }
            $parent = $delivery->attendance?->student?->parents()->whereKey($delivery->parent_id)
                ->where('role', 'parent')->where('is_active', true)->first();
            $connection = TelegramConnection::where('user_id', $delivery->parent_id)
                ->where('telegram_bot_id', $delivery->telegram_bot_id)->whereNotNull('chat_id')->where('chat_id', '!=', '')->first();
            if (! $parent || ! $connection || ! $delivery->bot?->is_active) {
                $delivery->update(['status' => 'skipped', 'reason' => 'recipient_unavailable']);

                return null;
            }
            $delivery->increment('attempts');
            try {
                $telegram->sendMessage($connection->chat_id, $delivery->message, $delivery->bot);
                $delivery->update(['status' => 'sent', 'sent_at' => now(), 'reason' => null]);

                return null;
            } catch (Throwable $exception) {
                // Never persist transport exceptions: they may contain bot tokens.
                $delivery->update(['status' => 'retrying', 'reason' => 'transport_error']);

                return $exception;
            }
        });
        if ($error) {
            throw new \RuntimeException('Pengiriman Telegram RFID gagal.');
        }
    }

    public function failed(?Throwable $exception): void
    {
        AttendanceNotificationDelivery::whereKey($this->deliveryId)
            ->whereNotIn('status', ['sent', 'skipped'])->update(['status' => 'failed', 'reason' => 'transport_error']);
    }
}
