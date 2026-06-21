<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QRScanController extends Controller
{
    public function index()
    {
        return view('student.scan');
    }

    public function semesterScan(Request $request)
    {
        Log::info('=== SEMESTER SCAN CALLED ===');
        Log::info('Request: ' . $request->fullUrl());

        $student = Auth::user();

        if (!$student) {
            return view('student.scan-result', [
                'success' => false,
                'message' => 'Please login as a student first.'
            ]);
        }

        $token = $request->query('token');
        $courseId = $request->query('course');

        if (!$token || !$courseId) {
            return view('student.scan-result', [
                'success' => false,
                'message' => 'Invalid QR code format.'
            ]);
        }

        $course = Course::where('id', $courseId)
            ->where('semester_qr_token', $token)
            ->first();

        if (!$course) {
            return view('student.scan-result', [
                'success' => false,
                'message' => 'Invalid QR code.'
            ]);
        }

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            return view('student.scan-result', [
                'success' => false,
                'message' => 'You are not enrolled in ' . $course->course_name
            ]);
        }

        $session = AttendanceSession::where('course_id', $courseId)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return view('student.scan-result', [
                'success' => false,
                'message' => 'No active session for this course.'
            ]);
        }

        $existingRecord = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->first();

        if ($existingRecord) {
            return view('student.scan-result', [
                'success' => false,
                'message' => 'Attendance already recorded. Status: ' . $existingRecord->status
            ]);
        }

        $now = Carbon::now();
        $sessionStartTime = Carbon::parse($session->start_time ?? $session->started_at ?? $now);
        $minutesLate = $now->diffInMinutes($sessionStartTime);

        if ($minutesLate <= 10) {
            $status = 'present';
            $userMessage = '✅ Attendance marked as PRESENT!';
        } elseif ($minutesLate <= 30) {
            $status = 'late';
            $userMessage = '⏰ Attendance marked as LATE. You arrived ' . $minutesLate . ' minutes late.';
        } else {
            $status = 'absent';
            $userMessage = '❌ Attendance marked as ABSENT. You arrived more than 30 minutes late.';
        }

        AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'scanned_at' => $now,
            'status' => $status,
            'is_manual' => false,
            'ip_address' => $request->ip(),
        ]);

        if ($status == 'present') {
            $session->increment('present_count');
        } elseif ($status == 'late') {
            $session->increment('late_count');
        } else {
            $session->increment('absent_count');
        }

        Log::info('✅ Semester attendance recorded: ' . $status);

        return view('student.scan-result', [
            'success' => true,
            'message' => $userMessage,
            'course_name' => $course->course_name,
            'status' => $status,
            'scanned_at' => $now->format('h:i A')
        ]);
    }

    public function processScan(Request $request)
    {
        Log::info('=== PROCESS SCAN CALLED ===');
        Log::info('URL: ' . $request->fullUrl());
        Log::info('Token: ' . $request->query('token'));
        Log::info('Session ID: ' . $request->query('session'));

        $student = Auth::user();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Please login as a student first.'
            ]);
        }

        $token = $request->query('token');
        $sessionId = $request->query('session');

        if (!$token || !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code format.'
            ]);
        }

        $session = AttendanceSession::where('id', $sessionId)
            ->where('session_token', $token)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            Log::warning("Session not found - ID: $sessionId, Token: $token");
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired QR code.'
            ]);
        }

        Log::info('Session found - Course ID: ' . $session->course_id);

        if ($session->qr_expires_at && Carbon::now()->gt($session->qr_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'QR code has expired.'
            ]);
        }

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $session->course_id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            Log::warning("Student not enrolled - Student: {$student->id}, Course: {$session->course_id}");
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course.'
            ]);
        }

        $existingRecord = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->first();

        if ($existingRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded. Status: ' . $existingRecord->status
            ]);
        }

        $sessionStartTime = $session->start_time ?? $session->started_at;
        $isLate = false;
        $minutesLate = 0;

        if ($sessionStartTime) {
            $minutesLate = Carbon::now()->diffInMinutes($sessionStartTime);
            $isLate = $minutesLate > 10;
        }

        $status = $isLate ? 'late' : 'present';

        AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'scanned_at' => Carbon::now(),
            'status' => $status,
            'is_manual' => false,
            'ip_address' => $request->ip(),
        ]);

        if ($status == 'present') {
            $session->increment('present_count');
        } else {
            $session->increment('late_count');
        }

        $message = $isLate ?
            '⏰ Attendance marked as LATE (arrived ' . round($minutesLate) . ' minutes late)' :
            '✅ Attendance marked as PRESENT!';

        Log::info('✅ Attendance recorded - Status: ' . $status);

        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $status
        ]);
    }

    public function checkSession(Request $request)
    {
        $student = Auth::user();

        if (!$student) {
            return response()->json(['hasSession' => false]);
        }

        $enrolledCourseIds = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->pluck('course_id');

        $activeSession = AttendanceSession::whereIn('course_id', $enrolledCourseIds)
            ->where('status', 'active')
            ->where('qr_expires_at', '>', Carbon::now())
            ->with('course', 'lecturer')
            ->first();

        if ($activeSession) {
            $expiresIn = Carbon::now()->diffInSeconds($activeSession->qr_expires_at);
            return response()->json([
                'hasSession' => true,
                'session' => [
                    'id' => $activeSession->id,
                    'course_name' => $activeSession->course->course_name ?? 'Unknown',
                    'lecturer_name' => $activeSession->lecturer->name ?? 'Unknown',
                    'room' => $activeSession->room,
                    'expires_in' => $expiresIn,
                    'session_code' => $activeSession->session_code
                ]
            ]);
        }

        return response()->json(['hasSession' => false]);
    }

    public function manualAttendance(Request $request)
    {
        $request->validate(['manual_code' => 'required|string|size:6']);

        $student = Auth::user();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Please login as a student first.'
            ]);
        }

        $manualCode = strtoupper($request->manual_code);

        $session = AttendanceSession::where('session_code', $manualCode)
            ->where('status', 'active')
            ->where('qr_expires_at', '>', Carbon::now())
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired manual code.'
            ]);
        }

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $session->course_id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course.'
            ]);
        }

        $existingRecord = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->first();

        if ($existingRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded.'
            ]);
        }

        $sessionStartTime = $session->start_time ?? $session->started_at;
        $isLate = false;
        $minutesLate = 0;

        if ($sessionStartTime) {
            $minutesLate = Carbon::now()->diffInMinutes($sessionStartTime);
            $isLate = $minutesLate > 10;
        }

        $status = $isLate ? 'late' : 'present';

        AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'scanned_at' => Carbon::now(),
            'status' => $status,
            'is_manual' => true,
            'ip_address' => $request->ip(),
        ]);

        if ($status == 'present') {
            $session->increment('present_count');
        } else {
            $session->increment('late_count');
        }

        return response()->json([
            'success' => true,
            'message' => 'Manual attendance recorded as ' . strtoupper($status) . '!'
        ]);
    }
}
