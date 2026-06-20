<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\Course;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    /**
     * Display a listing of all departments with stats
     */
    public function index()
    {
        $departments = Department::withCount(['students', 'courses', 'lecturers'])
            ->get()
            ->map(function($dept) {
                $dept->avg_attendance = $dept->overall_attendance;
                return $dept;
            });

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show a specific department with years, courses, students, lecturers
     */
    public function show(Department $department)
    {
        // Get all students grouped by year
        $studentsByYear = $department->students()
            ->leftJoin('enrollments', 'users.id', '=', 'enrollments.student_id')
            ->select(
                'current_year as year',
                DB::raw('count(distinct users.id) as total'),
                DB::raw('avg(enrollments.attendance_percentage) as avg_attendance')
            )
            ->groupBy('current_year')
            ->orderBy('current_year')
            ->get()
            ->keyBy('year');

        // Get all courses grouped by year
        $coursesByYear = $department->courses()
            ->with(['lecturer', 'students'])
            ->get()
            ->groupBy('year')
            ->map(function($courses) {
                return $courses->map(function($course) {
                    $course->student_count = $course->student_count;
                    $course->avg_attendance = $course->average_attendance;
                    return $course;
                });
            });

        // Get ALL lecturers in the system (not just those with department_id)
        // Then filter by those who teach courses in this department
        $allLecturers = User::where('role_id', 2)->get();

        $lecturers = $allLecturers->filter(function($lecturer) use ($department) {
            // Check if this lecturer teaches any course in this department
            $courseCount = Course::where('department_id', $department->id)
                ->where(function($query) use ($lecturer) {
                    $query->where('lecturer_id', $lecturer->id)
                          ->orWhere('lecturer_name', 'LIKE', '%' . $lecturer->name . '%')
                          ->orWhere('lecturer_name', 'LIKE', '%' . explode('@', $lecturer->email)[0] . '%');
                })
                ->count();

            // Also include if the lecturer belongs to this department
            return $courseCount > 0 || $lecturer->department_id == $department->id;
        })->values();

        // Now calculate stats for each lecturer
        $lecturers = $lecturers->map(function($lecturer) use ($department) {
            // Get courses for this lecturer in this department
            $courses = Course::where('department_id', $department->id)
                ->where(function($query) use ($lecturer) {
                    $query->where('lecturer_id', $lecturer->id)
                          ->orWhere('lecturer_name', 'LIKE', '%' . $lecturer->name . '%')
                          ->orWhere('lecturer_name', 'LIKE', '%' . explode('@', $lecturer->email)[0] . '%');
                })
                ->get();

            $lecturer->courses_count = $courses->count();

            $courseIds = $courses->pluck('id');

            if ($courseIds->count() > 0) {
                $studentCount = DB::table('enrollments')
                    ->whereIn('course_id', $courseIds)
                    ->distinct('student_id')
                    ->count('student_id');
                $lecturer->students_count = $studentCount;

                $avgAttendance = DB::table('enrollments')
                    ->whereIn('course_id', $courseIds)
                    ->avg('attendance_percentage');
                $lecturer->avg_attendance = round($avgAttendance ?? 0, 1);
            } else {
                $lecturer->students_count = 0;
                $lecturer->avg_attendance = 0;
            }

            return $lecturer;
        });

        // Department statistics
        $stats = [
            'total_students' => $department->students()->count(),
            'total_courses' => $department->courses()->count(),
            'total_lecturers' => $lecturers->count(),
            'overall_attendance' => $department->overall_attendance,
            'years' => range(1, 6),
        ];

        // Get available years with data
        $availableYears = $department->students()
            ->select('current_year')
            ->distinct()
            ->orderBy('current_year')
            ->pluck('current_year')
            ->toArray();

        // Get all semesters
        $semesters = Semester::orderBy('year')
            ->orderBy('semester')
            ->get();

        return view('admin.departments.show', compact(
            'department',
            'studentsByYear',
            'coursesByYear',
            'lecturers',
            'stats',
            'availableYears',
            'semesters'
        ));
    }

    /**
     * Show students by year within department
     */
    public function studentsByYear(Department $department, $year)
    {
        $students = $department->students()
            ->where('current_year', $year)
            ->with(['enrollments' => function($query) {
                $query->where('status', 'approved')->with('course');
            }])
            ->paginate(20);

        $yearLabel = $this->getYearLabel($year);

        return view('admin.departments.year.students', compact(
            'department',
            'year',
            'yearLabel',
            'students'
        ));
    }

    /**
     * Show courses by year within department
     */
    public function coursesByYear(Department $department, $year)
    {
        $courses = $department->courses()
            ->where('year', $this->getYearString($year))
            ->with(['lecturer', 'students'])
            ->get()
            ->map(function($course) {
                $course->student_count = $course->student_count;
                $course->avg_attendance = $course->average_attendance;
                return $course;
            });

        $yearLabel = $this->getYearLabel($year);

        return view('admin.departments.year.courses', compact(
            'department',
            'year',
            'yearLabel',
            'courses'
        ));
    }

    /**
     * Show courses for a specific semester in a department
     */
    public function semesterCourses(Department $department, $semesterId)
    {
        $semester = Semester::findOrFail($semesterId);

        $courses = Course::where('department_id', $department->id)
            ->where('year', $semester->year_name)
            ->where('semester', $semester->semester_name)
            ->with(['lecturer', 'students'])
            ->get()
            ->map(function($course) {
                $course->student_count = $course->student_count;
                $course->avg_attendance = $course->average_attendance;
                return $course;
            });

        return view('admin.departments.semester-courses', compact('department', 'semester', 'courses'));
    }

    /**
     * Export students by year to CSV
     */
    public function exportStudents(Department $department, $year)
    {
        $students = $department->students()
            ->where('current_year', $year)
            ->with(['enrollments' => function($query) {
                $query->where('status', 'approved')->with('course');
            }])
            ->get();

        // Prepare CSV data
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $department->code . '_Year_' . $year . '_Students.csv"',
        ];

        $callback = function() use ($students) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Add headers
            fputcsv($handle, ['Student ID', 'Name', 'Email', 'Courses', 'Attendance %', 'Risk Level']);

            // Add data
            foreach ($students as $student) {
                $attendance = $student->attendance_percentage ?? 0;
                $risk = $attendance >= 75 ? 'Low' : ($attendance >= 60 ? 'Medium' : 'High');
                $coursesCount = $student->enrollments->where('status', 'approved')->count();

                fputcsv($handle, [
                    $student->student_id ?? 'N/A',
                    $student->name,
                    $student->email,
                    $coursesCount,
                    number_format($attendance, 1) . '%',
                    $risk
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show create department form
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a new department
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:departments',
            'name' => 'required|string|max:255|unique:departments',
            'head_of_department' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Show edit department form
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update department
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:departments,code,' . $department->id,
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'head_of_department' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Delete department
     */
    public function destroy(Department $department)
    {
        if ($department->students()->count() > 0 || $department->courses()->count() > 0) {
            return back()->with('error', 'Cannot delete department with existing students or courses.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Get year label (1st, 2nd, 3rd, etc.)
     */
    private function getYearLabel($year)
    {
        $suffixes = ['th', 'st', 'nd', 'rd'];
        $suffix = $year <= 3 ? $suffixes[$year] : 'th';
        return $year . $suffix . ' Year';
    }

    /**
     * Get year string for database lookup
     */
    private function getYearString($year)
    {
        $map = [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
            6 => 'Sixth Year',
        ];
        return $map[$year] ?? 'First Year';
    }
}
