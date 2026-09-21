<?php

namespace App\Contracts;

use App\Models\TelegramBot;

interface TelegramTransport
{
    public function verify(TelegramBot $bot): array;

    public function sendMessage(TelegramBot $bot, string|int $chatId, string $text): void;
}
