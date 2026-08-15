<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Department;
use App\Models\Announcement;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\AttendanceEvaluation;
use App\Models\TimetableEntry;
use App\Helpers\AttendanceHelper;
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

        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with(['course', 'course.department'])
            ->get();

        $totalCourses = $enrollments->count();

        $evaluations = [];
        $totalAttendance = 0;
        $totalRollCall = 0;
        $eligibleCount = 0;
        $warningCount = 0;
        $notEligibleCount = 0;
        $lowRisk = 0;
        $mediumRisk = 0;
        $highRisk = 0;

        foreach ($enrollments as $enrollment) {
            $eval = AttendanceEvaluation::where('student_id', $studentId)
                ->where('course_id', $enrollment->course_id)
                ->orderBy('evaluation_date', 'desc')
                ->first();

            if ($eval) {
                $evaluations[] = [
                    'course' => $enrollment->course,
                    'attendance' => $eval->attendance_percentage,
                    'roll_call_total' => $eval->roll_call_total,
                    'consistency' => $eval->consistency_marks,
                    'punctuality' => $eval->punctuality_marks,
                    'participation' => $eval->participation_marks,
                    'eligibility' => $eval->eligibility_status,
                    'risk_level' => $eval->risk_level,
                    'risk_score' => $eval->risk_score,
                    'consecutive_absences' => $eval->consecutive_absences,
                    'trend' => $eval->attendance_trend,
                    'total_sessions' => $eval->total_sessions,
                    'attended_sessions' => $eval->attended_sessions,
                ];

                $totalAttendance += $eval->attendance_percentage;
                $totalRollCall += $eval->roll_call_total;

                if ($eval->eligibility_status == 'eligible') $eligibleCount++;
                elseif ($eval->eligibility_status == 'warning') $warningCount++;
                else $notEligibleCount++;

                if ($eval->risk_level == 'Low') $lowRisk++;
                elseif ($eval->risk_level == 'Medium') $mediumRisk++;
                else $highRisk++;
            } else {
                $evaluations[] = [
                    'course' => $enrollment->course,
                    'attendance' => 0,
                    'roll_call_total' => 0,
                    'consistency' => 0,
                    'punctuality' => 0,
                    'participation' => 0,
                    'eligibility' => 'not_eligible',
                    'risk_level' => 'Low',
                    'risk_score' => 0,
                    'consecutive_absences' => 0,
                    'trend' => 'stable',
                    'total_sessions' => 0,
                    'attended_sessions' => 0,
                ];
            }
        }

        $avgAttendance = $totalCourses > 0 ? round($totalAttendance / $totalCourses, 1) : 0;
        $avgRollCall = $totalCourses > 0 ? round($totalRollCall / $totalCourses, 1) : 0;

        $consecutiveStreak = 0;
        foreach ($evaluations as $eval) {
            if ($eval['consecutive_absences'] < 3 && $eval['attendance'] >= 75) {
                $consecutiveStreak = max($consecutiveStreak, 1);
            }
        }

        $healthScore = $this->calculateHealthScore($studentId);
        $healthCategory = $this->getHealthCategory($healthScore);

        $riskScore = 0;
        $riskLevel = 'Low';
        $riskFactors = [];

        $latestEvals = AttendanceEvaluation::where('student_id', $studentId)
            ->orderBy('evaluation_date', 'desc')
            ->get();

        if ($latestEvals->count() > 0) {
            $avgRiskScore = $latestEvals->avg('risk_score') ?? 0;
            $riskScore = round($avgRiskScore);
            $riskLevel = AttendanceHelper::getRiskLevel($riskScore);

            $latest = $latestEvals->first();
            if ($latest && $latest->risk_factors) {
                $riskFactors = is_array($latest->risk_factors) ? $latest->risk_factors : json_decode($latest->risk_factors, true);
                $riskFactors = $riskFactors ?? [];
            }
        }

        $attendanceRecords = AttendanceRecord::where('student_id', $studentId)
            ->with(['session.course'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recommendations = $this->generateRecommendations($studentId, $enrollments);

        $announcements = Announcement::forRole('student')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($announcements as $announcement) {
            if (!$announcement->isReadBy($student->id)) {
                $announcement->markAsRead($student->id);
            }
        }

        $pendingEnrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'pending')
            ->count();

        return view('student.dashboard', compact(
            'student', 'enrollments', 'evaluations', 'totalCourses',
            'avgAttendance', 'avgRollCall', 'eligibleCount', 'warningCount', 'notEligibleCount',
            'lowRisk', 'mediumRisk', 'highRisk', 'healthScore', 'healthCategory',
            'riskScore', 'riskLevel', 'riskFactors', 'consecutiveStreak',
            'attendanceRecords', 'recommendations', 'announcements', 'pendingEnrollments'
        ));
    }

    public function attendance()
    {
        $student = Auth::user();
        $studentId = $student->id;

        $records = AttendanceRecord::where('student_id', $studentId)
            ->with(['session.course'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with('course')
            ->get();

        $courseData = [];
        $totalAttendance = 0;
        $totalRollCall = 0;
        $totalSessions = 0;
        $totalAttended = 0;

        foreach ($enrollments as $enrollment) {
            $eval = AttendanceEvaluation::where('student_id', $studentId)
                ->where('course_id', $enrollment->course_id)
                ->orderBy('evaluation_date', 'desc')
                ->first();

            if ($eval) {
                $courseData[] = [
                    'course' => $enrollment->course,
                    'attendance' => $eval->attendance_percentage,
                    'roll_call_total' => $eval->roll_call_total,
                    'consistency' => $eval->consistency_marks,
                    'punctuality' => $eval->punctuality_marks,
                    'participation' => $eval->participation_marks,
                    'eligibility' => $eval->eligibility_status,
                    'risk_level' => $eval->risk_level,
                    'risk_score' => $eval->risk_score,
                    'consecutive_absences' => $eval->consecutive_absences,
                    'trend' => $eval->attendance_trend,
                    'total_sessions' => $eval->total_sessions,
                    'attended_sessions' => $eval->attended_sessions,
                ];

                $totalAttendance += $eval->attendance_percentage;
                $totalRollCall += $eval->roll_call_total;
                $totalSessions += $eval->total_sessions;
                $totalAttended += $eval->attended_sessions;
            } else {
                $courseData[] = [
                    'course' => $enrollment->course,
                    'attendance' => 0,
                    'roll_call_total' => 0,
                    'consistency' => 0,
                    'punctuality' => 0,
                    'participation' => 0,
                    'eligibility' => 'not_eligible',
                    'risk_level' => 'Low',
                    'risk_score' => 0,
                    'consecutive_absences' => 0,
                    'trend' => 'stable',
                    'total_sessions' => 0,
                    'attended_sessions' => 0,
                ];
            }
        }

        $courseCount = count($courseData);
        $overallAttendance = $courseCount > 0 ? round($totalAttendance / $courseCount, 1) : 0;
        $overallRollCall = $courseCount > 0 ? round($totalRollCall / $courseCount, 1) : 0;

        $presentCount = AttendanceRecord::where('student_id', $studentId)->where('status', 'present')->count();
        $lateCount = AttendanceRecord::where('student_id', $studentId)->where('status', 'late')->count();
        $absentCount = AttendanceRecord::where('student_id', $studentId)->where('status', 'absent')->count();

        return view('student.attendance', compact(
            'student', 'records', 'courseData', 'overallAttendance', 'overallRollCall',
            'presentCount', 'lateCount', 'absentCount', 'totalSessions', 'totalAttended'
        ));
    }

    public function attendanceHistory(Request $request)
    {
        $student = Auth::user();
        $studentId = $student->id;

        $query = AttendanceRecord::where('student_id', $studentId)->with(['session.course']);
        if ($request->filled('course_id')) {
            $query->whereHas('session', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $records = $query->orderBy('created_at', 'desc')->paginate(20);
        $courses = Enrollment::where('student_id', $studentId)->where('status', 'approved')->with('course')->get()->pluck('course');

        return view('student.attendance-history', compact('records', 'courses'));
    }

    public function attendancePeriod(Request $request)
    {
        $student = auth()->user();
        $period = $request->get('period', 'weekly');
        $offset = (int) $request->get('offset', 0);
        $courseId = $request->get('course_id');

        if ($period === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
            $periodLabel = $startDate->format('M d, Y') . ' – ' . $endDate->format('M d, Y');
        } elseif ($period === 'overall') {
            $startDate = Carbon::create(2000, 1, 1);
            $endDate   = Carbon::now()->addYear();
            $periodLabel = '[Graph] Semester';
        } else {
            $now = now();
            if ($period === 'weekly') {
                $now->addWeeks($offset);
                $startDate = $now->copy()->startOfWeek();
                $endDate   = $now->copy()->endOfWeek();
            } else {
                $now->addMonths($offset);
                $startDate = $now->copy()->startOfMonth();
                $endDate   = $now->copy()->endOfMonth();
            }
            $periodLabel = $startDate->format('M d, Y') . ' – ' . $endDate->format('M d, Y');
        }

        if ($startDate->diffInDays($endDate) > 365) $endDate = $startDate->copy()->addDays(365);

        $courseIds = Enrollment::where('student_id', $student->id)->where('status', 'approved')->pluck('course_id');
        $allCourses = Course::whereIn('id', $courseIds)->where('is_active', true)->get();

        $filteredCourses = $allCourses;
        if ($courseId) $filteredCourses = $allCourses->filter(fn($c) => $c->id == $courseId);

        $courses = collect();
        $totalStudents = 0;
        $eligibleCount = 0;
        $warningCount = 0;
        $atRiskCount = 0;
        $totalAttendanceSum = 0;
        $totalCoursesWithData = 0;

        foreach ($filteredCourses as $course) {
            $sessions = AttendanceSession::where('course_id', $course->id)
                ->where('status', 'ended')
                ->whereBetween('session_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get();

            $sessionIds = $sessions->pluck('id')->toArray();
            $records = AttendanceRecord::where('student_id', $student->id)
                ->whereIn('attendance_session_id', $sessionIds)
                ->get()
                ->keyBy('attendance_session_id');

            $attendedPeriods = 0;
            $totalPeriods = 0;

            foreach ($sessions as $session) {
                $periods = $session->conducted_periods ?? 1;
                $totalPeriods += $periods;
                $record = $records->get($session->id);
                if ($record && in_array($record->status, ['present', 'late'])) $attendedPeriods += $periods;
            }

            $attendancePercentage = $totalPeriods > 0 ? round(($attendedPeriods / $totalPeriods) * 100, 1) : 0;
            $eligibility = $attendancePercentage >= 75 ? 'Eligible' : ($attendancePercentage >= 60 ? 'Warning' : 'Not Eligible');
            $riskLevel = $attendancePercentage >= 75 ? 'Low' : ($attendancePercentage >= 60 ? 'Medium' : 'High');
            $hasData = $totalPeriods > 0;

            $studentData = (object) [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'student_id' => $student->student_id,
                'attendance_percentage' => $attendancePercentage,
                'status' => $eligibility,
                'risk_level' => $riskLevel,
                'total_courses' => 1,
            ];

            $students = collect([$studentData]);

            $courses->push((object) [
                'id' => $course->id,
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'students' => $students,
            ]);

            if ($hasData) {
                $totalStudents++;
                $totalAttendanceSum += $attendancePercentage;
                $totalCoursesWithData++;
                if ($eligibility === 'Eligible') $eligibleCount++;
                elseif ($eligibility === 'Warning') $warningCount++;
                else $atRiskCount++;
            }
        }

        $avgAttendance = $totalCoursesWithData > 0 ? round($totalAttendanceSum / $totalCoursesWithData, 1) : 0;

        return view('student.attendance-period', compact(
            'period', 'offset', 'startDate', 'endDate', 'periodLabel',
            'courses', 'allCourses', 'courseId', 'totalStudents',
            'eligibleCount', 'warningCount', 'atRiskCount', 'avgAttendance'
        ));
    }

    private function calculateHealthScore($studentId)
    {
        $evaluations = AttendanceEvaluation::where('student_id', $studentId)->orderBy('evaluation_date', 'desc')->get();
        if ($evaluations->isEmpty()) return 0;

        $avgAttendance = $evaluations->avg('attendance_percentage') ?? 0;
        $avgRollCall = $evaluations->avg('roll_call_total') ?? 0;
        $streakScore = $this->getStreakScore($studentId);
        $trendScore = $this->getTrendScore($studentId);

        $rollCallScore = $avgRollCall * 10;
        $score = ($avgAttendance * 0.40) + ($rollCallScore * 0.25) + ($streakScore * 0.20) + ($trendScore * 0.15);
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
        $evaluations = AttendanceEvaluation::where('student_id', $studentId)->orderBy('evaluation_date', 'desc')->limit(5)->get();
        $streak = 0;
        foreach ($evaluations as $eval) {
            if ($eval->consecutive_absences == 0 && $eval->attendance_percentage >= 75) $streak++;
            else break;
        }
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

    public function timetable()
    {
        $student = Auth::user();
        $studentId = $student->id;

        $enrolledCourseIds = Enrollment::where('student_id', $studentId)->where('status', 'approved')->pluck('course_id')->toArray();
        $timetableEntries = TimetableEntry::whereIn('course_id', $enrolledCourseIds)
            ->where('is_active', true)
            ->with(['course', 'course.department'])
            ->orderBy('day_of_week')->orderBy('start_time')
            ->get();

        if ($timetableEntries->isEmpty()) {
            $courses = Course::whereIn('id', $enrolledCourseIds)
                ->whereNotNull('schedule_day')->whereNotNull('schedule_time')->whereNotNull('schedule_end_time')
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
                        'time' => date('h:i A', strtotime($matchedEntry->start_time)) . ' - ' . date('h:i A', strtotime($matchedEntry->end_time)),
                    ];
                } else {
                    $timetable[$dayIndex][$slot['period']] = null;
                }
            }
        }

        $enrollments = Enrollment::where('student_id', $studentId)->where('status', 'approved')->with('course')->get();

        return view('student.timetable', compact('student', 'enrollments', 'timetable', 'days', 'timeSlots', 'weekStart', 'weekEnd'));
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

    public function progress()
    {
        $student = Auth::user();
        $studentId = $student->id;

        $enrollments = Enrollment::where('student_id', $studentId)->where('status', 'approved')->with(['course'])->get();

        $courseProgress = [];
        foreach ($enrollments as $enrollment) {
            $eval = AttendanceEvaluation::where('student_id', $studentId)->where('course_id', $enrollment->course_id)->orderBy('evaluation_date', 'desc')->first();
            if ($eval) {
                $courseProgress[] = [
                    'course' => $enrollment->course,
                    'attendance' => $eval->attendance_percentage,
                    'roll_call_total' => $eval->roll_call_total,
                    'consistency' => $eval->consistency_marks,
                    'punctuality' => $eval->punctuality_marks,
                    'participation' => $eval->participation_marks,
                    'eligibility_status' => $eval->eligibility_status,
                    'risk_level' => $eval->risk_level,
                    'risk_score' => $eval->risk_score,
                    'attended' => $eval->attended_sessions,
                    'total' => $eval->total_sessions,
                ];
            } else {
                $courseProgress[] = [
                    'course' => $enrollment->course,
                    'attendance' => 0,
                    'roll_call_total' => 0,
                    'consistency' => 0,
                    'punctuality' => 0,
                    'participation' => 0,
                    'eligibility_status' => 'pending',
                    'risk_level' => 'Low',
                    'risk_score' => 0,
                    'attended' => 0,
                    'total' => 0,
                ];
            }
        }

        $totalSessions = AttendanceRecord::where('student_id', $studentId)->count();
        $presentSessions = AttendanceRecord::where('student_id', $studentId)->where('status', 'present')->count();
        $lateSessions = AttendanceRecord::where('student_id', $studentId)->where('status', 'late')->count();
        $absentSessions = AttendanceRecord::where('student_id', $studentId)->where('status', 'absent')->count();

        return view('student.progress', compact('student', 'enrollments', 'courseProgress', 'totalSessions', 'presentSessions', 'lateSessions', 'absentSessions'));
    }

    public function show(User $student)
    {
        if ($student->role_id != 3) abort(404, 'User is not a student');
        $student->load(['department', 'enrollments' => function($query) {
            $query->where('status', 'approved')->with('course');
        }]);
        return view('admin.students.show', compact('student'));
    }

    public function announcements()
    {
        $student = Auth::user();
        $announcements = Announcement::forRole('student')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        foreach ($announcements as $announcement) {
            if (!$announcement->isReadBy($student->id)) $announcement->markAsRead($student->id);
        }
        return view('student.announcements.index', compact('announcements', 'student'));
    }

    public function showAnnouncement($id)
    {
        try {
            $student = Auth::user();
            $announcement = Announcement::with('creator')->findOrFail($id);
            if (!$announcement->isReadBy($student->id)) $announcement->markAsRead($student->id);
            return view('student.announcements.show', compact('announcement', 'student'));
        } catch (\Exception $e) {
            \Log::error('Error showing announcement: ' . $e->getMessage());
            return redirect()->route('student.announcements.index')->with('error', 'Announcement not found.');
        }
    }

    public function markAnnouncementRead($id)
    {
        $student = Auth::user();
        $announcement = Announcement::findOrFail($id);
        if (!$announcement->isReadBy($student->id)) $announcement->markAsRead($student->id);
        return response()->json(['success' => true]);
    }

    public function unreadAnnouncementsCount()
    {
        $student = Auth::user();
        $count = Announcement::forRole('student')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->whereDoesntHave('readers', function($q) use ($student) {
                $q->where('user_id', $student->id);
            })
            ->count();
        return response()->json(['unread_count' => $count]);
    }

    public function inbox()
    {
        $student = Auth::user();
        $messages = \App\Models\Message::where('recipient_id', $student->id)->where('recipient_type', 'student')->orderBy('created_at', 'desc')->paginate(15);
        return view('student.messages.inbox', compact('messages', 'student'));
    }

    public function showMessage($id)
    {
        $student = Auth::user();
        $message = \App\Models\Message::where('recipient_id', $student->id)->where('recipient_type', 'student')->where('id', $id)->firstOrFail();
        if (!$message->is_read) $message->update(['is_read' => true, 'read_at' => now()]);
        return view('student.messages.show', compact('message', 'student'));
    }

    public function unreadMessagesCount()
    {
        $student = Auth::user();
        $count = \App\Models\Message::where('recipient_id', $student->id)->where('recipient_type', 'student')->where('is_read', false)->count();
        return response()->json(['unread_count' => $count]);
    }

    public function notifications()
    {
        $student = Auth::user();
        $notifications = $student->notifications()->orderBy('created_at', 'desc')->paginate(20);
        return view('student.notifications', compact('notifications', 'student'));
    }

    public function markNotificationRead($id)
    {
        $student = Auth::user();
        $notification = $student->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        $student = Auth::user();
        $student->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    public function availableCourses(Request $request)
    {
        $student = Auth::user();
        $enrolledIds = Enrollment::where('student_id', $student->id)->pluck('course_id')->toArray();
        $courses = Course::where('is_active', true)->whereNotIn('id', $enrolledIds)
            ->with(['department', 'lecturer'])
            ->when($request->search, function($q) use ($request) {
                return $q->where('course_name', 'like', '%' . $request->search . '%')->orWhere('course_code', 'like', '%' . $request->search . '%');
            })
            ->when($request->department, function($q) use ($request) {
                return $q->where('department_id', $request->department);
            })
            ->paginate(12);

        $departments = Department::orderBy('name')->get();
        return view('student.courses.available', compact('courses', 'departments'));
    }

    public function requestEnrollment($courseId)
    {
        $student = Auth::user();
        $existing = Enrollment::where('student_id', $student->id)->where('course_id', $courseId)->first();
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

    public function myEnrollments()
    {
        $student = Auth::user();
        $enrollments = Enrollment::where('student_id', $student->id)->with(['course', 'course.department', 'course.lecturer'])->orderBy('created_at', 'desc')->get();
        $totalEnrollments = $enrollments->count();
        $approvedEnrollments = $enrollments->where('status', 'approved')->count();
        $pendingEnrollments = $enrollments->where('status', 'pending')->count();
        $rejectedEnrollments = $enrollments->where('status', 'rejected')->count();
        $avgAttendance = $enrollments->where('status', 'approved')->avg('attendance_percentage') ?? 0;
        return view('student.enrollments.index', compact('enrollments', 'totalEnrollments', 'approvedEnrollments', 'pendingEnrollments', 'rejectedEnrollments', 'avgAttendance'));
    }

    public function scan()
    {
        $student = Auth::user();
        $activeSession = AttendanceSession::where('status', 'active')->where('expires_at', '>', now())->first();
        $enrolledCourseIds = Enrollment::where('student_id', $student->id)->where('status', 'approved')->pluck('course_id')->toArray();
        $enrolledCourses = Course::whereIn('id', $enrolledCourseIds)->where('is_active', true)->get();
        return view('student.attendance.scan', compact('activeSession', 'enrolledCourses'));
    }

    public function processScan(Request $request)
    {
        $request->validate(['token' => 'required|string', 'session_id' => 'required|exists:attendance_sessions,id']);
        $student = Auth::user();
        $sessionId = $request->session_id;
        $token = $request->token;

        $session = AttendanceSession::where('id', $sessionId)->where('status', 'active')->where('expires_at', '>', now())->first();
        if (!$session) return response()->json(['success' => false, 'message' => 'Invalid or expired session.'], 400);
        if ($session->session_token !== $token) return response()->json(['success' => false, 'message' => 'Invalid QR code.'], 400);

        $isEnrolled = Enrollment::where('student_id', $student->id)->where('course_id', $session->course_id)->where('status', 'approved')->exists();
        if (!$isEnrolled) return response()->json(['success' => false, 'message' => 'Not enrolled.'], 403);

        $existing = AttendanceRecord::where('student_id', $student->id)->where('attendance_session_id', $session->id)->first();
        if ($existing) return response()->json(['success' => false, 'message' => 'Already scanned.', 'already_scanned' => true], 400);
        if ($session->status !== 'active') return response()->json(['success' => false, 'message' => 'Session ended.'], 400);

        $isLate = $session->started_at && now() > $session->started_at->addMinutes(15);

        $record = AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_session_id' => $session->id,
            'status' => $isLate ? 'late' : 'present',
            'scanned_at' => now(),
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent(),
        ]);

        return response()->json(['success' => true, 'message' => $isLate ? 'Marked LATE' : 'Marked PRESENT', 'status' => $isLate ? 'late' : 'present', 'record' => $record]);
    }

    public function manualAttendance(Request $request)
    {
        $request->validate(['manual_code' => 'required|string', 'course_id' => 'required|exists:courses,id']);
        $student = Auth::user();

        $session = AttendanceSession::where('manual_code', $request->manual_code)->where('status', 'active')->where('expires_at', '>', now())->where('course_id', $request->course_id)->first();
        if (!$session) return redirect()->back()->with('error', 'Invalid manual code.');
        if (!Enrollment::where('student_id', $student->id)->where('course_id', $session->course_id)->where('status', 'approved')->exists())
            return redirect()->back()->with('error', 'Not enrolled.');
        if (AttendanceRecord::where('student_id', $student->id)->where('attendance_session_id', $session->id)->first())
            return redirect()->back()->with('error', 'Already marked.');

        $isLate = $session->started_at && now() > $session->started_at->addMinutes(15);
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

    public function checkSession(Request $request)
    {
        $session = AttendanceSession::where('id', $request->session_id)->where('status', 'active')->where('expires_at', '>', now())->first();
        if ($session) {
            return response()->json(['success' => true, 'active' => true, 'expires_at' => $session->expires_at, 'course_name' => $session->course->course_name ?? 'N/A']);
        }
        return response()->json(['success' => false, 'active' => false, 'message' => 'No active session found']);
    }

    public function semesterScan(Request $request)
    {
        $token = $request->token;
        $courseId = $request->course;
        $student = Auth::user();
        $course = Course::findOrFail($courseId);

        if (!Enrollment::where('student_id', $student->id)->where('course_id', $courseId)->where('status', 'approved')->exists())
            return redirect()->route('student.scan')->with('error', 'Not enrolled.');
        if ($course->semester_qr_token !== $token) return redirect()->route('student.scan')->with('error', 'Invalid QR code.');

        $activeSession = AttendanceSession::where('course_id', $courseId)->where('status', 'active')->where('expires_at', '>', now())->first();
        if (!$activeSession) return redirect()->route('student.scan')->with('error', 'No active session.');
        if (AttendanceRecord::where('student_id', $student->id)->where('attendance_session_id', $activeSession->id)->first())
            return redirect()->route('student.scan')->with('error', 'Already marked.');

        $isLate = $activeSession->started_at && now() > $activeSession->started_at->addMinutes(15);
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
    // CHATBOT - PROFESSIONAL AI BACKEND ENGINE
    // ============================================

    public function chatbot()
    {
        return view('student.chatbot');
    }

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

    private function getBotResponse($query, $student)
{
    $studentId = $student->id;

    // --- 1. GREETINGS ---
    if (preg_match('/\b(hi|hello|hey|good morning|good afternoon|good evening|howdy)\b/i', $query)) {
        return '<i class="bi bi-hand-wave"></i> <strong>Hello, ' . $student->name . '!</strong><br><br>I am your MTU Academic Assistant. I have access to your live academic data and can help you with:<br><br>• <i class="bi bi-bar-chart-fill"></i> <strong>Attendance analysis</strong><br>• <i class="bi bi-check-circle-fill"></i> <strong>Exam eligibility status</strong><br>• <i class="bi bi-shield-fill"></i> <strong>Risk assessment</strong><br>• <i class="bi bi-calendar-fill"></i> <strong>Daily class schedule</strong><br>• <i class="bi bi-lightbulb-fill"></i> <strong>Personalized advice</strong><br><br><i class="bi bi-lightbulb-fill"></i> <em>Try asking: "What is my attendance?" or "Am I eligible for exams?"</em>';
    }

    // --- 2. ATTENDANCE WITH INSIGHT ---
    if (preg_match('/\b(attendance|percentage|present|absent|how many sessions)\b/i', $query)) {
        $evaluations = AttendanceEvaluation::where('student_id', $studentId)->with('course')->orderBy('evaluation_date', 'desc')->get();
        if ($evaluations->isEmpty()) return '<i class="bi bi-bar-chart-fill"></i> No attendance data available yet. Please attend your classes and check back later.';

        $avgAttendance = round($evaluations->avg('attendance_percentage') ?? 0, 1);
        $presentSessions = AttendanceRecord::where('student_id', $studentId)->where('status', 'present')->count();
        $lateSessions = AttendanceRecord::where('student_id', $studentId)->where('status', 'late')->count();
        $absentSessions = AttendanceRecord::where('student_id', $studentId)->where('status', 'absent')->count();

        $response = '<i class="bi bi-bar-chart-fill"></i> <strong>Attendance Summary for ' . $student->name . '</strong><br><br>';
        $response .= '• <strong>Current Rate:</strong> ' . $avgAttendance . '%<br>';
        $response .= '• <i class="bi bi-check-circle-fill text-success"></i> Present: ' . $presentSessions . ' sessions<br>';
        $response .= '• <i class="bi bi-clock-fill text-warning"></i> Late: ' . $lateSessions . ' sessions<br>';
        $response .= '• <i class="bi bi-x-circle-fill text-danger"></i> Absent: ' . $absentSessions . ' sessions<br><br>';

        if ($avgAttendance >= 75) $response .= '<i class="bi bi-check-circle-fill text-success"></i> <strong>Great standing!</strong> You are meeting the 75% eligibility threshold. <em>Keep up the consistent performance.</em>';
        elseif ($avgAttendance >= 60) $response .= '<i class="bi bi-exclamation-triangle-fill text-warning"></i> <strong>You are close to the threshold.</strong> We recommend attending the next 2-3 sessions to secure your eligibility status.';
        else $response .= '<i class="bi bi-exclamation-triangle-fill text-danger"></i> <strong>Your attendance is critically low.</strong> Please speak with your course lecturer immediately to create a recovery plan.';

        return $response;
    }

    // --- 3. ELIGIBILITY WITH RANK ---
    if (preg_match('/\b(eligible|eligibility|exam|final|can i take)\b/i', $query)) {
        $evaluations = AttendanceEvaluation::where('student_id', $studentId)->get();
        if ($evaluations->isEmpty()) return '<i class="bi bi-book-fill"></i> You don\'t have any attendance records yet. Attend your classes first.';

        $eligible = $evaluations->where('eligibility_status', 'eligible')->count();
        $total = $evaluations->count();
        $avgAttendance = round($evaluations->avg('attendance_percentage') ?? 0, 1);

        $response = '<i class="bi bi-check-circle-fill text-success"></i> <strong>Exam Eligibility Report</strong><br><br>';
        $response .= '• <strong>Status:</strong> ' . $eligible . ' out of ' . $total . ' courses eligible<br>';
        $response .= '• <strong>Overall Attendance:</strong> ' . $avgAttendance . '%<br><br>';

        if ($eligible == $total && $total > 0) $response .= '<i class="bi bi-star-fill text-warning"></i> <strong>Excellent!</strong> You are fully eligible for all your courses. <em>You are ready for exams.</em>';
        elseif ($eligible > 0) $response .= '<i class="bi bi-clipboard-fill"></i> You are eligible for ' . $eligible . ' course(s). <em>Focus on improving attendance in your remaining courses.</em>';
        else $response .= '<i class="bi bi-exclamation-triangle-fill text-danger"></i> You are not eligible for any courses yet. <em>Immediate attendance improvement is required.</em>';

        return $response;
    }

    // --- 4. RISK ANALYSIS WITH INSIGHT ---
    if (preg_match('/\b(risk|am i at risk|danger)\b/i', $query)) {
        $evaluations = AttendanceEvaluation::where('student_id', $studentId)->get();
        if ($evaluations->isEmpty()) return '<i class="bi bi-exclamation-triangle-fill"></i> No risk data available yet. Please attend your classes first.';

        $avgRisk = round($evaluations->avg('risk_score') ?? 0);
        $level = AttendanceHelper::getRiskLevel($avgRisk);

        $response = '<i class="bi bi-shield-fill"></i> <strong>Academic Risk Assessment</strong><br><br>';
        $response .= '• <strong>Current Level:</strong> ' . $level . ' Risk<br><br>';

        if ($level === 'Low') $response .= '<i class="bi bi-check-circle-fill text-success"></i> You are in a <strong>stable position</strong>. Keep maintaining your attendance and you will stay ahead.';
        elseif ($level === 'Medium') $response .= '<i class="bi bi-bar-chart-fill text-warning"></i> <strong>Some risk detected.</strong> We recommend focusing on attendance. A 5% increase can lower your risk significantly.';
        else $response .= '<i class="bi bi-exclamation-triangle-fill text-danger"></i> <strong>High risk detected!</strong> You are advised to contact your academic advisor immediately.';

        return $response;
    }

    // --- 5. PERSONALIZED RECOMMENDATIONS ---
    if (preg_match('/\b(recommend|advice|suggest|what should i do|help)\b/i', $query)) {
        $enrollments = Enrollment::where('student_id', $studentId)->where('status', 'approved')->with('course')->get();
        $recs = $this->generateRecommendations($studentId, $enrollments);

        if (empty($recs)) return '<i class="bi bi-lightbulb-fill text-warning"></i> <strong>You\'re doing great!</strong> You are maintaining excellent academic standing. Keep up the fantastic work.';

        $response = '<i class="bi bi-lightbulb-fill text-warning"></i> <strong>Personalized Academic Advice</strong><br><br>';
        foreach ($recs as $rec) $response .= '• ' . $rec['message'] . '<br>';
        return $response;
    }

    // --- 6. TIMETABLE ---
    if (preg_match('/\b(timetable|schedule|class|when is my next class)\b/i', $query)) {
        $today = Carbon::today()->format('l');
        $entries = TimetableEntry::whereHas('course', function($q) use ($student) {
            $q->whereIn('id', Enrollment::where('student_id', $student->id)->where('status', 'approved')->pluck('course_id'));
        })->where('day_of_week', $today)->where('is_active', true)->with('course')->get();

        if ($entries->isEmpty()) return '<i class="bi bi-calendar-fill"></i> No classes scheduled for <strong>today (' . $today . ')</strong>. Enjoy your free time!';

        $response = '<i class="bi bi-calendar-fill"></i> <strong>Today\'s Schedule (' . $today . ')</strong><br><br>';
        foreach ($entries as $entry) {
            $start = Carbon::parse($entry->start_time)->format('H:i');
            $end = Carbon::parse($entry->end_time)->format('H:i');
            $room = isset($entry->room) ? $entry->room : 'N/A';
            $response .= '• <strong>' . $entry->course->course_code . '</strong> (' . $start . ' - ' . $end . ') @ ' . $room . '<br>';
        }
        return $response;
    }

    // --- 7. TEACHERS ---
    if (preg_match('/\b(teacher\s*|teachers|lecturer\s*|lecturers)\b/i', $query)) {
        $enrollments = Enrollment::where('student_id', $studentId)->where('status', 'approved')->with('course.lecturer')->get();
        if ($enrollments->isEmpty()) return '<i class="bi bi-person-fill"></i> You are not enrolled in any courses yet.';

        $response = '<i class="bi bi-person-badge-fill"></i> <strong>Your Teachers</strong><br><br>';
        foreach ($enrollments as $enrollment) {
            $name = $enrollment->course->lecturer->name ?? 'To be assigned';
            $response .= '• <strong>' . $enrollment->course->course_code . '</strong> – ' . $name . '<br>';
        }
        return $response;
    }

    // --- 8. ACADEMIC HEALTH SCORE ---
    if (preg_match('/\b(health|score|how am i doing)\b/i', $query)) {
        $healthScore = $this->calculateHealthScore($studentId);
        $category = $this->getHealthCategory($healthScore);
        $avgAttendance = round(AttendanceEvaluation::where('student_id', $studentId)->avg('attendance_percentage') ?? 0, 1);

        $response = '<i class="bi bi-heart-fill text-danger"></i> <strong>Academic Health Overview</strong><br><br>';
        $response .= '• <strong>Score:</strong> ' . $healthScore . '/100<br>';
        $response .= '• <strong>Status:</strong> ' . $category . '<br>';
        $response .= '• <strong>Attendance Average:</strong> ' . $avgAttendance . '%<br><br>';

        if ($category === 'Excellent') $response .= '<i class="bi bi-star-fill text-warning"></i> <strong>Top tier performer!</strong> You are excelling in all areas.';
        elseif ($category === 'Stable') $response .= '<i class="bi bi-bar-chart-fill"></i> You are performing well. <em>Small improvements will push you to the top.</em>';
        elseif ($category === 'At Risk') $response .= '<i class="bi bi-exclamation-triangle-fill text-warning"></i> <strong>Action needed.</strong> Focus specifically on raising your attendance.';
        else $response .= '<i class="bi bi-exclamation-triangle-fill text-danger"></i> <strong>Critical status.</strong> Immediate intervention required. See your advisor.';

        return $response;
    }

    // --- 9. ACTIVE SESSIONS ---
    if (preg_match('/\b(active session|qr|scan)\b/i', $query)) {
        $activeSession = AttendanceSession::where('status', 'active')->where('expires_at', '>', now())->first();
        if ($activeSession) {
            $remaining = Carbon::now()->diffInMinutes($activeSession->expires_at);
            return '<i class="bi bi-circle-fill text-danger"></i> <strong>Active QR Session Found</strong><br><br>Course: <strong>' . $activeSession->course->course_name . '</strong><br>Manual Code: <strong>' . $activeSession->manual_code . '</strong><br><em>Expires in ' . $remaining . ' minutes.</em>';
        } else {
            return '<i class="bi bi-circle-fill text-secondary"></i> No active sessions at the moment. Check with your lecturer.';
        }
    }

    // --- 10. HELP ---
    if (preg_match('/\b(help|faq|what can you do)\b/i', $query)) {
        return '<i class="bi bi-robot"></i> <strong>MTU Uni Bot Commands</strong><br><br>
                • <i class="bi bi-bar-chart-fill"></i> Ask \'<strong>What is my attendance?</strong>\'<br>
                • <i class="bi bi-check-circle-fill"></i> Ask \'<strong>Am I eligible for the exam?</strong>\'<br>
                • <i class="bi bi-shield-fill"></i> Ask \'<strong>What is my risk level?</strong>\'<br>
                • <i class="bi bi-lightbulb-fill"></i> Ask \'<strong>What should I do?</strong>\'<br>
                • <i class="bi bi-calendar-fill"></i> Ask \'<strong>Show my timetable</strong>\'<br>
                • <i class="bi bi-person-badge-fill"></i> Ask \'<strong>Who is my teacher?</strong>\'<br>
                • <i class="bi bi-heart-fill"></i> Ask \'<strong>What is my health score?</strong>\'<br>
                • <i class="bi bi-circle-fill"></i> Ask \'<strong>Is there an active session?</strong>\'<br><br>
                <em>Pro Tip: Try asking in plain English for the best results!</em>';
    }

    // --- DEFAULT FALLBACK ---
    return '<i class="bi bi-robot"></i> I understand you\'re asking about: <strong>"' . htmlspecialchars($query) . '"</strong><br><br>I can help with attendance, eligibility, risk, timetable, teachers, and more. Try clicking one of the pills above or rephrasing your question.';
}

    private function generateRecommendations($studentId, $enrollments)
{
    $recommendations = [];
    foreach ($enrollments as $enrollment) {
        $eval = AttendanceEvaluation::where('student_id', $studentId)->where('course_id', $enrollment->course_id)->orderBy('evaluation_date', 'desc')->first();
        if (!$eval) continue;

        $attendance = $eval->attendance_percentage;
        $course = $enrollment->course;

        if ($attendance >= 90) {
            $recommendations[] = ['type' => 'excellent', 'message' => '<i class="bi bi-star-fill text-warning"></i> Excellent consistency in <strong>' . $course->course_name . '</strong>. Keep your momentum!'];
        } elseif ($attendance >= 75 && $attendance < 90) {
            $recommendations[] = ['type' => 'good', 'message' => '<i class="bi bi-bar-chart-fill"></i> Good standing in <strong>' . $course->course_name . '</strong>. 2-3 more sessions will secure your eligibility.'];
        } elseif ($attendance >= 50 && $attendance < 75) {
            $recommendations[] = ['type' => 'warning', 'message' => '<i class="bi bi-exclamation-triangle-fill text-warning"></i> Focus needed in <strong>' . $course->course_name . '</strong>. Attend next session to avoid risk.'];
        } else {
            $recommendations[] = ['type' => 'danger', 'message' => '<i class="bi bi-exclamation-triangle-fill text-danger"></i> Critical attendance warning for <strong>' . $course->course_name . '</strong>. Consult your lecturer immediately.'];
        }

        if ($eval->consecutive_absences >= 3) {
            $recommendations[] = ['type' => 'warning', 'message' => '<i class="bi bi-book-fill"></i> You missed ' . $eval->consecutive_absences . ' consecutive sessions in <strong>' . $course->course_name . '</strong>. Review missed topics.'];
        }
    }
    return $recommendations;
}

    private function calculateRollCallMark($percentage)
    {
        if ($percentage >= 95) return 10;
        if ($percentage >= 90) return 9;
        if ($percentage >= 85) return 8;
        if ($percentage >= 80) return 7;
        if ($percentage >= 75) return 6;
        if ($percentage >= 70) return 5;
        if ($percentage >= 65) return 4;
        if ($percentage >= 60) return 3;
        if ($percentage >= 55) return 2;
        return 1;
    }
}
