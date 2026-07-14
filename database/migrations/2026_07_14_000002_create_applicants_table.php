<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Data calon siswa
            $table->string('full_name', 150);
            $table->string('nickname', 50)->nullable();
            $table->string('birth_place', 60)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('religion', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('asal_sekolah', 150)->nullable();
            $table->string('nisn', 20)->nullable();

            // Pilihan program
            $table->enum('jenjang', ['SMA', 'SMK'])->nullable();
            $table->string('program_diminati', 120)->nullable();

            // Data Ayah
            $table->string('ayah_name', 100)->nullable();
            $table->string('ayah_occupation', 100)->nullable();
            $table->string('ayah_phone', 20)->nullable();
            $table->string('ayah_email', 100)->nullable();

            // Data Ibu
            $table->string('ibu_name', 100)->nullable();
            $table->string('ibu_occupation', 100)->nullable();
            $table->string('ibu_phone', 20)->nullable();
            $table->string('ibu_email', 100)->nullable();

            // Data Wali (opsional)
            $table->string('wali_name', 100)->nullable();
            $table->string('wali_occupation', 100)->nullable();
            $table->string('wali_phone', 20)->nullable();
            $table->string('wali_email', 100)->nullable();

            // Status
            $table->enum('status', ['draft', 'submitted', 'verified', 'accepted', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
