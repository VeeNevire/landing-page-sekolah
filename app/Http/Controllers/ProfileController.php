<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        AuditService::log('auth.logout', null, null, null, auth()->id());

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

        return redirect('/');
    }
}
