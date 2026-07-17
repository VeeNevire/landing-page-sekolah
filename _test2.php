<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the fallback
$full_name = 'Test Student';
$class_name = null;
$kelas = null;

$result = $class_name ?? $kelas?->nama_lengkap ?? ($full_name . ' (tanpa kelas)');
echo "Result: " . $result . PHP_EOL;
echo "Expected: Test Student (tanpa kelas)" . PHP_EOL;
echo "Length: " . strlen($result) . PHP_EOL;

// Also test if the Student model allows class_name to be nullable
$student = new App\Models\Student();
echo "Student fillable: " . json_encode($student->getFillable()) . PHP_EOL;
