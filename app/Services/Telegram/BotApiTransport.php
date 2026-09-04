<?php

namespace App\Services\Telegram;

use App\Contracts\TelegramTransport;
use App\Models\TelegramBot;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class BotApiTransport implements TelegramTransport
{
    public function verify(TelegramBot $bot): array
    {
        $payload = $this->request($bot)->get($this->url('getMe', $bot->token))->json();
        $this->assertSuccessful($payload);

        return $payload['result'];
    }

    public function sendMessage(TelegramBot $bot, string|int $chatId, string $text): void
    {
        $payload = $this->request($bot)->post($this->url('sendMessage', $bot->token), [
            'chat_id' => (string) $chatId,
            'text' => $text,
        ])->json();
        $this->assertSuccessful($payload);
    }

    public function getManagedBotToken(TelegramBot $manager, int|string $userId): string
    {
        $payload = $this->request($manager)->get($this->url('getManagedBotToken', $manager->token), [
            'user_id' => (string) $userId,
        ])->json();
        $this->assertSuccessful($payload);

        return (string) $payload['result'];
    }

    public function getMe(TelegramBot $bot): array
    {
        return $this->verify($bot);
    }

    private function request(TelegramBot $bot): PendingRequest
    {
        return Http::timeout(10)->retry(2, 250);
    }

    private function url(string $method, string $token): string
    {
        return 'https://api.telegram.org/bot'.$token.'/'.$method;
    }

    private function assertSuccessful(mixed $payload): void
    {
        if (! is_array($payload) || ($payload['ok'] ?? false) !== true) {
            throw new \RuntimeException('Telegram Bot API menolak permintaan.');
        }
    }
}
