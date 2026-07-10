<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name', 150)->nullable()->after('name');
            $table->enum('role', ['parent', 'teacher', 'homeroom', 'admin', 'principal'])->default('parent')->after('password');
            $table->boolean('is_active')->default(true)->after('role');
            $table->datetime('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'role', 'is_active', 'last_login_at']);
        });
    }
};
