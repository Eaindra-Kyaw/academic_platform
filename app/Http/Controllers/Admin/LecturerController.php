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
     * Display a listing of all lecturers with search
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = User::where('role_id', 2);

        // Apply search filter if search term exists
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%');
            });
        }

        $lecturers = $query->with(['department', 'taughtCourses'])
            ->get()
            ->map(function($lecturer) {
                // Count courses
                $lecturer->courses_count = $lecturer->taughtCourses()->count();

                // Count students
                $studentCount = DB::table('enrollments')
                    ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                    ->where('courses.lecturer_id', $lecturer->id)
                    ->distinct('enrollments.student_id')
                    ->count('enrollments.student_id');
                $lecturer->students_count = $studentCount;

                // Calculate attendance
                $avgAttendance = DB::table('enrollments')
                    ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                    ->where('courses.lecturer_id', $lecturer->id)
                    ->avg('enrollments.attendance_percentage');
                $lecturer->avg_attendance = round($avgAttendance ?? 0, 1);

                return $lecturer;
            });

        $departments = Department::all();

        return view('admin.lecturers.index', compact('lecturers', 'departments', 'search'));
    }

    /**
     * Show a specific lecturer
     */
    public function show(User $lecturer)
    {
        if ($lecturer->role_id != 2) {
            abort(404);
        }

        $lecturer->load(['department', 'taughtCourses']);

        $courses = $lecturer->taughtCourses()->with(['department', 'students'])->get();

        $studentCount = DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->where('courses.lecturer_id', $lecturer->id)
            ->distinct('enrollments.student_id')
            ->count('enrollments.student_id');

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
     * Show create lecturer form
     */
    public function create(Request $request)
    {
        $departments = Department::all();
        $selectedDepartment = $request->input('department');
        return view('admin.lecturers.create', compact('departments', 'selectedDepartment'));
    }

    /**
     * Store a new lecturer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'department_id' => 'nullable|exists:departments,id',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 2,
            'department_id' => $validated['department_id'],
            'is_active' => true,
            'must_change_password' => false,
        ]);

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Lecturer created successfully.');
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

        if ($lecturer->taughtCourses()->count() > 0) {
            return back()->with('error', 'Cannot delete lecturer with assigned courses.');
        }

        $lecturer->delete();

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Lecturer deleted successfully.');
    }
}
