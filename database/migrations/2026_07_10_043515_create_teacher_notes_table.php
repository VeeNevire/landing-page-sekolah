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
        Schema::create('teacher_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('academic_periods');
            $table->foreignId('author_id')->constrained('users');
            $table->enum('category', ['academic', 'behavior', 'career', 'general']);
            $table->text('note');
            $table->text('follow_up')->nullable();
            $table->boolean('visible_to_parent')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_notes');
    }
};
