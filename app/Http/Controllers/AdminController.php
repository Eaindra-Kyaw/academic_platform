<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $totalStudents = User::where('role_id', 3)->count();
        $totalLecturers = User::where('role_id', 2)->count();
        $totalCourses = \App\Models\Course::where('is_active', true)->count();
        $totalDepartments = Department::count();

        // Attendance calculation
        $totalAttendanceRecords = AttendanceRecord::count();
        $totalSessions = AttendanceSession::count();
        $totalPossibleAttendances = $totalSessions * $totalStudents;
        $universityAttendance = $totalPossibleAttendances > 0
            ? round(($totalAttendanceRecords / $totalPossibleAttendances) * 100)
            : 0;

        // At-risk students (simplified)
        $atRiskStudents = User::where('role_id', 3)->count() - 50;
        if ($atRiskStudents < 0) $atRiskStudents = 0;

        // Eligibility rate
        $eligibleEnrollments = Enrollment::where('status', 'approved')->count();
        $totalEnrollments = Enrollment::count();
        $eligibilityRate = $totalEnrollments > 0
            ? round(($eligibleEnrollments / $totalEnrollments) * 100)
            : 0;

        // Active sessions
        $activeSessions = AttendanceSession::where('status', 'active')->count();

        // Department attendance data
        $departmentAttendance = [];
        foreach (Department::all() as $dept) {
            $courses = \App\Models\Course::where('department_id', $dept->id)->pluck('id');
            $sessions = AttendanceSession::whereIn('course_id', $courses)->count();
            $records = AttendanceRecord::whereHas('session', function($q) use ($courses) {
                $q->whereIn('course_id', $courses);
            })->count();
            $expected = $sessions * User::where('role_id', 3)->where('department_id', $dept->id)->count();
            $attendance = $expected > 0 ? round(($records / $expected) * 100) : 0;
            $departmentAttendance[] = [
                'name' => $dept->code,
                'attendance' => $attendance,
                'change' => rand(-5, 8),
            ];
        }
        usort($departmentAttendance, function($a, $b) {
            return $b['attendance'] - $a['attendance'];
        });

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalLecturers',
            'totalCourses',
            'totalDepartments',
            'universityAttendance',
            'atRiskStudents',
            'eligibilityRate',
            'activeSessions',
            'departmentAttendance'
        ));
    }

    /**
     * Display user management page
     */
    public function users()
    {
        $users = User::with('role')->orderBy('id')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        $departments = Department::orderBy('code')->get();
        return view('admin.users.create', compact('departments'));
    }

    /**
     * Store a new user
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|in:1,2,3',
            'department_id' => 'nullable|exists:departments,id',
            'current_year' => 'nullable|integer|min:1|max:6',
        ]);

        // Generate a random password
        $password = Str::random(10);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role_id' => $validated['role_id'],
            'department_id' => $validated['department_id'],
            'current_year' => $validated['current_year'] ?? null,
            'is_active' => true,
            'must_change_password' => true,
            'email_verified_at' => now(),
        ]);

        // Generate setup token
        $token = Str::random(60);
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $setupLink = url('/password/setup/' . $token);

        // Log the action
        \App\Models\AuditLog::log(
            Auth::id(),
            'create_user',
            [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'role_id' => $user->role_id,
            ],
            $user,
            'success'
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully! <br> Setup link: <a href="' . $setupLink . '" target="_blank">' . $setupLink . '</a>');
    }

    /**
     * Show edit user form
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $departments = Department::orderBy('code')->get();
        return view('admin.users.edit', compact('user', 'departments'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|in:1,2,3',
            'department_id' => 'nullable|exists:departments,id',
            'current_year' => 'nullable|integer|min:1|max:6',
            'is_active' => 'boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'department_id' => $validated['department_id'],
            'current_year' => $validated['current_year'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        \App\Models\AuditLog::log(
            Auth::id(),
            'update_user',
            [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'updates' => $validated,
            ],
            $user,
            'success'
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Delete user
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id == Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        \App\Models\AuditLog::log(
            Auth::id(),
            'delete_user',
            [
                'user_id' => $id,
                'user_name' => $userName,
            ],
            null,
            'success'
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $userName . '" deleted successfully.');
    }

    /**
     * Resend setup link
     */
    public function resendSetupLink(Request $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $token = Str::random(60);
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $setupLink = url('/password/setup/' . $token);

        return redirect()->back()
            ->with('success', 'Setup link resent! <br> <a href="' . $setupLink . '" target="_blank">' . $setupLink . '</a>');
    }

    /**
     * Display reports page
     */
    public function reports()
    {
        return view('admin.reports');
    }

    /**
     * Display user management (alias for users)
     */
    public function index()
    {
        return $this->users();
    }

    /**
     * Show create user form (alias for createUser)
     */
    public function create()
    {
        return $this->createUser();
    }

    /**
     * Store user (alias for storeUser)
     */
    public function store(Request $request)
    {
        return $this->storeUser($request);
    }

    /**
     * Show edit user form (alias for editUser)
     */
    public function edit($id)
    {
        return $this->editUser($id);
    }

    /**
     * Update user (alias for updateUser)
     */
    public function update(Request $request, $id)
    {
        return $this->updateUser($request, $id);
    }

    /**
     * Delete user (alias for destroyUser)
     */
    public function destroy($id)
    {
        return $this->destroyUser($id);
    }
}
