<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class EnrollmentController extends Controller
{
    // Show available courses for the student (filtered by their year)
    public function availableCourses(Request $request)
    {
        $student = Auth::user();

        // Map student's current_year (1,2,3,4,5) to course year string
        $yearMapping = [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
        ];

        $studentYearString = $yearMapping[$student->current_year] ?? 'First Year';

        // Get courses for student's year that are active with search filter
        $query = Course::where('year', $studentYearString)
            ->where('is_active', true);

        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%")
                  ->orWhere('lecturer_name', 'like', "%{$search}%");
            });
        }

        // Apply department filter
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        // Apply semester filter
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $availableCourses = $query->orderBy('course_code')->paginate(12);

        // Get courses the student already requested/enrolled in
        $enrolledCourseIds = Enrollment::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('course_id')
            ->toArray();

        return view('student.courses.available', compact('availableCourses', 'enrolledCourseIds', 'studentYearString'));
    }

    // Request enrollment for a course
    public function requestEnrollment($courseId)
    {
        $student = Auth::user();

        // Check if already enrolled or pending
        $existing = Enrollment::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->first();

        if ($existing) {
            if ($existing->status == 'approved') {
                return redirect()->back()->with('error', 'You are already enrolled in this course.');
            }
            if ($existing->status == 'pending') {
                return redirect()->back()->with('error', 'You already requested enrollment for this course. Waiting for approval.');
            }
        }

        // Create enrollment request
        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $courseId,
            'status' => 'pending',
            'enrollment_date' => Carbon::now()->toDateString(),
        ]);

        return redirect()->back()->with('success', 'Enrollment request submitted successfully!');
    }

    // Show student's enrollment history
    public function myEnrollments()
    {
        $student = Auth::user();

        $enrollments = Enrollment::with('course')
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.enrollments.index', compact('enrollments'));
    }
}
