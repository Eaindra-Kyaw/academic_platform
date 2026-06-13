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
        $clickableLink = '<a href="' . $setupUrl . '" style="color: #166534; text-decoration: underline;" target="_blank">Click here to set password</a>';

        // ONLY show clickable link (no email message)
        $message = 'User created successfully!<br><br><strong>Setup Link:</strong> ' . $clickableLink;

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

    // Update user
public function updateUser(Request $request, $id)
{
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email|unique:users,email,' . $id,
        'role_id' => 'required|exists:roles,id',
        'department_id' => 'nullable|exists:departments,id',
        'current_year' => 'nullable|integer|min:1|max:6',
        'is_active' => 'boolean',
    ]);

    $user->update($validated);

    return redirect()->route('admin.users')->with('success', 'User updated successfully!');
}

// Delete user
public function deleteUser($id)
{
    $user = User::findOrFail($id);

    // Prevent deleting admin if it's the only admin
    if ($user->role_id == 1 && User::where('role_id', 1)->count() <= 1) {
        return redirect()->back()->with('error', 'Cannot delete the only admin user.');
    }

    $user->delete();

    return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
}
}
