<?php
// app/Http/Controllers/Auth/PasswordSetupController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PasswordSetupController extends Controller
{
    /**
     * Display the password setup form.
     */
    public function showSetupForm(Request $request): View
    {
        $token = $request->route('token');
        $email = $request->query('email');

        return view('auth.setup-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Handle password setup.
     */
    public function setupPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                    'must_change_password' => false,
                    'email_verified_at' => now(),
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password set successfully! You can now log in.')
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
