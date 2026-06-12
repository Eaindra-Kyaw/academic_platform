<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class StudentController extends Controller
{
    // Student Dashboard
    public function dashboard()
    {
        $student = Auth::user();

        // Get student's enrolled courses with attendance
        $enrollments = Enrollment::with('course')
            ->where('student_id', $student->id)
            ->where('status', 'approved')
            ->get();

        // Calculate attendance statistics
        $totalSessions = 0;
        $attendedSessions = 0;

        foreach ($enrollments as $enrollment) {
            // Get total sessions for this course
            $sessions = AttendanceSession::where('course_id', $enrollment->course_id)
                ->where('status', 'active')
                ->orWhere('status', 'closed')
                ->count();

            // Get attended sessions
            $attended = AttendanceRecord::where('student_id', $student->id)
                ->whereHas('session', function($q) use ($enrollment) {
                    $q->where('course_id', $enrollment->course_id);
                })
                ->count();

            $totalSessions += $sessions;
            $attendedSessions += $attended;
        }

        $attendancePercentage = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100) : 0;

        // Calculate Academic Health Score
        $healthScore = $this->calculateHealthScore($attendancePercentage);

        // Calculate Risk Score
        $riskScore = $this->calculateRiskScore($attendancePercentage);

        return view('student.dashboard', compact(
            'enrollments',
            'attendancePercentage',
            'healthScore',
            'riskScore'
        ));
    }

    // QR Attendance Scanner Page
    public function scan()
    {
        return view('student.scan');
    }

    // Check for active QR session
    public function checkSession(Request $request)
    {
        $student = Auth::user();

        // Find active session
        $activeSession = AttendanceSession::where('is_active', true)
            ->where('status', 'active')
            ->where('qr_expiry', '>', now())
            ->with('course')
            ->first();

        if ($activeSession) {
            // Check if student is enrolled in this course
            $isEnrolled = Enrollment::where('student_id', $student->id)
                ->where('course_id', $activeSession->course_id)
                ->where('status', 'approved')
                ->exists();

            // Check if attendance already recorded
            $alreadyRecorded = AttendanceRecord::where('student_id', $student->id)
                ->where('attendance_session_id', $activeSession->id)
                ->exists();

            $expiresIn = Carbon::now()->diffInSeconds($activeSession->qr_expiry);

            return response()->json([
                'hasSession' => true,
                'session' => [
                    'id' => $activeSession->id,
                    'course_name' => $activeSession->course->course_name,
                    'course_code' => $activeSession->course->course_code,
                    'lecturer_name' => $activeSession->course->lecturer_name,
                    'room' => $activeSession->room ?? $activeSession->course->room ?? 'Not specified',
                    'expires_in' => $expiresIn,
                    'session_token' => $activeSession->session_token,
                    'is_enrolled' => $isEnrolled,
                    'already_recorded' => $alreadyRecorded
                ]
            ]);
        }

        return response()->json([
            'hasSession' => false,
            'message' => 'No active QR session available'
        ]);
    }

    // Process QR Code Scan
    public function processQR(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $student = Auth::user();
        $qrData = $request->qr_code;

        // Try to find session by QR code or session token
        $session = AttendanceSession::where('qr_code', $qrData)
            ->orWhere('session_token', $qrData)
            ->where('is_active', true)
            ->where('status', 'active')
            ->where('qr_expiry', '>', now())
            ->with('course')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'QR code expired or invalid. Please request a new QR from your lecturer.'
            ]);
        }

        // Check if student is enrolled in this course
        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('course_id', $session->course_id)
            ->where('status', 'approved')
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course. Please contact your admin.'
            ]);
        }

        // Check if attendance already recorded for this session
        $alreadyRecorded = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->exists();

        if ($alreadyRecorded) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded for this session.'
            ]);
        }

        // Check if session is locked
        if ($session->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'This attendance session is locked. Please contact your lecturer.'
            ]);
        }

        // Record attendance
        $attendanceRecord = AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'scanned_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'present'
        ]);

        // Update session statistics
        $session->increment('present_count');

        // Check if student is late (if start_time passed by more than 10 minutes)
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

    // Manual Attendance with Code
    public function manualAttendance(Request $request)
    {
        $request->validate([
            'manual_code' => 'required|string'
        ]);

        $student = Auth::user();
        $manualCode = $request->manual_code;

        // Find session by session_token (acting as manual code)
        $session = AttendanceSession::where('session_token', $manualCode)
            ->where('is_active', true)
            ->where('status', 'active')
            ->where('qr_expiry', '>', now())
            ->with('course')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired manual code.'
            ]);
        }

        // Check enrollment
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

        // Check duplicate
        $alreadyRecorded = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->exists();

        if ($alreadyRecorded) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded.'
            ]);
        }

        // Check if session is locked
        if ($session->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'This attendance session is locked.'
            ]);
        }

        // Record attendance
        $attendanceRecord = AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'scanned_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'present',
            'is_manual' => true
        ]);

        $session->increment('present_count');

        return response()->json([
            'success' => true,
            'message' => 'Manual attendance recorded successfully!',
            'course' => $session->course->course_name
        ]);
    }

    // Student Attendance History
    public function attendance()
    {
        $student = Auth::user();

        $attendanceRecords = AttendanceRecord::with(['session.course'])
            ->where('student_id', $student->id)
            ->orderBy('scanned_at', 'desc')
            ->paginate(20);

        // Calculate attendance by course
        $enrollments = Enrollment::with('course')
            ->where('student_id', $student->id)
            ->where('status', 'approved')
            ->get();

        $courseAttendance = [];
        foreach ($enrollments as $enrollment) {
            $totalSessions = AttendanceSession::where('course_id', $enrollment->course_id)
                ->whereIn('status', ['active', 'closed'])
                ->count();

            $attended = AttendanceRecord::where('student_id', $student->id)
                ->whereHas('session', function($q) use ($enrollment) {
                    $q->where('course_id', $enrollment->course_id);
                })
                ->count();

            $percentage = $totalSessions > 0 ? round(($attended / $totalSessions) * 100) : 0;

            $courseAttendance[] = [
                'course' => $enrollment->course,
                'attended' => $attended,
                'total' => $totalSessions,
                'percentage' => $percentage,
                'status' => $percentage >= 75 ? 'Eligible' : ($percentage >= 60 ? 'Warning' : 'At Risk')
            ];
        }

        return view('student.attendance', compact('attendanceRecords', 'courseAttendance'));
    }

    // Student Timetable
    public function timetable()
    {
        $student = Auth::user();

        // Get enrolled courses with schedule
        $enrollments = Enrollment::with('course')
            ->where('student_id', $student->id)
            ->where('status', 'approved')
            ->get();

        $courses = $enrollments->pluck('course');

        $schedule = [];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        foreach ($days as $day) {
            $schedule[$day] = $courses->filter(function($course) use ($day) {
                return $course->schedule_day === $day;
            });
        }

        return view('student.timetable', compact('schedule', 'courses'));
    }

    // Student Progress
    public function progress()
    {
        $student = Auth::user();

        // Calculate overall statistics
        $totalCourses = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->count();

        // Get attendance trend over time
        $last30Days = AttendanceRecord::where('student_id', $student->id)
            ->where('scanned_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(scanned_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('student.progress', compact('totalCourses', 'last30Days'));
    }

    // Private helper methods
    private function calculateHealthScore($attendancePercentage)
    {
        // AHS calculation based on attendance
        if ($attendancePercentage >= 90) return 95;
        if ($attendancePercentage >= 85) return 88;
        if ($attendancePercentage >= 80) return 82;
        if ($attendancePercentage >= 75) return 78;
        if ($attendancePercentage >= 70) return 72;
        if ($attendancePercentage >= 65) return 65;
        if ($attendancePercentage >= 60) return 58;
        if ($attendancePercentage >= 50) return 45;
        return 35;
    }

    private function calculateRiskScore($attendancePercentage)
    {
        // Risk is higher when attendance is low
        if ($attendancePercentage >= 90) return 15;
        if ($attendancePercentage >= 80) return 25;
        if ($attendancePercentage >= 75) return 35;
        if ($attendancePercentage >= 70) return 45;
        if ($attendancePercentage >= 65) return 55;
        if ($attendancePercentage >= 60) return 65;
        if ($attendancePercentage >= 50) return 75;
        return 85;
    }
}
