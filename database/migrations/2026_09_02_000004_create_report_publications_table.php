<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('telegram_template_id')->nullable()->constrained('telegram_templates')->nullOnDelete();
            $table->string('report_type', 40);
            $table->string('class_name', 80);
            $table->date('report_date')->nullable();
            $table->timestamp('published_at');
            $table->timestamps();
            $table->unique(['report_type', 'class_name', 'report_date']);
        });
    }

    public function down(): void { Schema::dropIfExists('report_publications'); }
};
