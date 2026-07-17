<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('tingkat')->comment('10, 11, 12');
            $table->string('nama', 60)->comment('contoh: RPL 1, DKV 2');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['jurusan_id', 'tingkat', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
