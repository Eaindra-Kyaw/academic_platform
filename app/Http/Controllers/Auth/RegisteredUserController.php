<?php
// app/Http/Controllers/Auth/RegisteredUserController.php

use App\Mail\AdminNewUserNotification;
use Illuminate\Support\Facades\Mail;

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed',
        'role_id' => 'required|exists:roles,id',
        'department_id' => 'nullable|exists:departments,id',
        'student_id' => 'nullable|string|max:50',
        'current_year' => 'nullable|integer|min:1|max:6',
    ]);

    // Create user with PENDING status
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role_id' => $validated['role_id'],
        'department_id' => $validated['department_id'],
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

    try {
        Mail::to($adminEmails)->send(new AdminNewUserNotification($user));
    } catch (\Exception $e) {
        // Log error but continue
        \Log::error('Failed to send admin notification: ' . $e->getMessage());
    }

    return redirect()->route('login')
        ->with('status', '✅ Registration successful! Your account is pending admin approval. You will receive an email once approved.');
}
