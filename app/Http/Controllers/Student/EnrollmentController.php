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
    public function availableCourses(Request $request)
    {
        $student = Auth::user();

        // Map year number to string
        $yearMapping = [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
        ];

        $studentYearString = $yearMapping[$student->current_year] ?? 'First Year';

        // Build query - filter by department AND year
        $query = Course::where('is_active', true)
            ->where('year', $studentYearString);

        // IMPORTANT: Filter by student's department
        if ($student->department_id) {
            $query->where('department_id', $student->department_id);
        }

        // Apply search filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%")
                  ->orWhere('lecturer_name', 'like', "%{$search}%");
            });
        }

        // Department filter (if user selects different department)
        if ($request->filled('department') && $request->department != '') {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $availableCourses = $query->orderBy('course_code')->paginate(12);

        // Pass both variables to the view
        return view('student.courses.available', compact('availableCourses', 'studentYearString'));
    }

    public function requestEnrollment($courseId)
    {
        $student = Auth::user();
        $courseId = (int) $courseId;

        // Check if enrollment already exists
        $existing = Enrollment::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->first();

        if ($existing) {
            if ($existing->status == 'approved') {
                return redirect()->back()->with('error', '❌ You are already enrolled in this course.');
            }
            if ($existing->status == 'pending') {
                return redirect()->back()->with('error', '⏳ You already requested enrollment for this course. Waiting for approval.');
            }
            if ($existing->status == 'rejected') {
                // Update rejected to pending
                $existing->status = 'pending';
                $existing->rejection_reason = null;
                $existing->rejected_at = null;
                $existing->enrollment_date = Carbon::now()->toDateString();
                $existing->save();

                return redirect()->back()->with('success', '✅ Your request has been resubmitted for approval!');
            }
        }

        // Create new enrollment
        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $courseId,
            'status' => 'pending',
            'enrollment_date' => Carbon::now()->toDateString(),
        ]);

        return redirect()->back()->with('success', '✅ Enrollment request submitted successfully!');
    }

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
