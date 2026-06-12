<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of courses with filters.
     */
    public function index(Request $request)
    {
        $query = Course::with(['department', 'lecturer']);

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
        }

        // Search by course code or name
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('course_code', 'like', '%' . $request->search . '%')
                  ->orWhere('course_name', 'like', '%' . $request->search . '%')
                  ->orWhere('lecturer_name', 'like', '%' . $request->search . '%');
            });
        }

        $courses = $query->orderBy('course_code')->paginate(15);
        $departments = Department::orderBy('name')->get();
        $lecturers = User::where('role_id', 2)->orderBy('name')->get();

        return view('admin.courses.index', compact('courses', 'departments', 'lecturers'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $lecturers = User::where('role_id', 2)->orderBy('name')->get();
        return view('admin.courses.create', compact('departments', 'lecturers'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'lecturer_id' => 'nullable|exists:users,id',
            'lecturer_name' => 'nullable|string|max:100',
            'course_code' => 'required|string|max:20|unique:courses',
            'course_name' => 'required|string|max:100',
            'credits' => 'required|integer|min:1|max:6',
            'year' => 'nullable|string|max:50',
            'semester' => 'nullable|string|max:50',
            'academic_year' => 'nullable|string|max:20',
            'room' => 'nullable|string|max:50',
            'schedule_day' => 'nullable|string|max:20',
            'schedule_time' => 'nullable',
            'schedule_end_time' => 'nullable',
            'is_active' => 'boolean',
        ]);

        // If lecturer is selected, auto-fill lecturer_name
        if ($request->filled('lecturer_id')) {
            $lecturer = User::find($request->lecturer_id);
            $validated['lecturer_name'] = $lecturer->name;
        }

        $validated['is_active'] = $request->has('is_active');

        Course::create($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully!');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load(['department', 'lecturer', 'enrollments.student']);
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        $departments = Department::orderBy('name')->get();
        $lecturers = User::where('role_id', 2)->orderBy('name')->get();
        return view('admin.courses.edit', compact('course', 'departments', 'lecturers'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'lecturer_id' => 'nullable|exists:users,id',
            'lecturer_name' => 'nullable|string|max:100',
            'course_code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'course_name' => 'required|string|max:100',
            'credits' => 'required|integer|min:1|max:6',
            'year' => 'nullable|string|max:50',
            'semester' => 'nullable|string|max:50',
            'academic_year' => 'nullable|string|max:20',
            'room' => 'nullable|string|max:50',
            'schedule_day' => 'nullable|string|max:20',
            'schedule_time' => 'nullable',
            'schedule_end_time' => 'nullable',
            'is_active' => 'boolean',
        ]);

        // If lecturer is selected, auto-fill lecturer_name
        if ($request->filled('lecturer_id')) {
            $lecturer = User::find($request->lecturer_id);
            $validated['lecturer_name'] = $lecturer->name;
        } else {
            $validated['lecturer_name'] = $request->lecturer_name;
        }

        $validated['is_active'] = $request->has('is_active');

        $course->update($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course from storage (soft delete).
     */
    public function destroy(Course $course)
    {
        // Check if course has enrollments
        if ($course->enrollments()->count() > 0) {
            return redirect()->route('admin.courses.index')
                ->with('error', 'Cannot delete course with enrolled students. Deactivate it instead.');
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully!');
    }

    /**
 * Restore a soft deleted course.
 */
public function restore($id)
{
    $course = Course::withTrashed()->findOrFail($id);
    $course->restore();

    return redirect()->route('admin.courses.index')
        ->with('success', 'Course restored successfully!');
}

/**
 * Permanently delete a soft deleted course.
 */
public function forceDelete($id)
{
    $course = Course::withTrashed()->findOrFail($id);

    // Check if course has enrollments
    if ($course->enrollments()->count() > 0) {
        return redirect()->route('admin.courses.index')
            ->with('error', 'Cannot delete course with enrolled students.');
    }

    $course->forceDelete();

    return redirect()->route('admin.courses.index')
        ->with('success', 'Course permanently deleted!');
}

    /**
     * Toggle course active status.
     */
    public function toggleStatus(Course $course)
    {
        $course->update(['is_active' => !$course->is_active]);

        $status = $course->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Course {$status} successfully!");
    }
}
