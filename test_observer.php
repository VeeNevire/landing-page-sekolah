<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$kelas = \App\Models\Kelas::whereNotNull('homeroom_teacher_id')->first();
$oldTeacher = $kelas->homeroom_teacher_id;
$newTeacher = \App\Models\User::whereIn('role', ['teacher', 'homeroom'])->where('id', '!=', $oldTeacher)->first()->id;

echo "Old: $oldTeacher\n";
echo "New: $newTeacher\n";

$kelas->update(['homeroom_teacher_id' => $newTeacher]);
echo "Kelas updated. Checking students...\n";

$students = \App\Models\Student::where('kelas_id', $kelas->id)->get();
foreach ($students as $s) {
    echo "Student {$s->full_name} homeroom_teacher_id: {$s->homeroom_teacher_id}\n";
}