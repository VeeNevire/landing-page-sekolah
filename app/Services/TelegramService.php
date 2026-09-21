<?php

namespace App\Services;

use App\Contracts\TelegramTransport;
use App\Models\TelegramBot;
use App\Models\TelegramConnection;
use App\Models\User;
use App\Services\Telegram\BotApiTransport;
use App\Services\Telegram\MtprotoBotTransport;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramService
{
    public function disableWebhook(?TelegramBot $bot = null): void
    {
        $response = $this->client()->post($this->apiUrl('deleteWebhook', $bot?->token), [
            'drop_pending_updates' => false,
        ]);

        $this->assertSuccessful($response->json());
    }

    public function getUpdates(?int $offset, int $timeout = 25, ?TelegramBot $bot = null): array
    {
        $response = $this->client($timeout + 10)->post($this->apiUrl('getUpdates', $bot?->token), [
            'offset' => $offset,
            'timeout' => $timeout,
            'allowed_updates' => ['message'],
        ]);

        $payload = $response->json();
        $this->assertSuccessful($payload);

        return is_array($payload['result'] ?? null) ? $payload['result'] : [];
    }

    public function processUpdate(array $update, ?TelegramBot $bot = null): void
    {
        $chatId = data_get($update, 'message.chat.id');
        $text = (string) data_get($update, 'message.text', '');

        if ($chatId && preg_match('/^\/start(?:@\w+)?\s+(\S+)$/', $text, $matches)) {
            $linked = $this->linkParentFromStart($chatId, $matches[1], $bot, data_get($update, 'message.from.username'));

            if (! $linked) {
                Log::warning('Telegram /start gagal menautkan akun user.', [
                    'chat_id' => (string) $chatId,
                    'bot_id' => $bot?->id,
                    'reason' => 'Token koneksi tidak valid atau sudah kedaluwarsa.',
                ]);
            }
        }
    }

    public function createParentLink(User $parent, ?TelegramBot $bot = null): ?string
    {
        if (! $bot && ! config('services.telegram.bot_token')) {
            return null;
        }

        $username = $this->botUsername($bot);
        if (! $username) {
            return null;
        }

        $token = Str::random(48);
        if ($bot) {
            TelegramConnection::updateOrCreate(
                ['user_id' => $parent->id, 'telegram_bot_id' => $bot->id],
                ['link_token_hash' => hash('sha256', $token), 'link_token_expires_at' => now()->addMinutes(30), 'chat_id' => null, 'connected_at' => null]
            );

            return 'https://t.me/'.ltrim($username, '@').'?start=link_'.$token;
        }

        $parent->forceFill([
            'telegram_link_token_hash' => hash('sha256', $token),
            'telegram_link_token_expires_at' => now()->addMinutes(30),
        ])->save();

        return 'https://t.me/'.ltrim($username, '@').'?start=link_'.$token;
    }

    public function linkParentFromStart(string|int $chatId, string $startToken, ?TelegramBot $bot = null, ?string $telegramUsername = null): bool
    {
        if (! str_starts_with($startToken, 'link_')) {
            return false;
        }

        $parent = DB::transaction(function () use ($chatId, $startToken, $bot, $telegramUsername) {
            if ($bot) {
                $connection = TelegramConnection::where('telegram_bot_id', $bot->id)
                    ->where('link_token_hash', hash('sha256', substr($startToken, 5)))
                    ->where('link_token_expires_at', '>', now())->lockForUpdate()->first();
                if (! $connection) {
                    return null;
                }
                $connection->update(['chat_id' => (string) $chatId, 'telegram_username' => $telegramUsername, 'link_token_hash' => null, 'link_token_expires_at' => null, 'connected_at' => now()]);

                return $connection->user;
            }
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

        $this->sendMessage($chatId, 'Telegram berhasil terhubung. Anda akan menerima notifikasi nilai siswa di sini.', $bot);

        return true;
    }

    public function sendMessage(string|int $chatId, string $text, ?TelegramBot $bot = null): void
    {
        if ($bot) {
            $this->transport($bot)->sendMessage($bot, $chatId, $text);

            return;
        }

        $token = $bot?->token ?: config('services.telegram.bot_token');
        if (! $token) {
            throw new \RuntimeException('TELEGRAM_BOT_TOKEN belum dikonfigurasi.');
        }

        $response = $this->client()->post($this->apiUrl('sendMessage', $token), [
            'chat_id' => (string) $chatId,
            'text' => $text,
        ]);

        $this->assertSuccessful($response->json());
    }

    public function verifyBot(TelegramBot $bot): array
    {
        return $this->transport($bot)->verify($bot);
    }

    private function transport(TelegramBot $bot): TelegramTransport
    {
        return $bot->mode === 'mtproto_bot'
            ? app(MtprotoBotTransport::class)
            : app(BotApiTransport::class);
    }

    private function botUsername(?TelegramBot $bot = null): ?string
    {
        $configuredUsername = $bot?->username ?: config('services.telegram.bot_username');
        if ($configuredUsername) {
            return ltrim($configuredUsername, '@');
        }

        $token = $bot?->token ?: config('services.telegram.bot_token');
        if (! $token) {
            return null;
        }

        $response = $this->client()->get($this->apiUrl('getMe', $token));
        $this->assertSuccessful($response->json());

        return $response->json('result.username');
    }

    private function apiUrl(string $method, ?string $token = null): string
    {
        return 'https://api.telegram.org/bot'.($token ?: config('services.telegram.bot_token')).'/'.$method;
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
