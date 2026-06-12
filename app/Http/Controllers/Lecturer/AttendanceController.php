<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function takeAttendance()
    {
        $lecturer = Auth::user();
        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->get();

        $activeSession = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('is_active', true)
            ->where('status', 'active')
            ->first();

        $expiresIn = $activeSession ? Carbon::now()->diffInSeconds($activeSession->qr_expiry) : 0;

        return view('lecturer.attendance.take', compact('courses', 'activeSession', 'expiresIn'));
    }

    public function generateQR(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $lecturer = Auth::user();
        $course = Course::find($request->course_id);

        // End any active sessions
        AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('is_active', true)
            ->update(['is_active' => false, 'status' => 'closed']);

        $sessionToken = strtoupper(substr(md5(uniqid()), 0, 10));
        $qrCode = route('student.scan') . '?token=' . $sessionToken;

        $session = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'session_date' => Carbon::now()->toDateString(),
            'start_time' => Carbon::now()->toTimeString(),
            'end_time' => Carbon::now()->addMinutes(30)->toTimeString(),
            'qr_code' => $qrCode,
            'qr_expiry' => Carbon::now()->addMinutes(5),
            'session_token' => $sessionToken,
            'is_active' => true,
            'status' => 'active',
            'room' => $course->room,
            'total_students' => Enrollment::where('course_id', $course->id)->where('status', 'approved')->count()
        ]);

        return redirect()->back()->with('success', 'QR Code generated! Expires in 5 minutes.');
    }

    public function endSession($id)
    {
        $session = AttendanceSession::findOrFail($id);
        $session->is_active = false;
        $session->status = 'closed';
        $session->end_time = Carbon::now()->toTimeString();
        $session->save();

        return redirect()->back()->with('success', 'Attendance session ended.');
    }

    public function manualAttendance(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student_id' => 'required|exists:users,id',
            'status' => 'required|in:present,absent,late'
        ]);

        $session = AttendanceSession::where('course_id', $request->course_id)
            ->where('lecturer_id', Auth::id())
            ->where('is_active', true)
            ->first();

        if (!$session) {
            return redirect()->back()->with('error', 'No active session for this course.');
        }

        $existing = AttendanceRecord::where('student_id', $request->student_id)
            ->where('attendance_session_id', $session->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Attendance already recorded for this student.');
        }

        AttendanceRecord::create([
            'student_id' => $request->student_id,
            'attendance_session_id' => $session->id,
            'scanned_at' => Carbon::now(),
            'status' => $request->status
        ]);

        if ($request->status == 'present') {
            $session->increment('present_count');
        } elseif ($request->status == 'late') {
            $session->increment('late_count');
        } else {
            $session->increment('absent_count');
        }

        return redirect()->back()->with('success', 'Manual attendance recorded.');
    }

    public function history()
    {
        $sessions = AttendanceSession::with('course')
            ->where('lecturer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('lecturer.attendance.history', compact('sessions'));
    }
}
