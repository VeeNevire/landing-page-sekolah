<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('attendance_id')->nullable()->unique()->constrained('attendance')->nullOnDelete();
        });
        Schema::create('rfid_notification_settings', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->foreignId('telegram_template_id')->nullable()->constrained('telegram_templates')->nullOnDelete();
        });
        DB::table('rfid_notification_settings')->insert(['id' => 1]);
        Schema::create('attendance_notification_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendance')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('telegram_bot_id')->nullable()->constrained('telegram_bots')->nullOnDelete();
            $table->string('status', 20)->index();
            $table->text('message');
            $table->unsignedInteger('attempts')->default(0);
            $table->string('reason', 40)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->unique(['attendance_id', 'parent_id'], 'attendance_parent_delivery_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_notification_deliveries');
        Schema::dropIfExists('rfid_notification_settings');
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attendance_id');
        });
    }
};
