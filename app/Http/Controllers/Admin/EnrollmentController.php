<?php
// app/Http/Controllers/Admin/EnrollmentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    /**
     * Display all departments (Page 1)
     */
    public function index(Request $request)
    {
        try {
            $departments = Department::withCount([
                'courses as enrollments_count' => function($query) {
                    $query->join('enrollments', 'courses.id', '=', 'enrollments.course_id');
                },
                'courses as pending_count' => function($query) {
                    $query->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
                          ->where('enrollments.status', 'pending');
                }
            ])->orderBy('name')->get();

            $stats = [
                'pending' => Enrollment::where('status', 'pending')->count(),
                'approved' => Enrollment::where('status', 'approved')->count(),
                'rejected' => Enrollment::where('status', 'rejected')->count(),
                'total' => Enrollment::count(),
            ];

            return view('admin.enrollments.index', compact('departments', 'stats'));

        } catch (\Exception $e) {
            \Log::error('Error loading departments: ' . $e->getMessage());
            return back()->with('error', 'Failed to load departments: ' . $e->getMessage());
        }
    }

    /**
     * Show courses for a specific department with year tabs (Page 2)
     */
    public function showDepartment($departmentId, Request $request)
    {
        try {
            $department = Department::findOrFail($departmentId);
            $year = $request->input('year', 'all');

            $query = Course::with(['department'])
                ->where('department_id', $departmentId);

            // Apply year filter if not 'all'
            if ($year != 'all') {
                // Convert numeric year to full year name if needed
                $yearName = Course::$yearNumericMap[$year] ?? $year;
                $query->where('year', $yearName);
            }

            $courses = $query->withCount([
                'enrollments as students_count',
                'enrollments as pending_count' => function($q) {
                    $q->where('status', 'pending');
                },
                'enrollments as approved_count' => function($q) {
                    $q->where('status', 'approved');
                },
                'enrollments as rejected_count' => function($q) {
                    $q->where('status', 'rejected');
                }
            ])->orderBy('course_code')->get();

            $stats = [
                'total_courses' => $courses->count(),
                'total_students' => Enrollment::whereHas('course', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->count(),
                'pending_enrollments' => Enrollment::whereHas('course', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->where('status', 'pending')->count(),
                'approved_enrollments' => Enrollment::whereHas('course', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->where('status', 'approved')->count(),
            ];

            // Get year counts for tabs - using full year names
            $yearCounts = [];
            $yearNames = ['First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Fifth Year', 'Sixth Year'];
            foreach ($yearNames as $i => $yearName) {
                $yearCounts[$i + 1] = Course::where('department_id', $departmentId)
                    ->where('year', $yearName)
                    ->count();
            }
            $yearCounts['total'] = Course::where('department_id', $departmentId)->count();

            return view('admin.enrollments.department-year', compact(
                'department',
                'courses',
                'year',
                'stats',
                'yearCounts'
            ));

        } catch (\Exception $e) {
            \Log::error('Error loading department courses: ' . $e->getMessage());
            return back()->with('error', 'Failed to load courses: ' . $e->getMessage());
        }
    }

    /**
     * Show courses for a specific department and year
     */
    public function showDepartmentYear($departmentId, $year)
    {
        try {
            $department = Department::findOrFail($departmentId);

            // Convert numeric year to full year name
            $yearName = Course::$yearNumericMap[$year] ?? $year;

            $query = Course::with(['department'])
                ->where('department_id', $departmentId)
                ->where('year', $yearName);

            $courses = $query->withCount([
                'enrollments as students_count',
                'enrollments as pending_count' => function($q) {
                    $q->where('status', 'pending');
                },
                'enrollments as approved_count' => function($q) {
                    $q->where('status', 'approved');
                },
                'enrollments as rejected_count' => function($q) {
                    $q->where('status', 'rejected');
                }
            ])->orderBy('course_code')->get();

            $stats = [
                'total_courses' => $courses->count(),
                'total_students' => Enrollment::whereHas('course', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->count(),
                'pending_enrollments' => Enrollment::whereHas('course', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->where('status', 'pending')->count(),
                'approved_enrollments' => Enrollment::whereHas('course', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->where('status', 'approved')->count(),
            ];

            // Get year counts for tabs
            $yearCounts = [];
            $yearNames = ['First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Fifth Year', 'Sixth Year'];
            foreach ($yearNames as $i => $yearName) {
                $yearCounts[$i + 1] = Course::where('department_id', $departmentId)
                    ->where('year', $yearName)
                    ->count();
            }
            $yearCounts['total'] = Course::where('department_id', $departmentId)->count();

            return view('admin.enrollments.department-year', compact(
                'department',
                'courses',
                'year',
                'stats',
                'yearCounts'
            ));

        } catch (\Exception $e) {
            \Log::error('Error loading department year courses: ' . $e->getMessage());
            return back()->with('error', 'Failed to load courses: ' . $e->getMessage());
        }
    }

    /**
     * Show students enrolled in a specific course (Page 3)
     */
    public function showCourse($courseId)
    {
        try {
            $course = Course::with('department')->findOrFail($courseId);

            $query = Enrollment::with(['student'])
                ->where('course_id', $courseId);

            $enrollments = $query->orderBy('created_at', 'desc')->paginate(15);

            $stats = [
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'approved' => (clone $query)->where('status', 'approved')->count(),
                'rejected' => (clone $query)->where('status', 'rejected')->count(),
                'total' => $enrollments->total(),
            ];

            return view('admin.enrollments.course', compact(
                'enrollments',
                'course',
                'stats'
            ));

        } catch (\Exception $e) {
            \Log::error('Error loading course enrollments: ' . $e->getMessage());
            return back()->with('error', 'Failed to load course enrollments: ' . $e->getMessage());
        }
    }

    /**
     * Approve an enrollment
     */
    // In approve method
public function approve($id)
{
    $enrollment = Enrollment::findOrFail($id);
    $enrollment->status = 'approved';
    $enrollment->approved_at = Carbon::now();  // ✅ Set approved date
    $enrollment->rejected_at = null;
    $enrollment->save();

    return redirect()->back()->with('success', 'Enrollment approved successfully!');
}

// In reject method
public function reject(Request $request, $id)
{
    $enrollment = Enrollment::findOrFail($id);
    $enrollment->status = 'rejected';
    $enrollment->rejected_at = Carbon::now();  // ✅ Set rejected date
    $enrollment->approved_at = null;
    $enrollment->save();

    return redirect()->back()->with('success', 'Enrollment rejected successfully!');
}
    /**
     * Bulk approve enrollments
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:enrollments,id',
        ]);

        try {
            $count = 0;
            $errors = [];

            foreach ($request->enrollment_ids as $id) {
                try {
                    $enrollment = Enrollment::find($id);
                    if ($enrollment && $enrollment->status === 'pending') {
                        $enrollment->update([
                            'status' => 'approved',
                            'approved_at' => Carbon::now(),
                        ]);
                        $count++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Failed for ID {$id}";
                }
            }

            $message = "{$count} enrollment(s) approved successfully.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " failed.";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process bulk approval: ' . $e->getMessage());
        }
    }

    /**
     * Bulk reject enrollments
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:enrollments,id',
            'rejection_reason' => 'required|string|min:5|max:500',
        ]);

        try {
            $count = 0;
            $errors = [];

            foreach ($request->enrollment_ids as $id) {
                try {
                    $enrollment = Enrollment::find($id);
                    if ($enrollment && $enrollment->status === 'pending') {
                        $enrollment->update([
                            'status' => 'rejected',
                            'rejection_reason' => $request->rejection_reason,
                            'rejected_at' => Carbon::now(),
                        ]);
                        $count++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Failed for ID {$id}";
                }
            }

            $message = "{$count} enrollment(s) rejected successfully.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " failed.";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process bulk rejection: ' . $e->getMessage());
        }
    }

    /**
     * Get student details for modal
     */
    public function showStudent($id)
    {
        try {
            $student = User::with(['department'])
                ->where('role_id', 3)
                ->findOrFail($id);

            $currentEnrollments = Enrollment::where('student_id', $id)
                ->where('status', 'approved')
                ->with('course')
                ->get()
                ->map(function($enrollment) {
                    return $enrollment->course->course_code . ' - ' . $enrollment->course->course_name;
                })
                ->join(', ') ?: 'No active courses';

            return response()->json([
                'name' => $student->name,
                'email' => $student->email,
                'student_id' => $student->student_id ?? 'N/A',
                'department' => $student->department->name ?? 'N/A',
                'current_year' => $student->current_year ?? 'N/A',
                'gpa' => $student->gpa ?? 'N/A',
                'total_credits' => $student->total_credits ?? 0,
                'current_courses' => $currentEnrollments,
                'enrollment_count' => Enrollment::where('student_id', $id)->where('status', 'approved')->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load student profile'], 500);
        }
    }

    /**
     * Get filtered enrollments with search and filters
     */
    public function filtered(Request $request)
    {
        try {
            $query = Enrollment::with(['student', 'course.department']);

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('student', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('student_id', 'LIKE', "%{$search}%");
                })->orWhereHas('course', function($q) use ($search) {
                    $q->where('course_code', 'LIKE', "%{$search}%")
                      ->orWhere('course_name', 'LIKE', "%{$search}%");
                });
            }

            // Apply status filter
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Apply department filter
            if ($request->filled('department') && $request->department !== 'all') {
                $query->whereHas('course', function($q) use ($request) {
                    $q->where('department_id', $request->department);
                });
            }

            // Apply course filter
            if ($request->filled('course') && $request->course !== 'all') {
                $query->where('course_id', $request->course);
            }

            $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);

            // Get stats for filtered results
            $stats = [
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'approved' => (clone $query)->where('status', 'approved')->count(),
                'rejected' => (clone $query)->where('status', 'rejected')->count(),
                'total' => (clone $query)->count(),
            ];

            // For filter dropdowns
            $departments = Department::orderBy('name')->get();
            $courses = Course::orderBy('course_code')->get();

            // Selected filter values
            $selectedYear = $request->input('year', 'all');
            $selectedDepartment = $request->input('department', 'all');
            $selectedCourse = $request->input('course', 'all');
            $selectedStatus = $request->input('status', 'all');
            $selectedDeptName = $selectedDepartment !== 'all' ? Department::find($selectedDepartment)?->name : null;
            $selectedCourseName = $selectedCourse !== 'all' ? Course::find($selectedCourse)?->course_code : null;

            return view('admin.enrollments.filtered', compact(
                'enrollments',
                'stats',
                'departments',
                'courses',
                'selectedYear',
                'selectedDepartment',
                'selectedCourse',
                'selectedStatus',
                'selectedDeptName',
                'selectedCourseName'
            ));

        } catch (\Exception $e) {
            \Log::error('Error loading filtered enrollments: ' . $e->getMessage());
            return back()->with('error', 'Failed to load enrollments: ' . $e->getMessage());
        }
    }

    /**
     * Add a student to a course (Admin only)
     */
    public function addStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:pending,approved,rejected',
            'attendance_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            // Check if enrollment already exists
            $exists = Enrollment::where('student_id', $request->student_id)
                ->where('course_id', $request->course_id)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Student is already enrolled in this course.');
            }

            $enrollment = Enrollment::create([
                'student_id' => $request->student_id,
                'course_id' => $request->course_id,
                'enrollment_date' => Carbon::now(),
                'status' => $request->status,
                'attendance_percentage' => $request->attendance_percentage ?? 0,
                'roll_call_mark' => 0,
                'eligibility_status' => 'eligible',
                'approved_at' => $request->status == 'approved' ? Carbon::now() : null,
            ]);

            $student = User::find($request->student_id);
            $course = Course::find($request->course_id);

            return back()->with('success', "Student {$student->name} added to {$course->course_code} successfully!");

        } catch (\Exception $e) {
            \Log::error('Error adding student to course: ' . $e->getMessage());
            return back()->with('error', 'Failed to add student: ' . $e->getMessage());
        }
    }

    /**
     * Remove a student from a course (Drop enrollment)
     */
    public function dropStudent($enrollmentId)
    {
        try {
            $enrollment = Enrollment::with(['student', 'course'])->findOrFail($enrollmentId);

            if ($enrollment->status !== 'approved') {
                return back()->with('error', 'Only approved enrollments can be dropped.');
            }

            $enrollment->update([
                'status' => 'dropped',
                'dropped_at' => Carbon::now(),
            ]);

            return back()->with('success', "Student {$enrollment->student->name} dropped from {$enrollment->course->course_code}.");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to drop student: ' . $e->getMessage());
        }
    }

    /**
     * Export enrollments to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = Enrollment::with(['student', 'course.department']);

            // Apply filters if provided
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('department') && $request->department !== 'all') {
                $query->whereHas('course', function($q) use ($request) {
                    $q->where('department_id', $request->department);
                });
            }

            if ($request->filled('course') && $request->course !== 'all') {
                $query->where('course_id', $request->course);
            }

            $enrollments = $query->get();

            $filename = 'enrollments_export_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($enrollments) {
                $file = fopen('php://output', 'w');

                // Add headers
                fputcsv($file, [
                    'ID',
                    'Student Name',
                    'Student Email',
                    'Student ID',
                    'Course Code',
                    'Course Name',
                    'Department',
                    'Status',
                    'Enrollment Date',
                    'Attendance %',
                    'Eligibility',
                    'Approved At'
                ]);

                // Add rows
                foreach ($enrollments as $enrollment) {
                    fputcsv($file, [
                        $enrollment->id,
                        $enrollment->student->name ?? 'N/A',
                        $enrollment->student->email ?? 'N/A',
                        $enrollment->student->student_id ?? 'N/A',
                        $enrollment->course->course_code ?? 'N/A',
                        $enrollment->course->course_name ?? 'N/A',
                        $enrollment->course->department->name ?? 'N/A',
                        $enrollment->status,
                        $enrollment->created_at ? $enrollment->created_at->format('Y-m-d') : 'N/A',
                        $enrollment->attendance_percentage ?? 0,
                        $enrollment->eligibility_status ?? 'N/A',
                        $enrollment->approved_at ? $enrollment->approved_at->format('Y-m-d') : 'N/A',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Error exporting enrollments: ' . $e->getMessage());
            return back()->with('error', 'Failed to export enrollments: ' . $e->getMessage());
        }
    }

    /**
     * Get enrollment statistics for dashboard
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_enrollments' => Enrollment::count(),
                'pending' => Enrollment::where('status', 'pending')->count(),
                'approved' => Enrollment::where('status', 'approved')->count(),
                'rejected' => Enrollment::where('status', 'rejected')->count(),
                'dropped' => Enrollment::where('status', 'dropped')->count(),
                'by_department' => [],
                'by_year' => [],
            ];

            // Get stats by department
            $stats['by_department'] = Department::withCount([
                'enrollments as total',
                'enrollments as pending' => function($q) {
                    $q->where('status', 'pending');
                },
                'enrollments as approved' => function($q) {
                    $q->where('status', 'approved');
                },
                'enrollments as rejected' => function($q) {
                    $q->where('status', 'rejected');
                }
            ])->get()->map(function($dept) {
                return [
                    'name' => $dept->name,
                    'total' => $dept->total ?? 0,
                    'pending' => $dept->pending ?? 0,
                    'approved' => $dept->approved ?? 0,
                    'rejected' => $dept->rejected ?? 0,
                ];
            });

            // Get stats by year
            for ($i = 1; $i <= 6; $i++) {
                $stats['by_year'][$i] = Enrollment::whereHas('student', function($q) use ($i) {
                    $q->where('current_year', $i);
                })->count();
            }

            return response()->json($stats);

        } catch (\Exception $e) {
            \Log::error('Error getting enrollment stats: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load stats'], 500);
        }
    }
}
