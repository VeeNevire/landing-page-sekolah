<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->foreignId('telegram_template_id')->nullable()->constrained('telegram_templates')->nullOnDelete()->after('published_at');
            $table->datetime('telegram_notified_at')->nullable()->after('telegram_template_id');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['telegram_template_id']);
            $table->dropColumn(['telegram_template_id', 'telegram_notified_at']);
        });
    }
};
