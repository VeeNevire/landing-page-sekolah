<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_bots', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('username', 120)->nullable();
            $table->text('token');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('telegram_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_bot_id')->constrained('telegram_bots')->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('report_type', 40)->default('grade');
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('telegram_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('telegram_bot_id')->constrained('telegram_bots')->cascadeOnDelete();
            $table->string('chat_id', 80)->nullable();
            $table->string('telegram_username', 120)->nullable();
            $table->string('link_token_hash', 64)->nullable()->index();
            $table->timestamp('link_token_expires_at')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'telegram_bot_id']);
            $table->unique(['telegram_bot_id', 'chat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_connections');
        Schema::dropIfExists('telegram_templates');
        Schema::dropIfExists('telegram_bots');
    }
};
