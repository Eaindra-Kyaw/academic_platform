<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QRScanController extends Controller
{
    /**
     * Show QR scan page for students
     */
    public function index()
    {
        $student = Auth::user();

        // Get the most recent active session for the student's courses
        $activeSession = null;
        $courseIds = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->pluck('course_id')
            ->toArray();

        if (!empty($courseIds)) {
            $activeSession = AttendanceSession::whereIn('course_id', $courseIds)
                ->where('status', 'active')
                ->with('course')
                ->latest()
                ->first();
        }

        return view('student.scan', compact('activeSession'));
    }

    /**
     * Check if there's an active session (for AJAX)
     */
    public function checkSession(Request $request)
    {
        $student = Auth::user();

        // Get active session for student's enrolled courses
        $courseIds = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->pluck('course_id')
            ->toArray();

        $hasSession = false;
        $sessionData = null;

        if (!empty($courseIds)) {
            $session = AttendanceSession::whereIn('course_id', $courseIds)
                ->where('status', 'active')
                ->with('course')
                ->latest()
                ->first();

            if ($session) {
                $hasSession = true;
                $sessionData = [
                    'id' => $session->id,
                    'course_name' => $session->course->course_name ?? 'N/A',
                    'course_code' => $session->course->course_code ?? 'N/A',
                    'manual_code' => $session->manual_code,
                    'session_code' => $session->session_code ?? $session->manual_code,
                    'room' => $session->room ?? 'Not specified',
                    'qr_mode' => $session->qr_mode,
                ];
            }
        }

        return response()->json([
            'hasSession' => $hasSession,
            'session' => $sessionData
        ]);
    }

    /**
     * Process QR scan (Dynamic QR - requires active session)
     */
    public function processScan(Request $request)
    {
        $token = $request->query('token');
        $sessionId = $request->query('session');

        $session = AttendanceSession::where('id', $sessionId)
            ->where('session_token', $token)
            ->where('status', 'active')
            ->where('qr_mode', 'session')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired QR code. Please contact your lecturer.'
            ]);
        }

        $student = Auth::user();

        // Check if student is enrolled in this course
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

        // Check if already scanned for this session
        $existing = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already scanned for this session.',
                'record' => $existing
            ]);
        }

        $status = 'present';
        $scannedAt = Carbon::now();

        // Check if late (after 15 minutes from session start)
        if ($session->started_at && Carbon::now()->diffInMinutes($session->started_at) > 15) {
            $status = 'late';
        }

        $record = AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'scanned_at' => $scannedAt,
            'status' => $status,
            'is_manual' => false,
            'ip_address' => $request->ip(),
        ]);

        // Update session stats
        if ($status == 'present') {
            $session->increment('present_count');
        } else {
            $session->increment('late_count');
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully! Status: ' . ucfirst($status),
            'status' => $status,
            'course_name' => $session->course->course_name ?? 'Unknown'
        ]);
    }

    /**
     * Process Semester QR Scan (Static QR - NO expiry, works for entire semester)
     */
    public function semesterScan(Request $request)
    {
        $token = $request->query('token');
        $courseId = $request->query('course');

        if (!$token || !$courseId) {
            return view('student.scan-result', [
                'success' => false,
                'message' => 'Invalid QR code format.'
            ]);
        }

        $course = Course::where('id', $courseId)->first();
        if (!$course) {
            return view('student.scan-result', [
                'success' => false,
                'message' => 'Course not found.'
            ]);
        }

        // ✅ Verify the semester QR token matches
        if ($course->semester_qr_token !== $token) {
            return view('student.scan-result', [
                'success' => false,
                'message' => 'Invalid QR code. Please use the correct semester QR code.'
            ]);
        }

        $student = Auth::user();

        // Check if student is enrolled in this course
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            return view('student.scan-result', [
                'success' => false,
                'message' => 'You are not enrolled in this course. Please contact your lecturer.'
            ]);
        }

        // ✅ Find the active semester session for this course
        $session = AttendanceSession::where('course_id', $courseId)
            ->where('qr_mode', 'semester')
            ->where('status', 'active')
            ->first();

        // If no active session exists, create one
        if (!$session) {
            $session = AttendanceSession::create([
                'course_id' => $courseId,
                'lecturer_id' => $course->lecturer_id,
                'session_token' => $course->semester_qr_token,
                'manual_code' => AttendanceSession::generateSessionCode(),
                'session_code' => AttendanceSession::generateSessionCode(),
                'session_date' => Carbon::now()->toDateString(),
                'period_count' => 0,
                'conducted_periods' => 1,
                'duration' => 999999,
                'started_at' => Carbon::now(),
                'room' => $course->room ?? 'N/A',
                'status' => 'active',
                'expires_at' => Carbon::now()->addYears(10),
                'qr_expires_at' => Carbon::now()->addYears(10),
                'qr_mode' => 'semester',
                'present_count' => 0,
                'late_count' => 0,
                'total_students' => Enrollment::where('course_id', $courseId)
                    ->where('status', 'approved')
                    ->count(),
            ]);
        }

        // ✅ Check if student already scanned TODAY
        $today = Carbon::now()->toDateString();
        $existing = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->whereDate('scanned_at', $today)
            ->first();

        if ($existing) {
            return view('student.scan-result', [
                'success' => false,
                'message' => 'You have already scanned for today (' . Carbon::now()->format('M d, Y') . ').',
                'record' => $existing,
                'already_scanned' => true
            ]);
        }

        // Record attendance - always "present" for semester QR
        $record = AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'scanned_at' => Carbon::now(),
            'status' => 'present',
            'is_manual' => false,
            'ip_address' => $request->ip(),
            'notes' => 'Semester QR scan - ' . Carbon::now()->format('Y-m-d'),
        ]);

        $session->increment('present_count');

        return view('student.scan-result', [
            'success' => true,
            'message' => '✅ Attendance recorded for ' . Carbon::now()->format('M d, Y') . '!',
            'record' => $record,
            'status' => 'present',
            'course_name' => $course->course_name ?? 'Unknown',
            'is_semester_qr' => true,
            'session' => $session,
            'scanned_at' => Carbon::now()->format('h:i A'),
        ]);
    }

    /**
     * Manual attendance entry (for students who can't scan)
     */
    public function manualAttendance(Request $request)
    {
        $request->validate([
            'manual_code' => 'required|string|min:6|max:6',
        ]);

        $student = Auth::user();

        // Find the session with the manual code (for student's enrolled courses)
        $courseIds = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->pluck('course_id')
            ->toArray();

        $session = AttendanceSession::where('manual_code', strtoupper($request->manual_code))
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid manual code or no active session. Please check the code and try again.'
            ]);
        }

        // Check if student is enrolled in this course
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

        // Check if already scanned for this session
        $existing = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already recorded attendance for this session.'
            ]);
        }

        $record = AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'scanned_at' => Carbon::now(),
            'status' => 'present',
            'is_manual' => true,
            'ip_address' => $request->ip(),
            'notes' => 'Manual code entry: ' . $request->manual_code,
        ]);

        $session->increment('present_count');

        return response()->json([
            'success' => true,
            'message' => '✅ Attendance recorded successfully!'
        ]);
    }

    /**
     * Get active session status for the student (AJAX)
     */
    public function getActiveSessionStatus()
    {
        $student = Auth::user();

        $courseIds = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->pluck('course_id')
            ->toArray();

        $activeSession = null;
        if (!empty($courseIds)) {
            $activeSession = AttendanceSession::whereIn('course_id', $courseIds)
                ->where('status', 'active')
                ->with('course')
                ->latest()
                ->first();
        }

        if (!$activeSession) {
            return response()->json([
                'hasSession' => false,
                'session' => null
            ]);
        }

        return response()->json([
            'hasSession' => true,
            'session' => [
                'id' => $activeSession->id,
                'course_name' => $activeSession->course->course_name ?? 'N/A',
                'course_code' => $activeSession->course->course_code ?? 'N/A',
                'manual_code' => $activeSession->manual_code,
                'session_code' => $activeSession->session_code ?? $activeSession->manual_code,
                'room' => $activeSession->room ?? 'N/A',
                'qr_mode' => $activeSession->qr_mode,
            ]
        ]);
    }

    /**
     * Static scan page for semester QR (redirects to semester scan)
     */
    public function staticScan(Request $request)
    {
        $token = $request->query('token');
        $courseId = $request->query('course');

        if ($token && $courseId) {
            return redirect()->route('student.scan.semester', ['token' => $token, 'course' => $courseId]);
        }

        return redirect()->route('student.scan')->with('error', 'Invalid QR code.');
    }
}
