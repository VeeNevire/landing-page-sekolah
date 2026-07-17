<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_custom_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jurusan_custom_subject_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['kelas_id', 'jurusan_custom_subject_id'], 'kelas_custom_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_custom_subject');
    }
};
