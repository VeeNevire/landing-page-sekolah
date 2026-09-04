<?php

namespace App\Services\Telegram;

use App\Models\TelegramMtprotoSession;
use danog\MadelineProto\API;
use danog\MadelineProto\Logger as MadelineProtoLogger;
use danog\MadelineProto\Settings;

class MtprotoAccountService
{
    public function qr(TelegramMtprotoSession $session): array
    {
        try {
            $api = $this->client($session);
            $qr = $api->qrLogin();
            if ($qr) {
                $session->forceFill(['status' => 'qr_pending', 'last_error' => null])->save();

                return [
                    'logged_in' => false,
                    'needs_2fa' => false,
                    'svg' => $qr->getQRSvg(280, 2),
                    'expires_in' => $qr->expiresIn(),
                ];
            }

            if ($api->getAuthorization() === API::WAITING_PASSWORD) {
                $session->forceFill(['status' => 'needs_2fa'])->save();

                return ['logged_in' => false, 'needs_2fa' => true, 'svg' => null, 'expires_in' => 0];
            }

            $user = $api->getSelf();
            $session->forceFill([
                'status' => 'connected',
                'telegram_user_id' => $user['id'] ?? null,
                'username' => $user['username'] ?? null,
                'first_name' => $user['first_name'] ?? null,
                'connected_at' => $session->connected_at ?: now(),
                'last_error' => null,
            ])->save();

            return [
                'logged_in' => true,
                'needs_2fa' => false,
                'svg' => null,
                'expires_in' => 0,
                'user' => $user,
            ];
        } catch (\Throwable $exception) {
            $session->forceFill(['status' => 'error', 'last_error' => 'Login Telegram gagal'])->save();
            throw $exception;
        }
    }

    public function client(TelegramMtprotoSession $session): API
    {
        $apiId = $session->api_id ?: config('services.telegram.mtproto_api_id');
        $apiHash = $session->api_hash ?: config('services.telegram.mtproto_api_hash');
        if (! $apiId || ! $apiHash) {
            throw new \RuntimeException('API ID dan API hash wajib diisi.');
        }

        $directory = storage_path('app/private/telegram-sessions');
        if (! is_dir($directory)) {
            mkdir($directory, 0700, true);
        }

        $settings = new Settings;
        $settings->getAppInfo()->setApiId((int) $apiId)->setApiHash($apiHash);
        $settings->getLogger()
            ->setType(MadelineProtoLogger::FILE_LOGGER)
            ->setExtra(storage_path('logs/madelineproto-account.log'));

        return new API($session->session_path, $settings);
    }

    public function logout(TelegramMtprotoSession $session): void
    {
        try {
            if (is_file($session->session_path)) {
                $api = $this->client($session);
                $api->logout();
            }
        } finally {
            $session->update([
                'status' => 'disconnected',
                'telegram_user_id' => null,
                'username' => null,
                'first_name' => null,
                'connected_at' => null,
            ]);
        }
    }
}
