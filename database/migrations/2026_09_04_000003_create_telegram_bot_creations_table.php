<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_bot_creations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('username', 32);
            $table->string('manager_username', 120)->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('telegram_bot_id')->nullable()->constrained('telegram_bots')->nullOnDelete();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_bot_creations');
    }
};
