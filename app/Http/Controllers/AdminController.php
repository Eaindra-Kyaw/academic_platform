<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\RiskPrediction;
use App\Models\AcademicHealthScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with real-time data
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
        // ATTENDANCE CALCULATIONS (REAL-TIME)
        // ============================================

        $totalAttendanceRecords = AttendanceRecord::count();
        $totalSessions = AttendanceSession::count();
        $totalEnrolledStudents = Enrollment::where('status', 'approved')->distinct('student_id')->count('student_id');

        // Calculate university attendance rate
        if ($totalSessions > 0 && $totalEnrolledStudents > 0) {
            $totalPossible = $totalSessions * $totalEnrolledStudents;
            $universityAttendance = $totalPossible > 0
                ? round(($totalAttendanceRecords / $totalPossible) * 100)
                : 0;
        } else {
            $universityAttendance = 0;
        }

        // ============================================
        // AT-RISK STUDENTS (REAL-TIME)
        // ============================================

        $atRiskStudentIds = Enrollment::where('status', 'approved')
            ->where(function($q) {
                $q->where('attendance_percentage', '<', 60)
                  ->orWhere('eligibility_status', 'warning')
                  ->orWhere('eligibility_status', 'not_eligible');
            })
            ->distinct('student_id')
            ->pluck('student_id')
            ->toArray();

        $atRiskStudents = count($atRiskStudentIds);

        // ============================================
        // ELIGIBILITY RATE (REAL-TIME)
        // ============================================

        $eligibleEnrollments = Enrollment::where('status', 'approved')
            ->where('eligibility_status', 'eligible')
            ->count();
        $totalApprovedEnrollments = Enrollment::where('status', 'approved')->count();
        $eligibilityRate = $totalApprovedEnrollments > 0
            ? round(($eligibleEnrollments / $totalApprovedEnrollments) * 100)
            : 0;

        // ============================================
        // ACTIVE SESSIONS (REAL-TIME)
        // ============================================

        $activeSessions = AttendanceSession::where('status', 'active')->count();

        // ============================================
        // DEPARTMENT ATTENDANCE (REAL-TIME)
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

            $deptSessions = AttendanceSession::whereIn('course_id', $courseIds)->count();

            $deptRecords = AttendanceRecord::whereHas('session', function($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            })->count();

            $expected = $deptSessions * $deptStudents;
            $attendance = $expected > 0 ? round(($deptRecords / $expected) * 100) : 0;

            // Calculate change from previous month
            $lastMonthRecords = AttendanceRecord::whereHas('session', function($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds)
                  ->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()]);
            })->count();

            $previousMonthExpected = $deptSessions * $deptStudents;
            $previousAttendance = $previousMonthExpected > 0 ? round(($lastMonthRecords / $previousMonthExpected) * 100) : 0;

            $change = $previousAttendance > 0 ? round($attendance - $previousAttendance) : 0;

            $departmentAttendance[] = [
                'id' => $dept->id,
                'name' => $dept->code,
                'full_name' => $dept->name,
                'attendance' => $attendance,
                'change' => $change,
                'students' => $deptStudents,
                'sessions' => $deptSessions,
                'records' => $deptRecords,
            ];
        }

        usort($departmentAttendance, function($a, $b) {
            return $b['attendance'] - $a['attendance'];
        });

        // ============================================
        // RISK DISTRIBUTION (REAL-TIME)
        // ============================================

        $riskDistribution = [
            'Low' => 0,
            'Medium' => 0,
            'High' => 0,
        ];

        $students = User::where('role_id', 3)->get();
        foreach ($students as $student) {
            $avgAttendance = Enrollment::where('student_id', $student->id)
                ->where('status', 'approved')
                ->avg('attendance_percentage') ?? 0;

            if ($avgAttendance >= 75) {
                $riskDistribution['Low']++;
            } elseif ($avgAttendance >= 60) {
                $riskDistribution['Medium']++;
            } elseif ($avgAttendance > 0) {
                $riskDistribution['High']++;
            }
        }

        // ============================================
        // RECENT SESSIONS (REAL-TIME)
        // ============================================

        $recentSessions = AttendanceSession::with(['course', 'records'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($session) {
                $present = $session->records->where('status', 'present')->count();
                $total = $session->records->count();
                return [
                    'course_name' => $session->course->course_name ?? 'N/A',
                    'present' => $present,
                    'total' => $total,
                    'status' => $present > ($total / 2) ? 'Improving' : 'Declining',
                ];
            });

        // ============================================
        // BUSIEST CLASSROOMS (REAL-TIME)
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
        // PENDING ENROLLMENTS (REAL-TIME)
        // ============================================

        $pendingEnrollments = Enrollment::where('status', 'pending')->count();

        // ============================================
        // ATTENDANCE TREND DATA (LAST 6 MONTHS)
        // ============================================

        $trendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $monthRecords = AttendanceRecord::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $monthSessions = AttendanceSession::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $monthStudents = User::where('role_id', 3)->count();

            $monthExpected = $monthSessions * $monthStudents;
            $monthAttendance = $monthExpected > 0 ? round(($monthRecords / $monthExpected) * 100) : 0;

            $trendData[] = [
                'month' => $month->format('M'),
                'attendance' => $monthAttendance,
            ];
        }

        // ============================================
        // MAKE SURE ALL VARIABLES ARE PASSED
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
            'trendData'
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
        // Define report types
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

        // Get departments and courses for filters
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

    /**
     * Export Students Report with filters
     */
    private function exportStudents($format, $request)
    {
        $query = User::where('role_id', 3)
            ->with(['department', 'enrollments.course']);

        // Apply filters
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('year')) {
            $query->where('current_year', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $students = $query->get();

        $filename = 'students_report_' . Carbon::now()->format('Y-m-d') . '.csv';

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($students) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Student ID',
                    'Name',
                    'Email',
                    'Department',
                    'Current Year',
                    'Total Enrollments',
                    'Attendance %',
                    'Eligibility Status',
                    'Created At'
                ]);

                foreach ($students as $student) {
                    $totalEnrollments = $student->enrollments()->count();
                    $avgAttendance = $student->enrollments()->avg('attendance_percentage') ?? 0;
                    $eligibilityStatus = $student->enrollments()->first()->eligibility_status ?? 'N/A';

                    fputcsv($file, [
                        $student->student_id ?? 'N/A',
                        $student->name,
                        $student->email,
                        $student->department->name ?? 'N/A',
                        $student->current_year ?? 'N/A',
                        $totalEnrollments,
                        round($avgAttendance, 2) . '%',
                        $eligibilityStatus,
                        $student->created_at->format('Y-m-d'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return back()->with('error', 'Only CSV format is supported for this report.');
    }

    /**
     * Export Attendance Report with filters
     */
    private function exportAttendance($format, $request)
    {
        $query = AttendanceRecord::with(['session.course', 'student']);

        // Apply filters
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

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $attendanceRecords = $query->orderBy('created_at', 'desc')->get();

        $filename = 'attendance_report_' . Carbon::now()->format('Y-m-d') . '.csv';

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($attendanceRecords) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Student Name',
                    'Student ID',
                    'Course',
                    'Session Date',
                    'Status',
                    'Check-in Time',
                ]);

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

        return back()->with('error', 'Only CSV format is supported for this report.');
    }

    /**
     * Export Enrollments Report with filters
     */
    private function exportEnrollments($format, $request)
    {
        $query = Enrollment::with(['student', 'course.department']);

        // Apply filters
        if ($request->filled('department_id')) {
            $query->whereHas('course', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('year')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('current_year', $request->year);
            });
        }

        $enrollments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'enrollments_report_' . Carbon::now()->format('Y-m-d') . '.csv';

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($enrollments) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Student Name',
                    'Student ID',
                    'Course Code',
                    'Course Name',
                    'Department',
                    'Status',
                    'Enrollment Date',
                    'Attendance %',
                    'Roll Call Mark',
                    'Eligibility'
                ]);

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

        return back()->with('error', 'Only CSV format is supported for this report.');
    }

    /**
     * Export Departments Report with filters
     */
    private function exportDepartments($format, $request)
    {
        $query = Department::withCount([
            'users as student_count' => function($q) {
                $q->where('role_id', 3);
            },
            'users as lecturer_count' => function($q) {
                $q->where('role_id', 2);
            },
            'courses',
            'enrollments'
        ]);

        if ($request->filled('department_id')) {
            $query->where('id', $request->department_id);
        }

        $departments = $query->get();

        $filename = 'departments_report_' . Carbon::now()->format('Y-m-d') . '.csv';

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($departments) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Department Code',
                    'Department Name',
                    'Head of Department',
                    'Total Students',
                    'Total Lecturers',
                    'Total Courses',
                    'Total Enrollments',
                ]);

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

        return back()->with('error', 'Only CSV format is supported for this report.');
    }

    /**
     * Export Risk Analysis Report with filters
     */
    private function exportRiskAnalysis($format, $request)
    {
        $query = User::where('role_id', 3)
            ->with(['department', 'enrollments'])
            ->whereHas('enrollments', function($q) {
                $q->where('attendance_percentage', '<', 75)
                  ->orWhere('eligibility_status', 'not_eligible')
                  ->orWhere('eligibility_status', 'warning');
            });

        // Apply filters
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $students = $query->get();

        $filename = 'risk_analysis_report_' . Carbon::now()->format('Y-m-d') . '.csv';

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($students, $request) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Student Name',
                    'Student ID',
                    'Department',
                    'Current Year',
                    'Attendance %',
                    'Eligibility Status',
                    'Risk Level',
                ]);

                foreach ($students as $student) {
                    $avgAttendance = $student->enrollments()->avg('attendance_percentage') ?? 0;
                    $eligibility = $student->enrollments()->first()->eligibility_status ?? 'eligible';

                    // Apply attendance filter
                    if ($request->filled('attendance_below') && $avgAttendance >= $request->attendance_below) {
                        continue;
                    }

                    $riskLevel = $avgAttendance < 60 ? 'Critical' : ($avgAttendance < 75 ? 'Moderate' : 'Low');

                    // Apply risk level filter
                    if ($request->filled('risk_level')) {
                        $filterRisk = strtolower($request->risk_level);
                        $studentRisk = strtolower($riskLevel);
                        if ($filterRisk !== $studentRisk) {
                            continue;
                        }
                    }

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

        return back()->with('error', 'Only CSV format is supported for this report.');
    }

    /**
     * Export Academic Health Report with filters
     */
    private function exportAcademicHealth($format, $request)
    {
        $query = User::where('role_id', 3)
            ->with(['department', 'enrollments']);

        // Apply filters
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('year')) {
            $query->where('current_year', $request->year);
        }

        $students = $query->get();

        $filename = 'academic_health_report_' . Carbon::now()->format('Y-m-d') . '.csv';

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($students, $request) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Student Name',
                    'Student ID',
                    'Department',
                    'Year',
                    'Total Enrollments',
                    'Approved Enrollments',
                    'Attendance %',
                    'Roll Call Mark',
                    'Eligibility',
                    'Health Score',
                ]);

                foreach ($students as $student) {
                    $enrollments = $student->enrollments;
                    $totalEnrollments = $enrollments->count();
                    $approvedEnrollments = $enrollments->where('status', 'approved')->count();
                    $avgAttendance = $enrollments->avg('attendance_percentage') ?? 0;
                    $avgRollCall = $enrollments->avg('roll_call_mark') ?? 0;
                    $eligibility = $enrollments->first()->eligibility_status ?? 'N/A';

                    // Calculate health score
                    $healthScore = ($avgAttendance * 0.5) + ($avgRollCall * 0.3) + (($approvedEnrollments / max($totalEnrollments, 1)) * 20);

                    // Apply health score filter
                    if ($request->filled('score_below') && $healthScore >= $request->score_below) {
                        continue;
                    }

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

        return back()->with('error', 'Only CSV format is supported for this report.');
    }

    /**
     * Export Semester Summary with filters
     */
    private function exportSemesterSummary($format, $request)
    {
        $filename = 'semester_summary_' . Carbon::now()->format('Y-m-d') . '.csv';

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($request) {
                $file = fopen('php://output', 'w');

                // Header
                fputcsv($file, ['Semester Summary Report']);
                fputcsv($file, ['Generated on:', Carbon::now()->format('Y-m-d H:i:s')]);

                // Filter info
                if ($request->filled('academic_year')) {
                    fputcsv($file, ['Academic Year:', $request->academic_year]);
                }
                if ($request->filled('semester')) {
                    fputcsv($file, ['Semester:', $request->semester]);
                }
                fputcsv($file, []);

                // Statistics
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

                // Department breakdown
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

        return back()->with('error', 'Only CSV format is supported for this report.');
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
