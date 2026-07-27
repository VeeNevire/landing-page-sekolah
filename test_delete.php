<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('role', 'teacher')->first();
echo "Deleting user: {$user->name} (ID: {$user->id})\n";
echo "Attendance records before: " . \App\Models\Attendance::where('recorded_by', $user->id)->count() . "\n";
echo "Teaching assignments before: " . \App\Models\TeachingAssignment::where('teacher_id', $user->id)->count() . "\n";

$user->delete();

echo "User deleted successfully\n";
echo "Attendance records after: " . \App\Models\Attendance::where('recorded_by', $user->id)->count() . "\n";
echo "Teaching assignments after: " . \App\Models\TeachingAssignment::where('teacher_id', $user->id)->count() . "\n";