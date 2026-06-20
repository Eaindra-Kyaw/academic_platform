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

    public function reports()
    {
        $lecturer = Auth::user();

        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->where('is_active', true)
            ->get();

        $sessions = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->with(['course', 'records'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $totalSessions = AttendanceSession::where('lecturer_id', $lecturer->id)->count();
        $activeSessions = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('status', 'active')
            ->count();

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

    // ============================================
    // TIMETABLE METHODS
    // ============================================

    /**
     * Display lecturer timetable - Calendar View
     */
   /**
 * Display lecturer timetable - Calendar View with merged multi-slot courses
 */
// ============================================
// TIMETABLE METHODS
// ============================================

/**
 * Display lecturer timetable - Auto-loaded from course data
 */
public function timetable()
{
    $lecturer = Auth::user();
    $lecturerId = $lecturer->id;

    // ============================================
    // AUTO-LOAD: Get all courses with schedule data
    // ============================================
    $courses = Course::where('lecturer_id', $lecturerId)
        ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
        ->where('is_active', true)
        ->get();

    // Get scheduled courses (auto-loaded from courses table)
    $scheduledCourses = Course::where('lecturer_id', $lecturerId)
        ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
        ->where('is_active', true)
        ->whereNotNull('schedule_day')
        ->whereNotNull('schedule_time')
        ->whereNotNull('schedule_end_time')
        ->whereRaw('schedule_time != schedule_end_time')
        ->orderBy('schedule_day')
        ->orderBy('schedule_time')
        ->get();

    $availableCourses = $courses;

    // Days
    $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $dayShort = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    $today = now();
    $weekStart = $today->copy()->startOfWeek();
    $weekEnd = $today->copy()->endOfWeek();

    $days = [];
    foreach ($dayNames as $index => $name) {
        $date = $weekStart->copy()->addDays($index);
        $days[] = [
            'name' => $name,
            'short' => $dayShort[$index],
            'date' => $date->format('d'),
            'month' => $date->format('M'),
            'is_today' => $date->isToday(),
            'is_weekend' => in_array($index, [5, 6]),
        ];
    }

    // 50-minute class time slots
    $timeSlots = [];
    $morningSlots = [
        ['start' => '08:00', 'end' => '08:50', 'label' => '08:00 - 08:50'],
        ['start' => '09:00', 'end' => '09:50', 'label' => '09:00 - 09:50'],
        ['start' => '10:00', 'end' => '10:50', 'label' => '10:00 - 10:50'],
        ['start' => '11:00', 'end' => '11:50', 'label' => '11:00 - 11:50'],
    ];
    $afternoonSlots = [
        ['start' => '13:00', 'end' => '13:50', 'label' => '01:00 - 01:50'],
        ['start' => '14:00', 'end' => '14:50', 'label' => '02:00 - 02:50'],
        ['start' => '15:00', 'end' => '15:50', 'label' => '03:00 - 03:50'],
        ['start' => '16:00', 'end' => '16:50', 'label' => '04:00 - 04:50'],
    ];
    $allSlots = array_merge($morningSlots, $afternoonSlots);

    foreach ($allSlots as $index => $slot) {
        $timeSlots[] = [
            'time' => $slot['label'],
            'period' => $index + 1,
            'start' => $slot['start'],
            'end' => $slot['end'],
        ];
    }

    // Build timetable grid with merged multi-slot courses
    $timetable = [];
    foreach ($days as $dayIndex => $day) {
        $timetable[$dayIndex] = [];
        foreach ($timeSlots as $slot) {
            $timetable[$dayIndex][$slot['period']] = null;
        }
    }

    // Place courses in the grid - auto-loaded from database
    foreach ($scheduledCourses as $course) {
        foreach ($days as $dayIndex => $day) {
            if (strtolower(trim($course->schedule_day)) === strtolower(trim($day['name']))) {
                $courseStart = strtotime($course->schedule_time);
                $courseEnd = strtotime($course->schedule_end_time);

                $coveredSlots = [];
                foreach ($timeSlots as $slot) {
                    $slotStart = strtotime($slot['start']);
                    $slotEnd = strtotime($slot['end']);

                    if (($courseStart >= $slotStart && $courseStart < $slotEnd) ||
                        ($courseEnd > $slotStart && $courseEnd <= $slotEnd) ||
                        ($courseStart <= $slotStart && $courseEnd >= $slotEnd)) {
                        $coveredSlots[] = $slot['period'];
                    }
                }

                if (!empty($coveredSlots)) {
                    $firstSlot = $coveredSlots[0];
                    $rowspan = count($coveredSlots);

                    $timetable[$dayIndex][$firstSlot] = [
                        'course_name' => $course->course_name ?? 'Unknown',
                        'course_code' => $course->course_code ?? 'N/A',
                        'room' => $course->room ?? 'N/A',
                        'time' => date('h:i A', strtotime($course->schedule_time)) . ' - ' .
                                 date('h:i A', strtotime($course->schedule_end_time)),
                        'year' => $course->year ?? '',
                        'rowspan' => $rowspan,
                    ];

                    for ($i = 1; $i < count($coveredSlots); $i++) {
                        $slotPeriod = $coveredSlots[$i];
                        $timetable[$dayIndex][$slotPeriod] = 'used';
                    }
                }
                break;
            }
        }
    }

    // Clean up 'used' slots
    foreach ($days as $dayIndex => $day) {
        foreach ($timeSlots as $slot) {
            if ($timetable[$dayIndex][$slot['period']] === 'used') {
                $timetable[$dayIndex][$slot['period']] = null;
            }
        }
    }

    // Next class
    $nextClass = null;
    $now = now();
    $currentDayName = $now->format('l');
    $currentTime = $now->format('H:i:s');

    $todayCourses = $scheduledCourses->filter(function ($course) use ($currentDayName) {
        return strtolower(trim($course->schedule_day)) === strtolower(trim($currentDayName));
    })->sortBy('schedule_time');

    foreach ($todayCourses as $course) {
        if ($course->schedule_time > $currentTime) {
            $nextClass = [
                'course_name' => $course->course_name ?? 'Unknown',
                'course_code' => $course->course_code ?? 'N/A',
                'room' => $course->room ?? 'N/A',
                'time' => date('h:i A', strtotime($course->schedule_time)) . ' - ' .
                         date('h:i A', strtotime($course->schedule_end_time)),
                'start_time' => now()->setTimeFromTimeString($course->schedule_time)->toDateTimeString(),
                'day' => $course->schedule_day,
            ];
            break;
        }
    }

    if (!$nextClass) {
        $tomorrow = $now->copy()->addDay();
        $tomorrowDayName = $tomorrow->format('l');
        $tomorrowCourses = $scheduledCourses->filter(function ($course) use ($tomorrowDayName) {
            return strtolower(trim($course->schedule_day)) === strtolower(trim($tomorrowDayName));
        })->sortBy('schedule_time');
        if ($tomorrowCourses->isNotEmpty()) {
            $course = $tomorrowCourses->first();
            $nextClass = [
                'course_name' => $course->course_name ?? 'Unknown',
                'course_code' => $course->course_code ?? 'N/A',
                'room' => $course->room ?? 'N/A',
                'time' => date('h:i A', strtotime($course->schedule_time)) . ' - ' .
                         date('h:i A', strtotime($course->schedule_end_time)),
                'start_time' => now()->setTimeFromTimeString($course->schedule_time)->addDay()->toDateTimeString(),
                'day' => $course->schedule_day,
                'is_tomorrow' => true,
            ];
        }
    }

    // Statistics
    $stats = [
        'total_courses' => $courses->count(),
        'total_weekly_hours' => 0,
        'total_classes' => $scheduledCourses->count(),
        'departments' => $courses->pluck('department.name')->unique()->filter()->count(),
        'year_levels' => $courses->pluck('year')->unique()->filter()->count(),
    ];

    foreach ($scheduledCourses as $course) {
        if ($course->schedule_time && $course->schedule_end_time) {
            $start = strtotime($course->schedule_time);
            $end = strtotime($course->schedule_end_time);
            $stats['total_weekly_hours'] += ($end - $start) / 3600;
        }
    }
    $stats['total_weekly_hours'] = round($stats['total_weekly_hours'], 1);

    $dayCounts = [];
    foreach ($scheduledCourses as $course) {
        if ($course->schedule_day) {
            $dayCounts[$course->schedule_day] = ($dayCounts[$course->schedule_day] ?? 0) + 1;
        }
    }
    $stats['busiest_day'] = !empty($dayCounts) ? array_keys($dayCounts, max($dayCounts))[0] : 'N/A';

    $totalSlots = 7 * 8;
    $usedSlots = 0;
    foreach ($timetable as $day) {
        foreach ($day as $slot) {
            if ($slot !== null && $slot !== 'used') $usedSlots++;
        }
    }
    $stats['free_periods'] = $totalSlots - $usedSlots;

    return view('lecturer.timetable', compact(
        'courses',
        'scheduledCourses',
        'availableCourses',
        'timetable',
        'days',
        'timeSlots',
        'weekStart',
        'weekEnd',
        'nextClass',
        'stats'
    ));
}

/**
 * Display timetable management page - For editing existing schedule
 */
public function manageTimetable()
{
    $lecturer = Auth::user();
    $lecturerId = $lecturer->id;

    // All courses (for dropdown)
    $availableCourses = Course::where('lecturer_id', $lecturerId)
        ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
        ->where('is_active', true)
        ->orderBy('course_code')
        ->get();

    // Courses already scheduled (auto-loaded)
    $scheduledCourses = Course::where('lecturer_id', $lecturerId)
        ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
        ->where('is_active', true)
        ->whereNotNull('schedule_day')
        ->whereNotNull('schedule_time')
        ->whereNotNull('schedule_end_time')
        ->whereRaw('schedule_time != schedule_end_time')
        ->orderBy('schedule_day')
        ->orderBy('schedule_time')
        ->get();

    return view('lecturer.timetable-manage', compact('availableCourses', 'scheduledCourses'));
}

/**
 * Add/Update course schedule - This should already be in your system
 * When a course is assigned to a lecturer, the schedule data should be set
 */
public function addToTimetable(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id',
        'schedule_day' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        'schedule_time' => 'required|date_format:H:i',
        'schedule_end_time' => 'required|date_format:H:i|after:schedule_time',
        'room' => 'nullable|string|max:50',
    ]);

    // Prevent same start and end time
    if ($request->schedule_time === $request->schedule_end_time) {
        return redirect()->back()->with('error', '⚠️ Start time and end time cannot be the same!');
    }

    $course = Course::where(function($query) {
        $query->where('lecturer_id', Auth::id())
              ->orWhere('lecturer_name', 'like', '%' . Auth::user()->name . '%');
    })->where('id', $request->course_id)->firstOrFail();

    // Update the course schedule
    $course->schedule_day = $request->schedule_day;
    $course->schedule_time = $request->schedule_time;
    $course->schedule_end_time = $request->schedule_end_time;
    $course->room = $request->room ?? $course->room;
    $course->save();

    return redirect()->route('lecturer.timetable.manage')
        ->with('success', '✅ ' . $course->course_code . ' schedule updated!');
}

/**
 * Remove course from timetable
 */
public function removeFromTimetable($id)
{
    try {
        $course = Course::where(function($query) {
            $query->where('lecturer_id', Auth::id())
                  ->orWhere('lecturer_name', 'like', '%' . Auth::user()->name . '%');
        })->where('id', $id)->first();

        if (!$course) {
            return redirect()->route('lecturer.timetable.manage')
                ->with('error', '❌ Course not found!');
        }

        if (!$course->schedule_day) {
            return redirect()->route('lecturer.timetable.manage')
                ->with('error', '⚠️ "' . $course->course_code . '" is not scheduled.');
        }

        $course->schedule_day = null;
        $course->schedule_time = null;
        $course->schedule_end_time = null;
        $course->save();

        return redirect()->route('lecturer.timetable.manage')
            ->with('success', '🗑️ "' . $course->course_code . '" removed from timetable!');

    } catch (\Exception $e) {
        return redirect()->route('lecturer.timetable.manage')
            ->with('error', '❌ Error: ' . $e->getMessage());
    }
}
}
