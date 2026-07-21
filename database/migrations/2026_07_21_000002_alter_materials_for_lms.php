<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->foreignId('module_id')
                  ->nullable()
                  ->constrained('course_modules')
                  ->nullOnDelete()
                  ->after('teaching_assignment_id');

            $table->string('file_path', 500)->nullable()->after('url');
            $table->string('file_name', 255)->nullable()->after('file_path');
            $table->unsignedBigInteger('file_size')->nullable()->after('file_name');
            $table->string('file_type', 100)->nullable()->after('file_size');
            $table->string('type', 20)->default('link')->after('description');
            $table->integer('order')->default(0)->after('type');

            $table->index('module_id');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn([
                'module_id',
                'file_path',
                'file_name',
                'file_size',
                'file_type',
                'type',
                'order',
            ]);
        });
    }
};
