<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class QRScanController extends Controller
{
    public function index()
    {
        return view('student.scan');
    }

    public function checkSession(Request $request)
    {
        $student = Auth::user();

        Log::info('Check session for student: ' . $student->id . ' - ' . $student->name);

        // Get active sessions for courses the student is enrolled in
        $enrolledCourseIds = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->pluck('course_id');

        Log::info('Enrolled course IDs: ' . $enrolledCourseIds->toJson());

        $activeSession = AttendanceSession::whereIn('course_id', $enrolledCourseIds)
            ->where('status', 'active')
            ->where('qr_expires_at', '>', Carbon::now())
            ->with('course', 'lecturer')
            ->first();

        if ($activeSession) {
            $expiresIn = Carbon::now()->diffInSeconds($activeSession->qr_expires_at);

            Log::info('Active session found: ' . $activeSession->id . ' - ' . $activeSession->course->course_name);

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

        Log::info('No active session found for student');

        return response()->json(['hasSession' => false]);
    }

    public function processScan(Request $request)
    {
        $student = Auth::user();
        $token = $request->query('token');
        $sessionId = $request->query('session');

        Log::info('Process scan - Student: ' . $student->id . ', Session: ' . $sessionId);

        // Find the session
        $session = AttendanceSession::where('id', $sessionId)
            ->where('session_token', $token)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            Log::warning('Session not found or invalid');
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired QR code.'
            ]);
        }

        // Check if QR code is expired
        if ($session->qr_expires_at && Carbon::now()->gt($session->qr_expires_at)) {
            Log::warning('QR code expired for session: ' . $sessionId);
            return response()->json([
                'success' => false,
                'message' => 'QR code has expired. Please ask your lecturer to refresh it.'
            ]);
        }

        // Check if student is enrolled in this course
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $session->course_id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            Log::warning('Student not enrolled in course: ' . $session->course_id);
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course.'
            ]);
        }

        // Check if attendance already recorded
        $existingRecord = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->first();

        if ($existingRecord) {
            Log::warning('Attendance already recorded for student: ' . $student->id);
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded for this session. You were marked as ' . $existingRecord->status . '.'
            ]);
        }

        // Determine status (present or late)
        $sessionStartTime = $session->start_time ?? $session->started_at;
        $isLate = false;
        $minutesLate = 0;

        if ($sessionStartTime) {
            $minutesLate = Carbon::now()->diffInMinutes($sessionStartTime);
            $isLate = $minutesLate > 10;
        }

        $status = $isLate ? 'late' : 'present';

        // Create attendance record
        AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'scanned_at' => Carbon::now(),
            'status' => $status,
            'is_manual' => false,
            'ip_address' => $request->ip(),
        ]);

        // Update session counts
        if ($status == 'present') {
            $session->increment('present_count');
        } else {
            $session->increment('late_count');
        }

        $message = $isLate ?
            'Attendance marked as LATE (you arrived ' . round($minutesLate) . ' minutes after session started)' :
            'Attendance marked as PRESENT successfully!';

        Log::info('Attendance recorded successfully: ' . $status);

        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $status
        ]);
    }

    public function manualAttendance(Request $request)
    {
        $request->validate([
            'manual_code' => 'required|string|size:6'
        ]);

        $student = Auth::user();
        $manualCode = strtoupper($request->manual_code);

        Log::info('Manual attendance - Student: ' . $student->id . ', Code: ' . $manualCode);

        // Find session by manual code
        $session = AttendanceSession::where('session_code', $manualCode)
            ->where('status', 'active')
            ->where('qr_expires_at', '>', Carbon::now())
            ->first();

        if (!$session) {
            Log::warning('Invalid manual code: ' . $manualCode);
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired manual code. Please check with your lecturer.'
            ]);
        }

        // Check if student is enrolled
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $session->course_id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            Log::warning('Student not enrolled in course: ' . $session->course_id);
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course.'
            ]);
        }

        // Check for existing record
        $existingRecord = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->first();

        if ($existingRecord) {
            Log::warning('Attendance already recorded');
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded for this session.'
            ]);
        }

        // Determine status
        $sessionStartTime = $session->start_time ?? $session->started_at;
        $isLate = false;
        $minutesLate = 0;

        if ($sessionStartTime) {
            $minutesLate = Carbon::now()->diffInMinutes($sessionStartTime);
            $isLate = $minutesLate > 10;
        }

        $status = $isLate ? 'late' : 'present';

        // Create record
        AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'scanned_at' => Carbon::now(),
            'status' => $status,
            'is_manual' => true,
            'ip_address' => $request->ip(),
        ]);

        // Update counts
        if ($status == 'present') {
            $session->increment('present_count');
        } else {
            $session->increment('late_count');
        }

        $message = $isLate ?
            'Manual attendance marked as LATE (arrived ' . round($minutesLate) . ' minutes late)' :
            'Manual attendance recorded successfully as PRESENT!';

        Log::info('Manual attendance recorded: ' . $status);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function myAttendance(Request $request)
    {
        $student = Auth::user();

        $attendanceRecords = AttendanceRecord::where('student_id', $student->id)
            ->with('session.course')
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [];
        foreach ($attendanceRecords as $record) {
            $courseName = $record->session->course->course_name ?? 'Unknown';
            if (!isset($summary[$courseName])) {
                $summary[$courseName] = ['present' => 0, 'late' => 0, 'absent' => 0];
            }
            $summary[$courseName][$record->status]++;
        }

        return response()->json([
            'success' => true,
            'records' => $attendanceRecords,
            'summary' => $summary
        ]);
    }
}
