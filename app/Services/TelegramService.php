<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TelegramService
{
    public function disableWebhook(): void
    {
        $response = $this->client()->post($this->apiUrl('deleteWebhook'), [
            'drop_pending_updates' => false,
        ]);

        $this->assertSuccessful($response->json());
    }

    public function getUpdates(?int $offset, int $timeout = 25): array
    {
        $response = $this->client($timeout + 10)->post($this->apiUrl('getUpdates'), [
            'offset' => $offset,
            'timeout' => $timeout,
            'allowed_updates' => ['message'],
        ]);

        $payload = $response->json();
        $this->assertSuccessful($payload);

        return is_array($payload['result'] ?? null) ? $payload['result'] : [];
    }

    public function processUpdate(array $update): void
    {
        $chatId = data_get($update, 'message.chat.id');
        $text = (string) data_get($update, 'message.text', '');

        if ($chatId && preg_match('/^\/start(?:@\w+)?\s+(\S+)$/', $text, $matches)) {
            $this->linkParentFromStart($chatId, $matches[1]);
        }
    }

    public function createParentLink(User $parent): ?string
    {
        if (! config('services.telegram.bot_token')) {
            return null;
        }

        $username = $this->botUsername();
        if (! $username) {
            return null;
        }

        $token = Str::random(48);
        $parent->forceFill([
            'telegram_link_token_hash' => hash('sha256', $token),
            'telegram_link_token_expires_at' => now()->addMinutes(30),
        ])->save();

        return 'https://t.me/'.ltrim($username, '@').'?start=link_'.$token;
    }

    public function linkParentFromStart(string|int $chatId, string $startToken): bool
    {
        if (! str_starts_with($startToken, 'link_')) {
            return false;
        }

        $parent = DB::transaction(function () use ($chatId, $startToken) {
            $parent = User::where('role', 'parent')
                ->where('telegram_link_token_hash', hash('sha256', substr($startToken, 5)))
                ->where('telegram_link_token_expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (! $parent) {
                return null;
            }

            User::where('telegram_chat_id', (string) $chatId)
                ->whereKeyNot($parent->id)
                ->update(['telegram_chat_id' => null]);

            $parent->forceFill([
                'telegram_chat_id' => (string) $chatId,
                'telegram_link_token_hash' => null,
                'telegram_link_token_expires_at' => null,
            ])->save();

            return $parent;
        });

        if (! $parent) {
            return false;
        }

        $this->sendMessage($chatId, 'Telegram berhasil terhubung. Anda akan menerima notifikasi nilai siswa di sini.');

        return true;
    }

    public function sendMessage(string|int $chatId, string $text): void
    {
        $token = config('services.telegram.bot_token');
        if (! $token) {
            throw new \RuntimeException('TELEGRAM_BOT_TOKEN belum dikonfigurasi.');
        }

        $response = $this->client()->post($this->apiUrl('sendMessage'), [
            'chat_id' => (string) $chatId,
            'text' => $text,
        ]);

        $this->assertSuccessful($response->json());
    }

    private function botUsername(): ?string
    {
        $configuredUsername = config('services.telegram.bot_username');
        if ($configuredUsername) {
            return ltrim($configuredUsername, '@');
        }

        $token = config('services.telegram.bot_token');
        if (! $token) {
            return null;
        }

        $response = $this->client()->get($this->apiUrl('getMe'));
        $this->assertSuccessful($response->json());

        return $response->json('result.username');
    }

    private function apiUrl(string $method): string
    {
        return 'https://api.telegram.org/bot'.config('services.telegram.bot_token').'/'.$method;
    }

    private function assertSuccessful(mixed $payload): void
    {
        if (! is_array($payload) || ($payload['ok'] ?? false) !== true) {
            throw new \RuntimeException('Telegram API menolak permintaan.');
        }
    }

    private function client(int $timeout = 10): PendingRequest
    {
        return Http::timeout($timeout)->retry(2, 250);
    }
}
