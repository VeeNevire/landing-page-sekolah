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
        // teaching_assignments.teacher_id
        Schema::table('teaching_assignments', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->unsignedBigInteger('teacher_id')->nullable()->change();
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
        });

        // attendance.recorded_by
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['recorded_by']);
            $table->unsignedBigInteger('recorded_by')->nullable()->change();
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');
        });

        // teacher_notes.author_id
        Schema::table('teacher_notes', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->unsignedBigInteger('author_id')->nullable()->change();
            $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
        });

        // submission_grades.graded_by
        Schema::table('submission_grades', function (Blueprint $table) {
            $table->dropForeign(['graded_by']);
            $table->unsignedBigInteger('graded_by')->nullable()->change();
            $table->foreign('graded_by')->references('id')->on('users')->onDelete('set null');
        });

        // students.homeroom_teacher_id
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['homeroom_teacher_id']);
            $table->unsignedBigInteger('homeroom_teacher_id')->nullable()->change();
            $table->foreign('homeroom_teacher_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teaching_assignments', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->unsignedBigInteger('teacher_id')->nullable(false)->change();
            $table->foreign('teacher_id')->references('id')->on('users');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['recorded_by']);
            $table->unsignedBigInteger('recorded_by')->nullable(false)->change();
            $table->foreign('recorded_by')->references('id')->on('users');
        });

        Schema::table('teacher_notes', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->unsignedBigInteger('author_id')->nullable(false)->change();
            $table->foreign('author_id')->references('id')->on('users');
        });

        Schema::table('submission_grades', function (Blueprint $table) {
            $table->dropForeign(['graded_by']);
            $table->unsignedBigInteger('graded_by')->nullable(false)->change();
            $table->foreign('graded_by')->references('id')->on('users');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['homeroom_teacher_id']);
            $table->unsignedBigInteger('homeroom_teacher_id')->nullable(false)->change();
            $table->foreign('homeroom_teacher_id')->references('id')->on('users');
        });
    }
};