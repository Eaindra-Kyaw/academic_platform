<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Mail\AdminNewUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        // ✅ FETCH ROLES AND DEPARTMENTS TO PASS TO THE VIEW
        $roles = Role::all();
        $departments = Department::all();

        return view('auth.register', compact('roles', 'departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'student_id' => 'nullable|string|max:50|unique:users,student_id',
            'current_year' => 'nullable|integer|min:1|max:6',
        ]);

        // Create user with PENDING status
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'department_id' => $validated['department_id'] ?? null,
            'student_id' => $validated['student_id'] ?? null,
            'current_year' => $validated['current_year'] ?? null,
            'is_active' => false, // ❌ NOT active until admin approves
            'registration_status' => 'pending',
            'registered_at' => now(),
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        // ✅ Send notification to ADMIN
        $adminEmails = User::where('role_id', 1)->pluck('email')->toArray();

        // ✅ Send the email using the now-existing blade file
        Mail::to($adminEmails)->send(new AdminNewUserNotification($user));

        return redirect()->route('login')
            ->with('status', '✅ Registration successful! Your account is pending admin approval. You will receive an email once approved.');
    }
}
