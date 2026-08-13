<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pkl_companies', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('bidang')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('kontak_person')->nullable();
            $table->string('kontak_telepon')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkl_companies');
    }
};
