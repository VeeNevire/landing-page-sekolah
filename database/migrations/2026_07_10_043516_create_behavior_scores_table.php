<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('behavior_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('academic_periods');
            $table->enum('aspect', ['discipline', 'responsibility', 'collaboration', 'independence']);
            $table->string('grade', 5);
            $table->string('note', 500)->nullable();
            $table->unique(['student_id', 'period_id', 'aspect']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('behavior_scores');
    }
};
