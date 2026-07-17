<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$obj = app()->make('App\Http\Controllers\Admin\AdminController');
$ref = new ReflectionClass($obj);

echo "All methods:\n";
$found = false;
foreach ($ref->getMethods() as $m) {
    if (strpos($m->name, 'jurusans') !== false) {
        echo "  FOUND: '" . $m->name . "' (len=" . strlen($m->name) . ")\n";
        echo "  Bytes: " . bin2hex($m->name) . "\n";
        if ($m->name === 'jurusansSubjectsSave') {
            $found = true;
            echo "  --- EXACT MATCH ---\n";
        }
    }
}

if (!$found) {
    echo "No exact match for 'jurusansSubjectsSave' (len=" . strlen('jurusansSubjectsSave') . ")\n";
    echo "Comparing byte by byte...\n";
    $target = 'jurusansSubjectsSave';
    foreach ($ref->getMethods() as $m) {
        if (strpos($m->name, 'jurusans') !== false) {
            $minLen = min(strlen($m->name), strlen($target));
            for ($i = 0; $i < $minLen; $i++) {
                if ($m->name[$i] !== $target[$i]) {
                    echo "Diff at pos $i: '" . $m->name[$i] . "' (" . bin2hex($m->name[$i]) . ") vs '" . $target[$i] . "' (" . bin2hex($target[$i]) . ")\n";
                    break;
                }
            }
            if (strlen($m->name) !== strlen($target)) {
                echo "Length mismatch: " . strlen($m->name) . " vs " . strlen($target) . "\n";
            }
        }
    }
}
echo "Done\n";
