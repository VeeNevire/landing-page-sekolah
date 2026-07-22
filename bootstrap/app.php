<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['role' => \App\Http\Middleware\CheckRole::class]);
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo(function ($request) {
            $role = $request->user()?->role;
            return match($role) {
                'admin' => '/admin/dashboard',
                'student' => '/siswa/dashboard',
                'teacher', 'homeroom' => '/guru/dashboard',
                'principal' => '/admin/dashboard',
                'applicant' => '/ppdb/status',
                default => '/portal/dashboard',
            };
        });
         $middleware->validateCsrfTokens(except: [
        'admin/jadwal',
        'admin/jadwal/*',
        'admin/students',
        'admin/students/*',
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
