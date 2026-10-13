<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfid_settings', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->boolean('late_enabled')->default(false);
            $table->time('late_after')->nullable();
            $table->timestamps();
        });
        DB::table('rfid_settings')->insert(['id' => 1, 'created_at' => now(), 'updated_at' => now()]);

        Schema::table('rfid_reader_state', function (Blueprint $table) {
            $table->string('last_uid', 32)->nullable()->after('last_message');
            $table->timestamp('device_seen_at')->nullable()->after('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::table('rfid_reader_state', function (Blueprint $table) {
            $table->dropColumn(['last_uid', 'device_seen_at']);
        });
        Schema::dropIfExists('rfid_settings');
    }
};
