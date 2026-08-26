<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function connect(Request $request, TelegramService $telegram)
    {
        $link = $telegram->createParentLink($request->user());

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
}
