<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\QrCodeHelper;
use App\Models\Course;
use App\Models\AttendanceSession;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LecturerController extends Controller
{
    public function dashboard()
    {
        $lecturerId = Auth::id();

        // Get lecturer's courses
        $courses = Course::where('lecturer_id', $lecturerId)
                        ->orWhere('lecturer_name', 'like', '%' . Auth::user()->name . '%')
                        ->get();

        // Get all enrolled students for manual attendance dropdown
        $students = User::whereHas('enrollments', function($query) use ($courses) {
            $query->whereIn('course_id', $courses->pluck('id'))
                  ->where('status', 'approved');
        })->get();

        // Check for active session
        $activeSession = AttendanceSession::where('lecturer_id', $lecturerId)
                                          ->where('status', 'active')
                                          ->first();

        $qrCode = null;
        $expiresIn = 0;

        if ($activeSession) {
            $qrData = json_encode([
                'session_id' => $activeSession->id,
                'course_id' => $activeSession->course_id,
                'manual_code' => $activeSession->manual_code ?? 'N/A',
                'timestamp' => now()->toIso8601String(),
                'expires_at' => $activeSession->expires_at ? $activeSession->expires_at->toIso8601String() : null
            ]);

            $qrCode = QrCodeHelper::generate($qrData, 120);

            if ($activeSession->expires_at) {
                $expiresIn = max(0, now()->diffInSeconds($activeSession->expires_at, false));
                if ($expiresIn <= 0) {
                    $activeSession->update(['status' => 'ended', 'ended_at' => now()]);
                    $activeSession = null;
                    $qrCode = null;
                }
            }
        }

        // Get announcements for lecturer dashboard
        $announcements = Announcement::forRole('lecturer')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Mark announcements as read when viewed on dashboard
        foreach ($announcements as $announcement) {
            if (!$announcement->isReadBy(Auth::id())) {
                $announcement->markAsRead(Auth::id());
            }
        }

        // Calculate statistics
        $totalStudents = 0;
        $atRiskStudents = 0;
        $avgAttendance = 0;
        $courseEngagement = 0;
        $lowAlerts = 0;
        $activeSessions = $activeSession ? 1 : 0;

        // Live attendance stats for active session
        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;
        $presentPercent = 0;
        $absentPercent = 0;
        $latePercent = 0;
        $totalInSession = 0;
        $lateStudents = collect();

        if ($activeSession && $activeSession->course) {
            $totalInSession = Enrollment::where('course_id', $activeSession->course_id)
                                       ->where('status', 'approved')
                                       ->count();

            $presentCount = $activeSession->records()->where('status', 'present')->count();
            $lateCount = $activeSession->records()->where('status', 'late')->count();
            $presentCount += $lateCount;
            $absentCount = max(0, $totalInSession - $presentCount);

            if ($totalInSession > 0) {
                $presentPercent = round(($presentCount / $totalInSession) * 100, 1);
                $absentPercent = round(($absentCount / $totalInSession) * 100, 1);
                $latePercent = round(($lateCount / $totalInSession) * 100, 1);
            }

            $lateStudents = $activeSession->records()
                                         ->where('status', 'late')
                                         ->with('student')
                                         ->get();
        }

        // Calculate overall stats from all courses
        foreach ($courses as $course) {
            $enrolledCount = Enrollment::where('course_id', $course->id)
                                      ->where('status', 'approved')
                                      ->count();
            $totalStudents += $enrolledCount;

            $sessions = AttendanceSession::where('course_id', $course->id)->get();
            $totalAttendance = 0;
            foreach ($sessions as $session) {
                $totalAttendance += $session->records()->count();
            }
            if ($sessions->count() > 0 && $enrolledCount > 0) {
                $courseAvg = ($totalAttendance / ($sessions->count() * $enrolledCount)) * 100;
                $avgAttendance += $courseAvg;

                if ($courseAvg < 60) {
                    $atRiskStudents += $enrolledCount;
                    $lowAlerts++;
                }
            }
        }

        if ($courses->count() > 0) {
            $avgAttendance = round($avgAttendance / $courses->count(), 1);
            $courseEngagement = round($avgAttendance, 0);
        }

        // At-risk students list
        $atRiskList = collect();
        foreach ($courses as $course) {
            $students_in_course = User::whereHas('enrollments', function($q) use ($course) {
                $q->where('course_id', $course->id)->where('status', 'approved');
            })->get();

            foreach ($students_in_course as $student) {
                $attendancePercentage = $this->getStudentAttendancePercentage($student->id, $course->id);
                if ($attendancePercentage < 60) {
                    $atRiskList->push((object)[
                        'student' => $student,
                        'attendance_percentage' => $attendancePercentage,
                        'risk_level' => $attendancePercentage < 40 ? 'High' : ($attendancePercentage < 60 ? 'Medium' : 'Low')
                    ]);
                }
            }
        }

        return view('lecturer.dashboard', compact(
            'courses', 'students', 'activeSession', 'qrCode', 'expiresIn',
            'totalStudents', 'atRiskStudents', 'avgAttendance', 'courseEngagement',
            'lowAlerts', 'activeSessions', 'presentCount', 'absentCount', 'lateCount',
            'presentPercent', 'absentPercent', 'latePercent', 'totalInSession',
            'lateStudents', 'atRiskList', 'announcements'
        ));
    }

    private function getStudentAttendancePercentage($studentId, $courseId)
    {
        $totalSessions = AttendanceSession::where('course_id', $courseId)->count();
        if ($totalSessions == 0) return 100;

        $presentSessions = AttendanceSession::where('course_id', $courseId)
            ->whereHas('records', function($q) use ($studentId) {
                $q->where('student_id', $studentId)
                  ->whereIn('status', ['present', 'late']);
            })->count();

        return round(($presentSessions / $totalSessions) * 100, 1);
    }

    public function generateQr(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'duration' => 'required|integer|in:30,45,60,90',
            'room' => 'nullable|string|max:255'
        ]);

        $existingSession = AttendanceSession::where('lecturer_id', Auth::id())
                                            ->where('status', 'active')
                                            ->first();

        if ($existingSession) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active session. Please end it first.'
                ], 400);
            }
            return redirect()->back()->with('error', 'You already have an active session. Please end it first.');
        }

        $manualCode = strtoupper(substr(md5(uniqid() . time() . rand()), 0, 6));

        $session = AttendanceSession::create([
            'course_id' => $request->course_id,
            'lecturer_id' => Auth::id(),
            'manual_code' => $manualCode,
            'room' => $request->room,
            'duration' => $request->duration,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addMinutes($request->duration),
            'session_token' => bin2hex(random_bytes(32))
        ]);

        $qrData = json_encode([
            'session_id' => $session->id,
            'course_id' => $session->course_id,
            'manual_code' => $manualCode,
            'token' => $session->session_token,
            'expires_at' => $session->expires_at->toIso8601String()
        ]);

        $qrCode = QrCodeHelper::generate($qrData, 120);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'manual_code' => $manualCode,
                'qr_code' => $qrCode,
                'expires_at' => $session->expires_at->format('Y-m-d H:i:s')
            ]);
        }

        return redirect()->route('lecturer.dashboard')->with('success', 'QR session created successfully!');
    }

    public function endSession($sessionId, Request $request)
    {
        $session = AttendanceSession::where('id', $sessionId)
                                    ->where('lecturer_id', Auth::id())
                                    ->firstOrFail();

        $session->update([
            'status' => 'ended',
            'ended_at' => now()
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('lecturer.dashboard')
                        ->with('success', 'Session ended successfully');
    }

    public function refreshQr($sessionId, Request $request)
    {
        $session = AttendanceSession::where('id', $sessionId)
                                    ->where('lecturer_id', Auth::id())
                                    ->where('status', 'active')
                                    ->firstOrFail();

        $newManualCode = strtoupper(substr(md5(uniqid() . time() . rand()), 0, 6));
        $newToken = bin2hex(random_bytes(32));

        $session->update([
            'manual_code' => $newManualCode,
            'session_token' => $newToken
        ]);

        if ($session->expires_at && $session->expires_at->isPast()) {
            $session->update(['expires_at' => now()->addMinutes($session->duration)]);
        }

        $qrData = json_encode([
            'session_id' => $session->id,
            'course_id' => $session->course_id,
            'manual_code' => $newManualCode,
            'token' => $newToken,
            'expires_at' => $session->expires_at ? $session->expires_at->toIso8601String() : null
        ]);

        $qrCode = QrCodeHelper::generate($qrData, 120);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'manual_code' => $newManualCode,
                'qr_code' => $qrCode,
                'expires_at' => $session->expires_at ? $session->expires_at->format('Y-m-d H:i:s') : null
            ]);
        }

        return redirect()->route('lecturer.dashboard')->with('success', 'QR refreshed!');
    }

    public function sessionStats($sessionId)
    {
        $session = AttendanceSession::with(['course', 'records.student'])
                                   ->where('lecturer_id', Auth::id())
                                   ->findOrFail($sessionId);

        $presentCount = $session->records->whereIn('status', ['present', 'late'])->count();
        $totalStudents = Enrollment::where('course_id', $session->course_id)
                                   ->where('status', 'approved')
                                   ->count();
        $lateCount = $session->records->where('status', 'late')->count();

        return response()->json([
            'success' => true,
            'present' => $presentCount,
            'total' => $totalStudents,
            'percentage' => $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100, 1) : 0,
            'late' => $lateCount,
            'records' => $session->records->map(function($record) {
                return [
                    'student_name' => $record->student->name,
                    'student_email' => $record->student->email,
                    'status' => $record->status,
                    'scanned_at' => $record->created_at->format('H:i:s')
                ];
            })
        ]);
    }

    public function students()
    {
        $courses = Course::where('lecturer_id', Auth::id())->get();
        $students = User::whereHas('enrollments', function($query) use ($courses) {
            $query->whereIn('course_id', $courses->pluck('id'))->where('status', 'approved');
        })->with('enrollments')->get();

        return view('lecturer.students', compact('students', 'courses'));
    }

    public function schedule()
    {
        return view('lecturer.schedule');
    }

    /**
     * Reports Page - Updated with data
     */
    public function reports()
    {
        $lecturer = Auth::user();

        // Get lecturer's courses
        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->where('is_active', true)
            ->get();

        // Get all sessions for reports
        $sessions = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->with(['course', 'records'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Statistics
        $totalSessions = AttendanceSession::where('lecturer_id', $lecturer->id)->count();
        $activeSessions = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('status', 'active')
            ->count();

        // Calculate overall attendance
        $totalStudents = 0;
        $totalPresent = 0;
        $totalLate = 0;
        $totalAbsent = 0;

        foreach ($sessions as $session) {
            $present = $session->records->where('status', 'present')->count();
            $late = $session->records->where('status', 'late')->count();
            $absent = $session->records->where('status', 'absent')->count();

            $totalPresent += $present;
            $totalLate += $late;
            $totalAbsent += $absent;
            $totalStudents += ($present + $late + $absent);
        }

        $averageAttendance = $totalStudents > 0 ? round(($totalPresent / $totalStudents) * 100) : 0;

        // Course-specific statistics
        $courseStats = [];
        foreach ($courses as $course) {
            $courseSessions = AttendanceSession::where('course_id', $course->id)
                ->with('records')
                ->get();

            $coursePresent = 0;
            $courseTotal = 0;
            $courseLate = 0;
            $courseAbsent = 0;

            foreach ($courseSessions as $session) {
                $present = $session->records->where('status', 'present')->count();
                $late = $session->records->where('status', 'late')->count();
                $absent = $session->records->where('status', 'absent')->count();

                $coursePresent += $present;
                $courseLate += $late;
                $courseAbsent += $absent;
                $courseTotal += ($present + $late + $absent);
            }

            $courseStats[$course->id] = [
                'name' => $course->course_name,
                'code' => $course->course_code,
                'sessions' => $courseSessions->count(),
                'attendance' => $courseTotal > 0 ? round(($coursePresent / $courseTotal) * 100) : 0,
                'students' => Enrollment::where('course_id', $course->id)->where('status', 'approved')->count(),
                'present' => $coursePresent,
                'late' => $courseLate,
                'absent' => $courseAbsent,
                'total' => $courseTotal,
            ];
        }

        return view('lecturer.reports', compact(
            'courses',
            'sessions',
            'courseStats',
            'totalSessions',
            'activeSessions',
            'averageAttendance',
            'totalStudents',
            'totalPresent',
            'totalLate',
            'totalAbsent'
        ));
    }

    /**
     * Display lecturer announcements page
     */
    public function announcements()
    {
        $user = Auth::user();

        $announcements = Announcement::forRole('lecturer')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        foreach ($announcements as $announcement) {
            if (!$announcement->isReadBy($user->id)) {
                $announcement->markAsRead($user->id);
            }
        }

        $courses = Course::where('lecturer_id', Auth::id())
            ->where('is_active', true)
            ->get();

        return view('lecturer.announcements.index', compact('announcements', 'courses'));
    }

    /**
     * Display a single announcement detail
     */
    public function showAnnouncement($id)
    {
        $user = Auth::user();

        $announcement = Announcement::findOrFail($id);

        if (!$announcement->isReadBy($user->id)) {
            $announcement->markAsRead($user->id);
        }

        $courses = Course::where('lecturer_id', Auth::id())
            ->where('is_active', true)
            ->get();

        return view('lecturer.announcements.show', compact('announcement', 'user', 'courses'));
    }

    /**
 * Display lecturer timetable
 */
public function timetable()
{
    $lecturer = Auth::user();

    // Get lecturer's courses
    $courses = Course::where('lecturer_id', $lecturer->id)
        ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
        ->where('is_active', true)
        ->get();

    // ============================================
    // SAMPLE TIMETABLE DATA (Replace with your actual data)
    // ============================================

    // Days of the week
    $days = [];
    $today = now();
    $weekStart = $today->copy()->startOfWeek();
    $weekEnd = $today->copy()->endOfWeek();

    $dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    foreach ($dayNames as $index => $name) {
        $date = $weekStart->copy()->addDays($index);
        $days[] = [
            'label' => $name,
            'date' => $date->format('d'),
            'is_today' => $date->isToday(),
        ];
    }

    // Time slots
    $timeSlots = [];
    $timeRanges = [
        ['time' => '8:00 - 9:00', 'period' => 1],
        ['time' => '9:00 - 10:00', 'period' => 2],
        ['time' => '10:00 - 11:00', 'period' => 3],
        ['time' => '11:00 - 12:00', 'period' => 4],
        ['time' => '12:00 - 1:00', 'period' => 5],
        ['time' => '1:00 - 2:00', 'period' => 6],
        ['time' => '2:00 - 3:00', 'period' => 7],
        ['time' => '3:00 - 4:00', 'period' => 8],
        ['time' => '4:00 - 5:00', 'period' => 9],
    ];

    foreach ($timeRanges as $range) {
        $timeSlots[] = [
            'time' => $range['time'],
            'period' => $range['period'],
        ];
    }

    // ============================================
    // SAMPLE CLASS DATA (Replace with your actual database data)
    // ============================================

    // Example: If you have a timetable table in your database,
    // you would query it here.
    // For now, we'll use sample data:

    $sampleTimetable = [
        // Monday (index 0)
        [
            1 => ['course_name' => 'Machine Learning', 'course_code' => 'CEIT-52033', 'room' => '1-3-7', 'time' => '8:00-9:00', 'students' => 45],
            2 => ['course_name' => 'Data Science', 'course_code' => 'CEIT-52034', 'room' => '1-3-8', 'time' => '9:00-10:00', 'students' => 38],
        ],
        // Tuesday (index 1)
        [
            3 => ['course_name' => 'Web Development', 'course_code' => 'CEIT-52035', 'room' => '2-1-5', 'time' => '10:00-11:00', 'students' => 42],
            4 => ['course_name' => 'Machine Learning', 'course_code' => 'CEIT-52033', 'room' => '1-3-7', 'time' => '11:00-12:00', 'students' => 45],
        ],
        // Wednesday (index 2)
        [
            1 => ['course_name' => 'Database Systems', 'course_code' => 'CEIT-52036', 'room' => '1-3-9', 'time' => '8:00-9:00', 'students' => 40],
            5 => ['course_name' => 'Web Development', 'course_code' => 'CEIT-52035', 'room' => '2-1-5', 'time' => '12:00-1:00', 'students' => 42],
        ],
        // Thursday (index 3)
        [
            2 => ['course_name' => 'Data Science', 'course_code' => 'CEIT-52034', 'room' => '1-3-8', 'time' => '9:00-10:00', 'students' => 38],
            6 => ['course_name' => 'Database Systems', 'course_code' => 'CEIT-52036', 'room' => '1-3-9', 'time' => '1:00-2:00', 'students' => 40],
        ],
        // Friday (index 4)
        [
            3 => ['course_name' => 'Machine Learning', 'course_code' => 'CEIT-52033', 'room' => '1-3-7', 'time' => '10:00-11:00', 'students' => 45],
            7 => ['course_name' => 'Data Science', 'course_code' => 'CEIT-52034', 'room' => '1-3-8', 'time' => '2:00-3:00', 'students' => 38],
        ],
        // Saturday (index 5) - No classes
        [],
        // Sunday (index 6) - No classes
        [],
    ];

    // Build the timetable array
    $timetable = [];
    for ($day = 0; $day < 7; $day++) {
        $timetable[$day] = [];
        foreach ($timeRanges as $range) {
            $period = $range['period'];
            $timetable[$day][$period] = $sampleTimetable[$day][$period] ?? null;
        }
    }

    // Find next class
    $nextClass = null;
    $now = now();
    $currentDay = $now->dayOfWeek; // 0=Sunday, 1=Monday, etc.
    $currentHour = $now->hour;
    $currentMinute = $now->minute;
    $currentTime = $currentHour + ($currentMinute / 60);

    // Check today's remaining classes
    $dayIndex = $currentDay == 0 ? 6 : $currentDay - 1; // Convert to our 0=Monday format
    foreach ($timeRanges as $range) {
        $period = $range['period'];
        if (isset($sampleTimetable[$dayIndex][$period])) {
            // Parse time
            $timeParts = explode('-', $range['time']);
            $startTime = trim($timeParts[0]);
            $startHour = (int)explode(':', $startTime)[0];
            $startMinute = (int)explode(':', $startTime)[1];
            $classStartTime = $startHour + ($startMinute / 60);

            if ($classStartTime > $currentTime) {
                $class = $sampleTimetable[$dayIndex][$period];
                $class['start_time'] = now()->setTime($startHour, $startMinute);
                $class['time'] = $range['time'];
                $nextClass = $class;
                break;
            }
        }
    }

    return view('lecturer.timetable', compact(
        'courses',
        'days',
        'timeSlots',
        'timetable',
        'weekStart',
        'weekEnd',
        'nextClass'
    ));
}
}
