<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test: change Kelas wali and see if students get updated in DB
$kelas = \App\Models\Kelas::where('id', 6)->first(); // X RPL 1 with wali 3
echo "Before: Kelas wali = {$kelas->homeroom_teacher_id}\n";

$newWali = \App\Models\User::whereIn('role', ['teacher', 'homeroom'])->where('id', '!=', 3)->first()->id;
echo "New wali = $newWali\n";

$kelas->update(['homeroom_teacher_id' => $newWali]);

// Refresh and check students
$kelas->refresh();
echo "After: Kelas wali = {$kelas->homeroom_teacher_id}\n";

$students = \App\Models\Student::where('kelas_id', 6)->get();
foreach ($students as $s) {
    echo "Student {$s->full_name} - homeroom_teacher_id in DB: {$s->homeroom_teacher_id}\n";
}