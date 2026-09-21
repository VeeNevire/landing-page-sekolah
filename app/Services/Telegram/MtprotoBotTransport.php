<?php

namespace App\Services\Telegram;

use App\Contracts\TelegramTransport;
use App\Models\TelegramBot;
use danog\MadelineProto\API;
use danog\MadelineProto\Logger as MadelineProtoLogger;
use danog\MadelineProto\Settings;

class MtprotoBotTransport implements TelegramTransport
{
    public function verify(TelegramBot $bot): array
    {
        $client = $this->client($bot);
        $client->botLogin($bot->token);

        return $client->getSelf();
    }

    public function sendMessage(TelegramBot $bot, string|int $chatId, string $text): void
    {
        $client = $this->client($bot);
        $client->botLogin($bot->token);
        $client->messages->sendMessage(peer: (string) $chatId, message: $text);
    }

    private function client(TelegramBot $bot): API
    {
        $apiId = $bot->api_id ?: config('services.telegram.mtproto_api_id');
        $apiHash = $bot->api_hash ?: config('services.telegram.mtproto_api_hash');
        if (! $apiId || ! $apiHash) {
            throw new \RuntimeException('API ID dan API hash wajib diisi untuk mode MTProto.');
        }

        $directory = storage_path('app/private/telegram-sessions');
        if (! is_dir($directory)) {
            mkdir($directory, 0700, true);
        }

        $settings = new Settings;
        $settings->getAppInfo()->setApiId((int) $apiId)->setApiHash($apiHash);
        $settings->getLogger()
            ->setType(MadelineProtoLogger::FILE_LOGGER)
            ->setExtra(storage_path('logs/madelineproto-bot-'.$bot->id.'.log'));

        return new API($directory.'/bot-'.$bot->id.'.madeline', $settings);
    }
}
