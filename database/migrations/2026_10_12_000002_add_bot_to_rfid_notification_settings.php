<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rfid_notification_settings', function (Blueprint $table) {
            $table->foreignId('telegram_bot_id')->nullable()->constrained('telegram_bots')->nullOnDelete();
        });
        $templateId = DB::table('rfid_notification_settings')->where('id', 1)->value('telegram_template_id');
        if ($templateId) {
            DB::table('rfid_notification_settings')->where('id', 1)->update([
                'telegram_bot_id' => DB::table('telegram_templates')->where('id', $templateId)->value('telegram_bot_id'),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('rfid_notification_settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('telegram_bot_id');
        });
    }
};
