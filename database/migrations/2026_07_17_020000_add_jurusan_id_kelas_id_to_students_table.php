<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('jurusan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropForeign(['jurusan_id']);
            $table->dropColumn(['jurusan_id', 'kelas_id']);
        });
    }
};
