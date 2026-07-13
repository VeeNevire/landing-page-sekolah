<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        $role = $user->role ?? 'parent';

        AuditService::log('auth.login', 'User', $user->id, $user->id);

        if ($role === 'admin') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        if (in_array($role, ['teacher', 'homeroom', 'principal'])) {
            return redirect()->intended(route('guru.dashboard', absolute: false));
        }

        return redirect()->intended(route('portal.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        AuditService::log('auth.logout', 'User', auth()->id(), auth()->id());
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
