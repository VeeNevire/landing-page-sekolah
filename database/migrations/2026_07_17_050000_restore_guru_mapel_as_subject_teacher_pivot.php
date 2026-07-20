<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('academic_periods')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->string('class_name', 80)->nullable();
            $table->timestamps();
            $table->unique(['semester_id', 'mapel_id', 'guru_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_mapel');
    }
};
