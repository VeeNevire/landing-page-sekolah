<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $fixes = [
            'attendance' => 'recorded_by',
            'teaching_assignments' => 'teacher_id',
            'teacher_notes' => 'author_id',
            'students' => 'homeroom_teacher_id',
            'submission_grades' => 'graded_by',
        ];

        foreach ($fixes as $table => $column) {
            Schema::table($table, function (Blueprint $t) use ($column) {
                $t->dropForeign([$column]);
                $t->unsignedBigInteger($column)->nullable()->change();
                $t->foreign($column)->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        $fixes = [
            'attendance' => 'recorded_by',
            'teaching_assignments' => 'teacher_id',
            'teacher_notes' => 'author_id',
            'students' => 'homeroom_teacher_id',
            'submission_grades' => 'graded_by',
        ];

        foreach ($fixes as $table => $column) {
            Schema::table($table, function (Blueprint $t) use ($column) {
                $t->dropForeign([$column]);
                $t->unsignedBigInteger($column)->nullable(false)->change();
                $t->foreign($column)->references('id')->on('users');
            });
        }
    }
};