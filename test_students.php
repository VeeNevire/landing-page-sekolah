<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$kelasWithStudents = \App\Models\Kelas::with('students')->get()->filter(fn($k) => $k->students->count() > 0)->values();

foreach ($kelasWithStudents as $k) {
    echo "Kelas: {$k->nama_lengkap} (ID: {$k->id}) - Wali: {$k->homeroom_teacher_id} - Students: {$k->students->count()}\n";
    foreach ($k->students as $s) {
        echo "  Student: {$s->full_name} - homeroom_teacher_id in DB: {$s->homeroom_teacher_id}\n";
    }
}