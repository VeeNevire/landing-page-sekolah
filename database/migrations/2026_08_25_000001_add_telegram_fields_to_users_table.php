<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_chat_id')->nullable()->unique()->after('email');
            $table->string('telegram_link_token_hash')->nullable()->index()->after('telegram_chat_id');
            $table->timestamp('telegram_link_token_expires_at')->nullable()->after('telegram_link_token_hash');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['telegram_chat_id']);
            $table->dropIndex(['telegram_link_token_hash']);
            $table->dropColumn([
                'telegram_chat_id',
                'telegram_link_token_hash',
                'telegram_link_token_expires_at',
            ]);
        });
    }
};
