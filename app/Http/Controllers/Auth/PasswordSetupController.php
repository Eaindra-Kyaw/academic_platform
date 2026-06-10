<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordSetupController extends Controller
{
    public function showSetupForm($token)
    {
        // Find user by token
        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            abort(404, 'Invalid or expired setup link. Please contact administrator.');
        }

        return view('auth.setup-password', ['token' => $token, 'email' => $user->email]);
    }

    public function setupPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)
            ->where('remember_token', $request->token)
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid setup link.']);
        }

        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->password_changed_at = now();
        $user->remember_token = null;
        $user->save();

        return redirect()->route('login')->with('status', 'Password set successfully! Please login.');
    }
}
