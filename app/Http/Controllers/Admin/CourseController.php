<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Course;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // ============================================================
    // NESTED METHODS (For department context)
    // ============================================================

    public function index(Department $department)
    {
        $courses = $department->courses()
            ->with(['lecturer', 'students', 'semester'])  // Add 'semester'
            ->orderBy('year')
            ->orderBy('course_code')
            ->get()
            ->groupBy('year');

        return view('admin.courses.index', compact('department', 'courses'));
    }

    public function create(Department $department)
    {
        $lecturers = User::where('role_id', 2)
            ->where('department_id', $department->id)
            ->get();

        $allLecturers = User::where('role_id', 2)->get();

        $years = [
            'First Year' => 'First Year',
            'Second Year' => 'Second Year',
            'Third Year' => 'Third Year',
            'Fourth Year' => 'Fourth Year',
            'Fifth Year' => 'Fifth Year',
            'Sixth Year' => 'Sixth Year',
        ];

        $semesters = ['First Semester', 'Second Semester'];

        // Get all semesters for dropdown
        $allSemesters = Semester::orderBy('year')->orderBy('semester')->get();

        return view('admin.courses.create', compact(
            'department',
            'lecturers',
            'allLecturers',
            'years',
            'semesters',
            'allSemesters'  // Add this
        ));
    }

    public function store(Request $request, Department $department)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:20|unique:courses',
            'course_name' => 'required|string|max:255',
            'semester_id' => 'nullable|exists:semesters,id',  // Add this
            'lecturer_id' => 'nullable|exists:users,id',
            'lecturer_name' => 'nullable|string|max:255',
            'credits' => 'required|integer|min:1|max:6',
            'year' => 'required|string',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'room' => 'nullable|string|max:50',
        ]);

        $course = $department->courses()->create($validated);

        return redirect()->route('admin.departments.courses.show', [$department, $course])
            ->with('success', 'Course created successfully.');
    }

    public function show(Department $department, Course $course)
    {
        $course->load(['lecturer', 'students', 'semester']);  // Add 'semester'
        $students = $course->students()->paginate(20);

        return view('admin.courses.show', compact('department', 'course', 'students'));
    }

    public function edit(Department $department, Course $course)
    {
        $lecturers = User::where('role_id', 2)->get();
        $deptLecturers = User::where('role_id', 2)
            ->where('department_id', $department->id)
            ->get();

        $years = [
            'First Year' => 'First Year',
            'Second Year' => 'Second Year',
            'Third Year' => 'Third Year',
            'Fourth Year' => 'Fourth Year',
            'Fifth Year' => 'Fifth Year',
            'Sixth Year' => 'Sixth Year',
        ];

        $semesters = ['First Semester', 'Second Semester'];

        // Get all semesters for dropdown
        $allSemesters = Semester::orderBy('year')->orderBy('semester')->get();

        return view('admin.courses.edit', compact(
            'department',
            'course',
            'lecturers',
            'deptLecturers',
            'years',
            'semesters',
            'allSemesters'  // Add this
        ));
    }

    public function update(Request $request, Department $department, Course $course)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'course_name' => 'required|string|max:255',
            'semester_id' => 'nullable|exists:semesters,id',  // Add this
            'lecturer_id' => 'nullable|exists:users,id',
            'lecturer_name' => 'nullable|string|max:255',
            'credits' => 'required|integer|min:1|max:6',
            'year' => 'required|string',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'room' => 'nullable|string|max:50',
        ]);

        $course->update($validated);

        return redirect()->route('admin.departments.courses.show', [$department, $course])
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Department $department, Course $course)
    {
        if ($course->attendanceRecords()->count() > 0) {
            return back()->with('error', 'Cannot delete course with existing attendance records.');
        }

        $course->delete();

        return redirect()->route('admin.departments.courses.index', $department)
            ->with('success', 'Course deleted successfully.');
    }

    // ============================================================
    // RESTORE & FORCE DELETE (for soft deletes)
    // ============================================================

    public function restore($id)
    {
        $course = Course::withTrashed()->findOrFail($id);
        $course->restore();
        return back()->with('success', 'Course restored successfully.');
    }

    public function forceDelete($id)
    {
        $course = Course::withTrashed()->findOrFail($id);
        $course->forceDelete();
        return back()->with('success', 'Course permanently deleted.');
    }

    public function toggleStatus(Course $course)
    {
        $course->is_active = !$course->is_active;
        $course->save();
        return back()->with('success', 'Course status updated.');
    }
}
