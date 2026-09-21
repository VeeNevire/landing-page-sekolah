<?php

namespace App\Services\Telegram;

use App\Models\TelegramBot;
use App\Models\TelegramMtprotoSession;

class MtprotoManagedBotService
{
    public function __construct(
        private MtprotoAccountService $accounts,
        private BotApiTransport $botApi,
    ) {}

    public function create(TelegramMtprotoSession $session, TelegramBot $manager, string $name, string $username): TelegramBot
    {
        if ($session->status !== 'connected' || ! $session->telegram_user_id) {
            throw new \RuntimeException('Hubungkan akun Telegram terlebih dahulu.');
        }

        $username = ltrim($username, '@');
        if (! preg_match('/^[A-Za-z][A-Za-z0-9_]{4,31}bot$/i', $username)) {
            throw new \InvalidArgumentException('Username bot harus 5-32 karakter dan diakhiri dengan bot.');
        }

        $managerInfo = $this->botApi->getMe($manager);
        if (! ($managerInfo['can_manage_bots'] ?? false)) {
            throw new \RuntimeException('Bot manager belum mengaktifkan Bot Management Mode di BotFather.');
        }

        $api = $this->accounts->client($session);
        $available = $api->bots->checkUsername(username: $username);
        if (($available['_'] ?? null) !== 'boolTrue' && $available !== true) {
            throw new \RuntimeException('Username bot tidak tersedia.');
        }

        $created = $api->bots->createBot(
            name: $name,
            username: $username,
            manager_id: '@'.ltrim((string) ($managerInfo['username'] ?? $manager->username), '@'),
        );
        $botId = (int) ($created['id'] ?? 0);
        if (! $botId) {
            throw new \RuntimeException('Telegram tidak mengembalikan identitas bot baru.');
        }

        $token = $this->botApi->getManagedBotToken($manager, $botId);

        return TelegramBot::create([
            'name' => $name,
            'mode' => 'mtproto_bot',
            'username' => $created['username'] ?? $username,
            'token' => $token,
            'api_id' => $session->api_id,
            'api_hash' => $session->api_hash,
            'is_active' => true,
        ]);
    }
}
