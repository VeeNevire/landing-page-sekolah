<?php

namespace App\Jobs;

use App\Models\TelegramBot;
use App\Models\TelegramBotCreation;
use App\Models\TelegramMtprotoSession;
use App\Services\Telegram\MtprotoManagedBotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateMtprotoBot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public int $creationId, public int $sessionId) {}

    public function handle(MtprotoManagedBotService $managedBots): void
    {
        $creation = TelegramBotCreation::find($this->creationId);
        $session = TelegramMtprotoSession::find($this->sessionId);
        if (! $creation || ! $session) {
            return;
        }

        $creation->update(['status' => 'processing']);

        try {
            $managerToken = config('services.telegram.mtproto_manager_bot_token');
            $managerUsername = config('services.telegram.mtproto_manager_bot_username');
            if (! $managerToken || ! $managerUsername) {
                throw new \RuntimeException('Manager bot belum dikonfigurasi di server.');
            }

            $manager = new TelegramBot([
                'mode' => 'bot_api',
                'username' => $managerUsername,
                'token' => $managerToken,
                'is_active' => true,
            ]);
            $bot = $managedBots->create($session, $manager, $creation->name, $creation->username);
            $creation->update(['status' => 'completed', 'telegram_bot_id' => $bot->id, 'error' => null]);
        } catch (\Throwable $exception) {
            report($exception);
            $creation->update(['status' => 'failed', 'error' => $this->friendlyError($exception)]);
        }
    }

    private function friendlyError(\Throwable $exception): string
    {
        $message = strtoupper($exception->getMessage());

        return match (true) {
            str_contains($message, 'USERNAME_OCCUPIED') => 'Username telah digunakan oleh bot lain. Silakan gunakan username yang berbeda.',
            str_contains($message, 'USERNAME_SUFFIX_MISSING') => 'Username bot harus diakhiri dengan “bot”.',
            str_contains($message, 'USERNAME_INVALID') => 'Format username bot tidak valid. Gunakan huruf, angka, underscore, dan akhiran “bot”.',
            str_contains($message, 'MANAGER_PERMISSION_MISSING') => 'Manager Bot belum mengaktifkan Bot Management Mode di BotFather.',
            default => 'Bot gagal dibuat. Periksa konfigurasi Telegram dan coba lagi.',
        };
    }
}
