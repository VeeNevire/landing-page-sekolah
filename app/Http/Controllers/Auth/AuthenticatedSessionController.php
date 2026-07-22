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

        AuditService::log('auth.login', 'User', $user->id, $user->name, $user->id);

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'student') {
            return redirect()->route('siswa.dashboard');
        }

        if ($role === 'principal') {
            return redirect()->route('admin.dashboard');
        }

        if (in_array($role, ['teacher', 'homeroom'])) {
            return redirect()->route('guru.dashboard');
        }

        if ($role === 'applicant') {
            $applicant = $user->applicant;
            
            if (!$applicant) {
                return redirect()->route('ppdb.form', ['step' => 1]);
            }
            
            return match($applicant->status) {
                'paid' => redirect()->route('ppdb.success'),
                'rejected' => redirect()->route('ppdb.status'),
                'verified' => redirect()->route('ppdb.payment'),
                'submitted' => redirect()->route('ppdb.status'),
                default => match($applicant->completion_step) {
                    'completed' => redirect()->route('ppdb.status'),
                    'documents' => redirect()->route('ppdb.upload'),
                    'parent_data' => redirect()->route('ppdb.form', ['step' => 3]),
                    'student_data' => redirect()->route('ppdb.form', ['step' => 2]),
                    default => redirect()->route('ppdb.form', ['step' => 1]),
                },
            };
        }

        return redirect()->route('portal.dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        AuditService::log('auth.logout', 'User', auth()->id(), auth()->user()->name, auth()->id());
        
        // Revoke Google OAuth token jika user login via Google
        if ($user && $user->google_id) {
            try {
                $token = $user->google_token ?? null;
                if ($token) {
                    // Revoke Google access token
                    $client = new \GuzzleHttp\Client();
                    $client->post('https://oauth2.googleapis.com/revoke', [
                        'form_params' => ['token' => $token]
                    ]);
                }
            } catch (\Exception $e) {
                // Silent fail jika revoke gagal
            }
        }
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $redirectTo = $request->input('redirect_to', '/');

        return redirect($redirectTo);
    }
}
