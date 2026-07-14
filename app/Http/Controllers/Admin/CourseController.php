<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of courses for a department.
     */
    public function index(Department $department)
    {
        $courses = $department->courses()
            ->with(['lecturer', 'enrollments'])
            ->orderBy('course_code')
            ->paginate(20);

        // Get semester filters
        $semesters = Semester::orderBy('academic_year', 'desc')
            ->orderBy('semester_number', 'asc')
            ->get();

        // Get all lecturers for filter
        $lecturers = User::where('role_id', 2)->orderBy('name')->get();

        return view('admin.courses.index', compact('department', 'courses', 'semesters', 'lecturers'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(Department $department)
    {
        $lecturers = User::where('role_id', 2)
            ->where(function($query) use ($department) {
                $query->where('department_id', $department->id)
                      ->orWhereNull('department_id');
            })
            ->orderBy('name')
            ->get();

        // ✅ FIXED: use academic_year and semester_number
        $semesters = Semester::orderBy('academic_year', 'desc')
            ->orderBy('semester_number', 'asc')
            ->get();

        $years = [
            'First Year',
            'Second Year',
            'Third Year',
            'Fourth Year',
            'Fifth Year',
            'Sixth Year',
        ];

        return view('admin.courses.create', compact('department', 'lecturers', 'semesters', 'years'));
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request, Department $department)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:20|unique:courses,course_code',
            'course_name' => 'required|string|max:255',
            'lecturer_id' => 'nullable|exists:users,id',
            'lecturer_name' => 'nullable|string|max:255',
            'credits' => 'required|integer|min:1|max:6',
            'year' => 'required|string|in:First Year,Second Year,Third Year,Fourth Year,Fifth Year,Sixth Year',
            'semester' => 'required|string|in:First Semester,Second Semester',
            'academic_year' => 'nullable|string|max:20',
            'room' => 'nullable|string|max:50',
            'semester_id' => 'nullable|exists:semesters,id',
            'is_active' => 'nullable|boolean',
        ]);

        // If lecturer_id is provided, get lecturer name
        if ($validated['lecturer_id']) {
            $lecturer = User::find($validated['lecturer_id']);
            $validated['lecturer_name'] = $lecturer ? $lecturer->name : null;
        }

        $validated['department_id'] = $department->id;
        $validated['is_active'] = $request->has('is_active') ? true : true;

        // Set academic_year from semester if not provided
        if (empty($validated['academic_year']) && $validated['semester_id']) {
            $semester = Semester::find($validated['semester_id']);
            if ($semester) {
                $validated['academic_year'] = $semester->academic_year;
            }
        }

        $course = Course::create($validated);

        return redirect()->route('admin.departments.courses.index', $department)
            ->with('success', 'Course created successfully!');
    }

    /**
     * Display the specified course.
     */
    public function show(Department $department, Course $course)
    {
        $course->load(['lecturer', 'enrollments.student', 'attendanceSessions']);
        return view('admin.courses.show', compact('department', 'course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Department $department, Course $course)
    {
        $lecturers = User::where('role_id', 2)
            ->where(function($query) use ($department) {
                $query->where('department_id', $department->id)
                      ->orWhereNull('department_id');
            })
            ->orderBy('name')
            ->get();

        // ✅ FIXED: use academic_year and semester_number
        $semesters = Semester::orderBy('academic_year', 'desc')
            ->orderBy('semester_number', 'asc')
            ->get();

        $years = [
            'First Year',
            'Second Year',
            'Third Year',
            'Fourth Year',
            'Fifth Year',
            'Sixth Year',
        ];

        return view('admin.courses.edit', compact('department', 'course', 'lecturers', 'semesters', 'years'));
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Department $department, Course $course)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'course_name' => 'required|string|max:255',
            'lecturer_id' => 'nullable|exists:users,id',
            'lecturer_name' => 'nullable|string|max:255',
            'credits' => 'required|integer|min:1|max:6',
            'year' => 'required|string|in:First Year,Second Year,Third Year,Fourth Year,Fifth Year,Sixth Year',
            'semester' => 'required|string|in:First Semester,Second Semester',
            'academic_year' => 'nullable|string|max:20',
            'room' => 'nullable|string|max:50',
            'semester_id' => 'nullable|exists:semesters,id',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validated['lecturer_id']) {
            $lecturer = User::find($validated['lecturer_id']);
            $validated['lecturer_name'] = $lecturer ? $lecturer->name : null;
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        if (empty($validated['academic_year']) && $validated['semester_id']) {
            $semester = Semester::find($validated['semester_id']);
            if ($semester) {
                $validated['academic_year'] = $semester->academic_year;
            }
        }

        $course->update($validated);

        return redirect()->route('admin.departments.courses.index', $department)
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Department $department, Course $course)
    {
        // Check if course has enrollments
        if ($course->enrollments()->count() > 0) {
            return back()->with('error', 'Cannot delete course with existing enrollments.');
        }

        $course->delete();

        return redirect()->route('admin.departments.courses.index', $department)
            ->with('success', 'Course deleted successfully!');
    }

    /**
     * Toggle course active status.
     */
    public function toggleStatus(Department $department, Course $course)
    {
        $course->is_active = !$course->is_active;
        $course->save();

        $status = $course->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Course {$status} successfully!");
    }

    /**
     * Get available lecturers (AJAX).
     */
    public function getLecturers(Request $request)
    {
        $departmentId = $request->input('department_id');
        $lecturers = User::where('role_id', 2)
            ->when($departmentId, function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return response()->json($lecturers);
    }

    /**
     * Export courses to CSV.
     */
    public function export(Department $department)
    {
        $courses = $department->courses()
            ->with(['lecturer', 'department'])
            ->get();

        $filename = 'courses_' . $department->code . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($courses, $department) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Course Code',
                'Course Name',
                'Department',
                'Lecturer',
                'Credits',
                'Year',
                'Semester',
                'Academic Year',
                'Room',
                'Status',
                'Students',
            ]);

            foreach ($courses as $course) {
                fputcsv($file, [
                    $course->course_code,
                    $course->course_name,
                    $department->name,
                    $course->lecturer->name ?? 'Not Assigned',
                    $course->credits,
                    $course->year,
                    $course->semester,
                    $course->academic_year,
                    $course->room ?? 'N/A',
                    $course->is_active ? 'Active' : 'Inactive',
                    $course->enrollments()->where('status', 'approved')->count(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
