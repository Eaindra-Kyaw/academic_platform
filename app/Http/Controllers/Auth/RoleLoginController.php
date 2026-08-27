<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleLoginController extends Controller
{
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role_id == 1) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
            Auth::logout();
            return back()->withErrors(['email' => 'This account does not have admin access.']);
        }
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function lecturerLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role_id == 2) {
                $request->session()->regenerate();
                return redirect()->route('lecturer.dashboard');
            }
            Auth::logout();
            return back()->withErrors(['email' => 'This account does not have lecturer access.']);
        }
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function studentLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role_id == 3) {
                $request->session()->regenerate();
                return redirect()->route('student.dashboard');
            }
            Auth::logout();
            return back()->withErrors(['email' => 'This account does not have student access.']);
        }
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }
}
