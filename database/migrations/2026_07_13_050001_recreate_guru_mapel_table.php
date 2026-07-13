<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('guru_mapel');

        Schema::create('guru_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('academic_periods')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['semester_id', 'mapel_id', 'guru_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_mapel');
    }
};
