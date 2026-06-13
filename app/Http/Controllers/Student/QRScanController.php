<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class QRScanController extends Controller
{
    public function index()
    {
        return view('student.scan');
    }

    public function checkSession(Request $request)
    {
        $student = Auth::user();

        $activeSession = AttendanceSession::where('status', 'active')
            ->where('qr_expires_at', '>', now())
            ->with('course')
            ->first();

        if ($activeSession) {
            $isEnrolled = Enrollment::where('student_id', $student->id)
                ->where('course_id', $activeSession->course_id)
                ->where('status', 'approved')
                ->exists();

            $alreadyRecorded = AttendanceRecord::where('student_id', $student->id)
                ->where('attendance_session_id', $activeSession->id)
                ->exists();

            return response()->json([
                'hasSession' => true,
                'session' => [
                    'id' => $activeSession->id,
                    'course_name' => $activeSession->course->course_name,
                    'course_code' => $activeSession->course->course_code,
                    'lecturer_name' => $activeSession->course->lecturer_name,
                    'room' => $activeSession->room ?? 'Not specified',
                    'expires_in' => now()->diffInSeconds($activeSession->qr_expires_at),
                    'is_enrolled' => $isEnrolled,
                    'already_recorded' => $alreadyRecorded,
                ]
            ]);
        }

        return response()->json(['hasSession' => false]);
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'session' => 'required|integer',
        ]);

        $student = Auth::user();
        $token = $request->token;
        $sessionId = $request->session;

        $session = AttendanceSession::where('id', $sessionId)
            ->where('session_token', $token)
            ->where('status', 'active')
            ->where('qr_expires_at', '>', now())
            ->with('course')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'QR code expired or invalid. Please request a new QR from your lecturer.'
            ]);
        }

        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('course_id', $session->course_id)
            ->where('status', 'approved')
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course.'
            ]);
        }

        $alreadyRecorded = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->exists();

        if ($alreadyRecorded) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded for this session.'
            ]);
        }

        $attendanceRecord = AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'scanned_at' => Carbon::now(),
            'status' => 'present',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $session->increment('present_count');

        $startTime = Carbon::parse($session->session_date . ' ' . $session->start_time);
        $isLate = now()->gt($startTime->addMinutes(10));

        if ($isLate) {
            $session->increment('late_count');
            $attendanceRecord->update(['status' => 'late']);
        }

        return response()->json([
            'success' => true,
            'message' => $isLate ? 'Attendance recorded as LATE!' : 'Attendance recorded successfully!',
            'course' => $session->course->course_name,
            'time' => now()->format('g:i A'),
            'is_late' => $isLate
        ]);
    }

    public function manualAttendance(Request $request)
    {
        $request->validate([
            'manual_code' => 'required|string|size:6',
        ]);

        $student = Auth::user();
        $manualCode = $request->manual_code;

        $session = AttendanceSession::where('session_code', $manualCode)
            ->where('status', 'active')
            ->where('qr_expires_at', '>', now())
            ->with('course')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired manual code.'
            ]);
        }

        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('course_id', $session->course_id)
            ->where('status', 'approved')
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course.'
            ]);
        }

        $alreadyRecorded = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->exists();

        if ($alreadyRecorded) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded.'
            ]);
        }

        AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'scanned_at' => Carbon::now(),
            'status' => 'present',
            'is_manual' => true,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $session->increment('present_count');

        return response()->json([
            'success' => true,
            'message' => 'Manual attendance recorded successfully!',
            'course' => $session->course->course_name
        ]);
    }
}
