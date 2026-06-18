<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // ============================================================
    // NESTED METHODS (For department context)
    // ============================================================

    /**
     * Display courses for a specific department (nested)
     */
    public function index(Department $department)
    {
        $courses = $department->courses()
            ->with(['lecturer', 'students'])
            ->orderBy('year')
            ->orderBy('course_code')
            ->get()
            ->groupBy('year');

        return view('admin.courses.index', compact('department', 'courses'));
    }

    /**
     * Show create course form (nested)
     */
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

        return view('admin.courses.create', compact(
            'department',
            'lecturers',
            'allLecturers',
            'years',
            'semesters'
        ));
    }

    /**
     * Store a new course (nested)
     */
    public function store(Request $request, Department $department)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:20|unique:courses',
            'course_name' => 'required|string|max:255',
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

    /**
     * Show a specific course (nested)
     */
    public function show(Department $department, Course $course)
    {
        $course->load(['lecturer', 'students']);
        $students = $course->students()->paginate(20);

        return view('admin.courses.show', compact('department', 'course', 'students'));
    }

    /**
     * Show edit course form (nested)
     */
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

        return view('admin.courses.edit', compact(
            'department',
            'course',
            'lecturers',
            'deptLecturers',
            'years',
            'semesters'
        ));
    }

    /**
     * Update course (nested)
     */
    public function update(Request $request, Department $department, Course $course)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'course_name' => 'required|string|max:255',
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

    /**
     * Delete course (nested)
     */
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
    // STANDALONE METHODS (For sidebar navigation)
    // ============================================================

    /**
     * Display all courses (standalone - from sidebar)
     */
    public function standaloneIndex()
    {
        $courses = Course::with(['department', 'lecturer', 'students'])
            ->orderBy('department_id')
            ->orderBy('year')
            ->orderBy('course_code')
            ->paginate(20);

        $departments = Department::all();

        return view('admin.courses.standalone-index', compact('courses', 'departments'));
    }

    /**
     * Show create course form (standalone)
     */
    public function standaloneCreate()
    {
        $departments = Department::all();
        $lecturers = User::where('role_id', 2)->get();

        $years = [
            'First Year' => 'First Year',
            'Second Year' => 'Second Year',
            'Third Year' => 'Third Year',
            'Fourth Year' => 'Fourth Year',
            'Fifth Year' => 'Fifth Year',
            'Sixth Year' => 'Sixth Year',
        ];

        $semesters = ['First Semester', 'Second Semester'];

        return view('admin.courses.standalone-create', compact('departments', 'lecturers', 'years', 'semesters'));
    }

    /**
     * Store a new course (standalone)
     */
    public function standaloneStore(Request $request)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:20|unique:courses',
            'course_name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'lecturer_id' => 'nullable|exists:users,id',
            'lecturer_name' => 'nullable|string|max:255',
            'credits' => 'required|integer|min:1|max:6',
            'year' => 'required|string',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'room' => 'nullable|string|max:50',
        ]);

        $course = Course::create($validated);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Course created successfully.');
    }

    /**
     * Show a specific course (standalone)
     */
    public function standaloneShow(Course $course)
    {
        $course->load(['department', 'lecturer', 'students']);
        $students = $course->students()->paginate(20);

        return view('admin.courses.standalone-show', compact('course', 'students'));
    }

    /**
     * Show edit course form (standalone)
     */
    public function standaloneEdit(Course $course)
    {
        $departments = Department::all();
        $lecturers = User::where('role_id', 2)->get();

        $years = [
            'First Year' => 'First Year',
            'Second Year' => 'Second Year',
            'Third Year' => 'Third Year',
            'Fourth Year' => 'Fourth Year',
            'Fifth Year' => 'Fifth Year',
            'Sixth Year' => 'Sixth Year',
        ];

        $semesters = ['First Semester', 'Second Semester'];

        return view('admin.courses.standalone-edit', compact('course', 'departments', 'lecturers', 'years', 'semesters'));
    }

    /**
     * Update course (standalone)
     */
    public function standaloneUpdate(Request $request, Course $course)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'course_name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'lecturer_id' => 'nullable|exists:users,id',
            'lecturer_name' => 'nullable|string|max:255',
            'credits' => 'required|integer|min:1|max:6',
            'year' => 'required|string',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'room' => 'nullable|string|max:50',
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Delete course (standalone)
     */
    public function standaloneDestroy(Course $course)
    {
        if ($course->attendanceRecords()->count() > 0) {
            return back()->with('error', 'Cannot delete course with existing attendance records.');
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
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
