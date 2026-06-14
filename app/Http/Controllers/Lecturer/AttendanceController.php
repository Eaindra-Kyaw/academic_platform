<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function takeAttendance()
    {
        return $this->sessions();
    }

    public function sessions()
    {
        $lecturer = Auth::user();

        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->where('is_active', true)
            ->get();

        $activeSession = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('status', 'active')
            ->with('course')
            ->first();

        $recentSessions = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('status', 'ended')
            ->where('session_date', '>=', Carbon::now()->subDays(7))
            ->with('course')
            ->orderBy('session_date', 'desc')
            ->limit(10)
            ->get();

        // Get all students for manual attendance dropdown
        $courseIds = $courses->pluck('id');
        $students = User::whereHas('enrollments', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds)->where('status', 'approved');
        })->get();

        $expiresIn = $activeSession && $activeSession->qr_expires_at ? Carbon::now()->diffInSeconds($activeSession->qr_expires_at) : 0;

        return view('lecturer.attendance.take-attendance', compact('courses', 'activeSession', 'recentSessions', 'students', 'expiresIn'));
    }

    public function createSession(Request $request)
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

        $totalStudents = Enrollment::where('course_id', $course->id)
            ->where('status', 'approved')
            ->count();

        $sessionToken = AttendanceSession::generateSessionToken();
        $sessionCode = AttendanceSession::generateSessionCode();

        $session = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'session_token' => $sessionToken,
            'session_code' => $sessionCode,
            'manual_code' => $sessionCode,
            'duration' => $duration,
            'session_date' => Carbon::now()->toDateString(),
            'start_time' => Carbon::now(),
            'started_at' => Carbon::now(),
            'room' => $request->room ?? $course->room,
            'status' => 'active',
            'expires_at' => $expiresAt,
            'qr_expires_at' => $expiresAt,
            'total_students' => $totalStudents,
            'present_count' => 0,
            'late_count' => 0,
            'absent_count' => 0,
        ]);

        return redirect()->route('lecturer.attendance.sessions')
            ->with('success', 'QR session created! Expires in ' . $duration . ' minutes.')
            ->with('new_session', $session);
    }

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

        $totalStudents = Enrollment::where('course_id', $course->id)
            ->where('status', 'approved')
            ->count();

        $sessionToken = AttendanceSession::generateSessionToken();
        $sessionCode = AttendanceSession::generateSessionCode();

        $session = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'session_token' => $sessionToken,
            'session_code' => $sessionCode,
            'manual_code' => $sessionCode,
            'duration' => $duration,
            'session_date' => Carbon::now()->toDateString(),
            'start_time' => Carbon::now(),
            'started_at' => Carbon::now(),
            'room' => $request->room ?? $course->room,
            'status' => 'active',
            'expires_at' => $expiresAt,
            'qr_expires_at' => $expiresAt,
            'total_students' => $totalStudents,
            'present_count' => 0,
            'late_count' => 0,
            'absent_count' => 0,
        ]);

        $qrUrl = route('student.scan.process') . '?token=' . $session->session_token . '&session=' . $session->id;
        $qrCode = \QrCode::size(180)->generate($qrUrl);

        return response()->json([
            'success' => true,
            'session' => $session,
            'qr_code' => $qrCode,
            'message' => 'Session created successfully'
        ]);
    }

    public function endSession($id)
    {
        $session = AttendanceSession::where('lecturer_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $session->status = 'ended';
        $session->end_time = Carbon::now();
        $session->save();

        return redirect()->back()->with('success', 'Attendance session ended.');
    }

    public function endSessionAjax($id)
    {
        $session = AttendanceSession::where('lecturer_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $session->status = 'ended';
        $session->end_time = Carbon::now();
        $session->save();

        return response()->json(['success' => true, 'message' => 'Session ended']);
    }

    public function refreshSession($id)
    {
        $session = AttendanceSession::where('lecturer_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $newExpiry = Carbon::now()->addMinutes(5);
        $session->qr_expires_at = $newExpiry;
        $session->expires_at = $newExpiry;
        $session->save();

        return redirect()->back()->with('success', 'QR code refreshed! Expires in 5 minutes.');
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
        $qrCode = \QrCode::size(120)->generate($qrUrl);

        return response()->json([
            'success' => true,
            'qr_code' => $qrCode,
            'manual_code' => $session->session_code,
            'expires_in' => 300,
            'message' => 'QR code refreshed'
        ]);
    }

    public function getSessionStats($id)
    {
        $session = AttendanceSession::findOrFail($id);

        $records = AttendanceRecord::where('attendance_session_id', $id)
            ->with('student')
            ->get();

        $lateRecords = $records->filter(function($record) {
            return $record->status === 'late';
        })->values();

        return response()->json([
            'success' => true,
            'present' => $session->present_count ?? 0,
            'late' => $session->late_count ?? 0,
            'total' => $session->total_students ?? 0,
            'percentage' => $session->getAttendancePercentageAttribute(),
            'records' => $lateRecords->map(function($record) {
                return [
                    'student_name' => $record->student->name ?? 'Unknown',
                    'status' => $record->status,
                    'scanned_at' => $record->scanned_at
                ];
            })
        ]);
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
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return redirect()->back()->with('error', 'No active session for this course. Please start a QR session first.');
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
            'status' => $request->status,
            'is_manual' => true,
            'ip_address' => $request->ip(),
        ]);

        if ($request->status == 'present') {
            $session->increment('present_count');
        } elseif ($request->status == 'late') {
            $session->increment('late_count');
        } else {
            $session->increment('absent_count');
        }

        return redirect()->back()->with('success', 'Manual attendance recorded successfully.');
    }

    public function history()
    {
        $lecturer = Auth::user();

        $sessions = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('lecturer.attendance.history', compact('sessions'));
    }
}
