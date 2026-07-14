<?php
// app/Http/Controllers/Auth/PasswordSetupController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class PasswordSetupController extends Controller
{
    /**
     * Display the password setup form.
     */
    public function showSetupForm(Request $request): View
    {
        $token = $request->route('token');
        $email = $request->query('email');

        // Verify the token exists
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$tokenData) {
            abort(404, 'Invalid or expired setup link.');
        }

        // Check if token matches (plain text comparison since we're using plain text tokens)
        if ($token !== $tokenData->token) {
            abort(404, 'Invalid or expired setup link.');
        }

        // Check if token is expired (48 hours)
        $createdAt = Carbon::parse($tokenData->created_at);
        if ($createdAt->diffInHours(now()) > 48) {
            abort(404, 'This setup link has expired. Please contact admin for a new one.');
        }

        return view('auth.setup-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Handle password setup - User sets their own password
     */
    public function setupPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Find the user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Verify the token
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenData || $request->token !== $tokenData->token) {
            return back()->withErrors(['email' => 'Invalid or expired setup link.']);
        }

        // Check if token is expired (48 hours)
        $createdAt = Carbon::parse($tokenData->created_at);
        if ($createdAt->diffInHours(now()) > 48) {
            return back()->withErrors(['email' => 'This setup link has expired. Please contact admin for a new one.']);
        }

        // Update the user's password
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
            'email_verified_at' => now(),
            'remember_token' => Str::random(60),
        ]);

        // Delete the token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('login')
            ->with('status', '✅ Password set successfully! You can now login with your new password.');
    }
}
