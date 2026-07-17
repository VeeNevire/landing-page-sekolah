<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $obj = app()->make('App\Http\Controllers\Admin\AdminController');
    $ref = new ReflectionClass($obj);
    echo "Checking jurusanSubjectsSave...\n";
    $m = $ref->getMethod('jurusansSubjectsSave');
    echo "Method found: " . $m->getName() . "\n";
    echo "Declaring class: " . $m->getDeclaringClass()->getName() . "\n";
    echo "File: " . $m->getFileName() . "\n";
    echo "Start line: " . $m->getStartLine() . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
echo "Done\n";
