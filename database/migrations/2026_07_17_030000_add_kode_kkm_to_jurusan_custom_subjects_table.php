<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurusan_custom_subjects', function (Blueprint $table) {
            $table->string('kode', 20)->nullable()->after('jurusan_id');
            $table->decimal('kkm', 5, 2)->nullable()->after('nama');
            $table->dropColumn('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('jurusan_custom_subjects', function (Blueprint $table) {
            $table->dropColumn(['kode', 'kkm']);
            $table->text('deskripsi')->nullable()->after('nama');
        });
    }
};
