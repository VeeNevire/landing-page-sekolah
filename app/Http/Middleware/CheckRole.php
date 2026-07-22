<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!in_array($user?->role, $roles)) {
            if ($user) {
                return match($user->role) {
                    'principal', 'admin' => redirect()->route('admin.dashboard'),
                    'teacher', 'homeroom' => redirect()->route('guru.dashboard'),
                    'student' => redirect()->route('siswa.dashboard'),
                    default => redirect()->route('portal.dashboard'),
                };
            }

            abort(403, 'Akses ditolak. Anda tidak memiliki hak untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
