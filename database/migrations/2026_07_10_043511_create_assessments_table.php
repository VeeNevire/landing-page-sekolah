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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_assignment_id')->constrained()->cascadeOnDelete();
            $table->string('title', 160);
            $table->enum('component', ['quiz', 'homework', 'project', 'uts', 'uas', 'remedial', 'other']);
            $table->date('assessment_date');
            $table->decimal('max_score', 6, 2)->default(100.00);
            $table->decimal('weight_percent', 5, 2)->nullable();
            $table->datetime('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
