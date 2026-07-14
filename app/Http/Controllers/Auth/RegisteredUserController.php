<?php
// app/Http/Controllers/Auth/RegisteredUserController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Allowed email domains for registration
     */
    protected $allowedDomains = [
        'mtu.edu.mm',
        // Add more domains if needed
    ];

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        $roles = Role::whereIn('id', [2, 3])->get(); // Only Lecturer and Student roles

        return view('auth.register', compact('departments', 'roles'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'student_id' => ['nullable', 'string', 'max:50'],
            'current_year' => ['nullable', 'integer', 'min:1', 'max:6'],
            'specialization' => ['nullable', 'string', 'max:255'],
        ]);

        // ✅ CHECK: Only allow university email domains
        $email = $request->email;
        $domain = substr(strrchr($email, "@"), 1);

        if (!in_array($domain, $this->allowedDomains)) {
            throw ValidationException::withMessages([
                'email' => 'Only university email addresses (@mtu.edu.mm) are allowed to register.',
            ]);
        }

        // If role is student, require student_id
        if ($request->role_id == 3 && empty($request->student_id)) {
            throw ValidationException::withMessages([
                'student_id' => 'Student ID is required for student registration.',
            ]);
        }

        // If role is student, require current_year
        if ($request->role_id == 3 && empty($request->current_year)) {
            throw ValidationException::withMessages([
                'current_year' => 'Current year is required for student registration.',
            ]);
        }

        // If role is lecturer or student, require department
        if (in_array($request->role_id, [2, 3]) && empty($request->department_id)) {
            throw ValidationException::withMessages([
                'department_id' => 'Department is required for ' . ($request->role_id == 2 ? 'lecturer' : 'student') . ' registration.',
            ]);
        }

        // ✅ CREATE USER - NOT VERIFIED (email_verified_at = null)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'student_id' => $request->student_id,
            'current_year' => $request->current_year,
            'specialization' => $request->specialization,
            'is_active' => true,
            'must_change_password' => false,
            'email_verified_at' => null, // ❌ NOT VERIFIED - User must verify email
        ]);

        // ✅ SEND VERIFICATION EMAIL
        event(new Registered($user));

        // ✅ DO NOT LOGIN - User must verify email first
        Auth::logout();

        // Redirect to login with message
        return redirect()->route('login')
            ->with('status', '✅ Registration successful! Please check your email (' . $user->email . ') to verify your account before logging in.');
    }
}
