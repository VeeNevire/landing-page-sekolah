<?php

use App\Http\Controllers\Api\RfidAttendanceController;
use Illuminate\Support\Facades\Route;

Route::post('/rfid/scan', [RfidAttendanceController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('api.rfid.scan');
