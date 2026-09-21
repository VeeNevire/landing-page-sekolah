<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_mtproto_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('api_id');
            $table->text('api_hash');
            $table->string('session_path', 255);
            $table->unsignedBigInteger('telegram_user_id')->nullable();
            $table->string('username', 120)->nullable();
            $table->string('first_name', 120)->nullable();
            $table->string('status', 30)->default('disconnected');
            $table->timestamp('connected_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_mtproto_sessions');
    }
};
