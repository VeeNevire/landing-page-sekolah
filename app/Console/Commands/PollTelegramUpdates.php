<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Throwable;

class PollTelegramUpdates extends Command
{
    protected $signature = 'telegram:poll {--once : Request updates once and exit}';

    protected $description = 'Poll Telegram updates and link parent accounts';

    public function handle(TelegramService $telegram): int
    {
        $token = config('services.telegram.bot_token');
        if (! $token) {
            $this->error('TELEGRAM_BOT_TOKEN belum dikonfigurasi.');

            return self::FAILURE;
        }

        $cacheKey = 'telegram.poll.offset.'.substr(hash('sha256', $token), 0, 16);
        $offset = Cache::get($cacheKey);

        try {
            $telegram->disableWebhook();
        } catch (Throwable $exception) {
            $this->error('Gagal menonaktifkan webhook Telegram: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Long polling Telegram aktif. Tekan Ctrl+C untuk berhenti.');

        do {
            try {
                $updates = $telegram->getUpdates($offset, $this->option('once') ? 0 : 25);

                foreach ($updates as $update) {
                    $telegram->processUpdate($update);

                    if (isset($update['update_id'])) {
                        $offset = (int) $update['update_id'] + 1;
                        Cache::forever($cacheKey, $offset);
                    }
                }
            } catch (Throwable $exception) {
                report($exception);

                if ($this->option('once')) {
                    $this->error('Polling Telegram gagal: '.$exception->getMessage());

                    return self::FAILURE;
                }

                $this->warn('Koneksi Telegram bermasalah. Mencoba lagi dalam 5 detik.');
                sleep(5);
            }
        } while (! $this->option('once'));

        return self::SUCCESS;
    }
}
