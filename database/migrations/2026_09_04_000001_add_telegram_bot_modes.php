<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('telegram_bots', function (Blueprint $table) {
            $table->string('mode', 20)->default('bot_api')->after('name');
            $table->unsignedInteger('api_id')->nullable()->after('token');
            $table->text('api_hash')->nullable()->after('api_id');
            $table->timestamp('last_verified_at')->nullable()->after('is_active');
            $table->text('last_error')->nullable()->after('last_verified_at');
        });

        Schema::table('telegram_templates', function (Blueprint $table) {
            $table->json('parameter_schema')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('telegram_templates', function (Blueprint $table) {
            $table->dropColumn('parameter_schema');
        });
        Schema::table('telegram_bots', function (Blueprint $table) {
            $table->dropColumn(['mode', 'api_id', 'api_hash', 'last_verified_at', 'last_error']);
        });
    }
};
