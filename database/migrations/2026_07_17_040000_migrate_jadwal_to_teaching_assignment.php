<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah teaching_assignment_id (nullable dulu)
        Schema::table('jadwal', function (Blueprint $table) {
            $table->foreignId('teaching_assignment_id')->nullable()->constrained('teaching_assignments')->cascadeOnDelete();
        });

        // Migrasi data: cocokkan guru_mapel_id ke teaching_assignment
        DB::statement('
            UPDATE jadwal j
            JOIN guru_mapel gm ON j.guru_mapel_id = gm.id
            JOIN teaching_assignments ta ON ta.period_id = gm.semester_id
                AND ta.subject_id = gm.mapel_id
                AND ta.teacher_id = gm.guru_id
                AND (ta.class_name = gm.class_name OR (ta.class_name IS NULL AND gm.class_name IS NULL))
            SET j.teaching_assignment_id = ta.id
        ');

        // Hapus jadwal yang gak ketemu padanannya
        DB::table('jadwal')->whereNull('teaching_assignment_id')->delete();

        // Drop FK & column guru_mapel_id
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropForeign(['guru_mapel_id']);
            $table->dropColumn('guru_mapel_id');
        });

        // Jadikan teaching_assignment_id NOT NULL
        Schema::table('jadwal', function (Blueprint $table) {
            $table->foreignId('teaching_assignment_id')->nullable(false)->change();
        });

        // Update unique constraint
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropUnique(['guru_mapel_id', 'day']);
            $table->unique(['teaching_assignment_id', 'day']);
        });

        // Drop tabel guru_mapel (pindah ke TeachingAssignment)
        Schema::dropIfExists('guru_mapel');
    }

    public function down(): void
    {
        // Restore guru_mapel table
        Schema::create('guru_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('academic_periods')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->string('class_name', 80)->nullable();
            $table->unique(['semester_id', 'mapel_id', 'guru_id']);
            $table->timestamps();
        });

        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropUnique(['teaching_assignment_id', 'day']);
            $table->foreignId('guru_mapel_id')->nullable()->constrained('guru_mapel')->cascadeOnDelete();
        });

        // Reverse data (best effort)
        DB::statement('
            UPDATE jadwal j
            JOIN teaching_assignments ta ON j.teaching_assignment_id = ta.id
            JOIN guru_mapel gm ON gm.semester_id = ta.period_id
                AND gm.mapel_id = ta.subject_id
                AND gm.guru_id = ta.teacher_id
            SET j.guru_mapel_id = gm.id
        ');

        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropColumn('teaching_assignment_id');
            $table->foreignId('guru_mapel_id')->nullable(false)->change();
            $table->unique(['guru_mapel_id', 'day']);
        });
    }
};
