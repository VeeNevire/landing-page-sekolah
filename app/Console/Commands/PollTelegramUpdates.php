<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use App\Models\TelegramBot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class PollTelegramUpdates extends Command
{
    protected $signature = 'telegram:poll {--once : Request updates once and exit}';

    protected $description = 'Poll Telegram updates and link parent accounts';

    public function handle(TelegramService $telegram): int
    {
        $bots = TelegramBot::where('is_active', true)->get();
        if ($bots->isEmpty() && ! config('services.telegram.bot_token')) {
            $this->error('TELEGRAM_BOT_TOKEN belum dikonfigurasi.');

            return self::FAILURE;
        }

        $this->info('Long polling Telegram aktif. Tekan Ctrl+C untuk berhenti.');

        do {
            try {
                foreach ($bots->isEmpty() ? [null] : $bots as $bot) {
                    $token = $bot?->token ?: config('services.telegram.bot_token');
                    $cacheKey = 'telegram.poll.offset.'.substr(hash('sha256', $token), 0, 16);
                    $offset = Cache::get($cacheKey);
                    $telegram->disableWebhook($bot);
                    $updates = $telegram->getUpdates($offset, $this->option('once') ? 0 : 25, $bot);

                    foreach ($updates as $update) {
                        $telegram->processUpdate($update, $bot);

                        if (isset($update['update_id'])) {
                            $offset = (int) $update['update_id'] + 1;
                            Cache::forever($cacheKey, $offset);
                        }
                    }
                }
            } catch (Throwable $exception) {
                report($exception);
                Log::error('Polling Telegram gagal.', [
                    'bot_ids' => $bots->pluck('id')->values()->all(),
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                ]);

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
