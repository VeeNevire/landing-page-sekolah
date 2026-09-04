<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\TelegramBot;
use App\Models\TelegramConnection;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramController extends Controller
{
    public function connect(Request $request, TelegramService $telegram, ?TelegramBot $bot = null)
    {
        try {
            $link = $telegram->createParentLink($request->user(), $bot);
        } catch (Throwable $exception) {
            Log::error('Gagal membuat link koneksi Telegram user.', [
                'user_id' => $request->user()->id,
                'bot_id' => $bot?->id,
                'bot_username' => $bot?->username,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Koneksi Telegram sedang bermasalah. Silakan coba beberapa saat lagi.');
        }

        if (! $link) {
            Log::warning('Link koneksi Telegram tidak dapat dibuat karena bot belum dikonfigurasi.', [
                'user_id' => $request->user()->id,
                'bot_id' => $bot?->id,
            ]);

            return back()->with('error', 'Koneksi Telegram belum tersedia. Silakan coba beberapa saat lagi.');
        }

        return redirect()->away($link);
    }

    public function disconnect(Request $request)
    {
        $request->user()->forceFill([
            'telegram_chat_id' => null,
            'telegram_link_token_hash' => null,
            'telegram_link_token_expires_at' => null,
        ])->save();

        return back()->with('success', 'Telegram berhasil diputuskan.');
    }

    public function disconnectBot(Request $request, TelegramBot $bot)
    {
        TelegramConnection::where('user_id', $request->user()->id)->where('telegram_bot_id', $bot->id)->delete();
        return back()->with('success', 'Koneksi bot berhasil diputuskan.');
    }
}
