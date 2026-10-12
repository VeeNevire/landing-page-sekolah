<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceNotificationDelivery;
use App\Models\TelegramBot;
use App\Models\TelegramTemplate;
use App\Services\AuditService;
use App\Services\RfidNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RfidNotificationController extends Controller
{
    public function index()
    {
        return view('admin.rfid-notifications', [
            'selectedTemplate' => DB::table('rfid_notification_settings')->where('id', 1)->value('telegram_template_id'),
            'selectedBot' => DB::table('rfid_notification_settings')->where('id', 1)->value('telegram_bot_id'),
            'bots' => TelegramBot::where('is_active', true)->orderBy('name')->get(),
            'templates' => TelegramTemplate::where('report_type', 'attendance')->where('is_active', true)->orderBy('name')->get(),
            'deliveries' => AttendanceNotificationDelivery::with(['attendance.student', 'parent', 'bot'])->latest()->paginate(30),
        ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'telegram_template_id' => ['nullable', 'required_with:telegram_bot_id', 'integer', Rule::exists('telegram_templates', 'id')
                ->where('report_type', 'attendance')->where('is_active', true)],
            'telegram_bot_id' => ['nullable', 'required_with:telegram_template_id', 'integer', Rule::exists('telegram_bots', 'id')->where('is_active', true)],
        ]);
        DB::table('rfid_notification_settings')->where('id', 1)->update([
            'telegram_template_id' => $data['telegram_template_id'] ?? null,
            'telegram_bot_id' => $data['telegram_bot_id'] ?? null,
        ]);
        AuditService::log('rfid.notifications.configure', null, null, null, $request->user()->id);

        return back()->with('success', 'Pengaturan notifikasi RFID disimpan.');
    }

    public function retry(Request $request, AttendanceNotificationDelivery $delivery)
    {
        $updated = AttendanceNotificationDelivery::whereKey($delivery->id)->where('status', 'failed')
            ->update(['status' => 'pending', 'reason' => null]);
        abort_unless($updated, 409, 'Hanya pengiriman gagal yang dapat dicoba ulang.');
        app(RfidNotificationService::class)->enqueue($delivery->id);
        AuditService::log('rfid.notifications.retry', 'AttendanceNotificationDelivery', $delivery->id, null, $request->user()->id);

        return back()->with('success', 'Pengiriman akan dicoba ulang melalui antrean.');
    }
}
