<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\QrCodeHelper;

class AttendanceController extends Controller
{
    public function takeAttendance()
    {
        $lecturerId = Auth::id();

        // Get lecturer's courses
        $courses = Course::where('lecturer_id', $lecturerId)
                        ->orWhere('lecturer_name', 'like', '%' . Auth::user()->name . '%')
                        ->where('is_active', 1)
                        ->get();

        // Check for active session
        $activeSession = AttendanceSession::where('lecturer_id', $lecturerId)
                                          ->where('status', 'active')
                                          ->first();

        $qrCode = null;
        $expiresIn = 0;

        if ($activeSession) {
            // Prepare QR data
            $qrData = json_encode([
                'session_id' => $activeSession->id,
                'course_id' => $activeSession->course_id,
                'manual_code' => $activeSession->manual_code ?? 'N/A',
                'token' => $activeSession->session_token,
                'expires_at' => $activeSession->expires_at ? $activeSession->expires_at->toIso8601String() : null
            ]);

            $qrCode = QrCodeHelper::generate($qrData, 200);

            if ($activeSession->expires_at) {
                $expiresIn = max(0, now()->diffInSeconds($activeSession->expires_at, false));
            }
        }

        // Get recent sessions (last 10) - FIXED: Using created_at instead of session_date
        $recentSessions = AttendanceSession::where('lecturer_id', $lecturerId)
                                          ->where('status', 'ended')
                                          ->orderBy('created_at', 'desc')
                                          ->limit(10)
                                          ->get();

        // Get all students for manual attendance dropdown
        $courseIds = $courses->pluck('id')->toArray();
        $students = User::whereHas('enrollments', function($query) use ($courseIds) {
            $query->whereIn('course_id', $courseIds)
                  ->where('status', 'approved');
        })->get();

        return view('lecturer.attendance.take', compact(
            'courses',
            'activeSession',
            'qrCode',
            'expiresIn',
            'recentSessions',
            'students'
        ));
    }

    public function createSession(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'duration' => 'required|integer|min:15|max:120',
            'room' => 'nullable|string|max:255'
        ]);

        // Check for existing active session
        $existingSession = AttendanceSession::where('lecturer_id', Auth::id())
                                            ->where('status', 'active')
                                            ->first();

        if ($existingSession) {
            return redirect()->back()->with('error', 'You already have an active session. Please end it first.');
        }

        // Generate unique tokens
        $manualCode = strtoupper(substr(md5(uniqid() . time() . rand()), 0, 6));
        $sessionToken = bin2hex(random_bytes(32));

        $session = AttendanceSession::create([
            'course_id' => $request->course_id,
            'lecturer_id' => Auth::id(),
            'session_token' => $sessionToken,
            'manual_code' => $manualCode,
            'room' => $request->room,
            'duration' => $request->duration,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addMinutes($request->duration),
        ]);

        return redirect()->route('lecturer.attendance.take')
                        ->with('success', 'QR Session created successfully! Manual code: ' . $manualCode);
    }

    public function endSession($id)
    {
        $session = AttendanceSession::where('id', $id)
                                    ->where('lecturer_id', Auth::id())
                                    ->firstOrFail();

        $session->update([
            'status' => 'ended',
            'ended_at' => now()
        ]);

        return redirect()->route('lecturer.attendance.take')
                        ->with('success', 'Session ended successfully.');
    }

    public function refreshSession($id)
    {
        $session = AttendanceSession::where('id', $id)
                                    ->where('lecturer_id', Auth::id())
                                    ->where('status', 'active')
                                    ->firstOrFail();

        // Generate new codes
        $newManualCode = strtoupper(substr(md5(uniqid() . time() . rand()), 0, 6));
        $newToken = bin2hex(random_bytes(32));

        $session->update([
            'manual_code' => $newManualCode,
            'session_token' => $newToken,
            'expires_at' => now()->addMinutes($session->duration)
        ]);

        return redirect()->route('lecturer.attendance.take')
                        ->with('success', 'QR Code refreshed! New manual code: ' . $newManualCode);
    }

    public function manualAttendance(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student_id' => 'required|exists:users,id',
            'status' => 'required|in:present,absent,late'
        ]);

        // Check if there's an active session for this course
        $activeSession = AttendanceSession::where('lecturer_id', Auth::id())
                                          ->where('course_id', $request->course_id)
                                          ->where('status', 'active')
                                          ->first();

        if (!$activeSession) {
            return redirect()->back()->with('error', 'No active session for this course. Please create a QR session first.');
        }

        // Check if student is enrolled
        $isEnrolled = Enrollment::where('course_id', $request->course_id)
                                ->where('student_id', $request->student_id)
                                ->where('status', 'approved')
                                ->exists();

        if (!$isEnrolled) {
            return redirect()->back()->with('error', 'Student is not enrolled in this course.');
        }

        // Create or update attendance record
        $attendance = AttendanceRecord::updateOrCreate(
            [
                'attendance_session_id' => $activeSession->id,
                'student_id' => $request->student_id,
            ],
            [
                'status' => $request->status,
                'scanned_at' => now(),
                'ip_address' => $request->ip(),
            ]
        );

        $statusText = $request->status == 'present' ? 'marked present' : ($request->status == 'late' ? 'marked late' : 'marked absent');
        return redirect()->back()->with('success', "Student {$statusText} successfully.");
    }

    public function sessions()
    {
        $sessions = AttendanceSession::where('lecturer_id', Auth::id())
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(20);

        return view('lecturer.attendance.sessions', compact('sessions'));
    }

    public function history(Request $request)
    {
        $query = AttendanceSession::where('lecturer_id', Auth::id());

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by date range - FIXED: Using created_at instead of session_date
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $sessions = $query->orderBy('created_at', 'desc')
                         ->paginate(20);

        $courses = Course::where('lecturer_id', Auth::id())->get();

        return view('lecturer.attendance.history', compact('sessions', 'courses'));
    }

    public function viewSession($id)
    {
        $session = AttendanceSession::with(['course', 'attendanceRecords.student'])
                                   ->where('lecturer_id', Auth::id())
                                   ->findOrFail($id);

        $totalStudents = Enrollment::where('course_id', $session->course_id)
                                   ->where('status', 'approved')
                                   ->count();

        $presentCount = $session->attendanceRecords()->whereIn('status', ['present', 'late'])->count();
        $lateCount = $session->attendanceRecords()->where('status', 'late')->count();
        $absentCount = $totalStudents - $presentCount;

        return view('lecturer.attendance.view', compact(
            'session',
            'totalStudents',
            'presentCount',
            'lateCount',
            'absentCount'
        ));
    }

    public function getSessionStats($id)
    {
        $session = AttendanceSession::with('attendanceRecords.student')
                                   ->where('lecturer_id', Auth::id())
                                   ->findOrFail($id);

        $totalStudents = Enrollment::where('course_id', $session->course_id)
                                   ->where('status', 'approved')
                                   ->count();

        $presentCount = $session->attendanceRecords()->whereIn('status', ['present', 'late'])->count();
        $lateCount = $session->attendanceRecords()->where('status', 'late')->count();

        return response()->json([
            'success' => true,
            'present' => $presentCount,
            'total' => $totalStudents,
            'percentage' => $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100, 1) : 0,
            'late' => $lateCount,
            'records' => $session->attendanceRecords->map(function($record) {
                return [
                    'student_name' => $record->student->name,
                    'status' => $record->status,
                    'scanned_at' => $record->scanned_at ? $record->scanned_at->format('H:i:s') : $record->created_at->format('H:i:s')
                ];
            })
        ]);
    }
}
