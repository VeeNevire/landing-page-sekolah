<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\CreateMtprotoBot;
use App\Models\TelegramBot;
use App\Models\TelegramBotCreation;
use App\Models\TelegramMtprotoSession;
use App\Models\TelegramTemplate;
use App\Services\Telegram\MtprotoAccountService;
use App\Services\TelegramService;
use App\Services\TelegramTemplateRegistry;
use Illuminate\Http\Request;
use Throwable;

class TelegramController extends Controller
{
    public function index(Request $request, TelegramTemplateRegistry $registry)
    {
        $mtprotoSession = TelegramMtprotoSession::query()->first();
        $creation = $request->filled('creation')
            ? TelegramBotCreation::with('bot')->find($request->integer('creation'))
            : null;

        return view('admin.telegram', [
            'bots' => TelegramBot::withCount('connections')->latest()->get(),
            'templates' => TelegramTemplate::with('bot')->latest()->get(),
            'parameterSchemas' => collect(['grade', 'exam_plan', 'attendance'])
                ->mapWithKeys(fn ($type) => [$type => $registry->for($type)])->all(),
            'mtprotoSession' => $mtprotoSession,
            'creation' => $creation,
        ]);
    }

    public function mtprotoQr(MtprotoAccountService $accounts)
    {
        $apiId = config('services.telegram.mtproto_api_id');
        $apiHash = config('services.telegram.mtproto_api_hash');
        if (! $apiId || ! $apiHash) {
            return response()->json(['message' => 'TELEGRAM_MTPROTO_API_ID dan TELEGRAM_MTPROTO_API_HASH belum dikonfigurasi di .env.'], 422);
        }

        $path = storage_path('app/private/telegram-sessions/web-account.madeline');
        $session = TelegramMtprotoSession::updateOrCreate(
            ['id' => 1],
            ['api_id' => (int) $apiId, 'api_hash' => $apiHash, 'session_path' => $path, 'status' => 'qr_pending', 'last_error' => null],
        );

        try {
            return response()->json($accounts->qr($session));
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'QR Telegram gagal dibuat. Periksa API ID/API hash.'], 422);
        }
    }

    public function mtprotoQrStatus(MtprotoAccountService $accounts)
    {
        $session = TelegramMtprotoSession::query()->first();
        if (! $session) {
            return response()->json(['logged_in' => false, 'status' => 'disconnected']);
        }

        try {
            $result = $accounts->qr($session);

            return response()->json($result + ['status' => $session->fresh()->status]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'Status login Telegram gagal.'], 422);
        }
    }

    public function mtprotoLogout(MtprotoAccountService $accounts)
    {
        $session = TelegramMtprotoSession::query()->first();
        if ($session) {
            $accounts->logout($session);
        }

        return back()->with('success', 'Koneksi Telegram MTProto diputuskan.');
    }

    public function mtprotoBotStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:1|max:64',
            'username' => ['required', 'string', 'min:5', 'max:32', 'regex:/^[A-Za-z][A-Za-z0-9_]*bot$/i'],
        ]);
        $session = TelegramMtprotoSession::query()->firstOrFail();
        $managerToken = config('services.telegram.mtproto_manager_bot_token');
        $managerUsername = config('services.telegram.mtproto_manager_bot_username');
        if (! $managerToken || ! $managerUsername) {
            return back()->with('error', 'Manager bot belum dikonfigurasi di server.');
        }

        $creation = TelegramBotCreation::create($data + [
            'manager_username' => $managerUsername,
            'status' => 'pending',
        ]);
        CreateMtprotoBot::dispatch($creation->id, $session->id);

        return redirect()->route('admin.telegram.index', ['creation' => $creation->id])
            ->with('success', 'Permintaan pembuatan bot diterima. Proses berjalan di background.');
    }

    public function mtprotoBotCreationStatus(TelegramBotCreation $creation)
    {
        $creation->load('bot');

        return response()->json([
            'status' => $creation->status,
            'error' => $creation->error,
            'bot' => $creation->bot ? ['id' => $creation->bot->id, 'username' => $creation->bot->username] : null,
        ]);
    }

    public function botStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'mode' => 'required|in:bot_api,mtproto_bot',
            'username' => 'nullable|string|max:120',
            'token' => 'required|string|max:255',
            'api_id' => 'nullable|required_if:mode,mtproto_bot|integer|min:1',
            'api_hash' => 'nullable|required_if:mode,mtproto_bot|string|max:255',
        ]);
        TelegramBot::create($data + ['is_active' => $request->boolean('is_active', true)]);

        return back()->with('success', 'Bot Telegram berhasil ditambahkan.');
    }

    public function botUpdate(Request $request, TelegramBot $bot)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'mode' => 'required|in:bot_api,mtproto_bot',
            'username' => 'nullable|string|max:120',
            'token' => 'nullable|string|max:255',
            'api_id' => 'nullable|required_if:mode,mtproto_bot|integer|min:1',
            'api_hash' => 'nullable|required_if:mode,mtproto_bot|string|max:255',
        ]);
        if ($data['token'] ?? null) {
            $bot->token = $data['token'];
        }
        unset($data['token']);
        if (($data['mode'] ?? null) === 'bot_api') {
            $data['api_id'] = null;
            $data['api_hash'] = null;
        }
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
            $bot->forceFill(['last_verified_at' => now(), 'last_error' => null])->save();

            return back()->with('success', 'Bot tersambung dengan baik'.($username ? ' (@'.$username.')' : '').'.');
        } catch (Throwable $exception) {
            report($exception);
            $bot->forceFill(['last_error' => 'Verifikasi gagal'])->save();

            return back()->with('error', 'Bot tidak dapat tersambung. Periksa token dan koneksi Telegram.');
        }
    }

    public function templateStore(Request $request, TelegramTemplateRegistry $registry)
    {
        $data = $request->validate([
            'telegram_bot_id' => 'nullable|exists:telegram_bots,id',
            'name' => 'required|string|max:120',
            'report_type' => 'required|in:grade,exam_plan,attendance',
            'body' => 'required|string|max:4000',
        ]);
        $this->validateTemplateParameters($data['body'], $data['report_type'], $registry);
        $data['parameter_schema'] = $registry->for($data['report_type']);
        TelegramTemplate::create($data + ['telegram_bot_id' => null, 'is_active' => $request->boolean('is_active', true)]);

        return back()->with('success', 'Template Bot berhasil ditambahkan.');
    }

    public function templateUpdate(Request $request, TelegramTemplate $template, TelegramTemplateRegistry $registry)
    {
        $data = $request->validate([
            'telegram_bot_id' => 'nullable|exists:telegram_bots,id',
            'name' => 'required|string|max:120',
            'report_type' => 'required|in:grade,exam_plan,attendance',
            'body' => 'required|string|max:4000',
        ]);
        $this->validateTemplateParameters($data['body'], $data['report_type'], $registry);
        $data['parameter_schema'] = $registry->for($data['report_type']);
        $template->update($data + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Template Bot berhasil diperbarui.');
    }

    public function templateDestroy(TelegramTemplate $template)
    {
        $template->delete();

        return back()->with('success', 'Template Bot berhasil dihapus.');
    }

    private function validateTemplateParameters(string $body, string $reportType, TelegramTemplateRegistry $registry): void
    {
        preg_match_all('/{{\s*([a-zA-Z0-9_]+)\s*}}/', $body, $matches);
        $allowed = $registry->allKeys($reportType);
        $unknown = array_values(array_diff(array_unique($matches[1] ?? []), $allowed));
        if ($unknown) {
            abort(422, 'Parameter template tidak dikenal: {{'.implode('}}, {{', $unknown).'}}');
        }
    }
}
