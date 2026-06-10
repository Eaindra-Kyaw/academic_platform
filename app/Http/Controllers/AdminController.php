<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function users()
    {
        return view('admin.users');
    }

    public function courses()
    {
        return view('admin.courses');
    }

    public function departments()
    {
        return view('admin.departments');
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|in:1,2,3',
            'department_id' => 'nullable|exists:departments,id',
            'current_year' => 'nullable|integer|min:1|max:6',
        ]);

        // Generate unique token for password setup
        $token = Str::random(60);

        $user = User::create([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(16)),
            'department_id' => $request->department_id,
            'current_year' => $request->current_year,
            'is_active' => true,
            'must_change_password' => true,
            'remember_token' => $token,
        ]);

        // Generate password setup URL
        $setupUrl = url('/password/setup/' . $token);

        // Send welcome email (or show link on screen)
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user, $setupUrl));
            $message = 'User created successfully! Welcome email sent to ' . $user->email;
        } catch (\Exception $e) {
            $message = 'User created successfully! Setup link: ' . $setupUrl;
        }

        return redirect()->back()->with('success', $message);
    }

    public function resendSetupLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Generate new token
        $token = Str::random(60);
        $user->remember_token = $token;
        $user->must_change_password = true;
        $user->save();

        $setupLink = url('/password/setup/' . $token);

        return response()->json(['link' => $setupLink, 'email' => $user->email]);
    }
}
