<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramBot;
use App\Models\TelegramTemplate;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use Throwable;

class TelegramController extends Controller
{
    public function index()
    {
        return view('admin.telegram', [
            'bots' => TelegramBot::withCount('connections')->latest()->get(),
            'templates' => TelegramTemplate::with('bot')->latest()->get(),
        ]);
    }

    public function botStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'username' => 'nullable|string|max:120',
            'token' => 'required|string|max:255',
        ]);
        TelegramBot::create($data + ['is_active' => $request->boolean('is_active', true)]);
        return back()->with('success', 'Bot Telegram berhasil ditambahkan.');
    }

    public function botUpdate(Request $request, TelegramBot $bot)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'username' => 'nullable|string|max:120',
            'token' => 'nullable|string|max:255',
        ]);
        if ($data['token'] ?? null) {
            $bot->token = $data['token'];
        }
        unset($data['token']);
        $bot->fill($data);
        $bot->is_active = $request->boolean('is_active');
        $bot->save();
        return back()->with('success', 'Bot Telegram berhasil diperbarui.');
    }

    public function botDestroy(TelegramBot $bot)
    {
        $bot->delete();
        return back()->with('success', 'Bot Telegram berhasil dihapus.');
    }

    public function botTest(TelegramBot $bot, TelegramService $telegram)
    {
        try {
            $info = $telegram->verifyBot($bot);
            $username = $info['username'] ?? null;
            return back()->with('success', 'Bot tersambung dengan baik'.($username ? ' (@'.$username.')' : '').'.');
        } catch (Throwable $exception) {
            report($exception);
            return back()->with('error', 'Bot tidak dapat tersambung. Periksa token dan koneksi Telegram.');
        }
    }

    public function templateStore(Request $request)
    {
        $data = $request->validate([
            'telegram_bot_id' => 'nullable|exists:telegram_bots,id',
            'name' => 'required|string|max:120',
            'report_type' => 'required|in:grade,exam_plan,attendance',
            'body' => 'required|string|max:4000',
        ]);
        TelegramTemplate::create($data + ['telegram_bot_id' => null, 'is_active' => $request->boolean('is_active', true)]);
        return back()->with('success', 'Template Bot berhasil ditambahkan.');
    }

    public function templateUpdate(Request $request, TelegramTemplate $template)
    {
        $data = $request->validate([
            'telegram_bot_id' => 'nullable|exists:telegram_bots,id',
            'name' => 'required|string|max:120',
            'report_type' => 'required|in:grade,exam_plan,attendance',
            'body' => 'required|string|max:4000',
        ]);
        $template->update($data + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Template Bot berhasil diperbarui.');
    }

    public function templateDestroy(TelegramTemplate $template)
    {
        $template->delete();
        return back()->with('success', 'Template Bot berhasil dihapus.');
    }
}
