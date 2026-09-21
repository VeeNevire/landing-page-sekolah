<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfid_reader_state', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->uuid('session_id')->nullable();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('uid', 32)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('last_message')->nullable();
            $table->timestamp('last_seen_at')->nullable();
        });
        DB::table('rfid_reader_state')->insert(['id' => 1]);
    }

    public function down(): void
    {
        Schema::dropIfExists('rfid_reader_state');
    }
};
