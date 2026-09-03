<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\TelegramBot;
use App\Models\TelegramConnection;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function connect(Request $request, TelegramService $telegram, ?TelegramBot $bot = null)
    {
        $link = $telegram->createParentLink($request->user(), $bot);

        if (! $link) {
            return back()->with('error', 'Bot Telegram belum dikonfigurasi.');
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
