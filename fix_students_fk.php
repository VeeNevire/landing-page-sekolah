<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('students', function (Blueprint $table) {
    // The foreign key was already dropped but not re-added
    // Just add it back with ON DELETE SET NULL
    $table->foreign('homeroom_teacher_id')->references('id')->on('users')->onDelete('set null');
});

echo "Fixed students.homeroom_teacher_id foreign key\n";