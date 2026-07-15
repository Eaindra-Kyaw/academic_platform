<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\TimetableEntry;
use App\Models\AttendanceEvaluation;
use App\Helpers\AttendanceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Take Attendance - Shows QR scanner and current active session
     */
    public function takeAttendance(Request $request)
    {
        $lecturer = Auth::user();

        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->where('is_active', true)
            ->get()
            ->unique('id');

        if ($request->has('back')) {
            session(['show_create_form' => true]);
            return redirect()->route('lecturer.attendance.take')
                ->with('info', 'Returned to main page. Your QR session is still active.');
        }

        if ($request->has('session')) {
            session()->forget('show_create_form');
            $sessionId = $request->session;
            $session = AttendanceSession::where('lecturer_id', $lecturer->id)
                ->where('id', $sessionId)
                ->first();
            if ($session) {
                $session->status = 'active';
                $session->started_at = Carbon::now();
                $session->save();
                return redirect()->route('lecturer.attendance.take')
                    ->with('success', 'Loaded semester QR for: ' . ($session->course->course_name ?? 'Unknown'));
            }
        }

        $activeSession = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('status', 'active')
            ->with(['course', 'records.student'])
            ->first();

        if (session('viewing_session_id')) {
            $viewingSession = AttendanceSession::where('lecturer_id', $lecturer->id)
                ->where('id', session('viewing_session_id'))
                ->where('status', 'active')
                ->with(['course', 'records.student'])
                ->first();
            if ($viewingSession) {
                $activeSession = $viewingSession;
            }
        }

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

        $existingStaticQrs = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('qr_mode', 'semester')
            ->where('status', 'active')
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->get();

        $showCreateForm = session('show_create_form', false);

        return view('lecturer.attendance.take-attendance', compact(
            'courses',
            'activeSession',
            'students',
            'expiresIn',
            'existingStaticQrs',
            'showCreateForm'
        ));
    }

    /**
     * Session History (Paginated list of all sessions)
     */
    public function sessions()
    {
        $lecturer = Auth::user();

        $sessions = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->with(['course', 'records'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $activeSessions = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('status', 'active')
            ->with(['course', 'records'])
            ->get();

        $totalSessions = AttendanceSession::where('lecturer_id', $lecturer->id)->count();
        $activeSessionsCount = $activeSessions->count();

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

        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->where('is_active', true)
            ->get()
            ->unique('id');

        return view('lecturer.attendance.history', compact(
            'sessions',
            'activeSessions',
            'totalSessions',
            'activeSessionsCount',
            'averageAttendance',
            'totalStudents',
            'courses'
        ));
    }

    /**
     * Display all attendance records for lecturer's courses (raw logs)
     */
    public function allRecords(Request $request)
    {
        $lecturer = Auth::user();

        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->where('is_active', true)
            ->get()
            ->unique('id');

        $courseIds = $courses->pluck('id')->toArray();

        $query = AttendanceRecord::whereHas('session', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->with(['session.course', 'student']);

        if ($request->filled('course_id')) {
            $query->whereHas('session', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('lecturer.attendance.all-records', compact('records', 'courses'));
    }

    /**
     * Create a new attendance session (with period_count)
     */
    public function createSession(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'qr_mode' => 'required|in:session,semester',
            'period_count' => 'required|integer|min:1|max:8',
            'duration' => 'required_if:qr_mode,session|integer|min:5|max:120',
            'room' => 'nullable|string|max:50',
        ]);

        $lecturer = Auth::user();
        $course = Course::findOrFail($request->course_id);
        $periodCount = (int) $request->period_count;

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
                    'reason' => 'New session started for this course',
                ],
                $existingSession,
                'success'
            );
        }

        session()->forget('show_create_form');

        $sessionToken = AttendanceSession::generateSessionToken();
        $sessionCode = AttendanceSession::generateSessionCode();

        // Determine QR expiry duration
        if ($request->qr_mode == 'semester') {
            $duration = 480; // 8 hours
            $expiresAt = Carbon::now()->addHours(8);
            $qrExpiresAt = Carbon::now()->addHours(8);
        } else {
            $duration = (int) $request->duration;
            $expiresAt = Carbon::now()->addMinutes($duration);
            $qrExpiresAt = Carbon::now()->addMinutes($duration);
        }

        // Create session with period_count and conducted_periods
        $session = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'session_token' => $sessionToken,
            'manual_code' => $sessionCode,
            'session_code' => $sessionCode,
            'session_date' => Carbon::now()->toDateString(),
            'period_count' => $periodCount,
            'conducted_periods' => $periodCount,
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

        // If semester QR, save token on course
        if ($request->qr_mode == 'semester' && !$course->semester_qr_token) {
            $course->semester_qr_token = $sessionToken;
            $course->save();
        }

        AuditLog::log(
            Auth::id(),
            'create_session',
            [
                'course_id' => $course->id,
                'course_name' => $course->course_name,
                'qr_mode' => $request->qr_mode,
                'session_id' => $session->id,
                'manual_code' => $session->manual_code,
                'period_count' => $periodCount,
                'duration' => $duration,
            ],
            $session,
            'success'
        );

        $message = $request->qr_mode == 'semester'
            ? 'Semester QR activated! Periods: ' . $periodCount
            : 'Dynamic QR session created! Expires in ' . $duration . ' minutes. Periods: ' . $periodCount;

        return redirect()->route('lecturer.attendance.take')
            ->with('success', $message)
            ->with('new_session', $session);
    }

    /**
     * End an active attendance session – also recalculates evaluations
     */
    public function endSession($id)
    {
        $session = AttendanceSession::where('lecturer_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $session->status = 'ended';
        $session->ended_at = Carbon::now();
        $session->save();

        session()->forget('show_create_form');
        session()->forget('viewing_session_id');

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

        $this->recalculateCourseEvaluations($session->course_id);

        return redirect()->route('lecturer.attendance.take')
            ->with('success', 'QR session ended. Attendance evaluations updated with KG+12 calculations.');
    }

    /**
     * Recalculate KG+12 evaluations for all students in a course (period-based)
     */
    private function recalculateCourseEvaluations($courseId)
    {
        $enrollments = Enrollment::where('course_id', $courseId)
            ->where('status', 'approved')
            ->with('student')
            ->get();

        if ($enrollments->isEmpty()) {
            return;
        }

        $sessions = AttendanceSession::where('course_id', $courseId)
            ->where('status', 'ended')
            ->where('is_cancelled', false)
            ->orderBy('session_date', 'asc')
            ->get();

        $totalPeriods = $sessions->sum('conducted_periods');
        if ($totalPeriods == 0) {
            foreach ($enrollments as $enrollment) {
                DB::table('attendance_evaluations')->updateOrInsert(
                    ['student_id' => $enrollment->student_id, 'course_id' => $courseId],
                    [
                        'total_sessions' => 0,
                        'attended_sessions' => 0,
                        'attendance_percentage' => 0,
                        'consistency_marks' => 0.5,
                        'punctuality_marks' => 2.0,
                        'participation_marks' => 1.5,
                        'roll_call_total' => 4.0,
                        'eligibility_status' => 'not_eligible',
                        'consecutive_absences' => 0,
                        'attendance_trend' => 'stable',
                        'risk_score' => 0,
                        'risk_level' => 'Low',
                        'risk_factors' => json_encode(['No sessions conducted yet']),
                        'academic_health_score' => 0,
                        'recovery_status' => 'Stable',
                        'evaluation_date' => Carbon::today()->toDateString(),
                        'updated_at' => now(),
                    ]
                );
            }
            return;
        }

        $sessionIds = $sessions->pluck('id')->toArray();

        foreach ($enrollments as $enrollment) {
            $studentId = $enrollment->student_id;

            $records = AttendanceRecord::where('student_id', $studentId)
                ->whereIn('attendance_session_id', $sessionIds)
                ->get()
                ->keyBy('attendance_session_id');

            $attendedPeriods = 0;
            $lateCount = 0;

            foreach ($sessions as $session) {
                $record = $records->get($session->id);
                if ($record && in_array($record->status, ['present', 'late'])) {
                    $attendedPeriods += $session->conducted_periods;
                    if ($record->status === 'late') {
                        $lateCount++;
                    }
                }
            }

            $attendancePercentage = round(($attendedPeriods / max($totalPeriods, 1)) * 100, 1);
            $rollCall = AttendanceHelper::calculateRollCallMark($attendancePercentage, $lateCount, 1.5);
            $consecutiveAbsences = AttendanceHelper::getConsecutiveAbsences($studentId, $courseId);
            $trend = AttendanceHelper::getAttendanceTrend($studentId, $courseId);
            $riskScore = AttendanceHelper::calculateRiskScore(
                $attendancePercentage,
                $rollCall['total'],
                $consecutiveAbsences,
                $trend
            );
            $riskLevel = AttendanceHelper::getRiskLevel($riskScore);
            $eligibility = AttendanceHelper::getEligibilityStatus($attendancePercentage);
            $explanations = AttendanceHelper::getRiskExplanation(
                $attendancePercentage,
                $rollCall['total'],
                $consecutiveAbsences,
                $trend,
                $riskLevel
            );

            $academicHealthScore = round(($attendancePercentage * 0.4) + ($rollCall['total'] * 6), 0);
            $recoveryStatus = ($attendancePercentage >= 75 && $riskLevel == 'Low') ? 'Recovering'
                : (($attendancePercentage < 60 || $riskLevel == 'High') ? 'Declining' : 'Stable');

            DB::table('attendance_evaluations')->updateOrInsert(
                ['student_id' => $studentId, 'course_id' => $courseId],
                [
                    'total_sessions' => $totalPeriods,
                    'attended_sessions' => $attendedPeriods,
                    'attendance_percentage' => $attendancePercentage,
                    'consistency_marks' => $rollCall['consistency'],
                    'punctuality_marks' => $rollCall['punctuality'],
                    'participation_marks' => $rollCall['participation'],
                    'roll_call_total' => $rollCall['total'],
                    'eligibility_status' => $eligibility,
                    'consecutive_absences' => $consecutiveAbsences,
                    'attendance_trend' => $trend,
                    'risk_score' => $riskScore,
                    'risk_level' => $riskLevel,
                    'risk_factors' => json_encode($explanations),
                    'academic_health_score' => $academicHealthScore,
                    'recovery_status' => $recoveryStatus,
                    'evaluation_date' => Carbon::today()->toDateString(),
                    'updated_at' => now(),
                ]
            );
        }
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
     * History alias
     */
    public function history()
    {
        return $this->sessions();
    }

    /**
     * Manage Semester QR Codes
     */
    public function semesterQrManagement()
    {
        $lecturer = Auth::user();

        $semesterQrs = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('qr_mode', 'semester')
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->get();

        $activeCount = $semesterQrs->where('status', 'active')->count();
        $endedCount = $semesterQrs->where('status', 'ended')->count();

        return view('lecturer.attendance.semester-qr-management', compact(
            'semesterQrs',
            'activeCount',
            'endedCount'
        ));
    }

    /**
     * End a semester QR code
     */
    public function endSemesterQr($id)
    {
        $session = AttendanceSession::where('lecturer_id', Auth::id())
            ->where('id', $id)
            ->where('qr_mode', 'semester')
            ->firstOrFail();

        $session->status = 'ended';
        $session->ended_at = Carbon::now();
        $session->save();

        AuditLog::log(
            Auth::id(),
            'end_semester_qr',
            [
                'course_id' => $session->course_id,
                'course_name' => $session->course->course_name ?? 'Unknown',
                'session_id' => $session->id,
            ],
            $session,
            'success'
        );

        return redirect()->route('lecturer.semester-qr.management')
            ->with('success', 'Semester QR deactivated successfully!');
    }

    // ============================================================
    // AJAX METHODS
    // ============================================================

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

        // For AJAX, default period_count to 1 (or read from input)
        $periodCount = $request->input('period_count', 1);

        $sessionToken = AttendanceSession::generateSessionToken();
        $sessionCode = AttendanceSession::generateSessionCode();

        $session = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'session_token' => $sessionToken,
            'manual_code' => $sessionCode,
            'session_code' => $sessionCode,
            'session_date' => Carbon::now()->toDateString(),
            'period_count' => $periodCount,
            'conducted_periods' => $periodCount,
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

        $this->recalculateCourseEvaluations($session->course_id);

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
     * Get session stats for live attendance (period-based)
     */
    public function getSessionStats($id)
    {
        $session = AttendanceSession::with(['course', 'records.student'])
            ->findOrFail($id);

        $presentCount = $session->records->where('status', 'present')->count();
        $lateCount = $session->records->where('status', 'late')->count();
        $totalStudents = $session->total_students ?? 0;

        $periods = $session->conducted_periods ?? 1;
        $attendedPeriods = ($presentCount + $lateCount) * $periods;
        $totalPeriods = $totalStudents * $periods;
        $percentage = $totalPeriods > 0 ? round(($attendedPeriods / $totalPeriods) * 100, 1) : 0;

        $records = $session->records->map(function($record) {
            return [
                'student_name' => $record->student->name ?? 'Unknown',
                'student_email' => $record->student->email ?? 'N/A',
                'status' => $record->status,
                'scanned_at' => $record->created_at ? $record->created_at->toDateTimeString() : null,
                'is_manual' => $record->is_manual ?? false,
            ];
        });

        return response()->json([
            'success' => true,
            'present' => $presentCount,
            'late' => $lateCount,
            'total' => $totalStudents,
            'percentage' => $percentage,
            'periods' => $periods,
            'attended_periods' => $attendedPeriods,
            'total_periods' => $totalPeriods,
            'records' => $records,
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
