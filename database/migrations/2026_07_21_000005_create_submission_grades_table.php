<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('score', 6, 2);
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->constrained('users');
            $table->dateTime('graded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_grades');
    }
};
