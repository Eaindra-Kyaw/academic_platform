<?php
// app/Http/Controllers/Admin/ReportsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use App\Models\Department;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index(Request $request)
    {
        try {
            // Get basic statistics
            $stats = [
                'total_students' => Student::count(),
                'total_courses' => Course::count(),
                'total_departments' => Department::count(),
                'total_enrollments' => Enrollment::where('status', 'approved')->count(),
                'pending_enrollments' => Enrollment::where('status', 'pending')->count(),
            ];

            // Get enrollment trends by month (last 6 months)
            $enrollmentTrends = Enrollment::where('status', 'approved')
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Get department-wise enrollment
            $departmentEnrollments = Department::withCount(['courses' => function($q) {
                $q->whereHas('enrollments', function($eq) {
                    $eq->where('status', 'approved');
                });
            }])->get()->map(function($dept) {
                $count = 0;
                foreach ($dept->courses as $course) {
                    $count += $course->enrollments->where('status', 'approved')->count();
                }
                return [
                    'name' => $dept->name,
                    'count' => $count
                ];
            })->sortByDesc('count')->values();

            // Get year-wise enrollment
            $yearEnrollments = Student::select('current_year')
                ->withCount(['enrollments' => function($q) {
                    $q->where('status', 'approved');
                }])
                ->groupBy('current_year')
                ->get()
                ->mapWithKeys(function($item) {
                    return ['Year ' . ($item->current_year ?? 'N/A') => $item->enrollments_count];
                });

            // Get recent enrollments
            $recentEnrollments = Enrollment::with(['student', 'course'])
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('admin.reports.index', compact(
                'stats',
                'enrollmentTrends',
                'departmentEnrollments',
                'yearEnrollments',
                'recentEnrollments'
            ));

        } catch (\Exception $e) {
            \Log::error('Error loading reports: ' . $e->getMessage());
            return back()->with('error', 'Failed to load reports: ' . $e->getMessage());
        }
    }

    /**
     * Generate student report
     */
    public function students(Request $request)
    {
        try {
            $query = Student::with(['major', 'department', 'enrollments']);

            // Apply filters
            if ($request->filled('department')) {
                $query->where('department_id', $request->department);
            }

            if ($request->filled('year')) {
                $query->where('current_year', $request->year);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('student_id', 'like', "%{$search}%");
                });
            }

            $students = $query->orderBy('name')->paginate(20);
            $departments = Department::orderBy('name')->get();

            return view('admin.reports.students', compact('students', 'departments'));

        } catch (\Exception $e) {
            \Log::error('Error loading student report: ' . $e->getMessage());
            return back()->with('error', 'Failed to load student report: ' . $e->getMessage());
        }
    }

    /**
     * Generate enrollment report
     */
    public function enrollments(Request $request)
    {
        try {
            $query = Enrollment::with(['student', 'course.department']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('department')) {
                $query->whereHas('course.department', function($q) use ($request) {
                    $q->where('id', $request->department);
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);
            $departments = Department::orderBy('name')->get();

            // Get stats for filters
            $stats = [
                'total' => Enrollment::count(),
                'pending' => Enrollment::where('status', 'pending')->count(),
                'approved' => Enrollment::where('status', 'approved')->count(),
                'rejected' => Enrollment::where('status', 'rejected')->count(),
            ];

            return view('admin.reports.enrollments', compact('enrollments', 'departments', 'stats'));

        } catch (\Exception $e) {
            \Log::error('Error loading enrollment report: ' . $e->getMessage());
            return back()->with('error', 'Failed to load enrollment report: ' . $e->getMessage());
        }
    }

    /**
     * Generate attendance report
     */
    public function attendance(Request $request)
    {
        try {
            $query = Attendance::with(['student', 'course']);

            // Apply filters
            if ($request->filled('student_id')) {
                $query->where('student_id', $request->student_id);
            }

            if ($request->filled('course_id')) {
                $query->where('course_id', $request->course_id);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            $attendances = $query->orderBy('date', 'desc')->paginate(20);
            $students = Student::orderBy('name')->get();
            $courses = Course::orderBy('course_code')->get();

            return view('admin.reports.attendance', compact('attendances', 'students', 'courses'));

        } catch (\Exception $e) {
            \Log::error('Error loading attendance report: ' . $e->getMessage());
            return back()->with('error', 'Failed to load attendance report: ' . $e->getMessage());
        }
    }

    /**
     * Export report data
     */
    public function export(Request $request, $type)
    {
        try {
            switch ($type) {
                case 'students':
                    return $this->exportStudents($request);
                case 'enrollments':
                    return $this->exportEnrollments($request);
                case 'attendance':
                    return $this->exportAttendance($request);
                default:
                    return back()->with('error', 'Invalid export type');
            }
        } catch (\Exception $e) {
            \Log::error('Error exporting report: ' . $e->getMessage());
            return back()->with('error', 'Failed to export report: ' . $e->getMessage());
        }
    }

    /**
     * Export students to CSV
     */
    private function exportStudents(Request $request)
    {
        $query = Student::with(['major', 'department']);

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        $students = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student ID', 'Name', 'Email', 'Major', 'Department', 'Year', 'Status']);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->student_id ?? 'N/A',
                    $student->name,
                    $student->email,
                    $student->major->name ?? 'N/A',
                    $student->department->name ?? 'N/A',
                    $student->current_year ?? 'N/A',
                    $student->status ?? 'Active'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export enrollments to CSV
     */
    private function exportEnrollments(Request $request)
    {
        $query = Enrollment::with(['student', 'course.department']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="enrollments_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($enrollments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student', 'Course', 'Department', 'Status', 'Request Date', 'Approved Date']);

            foreach ($enrollments as $enrollment) {
                fputcsv($file, [
                    $enrollment->student->name ?? 'N/A',
                    $enrollment->course->course_code ?? 'N/A',
                    $enrollment->course->department->name ?? 'N/A',
                    ucfirst($enrollment->status),
                    $enrollment->created_at->format('Y-m-d H:i'),
                    $enrollment->approved_at ? Carbon::parse($enrollment->approved_at)->format('Y-m-d H:i') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export attendance to CSV
     */
    private function exportAttendance(Request $request)
    {
        $query = Attendance::with(['student', 'course']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $attendances = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student', 'Course', 'Date', 'Status', 'Check In Time', 'Check Out Time']);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->student->name ?? 'N/A',
                    $attendance->course->course_code ?? 'N/A',
                    $attendance->date ? Carbon::parse($attendance->date)->format('Y-m-d') : 'N/A',
                    ucfirst($attendance->status ?? 'N/A'),
                    $attendance->check_in_time ?? 'N/A',
                    $attendance->check_out_time ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
