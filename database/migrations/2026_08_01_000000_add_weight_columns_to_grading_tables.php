<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function addWeightColumns(Blueprint $table): void
    {
        $table->decimal('weight_quiz', 5, 2)->nullable();
        $table->decimal('weight_homework', 5, 2)->nullable();
        $table->decimal('weight_project', 5, 2)->nullable();
        $table->decimal('weight_assignment', 5, 2)->nullable();
        $table->decimal('weight_uts', 5, 2)->nullable();
        $table->decimal('weight_uas', 5, 2)->nullable();
    }

    private function dropWeightColumns(Blueprint $table): void
    {
        $table->dropColumn([
            'weight_quiz',
            'weight_homework',
            'weight_project',
            'weight_assignment',
            'weight_uts',
            'weight_uas',
        ]);
    }

    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $this->addWeightColumns($table);
        });

        Schema::table('jurusan_custom_subjects', function (Blueprint $table) {
            $this->addWeightColumns($table);
        });

        Schema::table('teaching_assignments', function (Blueprint $table) {
            $this->addWeightColumns($table);
        });
    }

    public function down(): void
    {
        Schema::table('teaching_assignments', function (Blueprint $table) {
            $this->dropWeightColumns($table);
        });

        Schema::table('jurusan_custom_subjects', function (Blueprint $table) {
            $this->dropWeightColumns($table);
        });

        Schema::table('subjects', function (Blueprint $table) {
            $this->dropWeightColumns($table);
        });
    }
};
