<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Department;
use App\Models\Announcement;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentController extends Controller
{
    /**
     * Display student dashboard
     */
    public function dashboard()
    {
        $student = Auth::user();
        $studentId = $student->id;

        // Get approved enrollments
        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with(['course', 'course.department'])
            ->get();

        // ============================================
        // STATISTICS
        // ============================================
        $totalCourses = $enrollments->count();
        $avgAttendance = round($enrollments->avg('attendance_percentage') ?? 0, 1);
        $avgRollCall = round($enrollments->avg('roll_call_mark') ?? 0, 1);
        $eligibleCourses = $enrollments->where('eligibility_status', 'eligible')->count();
        $atRiskCourses = $enrollments->where('eligibility_status', '!=', 'eligible')->count();

        // ============================================
        // ACADEMIC HEALTH SCORE
        // ============================================
        $healthScore = $this->calculateHealthScore($studentId);
        $healthCategory = $this->getHealthCategory($healthScore);

        // ============================================
        // RISK SCORE
        // ============================================
        $riskData = $this->calculateRisk($studentId);
        $riskScore = $riskData['score'];
        $riskLevel = $riskData['level'];
        $riskFactors = $riskData['factors'];

        // ============================================
        // ATTENDANCE STATS
        // ============================================
        $totalSessions = AttendanceRecord::where('student_id', $studentId)->count();
        $presentSessions = AttendanceRecord::where('student_id', $studentId)
            ->where('status', 'present')
            ->count();
        $lateSessions = AttendanceRecord::where('student_id', $studentId)
            ->where('status', 'late')
            ->count();
        $absentSessions = AttendanceRecord::where('student_id', $studentId)
            ->where('status', 'absent')
            ->count();
        $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100, 1) : 0;

        // ============================================
        // CONSECUTIVE STREAK
        // ============================================
        $consecutiveStreak = $this->getConsecutiveStreak($studentId);

        // ============================================
        // RECENT ATTENDANCE
        // ============================================
        $attendanceRecords = AttendanceRecord::where('student_id', $studentId)
            ->with(['session.course'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ============================================
        // RECENT ENROLLMENTS
        // ============================================
        $recentEnrollments = Enrollment::where('student_id', $studentId)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ============================================
        // RECOMMENDATIONS
        // ============================================
        $recommendations = $this->generateRecommendations($studentId, $enrollments);

        // ============================================
        // ANNOUNCEMENTS
        // ============================================
        $announcements = Announcement::forRole('student')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($announcements as $announcement) {
            if (!$announcement->isReadBy($student->id)) {
                $announcement->markAsRead($student->id);
            }
        }

        // ============================================
        // PENDING ENROLLMENTS COUNT
        // ============================================
        $pendingEnrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'pending')
            ->count();

        return view('student.dashboard', compact(
            'student',
            'enrollments',
            'totalCourses',
            'avgAttendance',
            'avgRollCall',
            'eligibleCourses',
            'atRiskCourses',
            'healthScore',
            'healthCategory',
            'riskScore',
            'riskLevel',
            'riskFactors',
            'attendanceRate',
            'presentSessions',
            'lateSessions',
            'absentSessions',
            'totalSessions',
            'consecutiveStreak',
            'attendanceRecords',
            'recentEnrollments',
            'recommendations',
            'announcements',
            'pendingEnrollments'
        ));
    }

    /**
     * Display student attendance
     */
    public function attendance()
    {
        $student = Auth::user();
        $studentId = $student->id;

        $records = AttendanceRecord::where('student_id', $studentId)
            ->with(['session.course'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Course summary
        $courseSummary = DB::table('attendance_records')
            ->join('attendance_sessions', 'attendance_records.attendance_session_id', '=', 'attendance_sessions.id')
            ->join('courses', 'attendance_sessions.course_id', '=', 'courses.id')
            ->where('attendance_records.student_id', $studentId)
            ->select(
                'courses.id as course_id',
                'courses.course_code',
                'courses.course_name',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN attendance_records.status IN ("present", "late") THEN 1 ELSE 0 END) as attended'),
                DB::raw('SUM(CASE WHEN attendance_records.status = "absent" THEN 1 ELSE 0 END) as absent')
            )
            ->groupBy('courses.id', 'courses.course_code', 'courses.course_name')
            ->get();

        foreach ($courseSummary as $course) {
            $course->percentage = $course->total > 0 ? round(($course->attended / $course->total) * 100, 1) : 0;
        }

        $totalSessions = AttendanceRecord::where('student_id', $studentId)->count();
        $presentSessions = AttendanceRecord::where('student_id', $studentId)->where('status', 'present')->count();
        $lateSessions = AttendanceRecord::where('student_id', $studentId)->where('status', 'late')->count();
        $absentSessions = AttendanceRecord::where('student_id', $studentId)->where('status', 'absent')->count();

        return view('student.attendance', compact(
            'student',
            'records',
            'courseSummary',
            'totalSessions',
            'presentSessions',
            'lateSessions',
            'absentSessions'
        ));
    }

    /**
     * Display student timetable
     */
    public function timetable()
    {
        $student = Auth::user();
        $studentId = $student->id;

        // Get enrolled course IDs
        $enrolledCourseIds = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->pluck('course_id')
            ->toArray();

        // Get timetable entries for enrolled courses
        $timetableEntries = TimetableEntry::whereIn('course_id', $enrolledCourseIds)
            ->where('is_active', true)
            ->with(['course', 'course.department'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // If no timetable entries, get schedule from courses table
        if ($timetableEntries->isEmpty()) {
            $courses = Course::whereIn('id', $enrolledCourseIds)
                ->whereNotNull('schedule_day')
                ->whereNotNull('schedule_time')
                ->whereNotNull('schedule_end_time')
                ->get();

            $timetableEntries = $courses->map(function($course) {
                $entry = new TimetableEntry();
                $entry->course_id = $course->id;
                $entry->day_of_week = $course->schedule_day;
                $entry->start_time = $course->schedule_time;
                $entry->end_time = $course->schedule_end_time;
                $entry->room = $course->room;
                $entry->course = $course;
                return $entry;
            });
        }

        // Build grid
        $days = $this->getDays();
        $timeSlots = $this->getTimeSlots();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $timetable = [];
        foreach ($days as $dayIndex => $day) {
            $timetable[$dayIndex] = [];
            foreach ($timeSlots as $slot) {
                $matchedEntry = null;
                foreach ($timetableEntries as $entry) {
                    if ($entry->day_of_week && $entry->start_time && $entry->end_time) {
                        if (strtolower(trim($entry->day_of_week)) === strtolower(trim($day['name']))) {
                            $entryStart = strtotime($entry->start_time);
                            $entryEnd = strtotime($entry->end_time);
                            $slotStart = strtotime($slot['start']);
                            $slotEnd = strtotime($slot['end']);

                            if (($entryStart >= $slotStart && $entryStart < $slotEnd) ||
                                ($entryEnd > $slotStart && $entryEnd <= $slotEnd) ||
                                ($entryStart <= $slotStart && $entryEnd >= $slotEnd)) {
                                $matchedEntry = $entry;
                                break;
                            }
                        }
                    }
                }

                if ($matchedEntry) {
                    $timetable[$dayIndex][$slot['period']] = [
                        'course_name' => $matchedEntry->course->course_name ?? 'Unknown',
                        'course_code' => $matchedEntry->course->course_code ?? 'N/A',
                        'room' => $matchedEntry->room ?? 'N/A',
                        'time' => date('h:i A', strtotime($matchedEntry->start_time)) . ' - ' .
                                 date('h:i A', strtotime($matchedEntry->end_time)),
                    ];
                } else {
                    $timetable[$dayIndex][$slot['period']] = null;
                }
            }
        }

        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with('course')
            ->get();

        return view('student.timetable', compact(
            'student',
            'enrollments',
            'timetable',
            'days',
            'timeSlots',
            'weekStart',
            'weekEnd'
        ));
    }

    private function getDays()
    {
        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $dayShort = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        $today = now();
        $weekStart = $today->copy()->startOfWeek();

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

        return $days;
    }

    private function getTimeSlots()
    {
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

        return $timeSlots;
    }

    /**
     * Display student progress
     */
    public function progress()
    {
        $student = Auth::user();
        $studentId = $student->id;

        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with(['course'])
            ->get();

        $courseProgress = [];
        foreach ($enrollments as $enrollment) {
            $courseSessions = AttendanceSession::where('course_id', $enrollment->course_id)->count();
            $attended = AttendanceRecord::where('student_id', $studentId)
                ->whereHas('session', function($q) use ($enrollment) {
                    $q->where('course_id', $enrollment->course_id);
                })
                ->whereIn('status', ['present', 'late'])
                ->count();

            $courseProgress[] = [
                'course' => $enrollment->course,
                'attended' => $attended,
                'total' => $courseSessions,
                'percentage' => $courseSessions > 0 ? round(($attended / $courseSessions) * 100, 1) : 0,
                'attendance_percentage' => $enrollment->attendance_percentage ?? 0,
                'roll_call_mark' => $enrollment->roll_call_mark ?? 0,
                'eligibility_status' => $enrollment->eligibility_status ?? 'pending',
            ];
        }

        $totalSessions = AttendanceRecord::where('student_id', $studentId)->count();
        $presentSessions = AttendanceRecord::where('student_id', $studentId)->where('status', 'present')->count();
        $lateSessions = AttendanceRecord::where('student_id', $studentId)->where('status', 'late')->count();
        $absentSessions = AttendanceRecord::where('student_id', $studentId)->where('status', 'absent')->count();

        return view('student.progress', compact(
            'student',
            'enrollments',
            'courseProgress',
            'totalSessions',
            'presentSessions',
            'lateSessions',
            'absentSessions'
        ));
    }

    /**
     * Show student details (for admin)
     */
    public function show(User $student)
    {
        if ($student->role_id != 3) {
            abort(404, 'User is not a student');
        }

        $student->load(['department', 'enrollments' => function($query) {
            $query->where('status', 'approved')->with('course');
        }]);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Display student announcements list
     */
    public function announcements()
    {
        $student = Auth::user();

        $announcements = Announcement::forRole('student')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        foreach ($announcements as $announcement) {
            if (!$announcement->isReadBy($student->id)) {
                $announcement->markAsRead($student->id);
            }
        }

        return view('student.announcements.index', compact('announcements', 'student'));
    }

    /**
     * Display a single announcement detail
     */
    public function showAnnouncement($id)
    {
        try {
            $student = Auth::user();
            $announcement = Announcement::with('creator')->findOrFail($id);

            if (!$announcement->isReadBy($student->id)) {
                $announcement->markAsRead($student->id);
            }

            return view('student.announcements.show', compact('announcement', 'student'));

        } catch (\Exception $e) {
            \Log::error('Error showing announcement: ' . $e->getMessage());
            return redirect()->route('student.announcements.index')
                ->with('error', 'Announcement not found.');
        }
    }

    /**
     * Mark announcement as read
     */
    public function markAnnouncementRead($id)
    {
        $student = Auth::user();
        $announcement = Announcement::findOrFail($id);

        if (!$announcement->isReadBy($student->id)) {
            $announcement->markAsRead($student->id);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get unread announcements count
     */
    public function unreadAnnouncementsCount()
    {
        $student = Auth::user();

        $count = Announcement::forRole('student')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->whereDoesntHave('readers', function($q) use ($student) {
                $q->where('user_id', $student->id);
            })
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Student messages inbox
     */
    public function inbox()
    {
        $student = Auth::user();
        $messages = \App\Models\Message::where('recipient_id', $student->id)
            ->where('recipient_type', 'student')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('student.messages.inbox', compact('messages', 'student'));
    }

    /**
     * Show a single message
     */
    public function showMessage($id)
    {
        $student = Auth::user();
        $message = \App\Models\Message::where('recipient_id', $student->id)
            ->where('recipient_type', 'student')
            ->where('id', $id)
            ->firstOrFail();

        if (!$message->is_read) {
            $message->update(['is_read' => true, 'read_at' => now()]);
        }

        return view('student.messages.show', compact('message', 'student'));
    }

    /**
     * Get unread messages count
     */
    public function unreadMessagesCount()
    {
        $student = Auth::user();
        $count = \App\Models\Message::where('recipient_id', $student->id)
            ->where('recipient_type', 'student')
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Student notifications
     */
    public function notifications()
    {
        $student = Auth::user();
        $notifications = $student->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('student.notifications', compact('notifications', 'student'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        $student = Auth::user();
        $notification = $student->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $student = Auth::user();
        $student->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Display available courses for enrollment
     */
    public function availableCourses(Request $request)
    {
        $student = Auth::user();

        // Get courses not yet enrolled in
        $enrolledIds = Enrollment::where('student_id', $student->id)
            ->pluck('course_id')
            ->toArray();

        $courses = Course::where('is_active', true)
            ->whereNotIn('id', $enrolledIds)
            ->with(['department', 'lecturer'])
            ->when($request->search, function($q) use ($request) {
                return $q->where('course_name', 'like', '%' . $request->search . '%')
                         ->orWhere('course_code', 'like', '%' . $request->search . '%');
            })
            ->when($request->department, function($q) use ($request) {
                return $q->where('department_id', $request->department);
            })
            ->paginate(12);

        $departments = Department::orderBy('name')->get();

        return view('student.courses.available', compact('courses', 'departments'));
    }

    /**
     * Request enrollment
     */
    public function requestEnrollment($courseId)
    {
        $student = Auth::user();

        $existing = Enrollment::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You have already ' . $existing->status . ' this course.');
        }

        $course = Course::findOrFail($courseId);

        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $courseId,
            'status' => 'pending',
            'enrollment_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Enrollment request sent for ' . $course->course_code);
    }

    /**
     * My enrollments
     */
    public function myEnrollments()
    {
        $student = Auth::user();

        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['course', 'course.department', 'course.lecturer'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalEnrollments = $enrollments->count();
        $approvedEnrollments = $enrollments->where('status', 'approved')->count();
        $pendingEnrollments = $enrollments->where('status', 'pending')->count();
        $rejectedEnrollments = $enrollments->where('status', 'rejected')->count();

        $avgAttendance = $enrollments->where('status', 'approved')->avg('attendance_percentage') ?? 0;

        return view('student.enrollments.index', compact(
            'enrollments',
            'totalEnrollments',
            'approvedEnrollments',
            'pendingEnrollments',
            'rejectedEnrollments',
            'avgAttendance'
        ));
    }

    /**
     * QR Scan page
     */
    public function scan()
    {
        $student = Auth::user();

        $activeSession = AttendanceSession::where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        $enrolledCourseIds = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->pluck('course_id')
            ->toArray();

        $enrolledCourses = Course::whereIn('id', $enrolledCourseIds)
            ->where('is_active', true)
            ->get();

        return view('student.attendance.scan', compact('activeSession', 'enrolledCourses'));
    }

    /**
     * Process QR Scan
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'session_id' => 'required|exists:attendance_sessions,id',
        ]);

        $student = Auth::user();
        $sessionId = $request->session_id;
        $token = $request->token;

        $session = AttendanceSession::where('id', $sessionId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired session. Please try again.'
            ], 400);
        }

        if ($session->session_token !== $token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. Please try again.'
            ], 400);
        }

        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('course_id', $session->course_id)
            ->where('status', 'approved')
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course.'
            ], 403);
        }

        $existing = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already marked attendance for this session.',
                'already_scanned' => true
            ], 400);
        }

        if ($session->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This session has ended.'
            ], 400);
        }

        $isLate = false;
        if ($session->started_at) {
            $lateThreshold = $session->started_at->addMinutes(15);
            if (now() > $lateThreshold) {
                $isLate = true;
            }
        }

        $record = AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'status' => $isLate ? 'late' : 'present',
            'scanned_at' => now(),
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $isLate ? 'Attendance marked as LATE' : 'Attendance marked as PRESENT',
            'status' => $isLate ? 'late' : 'present',
            'record' => $record,
        ]);
    }

    /**
     * Manual Attendance Entry (Fallback)
     */
    public function manualAttendance(Request $request)
    {
        $request->validate([
            'manual_code' => 'required|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        $student = Auth::user();

        $session = AttendanceSession::where('manual_code', $request->manual_code)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('course_id', $request->course_id)
            ->first();

        if (!$session) {
            return redirect()->back()->with('error', 'Invalid manual code or session expired.');
        }

        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('course_id', $session->course_id)
            ->where('status', 'approved')
            ->exists();

        if (!$isEnrolled) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        $existing = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You have already marked attendance for this session.');
        }

        $isLate = false;
        if ($session->started_at) {
            $lateThreshold = $session->started_at->addMinutes(15);
            if (now() > $lateThreshold) {
                $isLate = true;
            }
        }

        AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'status' => $isLate ? 'late' : 'present',
            'scanned_at' => now(),
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent(),
            'is_manual' => true,
        ]);

        return redirect()->back()->with('success', 'Attendance marked successfully!');
    }

    /**
     * Check if a session is active (AJAX)
     */
    public function checkSession(Request $request)
    {
        $sessionId = $request->session_id;
        $session = AttendanceSession::where('id', $sessionId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($session) {
            return response()->json([
                'success' => true,
                'active' => true,
                'expires_at' => $session->expires_at,
                'course_name' => $session->course->course_name ?? 'N/A',
            ]);
        }

        return response()->json([
            'success' => false,
            'active' => false,
            'message' => 'No active session found',
        ]);
    }

    /**
     * Semester QR scan
     */
    public function semesterScan(Request $request)
    {
        $token = $request->token;
        $courseId = $request->course;

        $student = Auth::user();
        $course = Course::findOrFail($courseId);

        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->where('status', 'approved')
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.scan')->with('error', 'You are not enrolled in this course.');
        }

        if ($course->semester_qr_token !== $token) {
            return redirect()->route('student.scan')->with('error', 'Invalid QR code.');
        }

        $activeSession = AttendanceSession::where('course_id', $courseId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if (!$activeSession) {
            return redirect()->route('student.scan')->with('error', 'No active session for this course.');
        }

        $existing = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $activeSession->id)
            ->first();

        if ($existing) {
            return redirect()->route('student.scan')->with('error', 'You have already marked attendance for this session.');
        }

        $isLate = false;
        if ($activeSession->started_at) {
            $lateThreshold = $activeSession->started_at->addMinutes(15);
            if (now() > $lateThreshold) {
                $isLate = true;
            }
        }

        AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $activeSession->id,
            'status' => $isLate ? 'late' : 'present',
            'scanned_at' => now(),
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent(),
        ]);

        return redirect()->route('student.scan')->with('success', 'Attendance marked successfully!');
    }

    // ============================================
    // CHATBOT METHODS
    // ============================================

    /**
     * Chatbot page
     */
    public function chatbot()
    {
        $student = Auth::user();
        return view('student.chatbot', compact('student'));
    }

    /**
     * Ask chatbot
     */
    public function askChatbot(Request $request)
    {
        $query = strtolower(trim($request->input('query', '')));
        $student = Auth::user();

        $response = $this->getBotResponse($query, $student);

        return response()->json([
            'success' => true,
            'response' => $response,
        ]);
    }

    /**
     * Get bot response based on query
     */
    private function getBotResponse($query, $student)
    {
        $studentId = $student->id;

        // ============================================
        // GREETINGS
        // ============================================
        if (preg_match('/\b(hi|hello|hey|good morning|good afternoon|good evening|howdy|sup|yo)\b/i', $query)) {
            return "👋 Hello! I'm your Academic Assistant. How can I help you today?<br><br>Try asking me about:<br>• 📊 Your attendance percentage<br>• ✅ Exam eligibility<br>• ⚠️ Risk level<br>• 💡 Recommendations<br>• 💚 Academic Health Score<br>• 📅 Timetable<br>• 📚 Course details<br>• 📊 Department comparison<br>• 📝 Exam readiness<br>• 📈 Attendance forecast";
        }

        // ============================================
        // HELP
        // ============================================
        if (preg_match('/\b(help|faq|what can you do|how to use|features|commands)\b/i', $query)) {
            return "🤖 <strong>I can help you with:</strong><br><br>
                    📊 <strong>Attendance</strong> - Ask 'What is my attendance?'<br>
                    ✅ <strong>Eligibility</strong> - Ask 'Am I eligible for exam?'<br>
                    ⚠️ <strong>Risk</strong> - Ask 'What is my risk level?'<br>
                    💡 <strong>Recommendations</strong> - Ask 'What should I do?'<br>
                    💚 <strong>Health</strong> - Ask 'What is my health score?'<br>
                    📅 <strong>Timetable</strong> - Ask 'Show my timetable'<br>
                    📚 <strong>Course Details</strong> - Ask 'Tell me about CEIT-52033'<br>
                    📊 <strong>Forecast</strong> - Ask 'Will I be eligible?'<br>
                    📊 <strong>Department Comparison</strong> - Ask 'Compare with department'<br>
                    📝 <strong>Exam Readiness</strong> - Ask 'Am I ready for exam?'<br>
                    💡 <strong>Study Tips</strong> - Ask 'Give me study tips'<br>
                    🏆 <strong>Rank</strong> - Ask 'What is my class rank?'<br>
                    📈 <strong>Trend</strong> - Ask 'Show my attendance trend'<br>
                    ⏰ <strong>Countdown</strong> - Ask 'How many days until exam?'<br>
                    👨‍🏫 <strong>Lecturer</strong> - Ask 'Who is my lecturer?'<br>
                    📚 <strong>Semester Summary</strong> - Ask 'How is my semester?'<br><br>
                    💡 <strong>Tip:</strong> Try asking your question in plain English!";
        }

        // ============================================
        // ATTENDANCE QUERIES
        // ============================================
        if (preg_match('/\b(attendance|percentage|present|absent|attended|sessions)\b/i', $query)) {
            $enrollments = Enrollment::where('student_id', $studentId)
                ->where('status', 'approved')
                ->get();

            $avgAttendance = round($enrollments->avg('attendance_percentage') ?? 0, 1);
            $totalSessions = AttendanceRecord::where('student_id', $studentId)->count();
            $presentSessions = AttendanceRecord::where('student_id', $studentId)
                ->where('status', 'present')
                ->count();
            $lateSessions = AttendanceRecord::where('student_id', $studentId)
                ->where('status', 'late')
                ->count();
            $absentSessions = AttendanceRecord::where('student_id', $studentId)
                ->where('status', 'absent')
                ->count();

            $response = "📊 <strong>Your Attendance Summary</strong><br>";
            $response .= "• Overall: <strong>{$avgAttendance}%</strong><br>";
            $response .= "• ✅ Present: {$presentSessions}<br>";
            $response .= "• ⏰ Late: {$lateSessions}<br>";
            $response .= "• ❌ Absent: {$absentSessions}<br>";
            $response .= "• 📚 Total: {$totalSessions} sessions";

            if ($avgAttendance >= 75) {
                $response .= "<br><br>✅ <strong>Good standing!</strong> Keep it up! 🎉";
            } elseif ($avgAttendance >= 60) {
                $response .= "<br><br>⚠️ <strong>You're close to eligibility.</strong> Attend upcoming sessions to improve.";
            } else {
                $response .= "<br><br>🚨 <strong>Your attendance needs attention.</strong> Contact your lecturer for support.";
            }

            return $response;
        }

        // ============================================
        // ELIGIBILITY QUERIES
        // ============================================
        if (preg_match('/\b(eligible|eligibility|exam|final|eligible for exam|can i take exam)\b/i', $query)) {
            $enrollments = Enrollment::where('student_id', $studentId)
                ->where('status', 'approved')
                ->get();

            $eligible = $enrollments->where('eligibility_status', 'eligible')->count();
            $total = $enrollments->count();
            $avgAttendance = round($enrollments->avg('attendance_percentage') ?? 0, 1);

            $response = "✅ <strong>Exam Eligibility Summary</strong><br>";
            $response .= "• Eligible: <strong>{$eligible}</strong> out of {$total} courses<br>";
            $response .= "• Overall Attendance: <strong>{$avgAttendance}%</strong>";

            if ($eligible == $total && $total > 0) {
                $response .= "<br><br>🎉 <strong>You are eligible for all courses!</strong> Great job!";
            } elseif ($eligible > 0) {
                $response .= "<br><br>📋 You are eligible for {$eligible} course(s). Focus on the remaining courses to become fully eligible.";
            } else {
                $response .= "<br><br>⚠️ <strong>No eligible courses yet.</strong> Improve your attendance to become eligible.";
            }

            return $response;
        }

        // ============================================
        // RISK QUERIES
        // ============================================
        if (preg_match('/\b(risk|risk level|am i at risk|danger|high risk)\b/i', $query)) {
            $riskData = $this->calculateRisk($studentId);
            $level = $riskData['level'];
            $score = $riskData['score'];
            $factors = $riskData['factors'];

            $response = "⚠️ <strong>Risk Analysis</strong><br>";
            $response .= "• Risk Level: <strong>{$level}</strong><br>";
            $response .= "• Risk Score: <strong>{$score}/100</strong>";

            if (!empty($factors)) {
                $response .= "<br><br>📌 <strong>Risk Factors:</strong><br>";
                foreach ($factors as $factor) {
                    $response .= "• " . $factor . "<br>";
                }
            }

            if ($level === 'Low') {
                $response .= "<br><br>✅ <strong>You're in good standing.</strong> Keep maintaining your attendance!";
            } elseif ($level === 'Medium') {
                $response .= "<br><br>📈 <strong>Some risk detected.</strong> Focus on improving attendance to reduce risk.";
            } else {
                $response .= "<br><br>🚨 <strong>High risk detected!</strong> Immediate action recommended. Contact your lecturer.";
            }

            return $response;
        }

        // ============================================
        // RECOMMENDATIONS
        // ============================================
        if (preg_match('/\b(recommend|recommendation|advice|suggest|what should i do|help)\b/i', $query)) {
            $enrollments = Enrollment::where('student_id', $studentId)
                ->where('status', 'approved')
                ->with('course')
                ->get();

            $recommendations = $this->generateRecommendations($studentId, $enrollments);

            if (empty($recommendations)) {
                return "💡 <strong>No recommendations at the moment.</strong><br>You're doing great! Keep maintaining your attendance.";
            }

            $response = "💡 <strong>Your Recommendations</strong><br><br>";
            foreach ($recommendations as $rec) {
                $icon = $rec['type'] === 'excellent' ? '🌟' : ($rec['type'] === 'good' ? '📈' : ($rec['type'] === 'warning' ? '⚠️' : '🚨'));
                $response .= "{$icon} " . $rec['message'] . "<br><br>";
            }

            return $response;
        }

        // ============================================
        // HEALTH SCORE
        // ============================================
        if (preg_match('/\b(health|academic health|health score|score)\b/i', $query)) {
            $healthScore = $this->calculateHealthScore($studentId);
            $category = $this->getHealthCategory($healthScore);

            $enrollments = Enrollment::where('student_id', $studentId)
                ->where('status', 'approved')
                ->get();
            $avgAttendance = round($enrollments->avg('attendance_percentage') ?? 0, 1);
            $avgRollCall = round($enrollments->avg('roll_call_mark') ?? 0, 1);

            $response = "💚 <strong>Academic Health Score</strong><br>";
            $response .= "• Score: <strong>{$healthScore}</strong><br>";
            $response .= "• Category: <strong>{$category}</strong><br>";
            $response .= "• Attendance: {$avgAttendance}%<br>";
            $response .= "• Roll Call: {$avgRollCall}/10";

            if ($category === 'Excellent') {
                $response .= "<br><br>🌟 <strong>Excellent!</strong> You're performing at the highest level!";
            } elseif ($category === 'Stable') {
                $response .= "<br><br>📊 <strong>Good standing.</strong> Continue maintaining your performance.";
            } elseif ($category === 'At Risk') {
                $response .= "<br><br>⚠️ <strong>At Risk.</strong> Focus on improving attendance and performance.";
            } else {
                $response .= "<br><br>🚨 <strong>Critical!</strong> Immediate intervention recommended.";
            }

            return $response;
        }

        // ============================================
        // TIMETABLE
        // ============================================
        if (preg_match('/\b(timetable|schedule|next class|class time|when is my class)\b/i', $query)) {
            return "📅 <strong>Your Timetable</strong><br><br>View your full timetable here:<br>👉 <a href='" . route('student.timetable') . "' style='color:#800000; text-decoration:underline;'>My Timetable</a><br><br>You can also check your daily schedule from the navigation menu.";
        }

        // ============================================
        // COURSES
        // ============================================
        if (preg_match('/\b(course|courses|my courses|enrolled|enrollments)\b/i', $query)) {
            $enrollments = Enrollment::where('student_id', $studentId)
                ->where('status', 'approved')
                ->with('course')
                ->get();

            if ($enrollments->isEmpty()) {
                return "📚 <strong>You are not enrolled in any courses yet.</strong><br><br>Browse available courses here:<br>👉 <a href='" . route('student.courses.available') . "' style='color:#800000; text-decoration:underline;'>Available Courses</a>";
            }

            $response = "📚 <strong>Your Enrolled Courses</strong><br><br>";
            foreach ($enrollments as $enrollment) {
                $response .= "• " . $enrollment->course->course_code . " - " . $enrollment->course->course_name . "<br>";
            }
            $response .= "<br>View all: <a href='" . route('student.my.enrollments') . "' style='color:#800000; text-decoration:underline;'>My Enrollments</a>";

            return $response;
        }

        // ============================================
        // COURSE-SPECIFIC QUERIES
        // ============================================
        if (preg_match('/\b([A-Z]{2,5}-\d+)\b/', $query, $matches)) {
            $courseCode = $matches[1];
            return $this->getCourseInfo($courseCode);
        }

        // ============================================
        // CLASS RANK
        // ============================================
        if (preg_match('/\b(rank|ranking|position|top|class rank|where do i stand)\b/i', $query)) {
            return $this->getClassRank();
        }

        // ============================================
        // ATTENDANCE TREND
        // ============================================
        if (preg_match('/\b(trend|weekly|progress|improvement|decline)\b/i', $query)) {
            return $this->getAttendanceTrend();
        }

        // ============================================
        // ATTENDANCE FORECAST
        // ============================================
        if (preg_match('/\b(forecast|predict|project|will i be eligible|future attendance)\b/i', $query)) {
            return $this->getAttendanceForecast();
        }

        // ============================================
        // STUDY TIPS
        // ============================================
        if (preg_match('/\b(study tips|how to study|study advice|prepare for class|improve|get better)\b/i', $query)) {
            return $this->getStudyTips();
        }

        // ============================================
        // DEPARTMENT COMPARISON
        // ============================================
        if (preg_match('/\b(compare|department average|class average|benchmark|peers)\b/i', $query)) {
            return $this->getDepartmentComparison();
        }

        // ============================================
        // EXAM READINESS
        // ============================================
        if (preg_match('/\b(exam ready|exam preparation|ready for exam|test prep)\b/i', $query)) {
            return $this->getExamReadiness();
        }

        // ============================================
        // EXAM COUNTDOWN
        // ============================================
        if (preg_match('/\b(countdown|exam countdown|days until exam|exam date|when is exam)\b/i', $query)) {
            return $this->getExamCountdown();
        }

        // ============================================
        // LECTURER INFO
        // ============================================
        if (preg_match('/\b(lecturer|teacher|professor|who teaches|instructor)\b/i', $query)) {
            if (preg_match('/\b([A-Z]{2,5}-\d+)\b/', $query, $matches)) {
                return $this->getLecturerInfo($matches[1]);
            }
            return $this->getLecturerInfo();
        }

        // ============================================
        // SEMESTER SUMMARY
        // ============================================
        if (preg_match('/\b(semester summary|semester overview|my semester|how is my semester)\b/i', $query)) {
            return $this->getSemesterSummary();
        }

        // ============================================
        // PROGRESS
        // ============================================
        if (preg_match('/\b(progress|performance|how am i doing|my progress)\b/i', $query)) {
            return "📊 <strong>Your Academic Progress</strong><br><br>View detailed progress here:<br>👉 <a href='" . route('student.progress') . "' style='color:#800000; text-decoration:underline;'>My Progress</a><br><br>You can see course-by-course attendance, roll call marks, and eligibility status.";
        }

        // ============================================
        // DEFAULT RESPONSE
        // ============================================
        return "🤖 I understand you're asking about: <strong>\"" . htmlspecialchars($query) . "\"</strong><br><br>
                I can help you with:<br>
                • 📊 <strong>Attendance</strong> - Ask 'What is my attendance?'<br>
                • ✅ <strong>Eligibility</strong> - Ask 'Am I eligible for exam?'<br>
                • ⚠️ <strong>Risk</strong> - Ask 'What is my risk level?'<br>
                • 💡 <strong>Recommendations</strong> - Ask 'What should I do?'<br>
                • 💚 <strong>Health</strong> - Ask 'What is my health score?'<br>
                • 📅 <strong>Timetable</strong> - Ask 'Show my timetable'<br>
                • 📚 <strong>Course Details</strong> - Ask 'Tell me about CEIT-52033'<br>
                • 📊 <strong>Forecast</strong> - Ask 'Will I be eligible?'<br>
                • 📊 <strong>Department Comparison</strong> - Ask 'Compare with department'<br>
                • 📝 <strong>Exam Readiness</strong> - Ask 'Am I ready for exam?'<br>
                • 💡 <strong>Study Tips</strong> - Ask 'Give me study tips'<br>
                • 🏆 <strong>Rank</strong> - Ask 'What is my class rank?'<br>
                • 📈 <strong>Trend</strong> - Ask 'Show my attendance trend'<br>
                • ⏰ <strong>Countdown</strong> - Ask 'How many days until exam?'<br>
                • 👨‍🏫 <strong>Lecturer</strong> - Ask 'Who is my lecturer?'<br>
                • 📚 <strong>Semester Summary</strong> - Ask 'How is my semester?'<br><br>
                💡 <strong>Tip:</strong> Try asking your question in plain English!";
    }

    // ============================================
    // CHATBOT HELPER METHODS
    // ============================================

    /**
     * Get course-specific information
     */
    private function getCourseInfo($courseCode)
    {
        $studentId = Auth::id();

        $enrollment = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->whereHas('course', function($q) use ($courseCode) {
                $q->where('course_code', $courseCode);
            })
            ->with(['course', 'course.lecturer'])
            ->first();

        if (!$enrollment) {
            return "❌ <strong>Course not found.</strong><br>You are not enrolled in <strong>{$courseCode}</strong>.";
        }

        $course = $enrollment->course;
        $attendance = $enrollment->attendance_percentage ?? 0;
        $rollCall = $enrollment->roll_call_mark ?? 0;
        $status = $enrollment->eligibility_status ?? 'pending';

        $response = "📚 <strong>{$course->course_code} - {$course->course_name}</strong><br>";
        $response .= "• Lecturer: " . ($course->lecturer->name ?? 'N/A') . "<br>";
        $response .= "• Department: " . ($course->department->name ?? 'N/A') . "<br>";
        $response .= "• Your Attendance: <strong>{$attendance}%</strong><br>";
        $response .= "• Roll Call Mark: <strong>{$rollCall}/10</strong><br>";
        $response .= "• Status: <strong>" . ucfirst(str_replace('_', ' ', $status)) . "</strong>";

        if ($attendance < 60) {
            $response .= "<br><br>⚠️ <strong>Recommendation:</strong> Your attendance is below 60%. Contact your lecturer for support.";
        } elseif ($attendance < 75) {
            $response .= "<br><br>📈 <strong>Recommendation:</strong> Attend upcoming sessions to reach the 75% eligibility threshold.";
        } else {
            $response .= "<br><br>✅ <strong>Great job!</strong> You're maintaining good attendance in this course.";
        }

        return $response;
    }

    /**
     * Get class rank
     */
    private function getClassRank()
    {
        $student = Auth::user();
        $studentId = $student->id;

        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with(['course'])
            ->get();

        if ($enrollments->isEmpty()) {
            return "📚 You are not enrolled in any courses yet.";
        }

        $response = "🏆 <strong>Your Class Rank</strong><br><br>";

        foreach ($enrollments as $enrollment) {
            $courseId = $enrollment->course_id;
            $attendance = $enrollment->attendance_percentage ?? 0;

            // Get all students in this course
            $allEnrollments = Enrollment::where('course_id', $courseId)
                ->where('status', 'approved')
                ->get();

            $totalStudents = $allEnrollments->count();

            // Count students with higher attendance
            $higher = $allEnrollments->filter(function($e) use ($attendance) {
                return ($e->attendance_percentage ?? 0) > $attendance;
            })->count();

            $rank = $higher + 1;
            $percentile = $totalStudents > 0 ? round((($totalStudents - $higher) / $totalStudents) * 100) : 0;

            $icon = $percentile >= 80 ? '🌟' : ($percentile >= 60 ? '📈' : '📊');
            $response .= "{$icon} <strong>{$enrollment->course->course_code}</strong><br>";
            $response .= "   Rank: <strong>#{$rank}</strong> of {$totalStudents} students<br>";
            $response .= "   Attendance: {$attendance}% (Top {$percentile}%)<br><br>";
        }

        return $response;
    }

    /**
     * Get attendance trend
     */
    private function getAttendanceTrend()
    {
        $student = Auth::user();
        $studentId = $student->id;

        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with(['course'])
            ->get();

        if ($enrollments->isEmpty()) {
            return "📚 You are not enrolled in any courses yet.";
        }

        $response = "📈 <strong>Attendance Trend</strong><br><br>";

        foreach ($enrollments as $enrollment) {
            $courseId = $enrollment->course_id;
            $course = $enrollment->course;

            // Get weekly attendance data
            $weeks = [];
            for ($i = 4; $i >= 0; $i--) {
                $weekStart = now()->startOfWeek()->subWeeks($i);
                $weekEnd = now()->startOfWeek()->subWeeks($i)->endOfWeek();

                $weekRecords = AttendanceRecord::where('student_id', $studentId)
                    ->whereHas('session', function($q) use ($courseId) {
                        $q->where('course_id', $courseId);
                    })
                    ->whereBetween('created_at', [$weekStart, $weekEnd])
                    ->whereIn('status', ['present', 'late'])
                    ->count();

                $weekSessions = AttendanceSession::where('course_id', $courseId)
                    ->whereBetween('created_at', [$weekStart, $weekEnd])
                    ->count();

                $weeks[] = [
                    'week' => $weekStart->format('d M'),
                    'attended' => $weekRecords,
                    'total' => $weekSessions,
                ];
            }

            $response .= "<strong>{$course->course_code}</strong><br>";
            foreach ($weeks as $week) {
                $icon = $week['total'] > 0 && $week['attended'] == $week['total'] ? '✅' :
                        ($week['total'] > 0 && $week['attended'] > 0 ? '🟡' : '❌');
                $response .= "   {$icon} {$week['week']}: {$week['attended']}/{$week['total']} sessions<br>";
            }
            $response .= "<br>";
        }

        return $response;
    }

    /**
     * Get attendance forecast
     */
    private function getAttendanceForecast()
    {
        $studentId = Auth::id();

        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with(['course'])
            ->get();

        if ($enrollments->isEmpty()) {
            return "📚 You are not enrolled in any courses yet.";
        }

        $forecasts = [];
        foreach ($enrollments as $enrollment) {
            $current = $enrollment->attendance_percentage ?? 0;
            $totalSessions = AttendanceSession::where('course_id', $enrollment->course_id)->count();
            $attended = AttendanceRecord::where('student_id', $studentId)
                ->whereHas('session', function($q) use ($enrollment) {
                    $q->where('course_id', $enrollment->course_id);
                })
                ->whereIn('status', ['present', 'late'])
                ->count();

            $remainingSessions = max(0, $totalSessions - $attended);
            $projected = $totalSessions > 0
                ? round((($attended + $remainingSessions * 0.8) / $totalSessions) * 100)
                : 0;

            $forecasts[] = [
                'course' => $enrollment->course,
                'current' => $current,
                'projected' => $projected,
                'status' => $projected >= 75 ? 'Likely Eligible' : ($projected >= 60 ? 'Needs Improvement' : 'At Risk'),
            ];
        }

        $response = "📊 <strong>Attendance Forecast</strong><br><br>";
        foreach ($forecasts as $forecast) {
            $icon = $forecast['projected'] >= 75 ? '✅' : ($forecast['projected'] >= 60 ? '📈' : '⚠️');
            $response .= "{$icon} <strong>{$forecast['course']->course_code}</strong><br>";
            $response .= "   Current: {$forecast['current']}% → Projected: <strong>{$forecast['projected']}%</strong><br>";
            $response .= "   Status: <strong>{$forecast['status']}</strong><br><br>";
        }

        return $response;
    }

    /**
     * Get study tips
     */
    private function getStudyTips()
    {
        $studentId = Auth::id();

        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with(['course'])
            ->get();

        $tips = [];
        foreach ($enrollments as $enrollment) {
            $attendance = $enrollment->attendance_percentage ?? 0;
            $course = $enrollment->course;

            if ($attendance < 60) {
                $tips[] = "🚨 Focus on <strong>{$course->course_code}</strong> - Your attendance ({$attendance}%) is critical. Attend the next 3 sessions.";
            } elseif ($attendance < 75) {
                $tips[] = "📈 Improve <strong>{$course->course_code}</strong> - Attend 2 more sessions to reach 75% eligibility.";
            } elseif ($attendance >= 90) {
                $tips[] = "🌟 Maintain <strong>{$course->course_code}</strong> - Your attendance ({$attendance}%) is excellent!";
            }
        }

        if (empty($tips)) {
            $tips[] = "📚 Keep up your good attendance! Consider reviewing course materials regularly.";
            $tips[] = "💡 Set a study schedule and stick to it.";
        }

        $response = "💡 <strong>Personalized Study Tips</strong><br><br>";
        foreach ($tips as $tip) {
            $response .= "• " . $tip . "<br>";
        }

        $response .= "<br>📌 <strong>General Tips:</strong><br>";
        $response .= "• Review notes within 24 hours of class<br>";
        $response .= "• Join study groups for difficult subjects<br>";
        $response .= "• Take breaks during study sessions<br>";
        $response .= "• Stay consistent with attendance";

        return $response;
    }

    /**
     * Get department comparison
     */
    private function getDepartmentComparison()
    {
        $student = Auth::user();
        $studentId = $student->id;

        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with(['course.department'])
            ->get();

        if ($enrollments->isEmpty()) {
            return "📚 You are not enrolled in any courses yet.";
        }

        $yourAvg = round($enrollments->avg('attendance_percentage') ?? 0, 1);
        $deptId = $student->department_id;

        // Get department average
        $deptStudents = User::where('department_id', $deptId)
            ->where('role_id', 3)
            ->pluck('id');

        $deptAvg = Enrollment::whereIn('student_id', $deptStudents)
            ->where('status', 'approved')
            ->avg('attendance_percentage') ?? 0;
        $deptAvg = round($deptAvg, 1);

        $diff = $yourAvg - $deptAvg;
        $comparison = $diff > 5 ? 'above' : ($diff < -5 ? 'below' : 'at');

        $response = "📊 <strong>Department Comparison</strong><br><br>";
        $response .= "• Your Attendance: <strong>{$yourAvg}%</strong><br>";
        $response .= "• Department Average: <strong>{$deptAvg}%</strong><br>";
        $response .= "• You are <strong>" . abs($diff) . "%</strong> {$comparison} the department average";

        if ($diff > 5) {
            $response .= "<br><br>🌟 <strong>Excellent!</strong> You're performing above the department average. Keep it up!";
        } elseif ($diff < -5) {
            $response .= "<br><br>📈 <strong>Room for improvement.</strong> Focus on increasing your attendance to match the department average.";
        } else {
            $response .= "<br><br>✅ <strong>Good standing.</strong> You're performing at the department average.";
        }

        return $response;
    }

    /**
     * Get exam readiness
     */
    private function getExamReadiness()
    {
        $studentId = Auth::id();

        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with(['course'])
            ->get();

        if ($enrollments->isEmpty()) {
            return "📚 You are not enrolled in any courses yet.";
        }

        $ready = 0;
        $notReady = 0;
        $details = [];

        foreach ($enrollments as $enrollment) {
            $attendance = $enrollment->attendance_percentage ?? 0;
            $rollCall = $enrollment->roll_call_mark ?? 0;
            $isReady = $attendance >= 75 && $rollCall >= 5;

            if ($isReady) {
                $ready++;
            } else {
                $notReady++;
            }

            $details[] = [
                'course' => $enrollment->course,
                'attendance' => $attendance,
                'roll_call' => $rollCall,
                'ready' => $isReady,
            ];
        }

        $total = $enrollments->count();
        $percentage = $total > 0 ? round(($ready / $total) * 100) : 0;

        $response = "📝 <strong>Exam Readiness</strong><br><br>";
        $response .= "• Ready for Exam: <strong>{$ready}</strong> courses<br>";
        $response .= "• Needs Improvement: <strong>{$notReady}</strong> courses<br>";
        $response .= "• Readiness Rate: <strong>{$percentage}%</strong><br><br>";

        foreach ($details as $detail) {
            $icon = $detail['ready'] ? '✅' : '⚠️';
            $response .= "{$icon} <strong>{$detail['course']->course_code}</strong> - ";
            $response .= "Attendance: {$detail['attendance']}%, ";
            $response .= "Roll Call: {$detail['roll_call']}/10";
            if (!$detail['ready']) {
                $response .= " <span style='color:#ef4444;'>(Needs Work)</span>";
            }
            $response .= "<br>";
        }

        if ($percentage >= 80) {
            $response .= "<br>🎉 <strong>You're well prepared!</strong> Continue your good work.";
        } elseif ($percentage >= 50) {
            $response .= "<br>📚 <strong>Keep preparing!</strong> Focus on the courses marked with ⚠️.";
        } else {
            $response .= "<br>🚨 <strong>Immediate attention needed!</strong> Contact your lecturer for support.";
        }

        return $response;
    }

    /**
     * Get exam countdown
     */
    private function getExamCountdown()
    {
        // Default exam period - can be dynamic from database
        $examDate = Carbon::create(2026, 12, 15, 9, 0, 0);
        $now = Carbon::now();

        $diff = $now->diff($examDate);

        if ($now > $examDate) {
            $diff = $examDate->diff($now);
            return "⏰ <strong>Exam Period</strong><br><br>Exams started <strong>{$diff->days} days</strong> ago.<br>Good luck! 🍀";
        }

        $response = "⏰ <strong>Exam Countdown</strong><br><br>";
        $response .= "• Days: <strong>{$diff->days}</strong><br>";
        $response .= "• Hours: <strong>{$diff->h}</strong><br>";
        $response .= "• Minutes: <strong>{$diff->i}</strong><br><br>";

        if ($diff->days <= 7) {
            $response .= "🚨 <strong>Exams are coming soon!</strong> Start preparing now!";
        } elseif ($diff->days <= 30) {
            $response .= "📚 <strong>You have enough time.</strong> Create a study schedule.";
        } else {
            $response .= "✅ <strong>You have plenty of time.</strong> Stay consistent with your studies.";
        }

        return $response;
    }

    /**
     * Get lecturer information
     */
    private function getLecturerInfo($courseCode = null)
    {
        $student = Auth::user();
        $studentId = $student->id;

        $query = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with(['course', 'course.lecturer']);

        if ($courseCode) {
            $query->whereHas('course', function($q) use ($courseCode) {
                $q->where('course_code', $courseCode);
            });
        }

        $enrollments = $query->get();

        if ($enrollments->isEmpty()) {
            return "👨‍🏫 You are not enrolled in any courses.";
        }

        $response = "👨‍🏫 <strong>Your Lecturers</strong><br><br>";

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            $lecturer = $course->lecturer;
            $lecturerName = $lecturer ? $lecturer->name : 'Not assigned';
            $lecturerEmail = $lecturer ? $lecturer->email : 'N/A';

            $response .= "<strong>{$course->course_code}</strong><br>";
            $response .= "   👤 {$lecturerName}<br>";
            $response .= "   📧 <a href='mailto:{$lecturerEmail}' style='color:#800000;'>{$lecturerEmail}</a><br><br>";
        }

        return $response;
    }

    /**
     * Get semester summary
     */
    private function getSemesterSummary()
    {
        $student = Auth::user();
        $studentId = $student->id;

        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with(['course'])
            ->get();

        if ($enrollments->isEmpty()) {
            return "📚 You are not enrolled in any courses yet.";
        }

        $totalCourses = $enrollments->count();
        $eligible = $enrollments->where('eligibility_status', 'eligible')->count();
        $avgAttendance = round($enrollments->avg('attendance_percentage') ?? 0, 1);
        $avgRollCall = round($enrollments->avg('roll_call_mark') ?? 0, 1);

        $healthScore = $this->calculateHealthScore($studentId);
        $healthCategory = $this->getHealthCategory($healthScore);

        $response = "📚 <strong>Semester Summary</strong><br><br>";
        $response .= "• 📖 Total Courses: <strong>{$totalCourses}</strong><br>";
        $response .= "• ✅ Eligible: <strong>{$eligible}</strong> of {$totalCourses}<br>";
        $response .= "• 📊 Attendance: <strong>{$avgAttendance}%</strong><br>";
        $response .= "• 🎯 Roll Call: <strong>{$avgRollCall}/10</strong><br>";
        $response .= "• 💚 Health Score: <strong>{$healthScore}</strong> ({$healthCategory})<br><br>";

        if ($eligible == $totalCourses) {
            $response .= "🎉 <strong>Excellent!</strong> You are eligible for all courses!";
        } elseif ($eligible >= $totalCourses * 0.7) {
            $response .= "📈 <strong>Good standing.</strong> Maintain your attendance to stay eligible.";
        } else {
            $response .= "⚠️ <strong>Need improvement.</strong> Focus on increasing your attendance.";
        }

        return $response;
    }

    // ============================================
    // PRIVATE HELPER METHODS
    // ============================================

    /**
     * Calculate Academic Health Score
     */
    private function calculateHealthScore($studentId)
    {
        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->get();

        $total = $enrollments->count();
        if ($total == 0) return 0;

        $avgAttendance = $enrollments->avg('attendance_percentage') ?? 0;
        $avgRollCall = $enrollments->avg('roll_call_mark') ?? 0;
        $streakScore = $this->getStreakScore($studentId);
        $trendScore = $this->getTrendScore($studentId);

        $score = ($avgAttendance * 0.40) + ($avgRollCall * 10 * 0.25) + ($streakScore * 0.20) + ($trendScore * 0.15);
        return min(100, round($score, 1));
    }

    private function getHealthCategory($score)
    {
        if ($score >= 90) return 'Excellent';
        if ($score >= 75) return 'Stable';
        if ($score >= 50) return 'At Risk';
        return 'Critical';
    }

    private function getStreakScore($studentId)
    {
        $streak = $this->getConsecutiveStreak($studentId);
        if ($streak >= 13) return 100;
        if ($streak >= 9) return 80;
        if ($streak >= 6) return 60;
        if ($streak >= 3) return 40;
        return 20;
    }

    private function getTrendScore($studentId)
    {
        $records = AttendanceRecord::where('student_id', $studentId)
            ->whereIn('status', ['present', 'late'])
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(3)
            ->get();

        if ($records->count() < 2) return 75;

        $months = $records->pluck('count')->toArray();
        $diff = $months[0] - $months[1];
        $percentChange = $months[1] > 0 ? ($diff / $months[1]) * 100 : 0;

        if ($percentChange > 10) return 100;
        if ($percentChange > 5) return 75;
        if ($percentChange < -10) return 0;
        if ($percentChange < -5) return 25;
        return 50;
    }

    private function getConsecutiveStreak($studentId)
    {
        $sessions = AttendanceSession::where('status', 'ended')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $streak = 0;
        foreach ($sessions as $session) {
            $record = AttendanceRecord::where('attendance_session_id', $session->id)
                ->where('student_id', $studentId)
                ->whereIn('status', ['present', 'late'])
                ->first();

            if ($record) {
                $streak++;
            } else {
                break;
            }
        }
        return $streak;
    }

    /**
     * Calculate Risk
     */
    private function calculateRisk($studentId)
    {
        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->get();

        $avgAttendance = $enrollments->avg('attendance_percentage') ?? 0;
        $avgRollCall = $enrollments->avg('roll_call_mark') ?? 0;

        if ($avgAttendance >= 90) $attendancePoints = 0;
        elseif ($avgAttendance >= 80) $attendancePoints = 25;
        elseif ($avgAttendance >= 70) $attendancePoints = 50;
        elseif ($avgAttendance >= 60) $attendancePoints = 75;
        else $attendancePoints = 100;

        if ($avgRollCall >= 9) $rollCallPoints = 0;
        elseif ($avgRollCall >= 7) $rollCallPoints = 25;
        elseif ($avgRollCall >= 5) $rollCallPoints = 50;
        elseif ($avgRollCall >= 3) $rollCallPoints = 75;
        else $rollCallPoints = 100;

        $consecutiveAbsences = $this->getConsecutiveAbsences($studentId);
        if ($consecutiveAbsences == 0) $consecutivePoints = 0;
        elseif ($consecutiveAbsences == 1) $consecutivePoints = 25;
        elseif ($consecutiveAbsences == 2) $consecutivePoints = 50;
        elseif ($consecutiveAbsences == 3) $consecutivePoints = 75;
        else $consecutivePoints = 100;

        $trendPoints = $this->getTrendScore($studentId);
        if ($trendPoints >= 75) $trendPoints = 0;
        elseif ($trendPoints >= 50) $trendPoints = 25;
        elseif ($trendPoints >= 25) $trendPoints = 50;
        else $trendPoints = 75;

        $score = ($attendancePoints * 0.40) + ($rollCallPoints * 0.25) + ($consecutivePoints * 0.20) + ($trendPoints * 0.15);
        $score = round($score);

        $level = 'Low';
        if ($score >= 70) $level = 'High';
        elseif ($score >= 40) $level = 'Medium';

        $factors = [];
        if ($avgAttendance < 70) $factors[] = 'Attendance below 70%';
        if ($avgRollCall < 5) $factors[] = 'Roll call score below 5';
        if ($consecutiveAbsences >= 3) $factors[] = $consecutiveAbsences . ' consecutive absences';

        return [
            'score' => $score,
            'level' => $level,
            'factors' => $factors,
        ];
    }

    private function getConsecutiveAbsences($studentId)
    {
        $sessions = AttendanceSession::where('status', 'ended')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $consecutive = 0;
        foreach ($sessions as $session) {
            $record = AttendanceRecord::where('attendance_session_id', $session->id)
                ->where('student_id', $studentId)
                ->where('status', 'absent')
                ->first();

            if ($record) {
                $consecutive++;
            } else {
                break;
            }
        }
        return $consecutive;
    }

    /**
     * Generate Recommendations
     */
    private function generateRecommendations($studentId, $enrollments)
    {
        $recommendations = [];

        foreach ($enrollments as $enrollment) {
            $attendance = $enrollment->attendance_percentage ?? 0;
            $course = $enrollment->course;

            if ($attendance >= 90) {
                $recommendations[] = [
                    'course' => $course,
                    'type' => 'excellent',
                    'message' => '🌟 Excellent attendance consistency — maintain this momentum in ' . $course->course_name,
                    'priority' => 'low',
                ];
            } elseif ($attendance >= 75 && $attendance < 90) {
                $recommendations[] = [
                    'course' => $course,
                    'type' => 'good',
                    'message' => '📈 Good attendance, but 2–3 more sessions will secure your eligibility in ' . $course->course_name,
                    'priority' => 'medium',
                ];
            } elseif ($attendance >= 50 && $attendance < 75) {
                $recommendations[] = [
                    'course' => $course,
                    'type' => 'warning',
                    'message' => '⚠️ Priority focus needed in ' . $course->course_name . ' — attend next session to avoid risk',
                    'priority' => 'high',
                ];
            } elseif ($attendance < 50) {
                $recommendations[] = [
                    'course' => $course,
                    'type' => 'danger',
                    'message' => '🚨 Critical attendance warning for ' . $course->course_name . ' — consult your lecturer immediately',
                    'priority' => 'high',
                ];
            }
        }

        $consecutiveAbsences = $this->getConsecutiveAbsences($studentId);
        if ($consecutiveAbsences >= 3) {
            $recommendations[] = [
                'course' => null,
                'type' => 'warning',
                'message' => '📚 You missed ' . $consecutiveAbsences . ' consecutive sessions — review missed topics before falling behind',
                'priority' => 'high',
            ];
        }

        return $recommendations;
    }
}
