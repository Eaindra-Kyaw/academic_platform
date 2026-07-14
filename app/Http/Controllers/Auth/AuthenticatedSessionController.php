<?php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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

        $user = Auth::user();

        // ✅ CHECK: Is email verified?
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Please verify your email address before logging in.'])
                ->with('verification_required', true);
        }

        // ✅ CHECK: Is user active?
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is pending approval. Please contact admin.']);
        }

        // ✅ CHECK: Must change password? (Skip for Admins)
        if ($user->must_change_password && $user->role_id != 1) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Please change your password before continuing.');
        }

        // ✅ REDIRECT BASED ON ROLE
        if ($user->role_id == 1) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role_id == 2) {
            return redirect()->route('lecturer.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
