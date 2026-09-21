<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('rfid_uid', 32)->nullable()->unique()->after('nis');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->timestamp('check_in_at')->nullable()->after('attendance_date');
            $table->string('source', 20)->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['check_in_at', 'source']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['rfid_uid']);
            $table->dropColumn('rfid_uid');
        });
    }
};
