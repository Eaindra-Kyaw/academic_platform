<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Take Attendance - Shows QR scanner and current active session
     */
    public function takeAttendance()
    {
        $lecturer = Auth::user();

        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->where('is_active', true)
            ->get();

        $activeSession = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('status', 'active')
            ->with(['course', 'records.student'])
            ->first();

        // If active session exists but qr_expires_at is NULL, set it
        if ($activeSession && is_null($activeSession->qr_expires_at)) {
            $activeSession->qr_expires_at = Carbon::now()->addMinutes($activeSession->duration ?? 30);
            $activeSession->save();
        }

        $courseIds = $courses->pluck('id');
        $students = User::whereHas('enrollments', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds)->where('status', 'approved');
        })->get();

        $expiresIn = $activeSession && $activeSession->qr_expires_at
            ? Carbon::now()->diffInSeconds($activeSession->qr_expires_at)
            : 0;

        return view('lecturer.attendance.take-attendance', compact(
            'courses',
            'activeSession',
            'students',
            'expiresIn'
        ));
    }

    /**
     * Session History - Shows all past and active sessions with statistics
     */
    public function sessions()
    {
        $lecturer = Auth::user();

        $sessions = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->with(['course', 'records'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalSessions = AttendanceSession::where('lecturer_id', $lecturer->id)->count();
        $activeSessions = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('status', 'active')
            ->count();

        $totalStudents = 0;
        $totalPresent = 0;

        foreach ($sessions as $session) {
            $present = $session->records->where('status', 'present')->count();
            $totalPresent += $present;
            $totalStudents += $session->records->count();
        }

        $averageAttendance = $totalStudents > 0
            ? round(($totalPresent / $totalStudents) * 100)
            : 0;

        return view('lecturer.attendance.history', compact(
            'sessions',
            'totalSessions',
            'activeSessions',
            'averageAttendance',
            'totalStudents'
        ));
    }

    /**
     * Create a new attendance session - FULLY FIXED
     */
    public function createSession(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'qr_mode' => 'required|in:session,semester',
            'duration' => 'required_if:qr_mode,session|integer|min:5|max:120',
            'room' => 'nullable|string|max:50',
        ]);

        $lecturer = Auth::user();
        $course = Course::findOrFail($request->course_id);

        // End any existing active session for this course
        $existingSession = AttendanceSession::where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if ($existingSession) {
            $existingSession->status = 'ended';
            $existingSession->ended_at = Carbon::now();
            $existingSession->save();

            AuditLog::log(
                Auth::id(),
                'end_session',
                [
                    'course_id' => $course->id,
                    'course_name' => $course->course_name,
                    'session_id' => $existingSession->id,
                    'reason' => 'New session started',
                ],
                $existingSession,
                'success'
            );
        }

        $sessionToken = AttendanceSession::generateSessionToken();
        $sessionCode = AttendanceSession::generateSessionCode();

        if ($request->qr_mode == 'semester') {
            $duration = 480;
            $expiresAt = Carbon::now()->addHours(8);
            $qrExpiresAt = Carbon::now()->addHours(8);

            if (!$course->semester_qr_token) {
                $course->semester_qr_token = $sessionToken;
                $course->save();
            }
        } else {
            $duration = (int) $request->duration;
            $expiresAt = Carbon::now()->addMinutes($duration);
            $qrExpiresAt = Carbon::now()->addMinutes($duration);
        }

        $session = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'session_token' => $sessionToken,
            'manual_code' => $sessionCode,
            'session_code' => $sessionCode,
            'duration' => $duration,
            'started_at' => Carbon::now(),
            'room' => $request->room ?? $course->room,
            'status' => 'active',
            'expires_at' => $expiresAt,
            'qr_expires_at' => $qrExpiresAt,
            'qr_mode' => $request->qr_mode,
            'present_count' => 0,
            'late_count' => 0,
            'total_students' => Enrollment::where('course_id', $course->id)
                ->where('status', 'approved')
                ->count(),
        ]);

        AuditLog::log(
            Auth::id(),
            'create_session',
            [
                'course_id' => $course->id,
                'course_name' => $course->course_name,
                'qr_mode' => $request->qr_mode,
                'session_id' => $session->id,
                'manual_code' => $session->manual_code,
                'duration' => $duration,
            ],
            $session,
            'success'
        );

        $message = $request->qr_mode == 'semester'
            ? 'Semester QR activated! Use the same QR code for all sessions.'
            : 'Dynamic QR session created! Expires in ' . $duration . ' minutes.';

        return redirect()->route('lecturer.attendance.take')
            ->with('success', $message)
            ->with('new_session', $session);
    }

    /**
     * End an active attendance session
     */
    public function endSession($id)
    {
        $session = AttendanceSession::where('lecturer_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $session->status = 'ended';
        $session->ended_at = Carbon::now();
        $session->save();

        $present = AttendanceRecord::where('attendance_session_id', $session->id)
            ->where('status', 'present')
            ->count();
        $late = AttendanceRecord::where('attendance_session_id', $session->id)
            ->where('status', 'late')
            ->count();
        $absent = AttendanceRecord::where('attendance_session_id', $session->id)
            ->where('status', 'absent')
            ->count();

        AuditLog::log(
            Auth::id(),
            'end_session',
            [
                'course_id' => $session->course_id,
                'course_name' => $session->course->course_name ?? 'Unknown',
                'session_id' => $session->id,
                'manual_code' => $session->manual_code,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
            ],
            $session,
            'success'
        );

        return redirect()->back()->with('success', 'Attendance session ended successfully.');
    }

    /**
     * Refresh QR code for active session
     */
    public function refreshSession($id)
    {
        $session = AttendanceSession::where('lecturer_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $newExpiry = Carbon::now()->addMinutes(5);
        $session->qr_expires_at = $newExpiry;
        $session->expires_at = $newExpiry;
        $session->save();

        AuditLog::log(
            Auth::id(),
            'refresh_qr',
            [
                'session_id' => $session->id,
                'manual_code' => $session->manual_code,
                'new_expiry' => $newExpiry->toDateTimeString(),
            ],
            $session,
            'success'
        );

        return redirect()->back()->with('success', 'QR code refreshed! Expires in 5 minutes.');
    }

    /**
     * Regenerate semester QR code
     */
    public function regenerateSemesterQr($courseId)
    {
        $course = Course::where('lecturer_id', Auth::id())->findOrFail($courseId);
        $course->semester_qr_token = AttendanceSession::generateSessionToken();
        $course->save();

        $activeSession = AttendanceSession::where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if ($activeSession) {
            $activeSession->session_token = $course->semester_qr_token;
            $activeSession->manual_code = AttendanceSession::generateSessionCode();
            $activeSession->save();
        }

        AuditLog::log(
            Auth::id(),
            'regenerate_semester_qr',
            [
                'course_id' => $course->id,
                'course_name' => $course->course_name,
            ],
            $course,
            'success'
        );

        return redirect()->back()->with('success', 'Semester QR code regenerated successfully!');
    }

    /**
     * Mark attendance manually
     */
    public function manualAttendance(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student_id' => 'required|exists:users,id',
            'status' => 'required|in:present,absent,late',
            'notes' => 'nullable|string|max:500',
        ]);

        $session = AttendanceSession::where('course_id', $request->course_id)
            ->where('lecturer_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return redirect()->back()->with('error', 'No active session for this course.');
        }

        $student = User::find($request->student_id);
        $existing = AttendanceRecord::where('student_id', $request->student_id)
            ->where('attendance_session_id', $session->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Attendance already recorded for this student.');
        }

        $record = AttendanceRecord::create([
            'student_id' => $request->student_id,
            'attendance_session_id' => $session->id,
            'scanned_at' => Carbon::now(),
            'status' => $request->status,
            'is_manual' => true,
            'ip_address' => $request->ip(),
            'notes' => $request->notes,
        ]);

        // Update session counts
        if ($request->status == 'present') {
            $session->increment('present_count');
        } elseif ($request->status == 'late') {
            $session->increment('late_count');
        }

        AuditLog::log(
            Auth::id(),
            'manual_attendance',
            [
                'course_id' => $session->course_id,
                'course_name' => $session->course->course_name ?? 'Unknown',
                'student_id' => $request->student_id,
                'student_name' => $student->name ?? 'Unknown',
                'status' => $request->status,
                'session_id' => $session->id,
                'manual_code' => $session->manual_code,
            ],
            $record,
            'success'
        );

        return redirect()->back()->with('success', 'Manual attendance recorded successfully.');
    }

    /**
     * History alias - redirects to sessions
     */
    public function history()
    {
        return $this->sessions();
    }

    // ============================================
    // AJAX METHODS
    // ============================================

    public function generateQr(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'duration' => 'required|integer|min:5|max:120',
            'room' => 'nullable|string|max:50',
        ]);

        $lecturer = Auth::user();
        $course = Course::findOrFail($request->course_id);
        $duration = (int) $request->duration;
        $expiresAt = Carbon::now()->addMinutes($duration);
        $qrExpiresAt = Carbon::now()->addMinutes($duration);

        $sessionToken = AttendanceSession::generateSessionToken();
        $sessionCode = AttendanceSession::generateSessionCode();

        $session = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'session_token' => $sessionToken,
            'manual_code' => $sessionCode,
            'session_code' => $sessionCode,
            'duration' => $duration,
            'started_at' => Carbon::now(),
            'room' => $request->room ?? $course->room,
            'status' => 'active',
            'expires_at' => $expiresAt,
            'qr_expires_at' => $qrExpiresAt,
            'qr_mode' => 'session',
            'present_count' => 0,
            'late_count' => 0,
            'total_students' => Enrollment::where('course_id', $course->id)
                ->where('status', 'approved')
                ->count(),
        ]);

        $qrUrl = route('student.scan.process') . '?token=' . $session->session_token . '&session=' . $session->id;
        $qrCode = '<img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($qrUrl) . '" alt="QR Code">';

        return response()->json([
            'success' => true,
            'session' => $session,
            'qr_code' => $qrCode,
            'message' => 'Session created successfully'
        ]);
    }

    public function endSessionAjax($id)
    {
        $session = AttendanceSession::where('lecturer_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $session->status = 'ended';
        $session->ended_at = Carbon::now();
        $session->save();

        return response()->json(['success' => true, 'message' => 'Session ended']);
    }

    public function refreshQrAjax($id)
    {
        $session = AttendanceSession::where('lecturer_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $newExpiry = Carbon::now()->addMinutes(5);
        $session->qr_expires_at = $newExpiry;
        $session->expires_at = $newExpiry;
        $session->save();

        $qrUrl = route('student.scan.process') . '?token=' . $session->session_token . '&session=' . $session->id;
        $qrCode = '<img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($qrUrl) . '" alt="QR Code">';

        return response()->json([
            'success' => true,
            'qr_code' => $qrCode,
            'manual_code' => $session->manual_code,
            'expires_in' => 300,
            'message' => 'QR code refreshed'
        ]);
    }

    /**
     * Get session statistics for real-time display
     */
    public function getSessionStats($id)
    {
        $session = AttendanceSession::findOrFail($id);

        $presentCount = AttendanceRecord::where('attendance_session_id', $id)
            ->where('status', 'present')
            ->count();

        $lateCount = AttendanceRecord::where('attendance_session_id', $id)
            ->where('status', 'late')
            ->count();

        $absentCount = AttendanceRecord::where('attendance_session_id', $id)
            ->where('status', 'absent')
            ->count();

        $totalStudents = Enrollment::where('course_id', $session->course_id)
            ->where('status', 'approved')
            ->count();

        $percentage = $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100) : 0;

        $records = AttendanceRecord::where('attendance_session_id', $id)
            ->with('student')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'present' => $presentCount,
            'late' => $lateCount,
            'absent' => $absentCount,
            'total' => $totalStudents,
            'percentage' => $percentage,
            'records' => $records->map(function($record) {
                return [
                    'student_name' => $record->student->name ?? 'Unknown',
                    'student_email' => $record->student->email ?? 'N/A',
                    'status' => $record->status,
                    'scanned_at' => $record->scanned_at ? $record->scanned_at->toDateTimeString() : null,
                    'is_manual' => $record->is_manual ?? false,
                ];
            })
        ]);
    }

    public function getActiveSession()
    {
        $lecturer = Auth::user();

        $activeSession = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('status', 'active')
            ->with(['course', 'records.student'])
            ->first();

        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'No active session found'
            ]);
        }

        $present = $activeSession->records->where('status', 'present')->count();
        $late = $activeSession->records->where('status', 'late')->count();
        $absent = $activeSession->records->where('status', 'absent')->count();
        $total = Enrollment::where('course_id', $activeSession->course_id)
            ->where('status', 'approved')
            ->count();

        return response()->json([
            'success' => true,
            'session' => [
                'id' => $activeSession->id,
                'course_name' => $activeSession->course->course_name ?? 'Unknown',
                'course_code' => $activeSession->course->course_code ?? 'N/A',
                'manual_code' => $activeSession->manual_code,
                'room' => $activeSession->room,
                'started_at' => $activeSession->started_at,
                'qr_expires_at' => $activeSession->qr_expires_at,
                'expires_in' => $activeSession->qr_expires_at ? Carbon::now()->diffInSeconds($activeSession->qr_expires_at) : 0,
                'statistics' => [
                    'present' => $present,
                    'late' => $late,
                    'absent' => $absent,
                    'total' => $total,
                    'percentage' => $total > 0 ? round(($present / $total) * 100) : 0,
                ]
            ]
        ]);
    }
}
