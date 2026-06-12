<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
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

        return view('admin.enrollments.index', compact('pendingEnrollments', 'approvedEnrollments', 'rejectedEnrollments'));
    }

    // Approve enrollment
    public function approve($id)
{
    $enrollment = Enrollment::findOrFail($id);
    $enrollment->status = 'approved';
    $enrollment->approved_at = Carbon::now();
    // Remove the line that sets rejection_reason to null
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
}
