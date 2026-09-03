<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (! $token || ! Schema::hasTable('telegram_bots')) return;

        $username = env('TELEGRAM_BOT_USERNAME');
        $exists = DB::table('telegram_bots')->when($username, fn ($q) => $q->where('username', $username))->value('id');
        if (! $exists) {
            DB::table('telegram_bots')->insert([
                'name' => 'Bot Telegram Utama',
                'username' => $username,
                'token' => Crypt::encryptString($token),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void {}
};
