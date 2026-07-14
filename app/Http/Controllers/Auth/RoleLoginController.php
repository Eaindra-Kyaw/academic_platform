<?php
// app/Http/Controllers/Auth/RoleLoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleLoginController extends Controller
{
    // Admin Login - Only allows users with role_id = 1
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if user is Admin (role_id = 1)
            if ($user->role_id == 1) {
                $request->session()->regenerate();
                // ✅ FIXED: Direct redirect instead of intended()
                return redirect()->route('admin.dashboard');
            }

            // Wrong role - logout and show error
            Auth::logout();
            return back()->withErrors([
                'email' => 'This account does not have admin access. Please use the correct login panel.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Lecturer Login - Only allows users with role_id = 2
    public function lecturerLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if user is Lecturer (role_id = 2)
            if ($user->role_id == 2) {
                $request->session()->regenerate();
                // ✅ FIXED: Direct redirect instead of intended()
                return redirect()->route('lecturer.dashboard');
            }

            // Wrong role - logout and show error
            Auth::logout();
            return back()->withErrors([
                'email' => 'This account does not have lecturer access. Please use the correct login panel.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Student Login - Only allows users with role_id = 3
    public function studentLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if user is Student (role_id = 3)
            if ($user->role_id == 3) {
                $request->session()->regenerate();
                // ✅ FIXED: Direct redirect instead of intended()
                return redirect()->route('student.dashboard');
            }

            // Wrong role - logout and show error
            Auth::logout();
            return back()->withErrors([
                'email' => 'This account does not have student access. Please use the correct login panel.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
