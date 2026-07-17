<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurusan_custom_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')->constrained()->cascadeOnDelete();
            $table->string('nama', 120);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurusan_custom_subjects');
    }
};
