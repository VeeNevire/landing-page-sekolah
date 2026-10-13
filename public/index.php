<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
// MadelineProto echoes a performance warning on Windows while Composer loads it;
// discard it so it does not corrupt responses such as PDF and Excel downloads.
ob_start();
require __DIR__.'/../vendor/autoload.php';
ob_end_clean();

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
