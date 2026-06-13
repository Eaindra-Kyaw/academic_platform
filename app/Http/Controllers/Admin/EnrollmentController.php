<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EnrollmentController extends Controller
{
    // Show all enrollment requests
    public function index()
    {
        $pendingEnrollments = Enrollment::with(['student', 'course'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        $approvedEnrollments = Enrollment::with(['student', 'course'])
            ->where('status', 'approved')
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();

        $rejectedEnrollments = Enrollment::with(['student', 'course'])
            ->where('status', 'rejected')
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        // For batch enrollment modal
        $allCourses = Course::where('is_active', true)->orderBy('course_code')->get();
        $allStudents = User::where('role_id', 3)->where('is_active', true)->orderBy('name')->get();

        $allDepartments = \App\Models\Department::orderBy('name')->get();

    return view('admin.enrollments.index', compact(
        'pendingEnrollments',
        'approvedEnrollments',
        'rejectedEnrollments',
        'allCourses',
        'allStudents',
        'allDepartments'  // Add this
    ));
    }

    // Approve enrollment
    public function approve($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->status = 'approved';
        $enrollment->approved_at = Carbon::now();
        $enrollment->save();

        return redirect()->back()->with('success', "Enrollment approved for {$enrollment->student->name} in {$enrollment->course->course_name}");
    }

    // Reject enrollment with reason
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ]);

        $enrollment = Enrollment::findOrFail($id);
        $enrollment->status = 'rejected';
        $enrollment->rejected_at = Carbon::now();
        $enrollment->rejection_reason = $request->rejection_reason;
        $enrollment->save();

        return redirect()->back()->with('success', "Enrollment rejected for {$enrollment->student->name}");
    }

    // Batch enroll students
    public function batchEnroll(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id',
        'student_ids' => 'required|array|min:1',
        'student_ids.*' => 'exists:users,id',
        'enrollment_date' => 'required|date',
    ]);

    $courseId = $request->course_id;
    $enrollmentDate = $request->enrollment_date;
    $successCount = 0;
    $failCount = 0;
    $failedStudents = [];

    foreach ($request->student_ids as $studentId) {
        // Check if enrollment already exists
        $exists = Enrollment::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->exists();

        if (!$exists) {
            Enrollment::create([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'status' => 'approved',
                'enrollment_date' => $enrollmentDate,
                'approved_at' => now(),
            ]);
            $successCount++;
        } else {
            $failCount++;
            $student = User::find($studentId);
            $failedStudents[] = $student->name;
        }
    }

    $message = "✅ $successCount students enrolled successfully.";
    if ($failCount > 0) {
        $message .= " ⚠️ $failCount students were already enrolled and skipped: " . implode(', ', $failedStudents);
    }

    return redirect()->route('admin.enrollments.index')->with('success', $message);
}
}
