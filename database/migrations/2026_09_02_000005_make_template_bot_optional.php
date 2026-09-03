<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('telegram_templates', function (Blueprint $table) {
            $table->foreignId('telegram_bot_id')->nullable()->change();
        });
        Schema::table('assessments', function (Blueprint $table) {
            $table->foreignId('telegram_bot_id')->nullable()->constrained('telegram_bots')->nullOnDelete()->after('telegram_template_id');
        });
        Schema::table('report_publications', function (Blueprint $table) {
            $table->foreignId('telegram_bot_id')->nullable()->constrained('telegram_bots')->nullOnDelete()->after('telegram_template_id');
        });
    }

    public function down(): void
    {
        Schema::table('report_publications', function (Blueprint $table) { $table->dropForeign(['telegram_bot_id']); $table->dropColumn('telegram_bot_id'); });
        Schema::table('assessments', function (Blueprint $table) { $table->dropForeign(['telegram_bot_id']); $table->dropColumn('telegram_bot_id'); });
        Schema::table('telegram_templates', function (Blueprint $table) { $table->foreignId('telegram_bot_id')->nullable(false)->change(); });
    }
};
