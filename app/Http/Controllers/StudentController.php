<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display student dashboard
     */
    public function dashboard()
    {
        $student = Auth::user();

        // Get enrollment stats
        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with('course')
            ->get();

        $stats = [
            'total_courses' => $enrollments->count(),
            'avg_attendance' => round($enrollments->avg('attendance_percentage') ?? 0, 1),
            'eligible_courses' => $enrollments->where('eligibility_status', 'eligible')->count(),
            'at_risk_courses' => $enrollments->where('eligibility_status', '!=', 'eligible')->count(),
        ];

        return view('student.dashboard', compact('student', 'enrollments', 'stats'));
    }

    /**
     * Display student attendance
     */
    public function attendance()
    {
        $student = Auth::user();

        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with('course')
            ->get();

        return view('student.attendance', compact('student', 'enrollments'));
    }

    /**
     * Display student timetable
     */
    public function timetable()
    {
        $student = Auth::user();

        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with('course')
            ->get();

        return view('student.timetable', compact('student', 'enrollments'));
    }

    /**
     * Display student progress
     */
    public function progress()
    {
        $student = Auth::user();

        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with('course')
            ->get();

        return view('student.progress', compact('student', 'enrollments'));
    }

    /**
     * Show a specific student profile (Admin view)
     */
    public function show(User $student)
    {
        // Ensure this is a student
        if ($student->role_id != 3) {
            abort(404, 'User is not a student');
        }

        $student->load(['department', 'enrollments' => function($query) {
            $query->where('status', 'approved')->with('course');
        }]);

        return view('admin.students.show', compact('student'));
    }
}
