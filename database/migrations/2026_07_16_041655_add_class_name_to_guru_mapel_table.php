<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru_mapel', function (Blueprint $table) {
            $table->string('class_name', 80)->nullable()->after('guru_id');
        });
    }

    public function down(): void
    {
        Schema::table('guru_mapel', function (Blueprint $table) {
            $table->dropColumn('class_name');
        });
    }
};
