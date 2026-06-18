<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LecturerController extends Controller
{
    /**
     * Display a listing of all lecturers
     */
    public function index()
    {
        $lecturers = User::where('role_id', 2)
            ->with(['department', 'taughtCourses'])
            ->get()
            ->map(function($lecturer) {
                // Get average attendance
                $avgAttendance = DB::table('enrollments')
                    ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                    ->where('courses.lecturer_id', $lecturer->id)
                    ->avg('enrollments.attendance_percentage');
                $lecturer->avg_attendance = round($avgAttendance ?? 0, 1);

                // Get student count
                $studentCount = DB::table('enrollments')
                    ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                    ->where('courses.lecturer_id', $lecturer->id)
                    ->distinct('enrollments.student_id')
                    ->count('enrollments.student_id');
                $lecturer->students_count = $studentCount;

                return $lecturer;
            });

        $departments = Department::all();

        return view('admin.lecturers.index', compact('lecturers', 'departments'));
    }

    /**
     * Show a specific lecturer
     */
    public function show(User $lecturer)
    {
        // Ensure this is a lecturer
        if ($lecturer->role_id != 2) {
            abort(404);
        }

        $lecturer->load(['department', 'taughtCourses']);

        // Get courses taught by this lecturer
        $courses = $lecturer->taughtCourses()->with(['department', 'students'])->get();

        // Get student count
        $studentCount = DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->where('courses.lecturer_id', $lecturer->id)
            ->distinct('enrollments.student_id')
            ->count('enrollments.student_id');

        // Get average attendance
        $avgAttendance = DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->where('courses.lecturer_id', $lecturer->id)
            ->avg('enrollments.attendance_percentage');

        $stats = [
            'total_courses' => $courses->count(),
            'total_students' => $studentCount,
            'avg_attendance' => round($avgAttendance ?? 0, 1),
        ];

        return view('admin.lecturers.show', compact('lecturer', 'courses', 'stats'));
    }

    /**
     * Show edit form for lecturer
     */
    public function edit(User $lecturer)
    {
        if ($lecturer->role_id != 2) {
            abort(404);
        }

        $departments = Department::all();
        return view('admin.lecturers.edit', compact('lecturer', 'departments'));
    }

    /**
     * Update lecturer
     */
    public function update(Request $request, User $lecturer)
    {
        if ($lecturer->role_id != 2) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $lecturer->id,
            'department_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
        ]);

        $lecturer->update($validated);

        return redirect()->route('admin.lecturers.show', $lecturer)
            ->with('success', 'Lecturer updated successfully.');
    }

    /**
     * Delete lecturer
     */
    public function destroy(User $lecturer)
    {
        if ($lecturer->role_id != 2) {
            abort(404);
        }

        // Check if lecturer has courses
        if ($lecturer->taughtCourses()->count() > 0) {
            return back()->with('error', 'Cannot delete lecturer with assigned courses.');
        }

        $lecturer->delete();

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Lecturer deleted successfully.');
    }
}
