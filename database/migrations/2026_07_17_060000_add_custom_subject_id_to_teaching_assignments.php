<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teaching_assignments', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->change();
            $table->foreignId('custom_subject_id')->nullable()->after('subject_id')
                ->constrained('jurusan_custom_subjects')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teaching_assignments', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable(false)->change();
            $table->dropConstrainedForeignId('custom_subject_id');
        });
    }
};
