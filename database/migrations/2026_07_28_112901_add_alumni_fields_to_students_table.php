<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->year('graduation_year')->nullable()->after('status');
            $table->string('alumni_status')->nullable()->after('graduation_year');
            $table->string('cv_path')->nullable()->after('alumni_status');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['graduation_year', 'alumni_status', 'cv_path']);
        });
    }
};