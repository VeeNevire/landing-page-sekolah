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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 20)->unique();
            $table->string('full_name', 150);
            $table->date('birth_date')->nullable();
            $table->string('class_name', 80);
            $table->string('program_name', 120);
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('users');
            $table->enum('status', ['active', 'graduated', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
