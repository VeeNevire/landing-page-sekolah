<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_question_id')->constrained()->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->string('selected_option')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('score', 6, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->unique(['quiz_attempt_id', 'quiz_question_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
};
