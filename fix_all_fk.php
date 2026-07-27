<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

$fixes = [
    'attendance' => 'recorded_by',
    'teaching_assignments' => 'teacher_id',
    'teacher_notes' => 'author_id',
    'students' => 'homeroom_teacher_id',
    'submission_grades' => 'graded_by',
];

foreach ($fixes as $table => $column) {
    try {
        Schema::table($table, function (Blueprint $t) use ($column) {
            $t->dropForeign([$column]);
            $t->unsignedBigInteger($column)->nullable()->change();
            $t->foreign($column)->references('id')->on('users')->onDelete('set null');
        });
        echo "✅ {$table}.{$column} -> ON DELETE SET NULL\n";
    } catch (\Exception $e) {
        echo "❌ {$table}.{$column}: " . $e->getMessage() . "\n";
    }
}