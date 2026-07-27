<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('teacher_notes', function (Blueprint $table) {
    $table->dropForeign(['author_id']);
    $table->unsignedBigInteger('author_id')->nullable()->change();
    $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
});

echo "Fixed teacher_notes.author_id foreign key\n";