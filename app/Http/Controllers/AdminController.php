<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\AttendanceEvaluation;
use App\Models\RiskPrediction;
use App\Models\AcademicHealthScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// ✅ Import Mail Classes
use App\Mail\AdminNewUserNotification;
use App\Mail\UserApprovedMail;
use App\Mail\UserRejectedMail;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with period‑based attendance data.
     */
    public function dashboard()
    {
        // ============================================
        // REAL-TIME STATISTICS
        // ============================================

        $totalStudents = User::where('role_id', 3)->count();
        $totalLecturers = User::where('role_id', 2)->count();
        $totalCourses = Course::where('is_active', true)->count();
        $totalDepartments = Department::count();

        // ============================================
        // ATTENDANCE CALCULATIONS (PERIOD‑BASED)
        // ============================================

        $totalPeriods = AttendanceSession::where('status', 'ended')
            ->where('is_cancelled', false)
            ->sum('conducted_periods') ?: 0;

        $attendedPeriods = 0;
        $sessions = AttendanceSession::where('status', 'ended')
            ->where('is_cancelled', false)
            ->with('records')
            ->get();
        foreach ($sessions as $session) {
            $presentLate = $session->records->whereIn('status', ['present', 'late'])->count();
            $attendedPeriods += $session->conducted_periods * $presentLate;
        }

        $totalEnrolledStudents = Enrollment::where('status', 'approved')->distinct('student_id')->count('student_id');

        $universityAttendance = 0;
        if ($totalPeriods > 0 && $totalEnrolledStudents > 0) {
            $totalPossible = $totalPeriods * $totalEnrolledStudents;
            $universityAttendance = $totalPossible > 0 ? round(($attendedPeriods / $totalPossible) * 100) : 0;
        }

        // ============================================
        // AT-RISK STUDENTS (from evaluations)
        // ============================================

        $latestEval = AttendanceEvaluation::select('student_id', DB::raw('MAX(evaluation_date) as latest_date'))
            ->groupBy('student_id')
            ->pluck('latest_date', 'student_id');

        $atRiskStudentIds = [];
        foreach ($latestEval as $studentId => $date) {
            $eval = AttendanceEvaluation::where('student_id', $studentId)
                ->where('evaluation_date', $date)
                ->first();
            if ($eval && ($eval->risk_level === 'Medium' || $eval->risk_level === 'High')) {
                $atRiskStudentIds[] = $studentId;
            }
        }
        $atRiskStudents = count($atRiskStudentIds);

        // ============================================
        // ELIGIBILITY RATE
        // ============================================

        $eligibleCount = AttendanceEvaluation::whereIn('student_id', function($q) {
                $q->select('student_id')
                  ->from('attendance_evaluations')
                  ->groupBy('student_id')
                  ->havingRaw('MAX(evaluation_date) = evaluation_date');
            })
            ->where('eligibility_status', 'eligible')
            ->count();

        $totalEvaluatedStudents = AttendanceEvaluation::distinct('student_id')->count();
        $eligibilityRate = $totalEvaluatedStudents > 0 ? round(($eligibleCount / $totalEvaluatedStudents) * 100) : 0;

        // ============================================
        // ACTIVE SESSIONS
        // ============================================

        $activeSessions = AttendanceSession::where('status', 'active')->count();

        // ============================================
        // DEPARTMENT ATTENDANCE (PERIOD‑BASED)
        // ============================================

        $departmentAttendance = [];
        $departments = Department::all();

        foreach ($departments as $dept) {
            $courseIds = Course::where('department_id', $dept->id)->pluck('id')->toArray();

            if (empty($courseIds)) {
                continue;
            }

            $deptStudents = User::where('role_id', 3)
                ->where('department_id', $dept->id)
                ->count();

            $deptSessions = AttendanceSession::whereIn('course_id', $courseIds)
                ->where('status', 'ended')
                ->where('is_cancelled', false)
                ->get();

            $totalPeriodsDept = $deptSessions->sum('conducted_periods');
            if ($totalPeriodsDept == 0) {
                $attendance = 0;
                $change = 0;
            } else {
                $attendedPeriodsDept = 0;
                foreach ($deptSessions as $session) {
                    $presentLate = AttendanceRecord::where('attendance_session_id', $session->id)
                        ->whereIn('status', ['present', 'late'])
                        ->count();
                    $attendedPeriodsDept += $session->conducted_periods * $presentLate;
                }
                $expected = $totalPeriodsDept * $deptStudents;
                $attendance = $expected > 0 ? round(($attendedPeriodsDept / $expected) * 100) : 0;
            }

            // Change from previous month
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
            $lastMonthSessions = AttendanceSession::whereIn('course_id', $courseIds)
                ->where('status', 'ended')
                ->where('is_cancelled', false)
                ->whereBetween('session_date', [$lastMonthStart, $lastMonthEnd])
                ->get();
            $lastMonthPeriods = $lastMonthSessions->sum('conducted_periods');
            if ($lastMonthPeriods > 0) {
                $lastMonthAttended = 0;
                foreach ($lastMonthSessions as $session) {
                    $presentLate = AttendanceRecord::where('attendance_session_id', $session->id)
                        ->whereIn('status', ['present', 'late'])
                        ->count();
                    $lastMonthAttended += $session->conducted_periods * $presentLate;
                }
                $lastMonthExpected = $lastMonthPeriods * $deptStudents;
                $previousAttendance = $lastMonthExpected > 0 ? round(($lastMonthAttended / $lastMonthExpected) * 100) : 0;
                $change = $previousAttendance > 0 ? round($attendance - $previousAttendance) : 0;
            } else {
                $change = 0;
            }

            $departmentAttendance[] = [
                'id' => $dept->id,
                'name' => $dept->code,
                'full_name' => $dept->name,
                'attendance' => $attendance,
                'change' => $change,
                'students' => $deptStudents,
                'sessions' => $deptSessions->count(),
                'records' => $attendedPeriodsDept ?? 0,
            ];
        }

        usort($departmentAttendance, function($a, $b) {
            return $b['attendance'] - $a['attendance'];
        });

        // ============================================
        // RISK DISTRIBUTION
        // ============================================

        $riskDistribution = ['Low' => 0, 'Medium' => 0, 'High' => 0];
        foreach ($latestEval as $studentId => $date) {
            $eval = AttendanceEvaluation::where('student_id', $studentId)
                ->where('evaluation_date', $date)
                ->first();
            if ($eval && isset($riskDistribution[$eval->risk_level])) {
                $riskDistribution[$eval->risk_level]++;
            }
        }

        // ============================================
        // RECENT SESSIONS
        // ============================================

        $recentSessions = AttendanceSession::with(['course', 'records'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($session) {
                $present = $session->records->where('status', 'present')->count();
                $totalEnrolled = Enrollment::where('course_id', $session->course_id)
                    ->where('status', 'approved')
                    ->count();
                $presentPercent = $totalEnrolled > 0 ? ($present / $totalEnrolled) * 100 : 0;
                return [
                    'course_name' => $session->course->course_name ?? 'N/A',
                    'present' => $present,
                    'total' => $totalEnrolled,
                    'status' => $presentPercent >= 50 ? 'Improving' : 'Declining',
                ];
            });

        // ============================================
        // BUSIEST CLASSROOMS
        // ============================================

        $classroomUsage = AttendanceSession::where('room', '!=', '')
            ->select('room', DB::raw('COUNT(*) as usage_count'))
            ->groupBy('room')
            ->orderBy('usage_count', 'desc')
            ->limit(3)
            ->get()
            ->map(function($item) {
                $totalSessions = AttendanceSession::count();
                return [
                    'room' => $item->room,
                    'usage' => $totalSessions > 0 ? min(100, round(($item->usage_count / $totalSessions) * 100 * 2)) : 0,
                ];
            });

        // ============================================
        // PENDING ENROLLMENTS
        // ============================================

        $pendingEnrollments = Enrollment::where('status', 'pending')->count();

        // ============================================
        // PENDING USER APPROVALS
        // ============================================

        $pendingUsers = User::where('registration_status', 'pending')->count();

        // ============================================
        // ATTENDANCE TREND (LAST 6 MONTHS) – PERIOD‑BASED
        // ============================================

        $trendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $sessionsInMonth = AttendanceSession::whereBetween('session_date', [$monthStart, $monthEnd])
                ->where('status', 'ended')
                ->where('is_cancelled', false)
                ->get();

            $totalExpected = 0;
            $totalAttended = 0;

            foreach ($sessionsInMonth as $session) {
                $enrolledCount = Enrollment::where('course_id', $session->course_id)
                    ->where('status', 'approved')
                    ->count();
                $attendedCount = AttendanceRecord::where('attendance_session_id', $session->id)
                    ->whereIn('status', ['present', 'late'])
                    ->count();
                $periods = $session->conducted_periods ?? 1;
                $totalExpected += $periods * $enrolledCount;
                $totalAttended += $periods * $attendedCount;
            }

            $attendancePercentage = $totalExpected > 0 ? round(($totalAttended / $totalExpected) * 100) : 0;

            $trendData[] = [
                'month' => $month->format('M'),
                'attendance' => $attendancePercentage,
            ];
        }

        // ============================================
        // RETURN VIEW
        // ============================================

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalLecturers',
            'totalCourses',
            'totalDepartments',
            'universityAttendance',
            'atRiskStudents',
            'eligibilityRate',
            'activeSessions',
            'departmentAttendance',
            'riskDistribution',
            'recentSessions',
            'classroomUsage',
            'pendingEnrollments',
            'trendData',
            'pendingUsers' // ✅ Added for dashboard badge
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
     * Store a new user (Admin creates user with pending status)
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|in:1,2,3',
            'department_id' => 'nullable|exists:departments,id',
            'student_id' => 'nullable|string|max:50',
            'current_year' => 'nullable|integer|min:1|max:6',
        ]);

        if ($validated['role_id'] == 3 && empty($validated['student_id'])) {
            return back()->withErrors(['student_id' => 'Student ID is required for students.']);
        }

        $mustChangePassword = ($validated['role_id'] == 1) ? false : true;
        $tempPassword = Str::random(10);

        // ✅ Create user with PENDING status
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'role_id' => $validated['role_id'],
            'department_id' => $validated['department_id'],
            'student_id' => $validated['student_id'] ?? null,
            'current_year' => $validated['current_year'] ?? null,
            'is_active' => false, // ❌ NOT active until admin approves
            'must_change_password' => $mustChangePassword,
            'email_verified_at' => now(),
            'registration_status' => 'pending',
            'registered_at' => now(),
        ]);

        // ✅ Generate token for password setup
        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $setupLink = url('/password/setup/' . $token . '?email=' . $user->email);

        // ✅ Send notification to ADMIN
        $adminEmails = User::where('role_id', 1)->pluck('email')->toArray();

        try {
            Mail::to($adminEmails)->send(new AdminNewUserNotification($user, $setupLink));
        } catch (\Exception $e) {
            \Log::error('Failed to send admin notification: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')
            ->with('success', "✅ User '{$user->name}' created! Admin notification sent.")
            ->with('temp_password', $tempPassword)
            ->with('setup_link', $setupLink);
    }

    // ============================================================
    // ✅ PENDING USERS APPROVAL METHODS
    // ============================================================

    /**
     * Display pending users for approval
     */
    public function pendingUsers()
    {
        $pendingUsers = User::where('registration_status', 'pending')
            ->where('is_active', false)
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        $stats = [
            'pending' => User::where('registration_status', 'pending')->count(),
            'approved' => User::where('registration_status', 'active')->count(),
            'rejected' => User::where('registration_status', 'rejected')->count(),
            'total' => User::count(),
        ];

        return view('admin.pending-users', compact('pendingUsers', 'stats'));
    }

    /**
     * Approve a pending user
     */
    public function approveUser($id)
    {
        $user = User::where('registration_status', 'pending')->findOrFail($id);

        // Generate token for password setup
        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $setupLink = url('/password/setup/' . $token . '?email=' . $user->email);

        $user->update([
            'registration_status' => 'active',
            'is_active' => true,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'must_change_password' => true,
        ]);

        // ✅ Send approval email to user
        try {
            Mail::to($user->email)->send(new UserApprovedMail($user, $setupLink));
        } catch (\Exception $e) {
            \Log::error('Failed to send approval email: ' . $e->getMessage());
        }

        // ✅ Log audit
        \App\Models\AuditLog::log(
            Auth::id(),
            'approve_user',
            [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ],
            $user,
            'success'
        );

        return redirect()->route('admin.pending-users')
            ->with('success', "✅ User '{$user->name}' has been approved! They will receive an email notification.");
    }

    /**
     * Reject a pending user
     */
    public function rejectUser(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $user = User::where('registration_status', 'pending')->findOrFail($id);

        $user->update([
            'registration_status' => 'rejected',
            'is_active' => false,
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
            'rejection_reason' => $request->reason,
        ]);

        // ✅ Send rejection email to user
        try {
            Mail::to($user->email)->send(new UserRejectedMail($user, $request->reason));
        } catch (\Exception $e) {
            \Log::error('Failed to send rejection email: ' . $e->getMessage());
        }

        // ✅ Log audit
        \App\Models\AuditLog::log(
            Auth::id(),
            'reject_user',
            [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'reason' => $request->reason,
            ],
            $user,
            'success'
        );

        return redirect()->route('admin.pending-users')
            ->with('success', "User '{$user->name}' has been rejected.");
    }

    // ============================================================
    // EXISTING METHODS
    // ============================================================

    /**
     * Get setup link for a user (for AJAX requests)
     */
    public function getSetupLink($id)
    {
        $user = User::findOrFail($id);

        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();

        if (!$tokenData) {
            $token = Str::random(60);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => $token,
                    'created_at' => now(),
                ]
            );
        } else {
            $token = Str::random(60);
            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->update([
                    'token' => $token,
                    'created_at' => now(),
                ]);
        }

        $setupLink = url('/password/setup/' . $token . '?email=' . $user->email);

        return response()->json([
            'success' => true,
            'link' => $setupLink,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name ?? 'N/A'
            ]
        ]);
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
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $setupLink = url('/password/setup/' . $token . '?email=' . $user->email);

        return redirect()->back()
            ->with('success', 'Setup link resent! <br> <a href="' . $setupLink . '" target="_blank">' . $setupLink . '</a>');
    }

    /**
     * Display reports page (Page 1 - Landing)
     */
    public function reports()
    {
        return view('admin.reports.index');
    }

    /**
     * Display report detail page with filters (Page 2)
     */
    public function reportDetail($type)
    {
        $reportConfigs = [
            'students' => [
                'title' => 'Student Report',
                'icon' => '👨‍🎓',
                'description' => 'Export student data with filters',
                'filters' => ['department', 'year', 'status']
            ],
            'attendance' => [
                'title' => 'Attendance Report',
                'icon' => '📋',
                'description' => 'Export attendance records with filters',
                'filters' => ['department', 'course', 'date_range']
            ],
            'enrollments' => [
                'title' => 'Enrollment Report',
                'icon' => '📚',
                'description' => 'Export enrollment data with filters',
                'filters' => ['department', 'course', 'status', 'year']
            ],
            'departments' => [
                'title' => 'Department Report',
                'icon' => '🏛️',
                'description' => 'Export department statistics',
                'filters' => ['department']
            ],
            'risk' => [
                'title' => 'Risk Analysis Report',
                'icon' => '⚠️',
                'description' => 'Export at-risk students with filters',
                'filters' => ['department', 'risk_level', 'attendance_below']
            ],
            'health' => [
                'title' => 'Academic Health Report',
                'icon' => '💚',
                'description' => 'Export Academic Health Scores',
                'filters' => ['department', 'year', 'score_below']
            ],
            'semester' => [
                'title' => 'Semester Summary',
                'icon' => '📅',
                'description' => 'Export complete semester summary',
                'filters' => ['academic_year', 'semester', 'format']
            ],
        ];

        if (!isset($reportConfigs[$type])) {
            return redirect()->route('admin.reports')->with('error', 'Invalid report type.');
        }

        $config = $reportConfigs[$type];
        $departments = Department::orderBy('name')->get();
        $courses = Course::orderBy('course_code')->get();

        return view('admin.reports.detail', [
            'reportType' => $type,
            'reportTitle' => $config['title'],
            'reportIcon' => $config['icon'],
            'reportDescription' => $config['description'],
            'availableFilters' => $config['filters'],
            'departments' => $departments,
            'courses' => $courses,
        ]);
    }

    /**
     * Export reports based on type
     */
    public function exportReport(Request $request, $type)
    {
        $format = $request->input('format', 'csv');

        switch ($type) {
            case 'students':
                return $this->exportStudents($format, $request);
            case 'attendance':
                return $this->exportAttendance($format, $request);
            case 'enrollments':
                return $this->exportEnrollments($format, $request);
            case 'departments':
                return $this->exportDepartments($format, $request);
            case 'risk':
                return $this->exportRiskAnalysis($format, $request);
            case 'health':
                return $this->exportAcademicHealth($format, $request);
            case 'semester':
                return $this->exportSemesterSummary($format, $request);
            default:
                return back()->with('error', 'Invalid report type.');
        }
    }

    // ============================================================
    // EXPORT HELPERS
    // ============================================================

    private function exportStudents($format, $request)
    {
        $query = User::where('role_id', 3)->with(['department', 'enrollments.course']);
        if ($request->filled('department_id')) $query->where('department_id', $request->department_id);
        if ($request->filled('year')) $query->where('current_year', $request->year);
        if ($request->filled('status')) $query->where('is_active', $request->status === 'active');
        $students = $query->get();
        $filename = 'students_report_' . Carbon::now()->format('Y-m-d') . '.csv';
        if ($format === 'csv') {
            $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\""];
            $callback = function() use ($students) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Student ID','Name','Email','Department','Current Year','Total Enrollments','Attendance %','Eligibility Status','Created At']);
                foreach ($students as $student) {
                    $avgAttendance = $student->enrollments()->avg('attendance_percentage') ?? 0;
                    $eligibilityStatus = $student->enrollments()->first()->eligibility_status ?? 'N/A';
                    fputcsv($file, [
                        $student->student_id ?? 'N/A',
                        $student->name,
                        $student->email,
                        $student->department->name ?? 'N/A',
                        $student->current_year ?? 'N/A',
                        $student->enrollments()->count(),
                        round($avgAttendance, 2) . '%',
                        $eligibilityStatus,
                        $student->created_at->format('Y-m-d'),
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }
        return back()->with('error', 'Only CSV format is supported.');
    }

    private function exportAttendance($format, $request)
    {
        $query = AttendanceRecord::with(['session.course', 'student']);
        if ($request->filled('department_id')) {
            $query->whereHas('session.course', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        if ($request->filled('course_id')) {
            $query->whereHas('session', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);
        $attendanceRecords = $query->orderBy('created_at', 'desc')->get();
        $filename = 'attendance_report_' . Carbon::now()->format('Y-m-d') . '.csv';
        if ($format === 'csv') {
            $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\""];
            $callback = function() use ($attendanceRecords) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Student Name','Student ID','Course','Session Date','Status','Check-in Time']);
                foreach ($attendanceRecords as $record) {
                    fputcsv($file, [
                        $record->student->name ?? 'N/A',
                        $record->student->student_id ?? 'N/A',
                        $record->session->course->course_code ?? 'N/A',
                        $record->session->created_at->format('Y-m-d') ?? 'N/A',
                        $record->status ?? 'N/A',
                        $record->scanned_at ? $record->scanned_at->format('H:i:s') : 'N/A',
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }
        return back()->with('error', 'Only CSV format is supported.');
    }

    private function exportEnrollments($format, $request)
    {
        $query = Enrollment::with(['student', 'course.department']);
        if ($request->filled('department_id')) {
            $query->whereHas('course', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        if ($request->filled('course_id')) $query->where('course_id', $request->course_id);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('year')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('current_year', $request->year);
            });
        }
        $enrollments = $query->orderBy('created_at', 'desc')->get();
        $filename = 'enrollments_report_' . Carbon::now()->format('Y-m-d') . '.csv';
        if ($format === 'csv') {
            $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\""];
            $callback = function() use ($enrollments) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Student Name','Student ID','Course Code','Course Name','Department','Status','Enrollment Date','Attendance %','Roll Call Mark','Eligibility']);
                foreach ($enrollments as $enrollment) {
                    fputcsv($file, [
                        $enrollment->student->name ?? 'N/A',
                        $enrollment->student->student_id ?? 'N/A',
                        $enrollment->course->course_code ?? 'N/A',
                        $enrollment->course->course_name ?? 'N/A',
                        $enrollment->course->department->name ?? 'N/A',
                        $enrollment->status ?? 'N/A',
                        $enrollment->created_at->format('Y-m-d'),
                        $enrollment->attendance_percentage ?? 0,
                        $enrollment->roll_call_mark ?? 0,
                        $enrollment->eligibility_status ?? 'N/A',
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }
        return back()->with('error', 'Only CSV format is supported.');
    }

    private function exportDepartments($format, $request)
    {
        $query = Department::withCount([
            'users as student_count' => function($q) { $q->where('role_id', 3); },
            'users as lecturer_count' => function($q) { $q->where('role_id', 2); },
            'courses',
            'enrollments'
        ]);
        if ($request->filled('department_id')) $query->where('id', $request->department_id);
        $departments = $query->get();
        $filename = 'departments_report_' . Carbon::now()->format('Y-m-d') . '.csv';
        if ($format === 'csv') {
            $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\""];
            $callback = function() use ($departments) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Department Code','Department Name','Head of Department','Total Students','Total Lecturers','Total Courses','Total Enrollments']);
                foreach ($departments as $dept) {
                    fputcsv($file, [
                        $dept->code ?? 'N/A',
                        $dept->name ?? 'N/A',
                        $dept->head_of_department ?? 'N/A',
                        $dept->student_count ?? 0,
                        $dept->lecturer_count ?? 0,
                        $dept->courses_count ?? 0,
                        $dept->enrollments_count ?? 0,
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }
        return back()->with('error', 'Only CSV format is supported.');
    }

    private function exportRiskAnalysis($format, $request)
    {
        $query = User::where('role_id', 3)
            ->with(['department', 'enrollments'])
            ->whereHas('enrollments', function($q) {
                $q->where('attendance_percentage', '<', 75)
                  ->orWhere('eligibility_status', 'not_eligible')
                  ->orWhere('eligibility_status', 'warning');
            });
        if ($request->filled('department_id')) $query->where('department_id', $request->department_id);
        $students = $query->get();
        $filename = 'risk_analysis_report_' . Carbon::now()->format('Y-m-d') . '.csv';
        if ($format === 'csv') {
            $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\""];
            $callback = function() use ($students, $request) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Student Name','Student ID','Department','Current Year','Attendance %','Eligibility Status','Risk Level']);
                foreach ($students as $student) {
                    $avgAttendance = $student->enrollments()->avg('attendance_percentage') ?? 0;
                    $eligibility = $student->enrollments()->first()->eligibility_status ?? 'eligible';
                    if ($request->filled('attendance_below') && $avgAttendance >= $request->attendance_below) continue;
                    $riskLevel = $avgAttendance < 60 ? 'Critical' : ($avgAttendance < 75 ? 'Moderate' : 'Low');
                    if ($request->filled('risk_level') && strtolower($request->risk_level) !== strtolower($riskLevel)) continue;
                    fputcsv($file, [
                        $student->name,
                        $student->student_id ?? 'N/A',
                        $student->department->name ?? 'N/A',
                        $student->current_year ?? 'N/A',
                        round($avgAttendance, 2) . '%',
                        $eligibility,
                        $riskLevel,
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }
        return back()->with('error', 'Only CSV format is supported.');
    }

    private function exportAcademicHealth($format, $request)
    {
        $query = User::where('role_id', 3)->with(['department', 'enrollments']);
        if ($request->filled('department_id')) $query->where('department_id', $request->department_id);
        if ($request->filled('year')) $query->where('current_year', $request->year);
        $students = $query->get();
        $filename = 'academic_health_report_' . Carbon::now()->format('Y-m-d') . '.csv';
        if ($format === 'csv') {
            $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\""];
            $callback = function() use ($students, $request) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Student Name','Student ID','Department','Year','Total Enrollments','Approved Enrollments','Attendance %','Roll Call Mark','Eligibility','Health Score']);
                foreach ($students as $student) {
                    $enrollments = $student->enrollments;
                    $totalEnrollments = $enrollments->count();
                    $approvedEnrollments = $enrollments->where('status', 'approved')->count();
                    $avgAttendance = $enrollments->avg('attendance_percentage') ?? 0;
                    $avgRollCall = $enrollments->avg('roll_call_mark') ?? 0;
                    $eligibility = $enrollments->first()->eligibility_status ?? 'N/A';
                    $healthScore = ($avgAttendance * 0.5) + ($avgRollCall * 0.3) + (($approvedEnrollments / max($totalEnrollments, 1)) * 20);
                    if ($request->filled('score_below') && $healthScore >= $request->score_below) continue;
                    fputcsv($file, [
                        $student->name,
                        $student->student_id ?? 'N/A',
                        $student->department->name ?? 'N/A',
                        $student->current_year ?? 'N/A',
                        $totalEnrollments,
                        $approvedEnrollments,
                        round($avgAttendance, 2) . '%',
                        round($avgRollCall, 2),
                        $eligibility,
                        round($healthScore, 2),
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }
        return back()->with('error', 'Only CSV format is supported.');
    }

    private function exportSemesterSummary($format, $request)
    {
        $filename = 'semester_summary_' . Carbon::now()->format('Y-m-d') . '.csv';
        if ($format === 'csv') {
            $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\""];
            $callback = function() use ($request) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Semester Summary Report']);
                fputcsv($file, ['Generated on:', Carbon::now()->format('Y-m-d H:i:s')]);
                if ($request->filled('academic_year')) fputcsv($file, ['Academic Year:', $request->academic_year]);
                if ($request->filled('semester')) fputcsv($file, ['Semester:', $request->semester]);
                fputcsv($file, []);
                $query = Enrollment::query();
                if ($request->filled('academic_year')) {
                    $query->whereHas('course', function($q) use ($request) {
                        $q->where('academic_year', $request->academic_year);
                    });
                }
                if ($request->filled('semester')) {
                    $query->whereHas('course', function($q) use ($request) {
                        $q->where('semester', $request->semester);
                    });
                }
                fputcsv($file, ['Summary Statistics']);
                fputcsv($file, ['Total Students:', User::where('role_id', 3)->count()]);
                fputcsv($file, ['Total Lecturers:', User::where('role_id', 2)->count()]);
                fputcsv($file, ['Total Courses:', Course::count()]);
                fputcsv($file, ['Total Enrollments:', $query->count()]);
                fputcsv($file, ['Active Enrollments:', (clone $query)->where('status', 'approved')->count()]);
                fputcsv($file, ['Pending Enrollments:', (clone $query)->where('status', 'pending')->count()]);
                fputcsv($file, ['Rejected Enrollments:', (clone $query)->where('status', 'rejected')->count()]);
                fputcsv($file, ['Dropped Enrollments:', (clone $query)->where('status', 'dropped')->count()]);
                fputcsv($file, []);
                fputcsv($file, ['Department Breakdown']);
                fputcsv($file, ['Department', 'Students', 'Lecturers', 'Courses', 'Enrollments']);
                $departments = Department::withCount(['students', 'lecturers', 'courses', 'enrollments'])->get();
                foreach ($departments as $dept) {
                    fputcsv($file, [
                        $dept->name,
                        $dept->students_count ?? 0,
                        $dept->lecturers_count ?? 0,
                        $dept->courses_count ?? 0,
                        $dept->enrollments_count ?? 0,
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }
        return back()->with('error', 'Only CSV format is supported.');
    }

    // ============================================================
    // ALIAS METHODS (for backward compatibility)
    // ============================================================

    public function index()
    {
        return $this->users();
    }

    public function create()
    {
        return $this->createUser();
    }

    public function store(Request $request)
    {
        return $this->storeUser($request);
    }

    public function edit($id)
    {
        return $this->editUser($id);
    }

    public function update(Request $request, $id)
    {
        return $this->updateUser($request, $id);
    }

    public function destroy($id)
    {
        return $this->destroyUser($id);
    }
}
