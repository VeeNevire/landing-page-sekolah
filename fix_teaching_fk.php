<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('teaching_assignments', function (Blueprint $table) {
    $table->dropForeign(['teacher_id']);
    $table->unsignedBigInteger('teacher_id')->nullable()->change();
    $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
});

echo "Fixed teaching_assignments.teacher_id foreign key\n";