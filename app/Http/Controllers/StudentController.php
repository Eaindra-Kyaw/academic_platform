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
     * Display student dashboard with KG+12 roll call and risk
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

        $totalCourses = $enrollments->count();

        // ============================================================
        // GET LATEST EVALUATIONS FOR EACH COURSE
        // ============================================================
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
                // No evaluation yet – default
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

        // Overall stats
        $avgAttendance = $totalCourses > 0 ? round($totalAttendance / $totalCourses, 1) : 0;
        $avgRollCall = $totalCourses > 0 ? round($totalRollCall / $totalCourses, 1) : 0;

        // Consecutive streak (from latest evaluation with highest streak)
        $consecutiveStreak = 0;
        foreach ($evaluations as $eval) {
            if ($eval['consecutive_absences'] < 3 && $eval['attendance'] >= 75) {
                $consecutiveStreak = max($consecutiveStreak, 1);
            }
        }

        // ============================================================
        // ACADEMIC HEALTH SCORE
        // ============================================================
        $healthScore = $this->calculateHealthScore($studentId);
        $healthCategory = $this->getHealthCategory($healthScore);

        // ============================================================
        // RISK SCORE (from evaluations)
        // ============================================================
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

            // Collect risk factors from latest evaluation
            $latest = $latestEvals->first();
            if ($latest && $latest->risk_factors) {
                $riskFactors = is_array($latest->risk_factors) ? $latest->risk_factors : json_decode($latest->risk_factors, true);
                $riskFactors = $riskFactors ?? [];
            }
        }

        // ============================================================
        // RECENT ATTENDANCE RECORDS
        // ============================================================
        $attendanceRecords = AttendanceRecord::where('student_id', $studentId)
            ->with(['session.course'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ============================================================
        // RECOMMENDATIONS
        // ============================================================
        $recommendations = $this->generateRecommendations($studentId, $enrollments);

        // ============================================================
        // ANNOUNCEMENTS
        // ============================================================
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

        // ============================================================
        // PENDING ENROLLMENTS COUNT
        // ============================================================
        $pendingEnrollments = Enrollment::where('student_id', $studentId)
            ->where('status', 'pending')
            ->count();

        return view('student.dashboard', compact(
            'student',
            'enrollments',
            'evaluations',
            'totalCourses',
            'avgAttendance',
            'avgRollCall',
            'eligibleCount',
            'warningCount',
            'notEligibleCount',
            'lowRisk',
            'mediumRisk',
            'highRisk',
            'healthScore',
            'healthCategory',
            'riskScore',
            'riskLevel',
            'riskFactors',
            'consecutiveStreak',
            'attendanceRecords',
            'recommendations',
            'announcements',
            'pendingEnrollments'
        ));
    }

    /**
     * Display student attendance with KG+12 roll call breakdown
     */
    public function attendance()
    {
        $student = Auth::user();
        $studentId = $student->id;

        // Get attendance records with pagination
        $records = AttendanceRecord::where('student_id', $studentId)
            ->with(['session.course'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get all enrolled courses with evaluations
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

        // Attendance stats from records
        $presentCount = AttendanceRecord::where('student_id', $studentId)
            ->where('status', 'present')
            ->count();
        $lateCount = AttendanceRecord::where('student_id', $studentId)
            ->where('status', 'late')
            ->count();
        $absentCount = AttendanceRecord::where('student_id', $studentId)
            ->where('status', 'absent')
            ->count();

        return view('student.attendance', compact(
            'student',
            'records',
            'courseData',
            'overallAttendance',
            'overallRollCall',
            'presentCount',
            'lateCount',
            'absentCount',
            'totalSessions',
            'totalAttended'
        ));
    }

    /**
     * Display student's attendance history with filters
     */
    public function attendanceHistory(Request $request)
    {
        $student = Auth::user();
        $studentId = $student->id;

        $query = AttendanceRecord::where('student_id', $studentId)
            ->with(['session.course']);

        // Filter by course
        if ($request->filled('course_id')) {
            $query->whereHas('session', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get enrolled courses for filter dropdown
        $courses = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->with('course')
            ->get()
            ->pluck('course');

        return view('student.attendance-history', compact('records', 'courses'));
    }

    // ============================================================
    // PERIOD-BASED ATTENDANCE (Weekly/Monthly with navigation)
    // ============================================================
    /**
     * Attendance Period – with navigation (weekly/monthly/overall/custom)
     * FIXED: passes $allCourses and all required variables to the view.
     */
    public function attendancePeriod(Request $request)
    {
        $student = auth()->user();
        $period = $request->get('period', 'weekly');
        $offset = (int) $request->get('offset', 0);
        $courseId = $request->get('course_id');

        // ---- 1. Determine date range ----
        if ($period === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
            $periodLabel = $startDate->format('M d, Y') . ' – ' . $endDate->format('M d, Y');
        } elseif ($period === 'overall') {
            $startDate = Carbon::create(2000, 1, 1);
            $endDate   = Carbon::now()->addYear();
            $periodLabel = '📊 Semester';
        } else {
            $now = now();
            if ($period === 'weekly') {
                $now->addWeeks($offset);
                $startDate = $now->copy()->startOfWeek();
                $endDate   = $now->copy()->endOfWeek();
            } else { // monthly
                $now->addMonths($offset);
                $startDate = $now->copy()->startOfMonth();
                $endDate   = $now->copy()->endOfMonth();
            }
            $periodLabel = $startDate->format('M d, Y') . ' – ' . $endDate->format('M d, Y');
        }

        // Enforce a reasonable max range (365 days)
        if ($startDate->diffInDays($endDate) > 365) {
            $endDate = $startDate->copy()->addDays(365);
        }

        // ---- 2. Get all enrolled courses (unfiltered) ----
        $courseIds = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->pluck('course_id');

        $allCourses = Course::whereIn('id', $courseIds)
            ->where('is_active', true)
            ->get();

        // ---- 3. Apply course filter (if any) ----
        $filteredCourses = $allCourses;
        if ($courseId) {
            $filteredCourses = $allCourses->filter(fn($c) => $c->id == $courseId);
        }

        // ---- 4. Build course data with attendance ----
        $courses = collect();
        $totalStudents = 0;
        $eligibleCount = 0;
        $warningCount = 0;
        $atRiskCount = 0;
        $totalAttendanceSum = 0;
        $totalCoursesWithData = 0;

        foreach ($filteredCourses as $course) {
            // Get all ended sessions for this course within the date range
            $sessions = AttendanceSession::where('course_id', $course->id)
                ->where('status', 'ended')
                ->whereBetween('session_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get();

            $sessionIds = $sessions->pluck('id')->toArray();

            // Student's records for these sessions
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
                if ($record && in_array($record->status, ['present', 'late'])) {
                    $attendedPeriods += $periods;
                }
            }

            $attendancePercentage = $totalPeriods > 0
                ? round(($attendedPeriods / $totalPeriods) * 100, 1)
                : 0;

            $eligibility = $attendancePercentage >= 75 ? 'Eligible'
                : ($attendancePercentage >= 60 ? 'Warning' : 'Not Eligible');
            $riskLevel = $attendancePercentage >= 75 ? 'Low'
                : ($attendancePercentage >= 60 ? 'Medium' : 'High');

            $hasData = $totalPeriods > 0;

            // Build a student object (this is you) for this course
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

        $avgAttendance = $totalCoursesWithData > 0
            ? round($totalAttendanceSum / $totalCoursesWithData, 1)
            : 0;

        // ---- 5. Pass everything to the view ----
        return view('student.attendance-period', compact(
            'period',
            'offset',
            'startDate',
            'endDate',
            'periodLabel',
            'courses',
            'allCourses',        // ✅ Now passed
            'courseId',
            'totalStudents',
            'eligibleCount',
            'warningCount',
            'atRiskCount',
            'avgAttendance'
        ));
    }

    /**
     * Calculate Academic Health Score (using KG+12 roll call)
     */
    private function calculateHealthScore($studentId)
    {
        $evaluations = AttendanceEvaluation::where('student_id', $studentId)
            ->orderBy('evaluation_date', 'desc')
            ->get();

        if ($evaluations->isEmpty()) return 0;

        $avgAttendance = $evaluations->avg('attendance_percentage') ?? 0;
        $avgRollCall = $evaluations->avg('roll_call_total') ?? 0;
        $streakScore = $this->getStreakScore($studentId);
        $trendScore = $this->getTrendScore($studentId);

        // Roll call score normalized to 0-100
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
        $evaluations = AttendanceEvaluation::where('student_id', $studentId)
            ->orderBy('evaluation_date', 'desc')
            ->limit(5)
            ->get();

        $streak = 0;
        foreach ($evaluations as $eval) {
            if ($eval->consecutive_absences == 0 && $eval->attendance_percentage >= 75) {
                $streak++;
            } else {
                break;
            }
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

    /**
     * Display student timetable
     */
    public function timetable()
    {
        $student = Auth::user();
        $studentId = $student->id;

        $enrolledCourseIds = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->pluck('course_id')
            ->toArray();

        $timetableEntries = TimetableEntry::whereIn('course_id', $enrolledCourseIds)
            ->where('is_active', true)
            ->with(['course', 'course.department'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

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
     * Display student progress with KG+12 roll call
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
            $eval = AttendanceEvaluation::where('student_id', $studentId)
                ->where('course_id', $enrollment->course_id)
                ->orderBy('evaluation_date', 'desc')
                ->first();

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
    // CHATBOT METHODS (updated with KG+12 roll call)
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
     * Get bot response based on query (updated with KG+12 roll call)
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
        // ATTENDANCE QUERIES (with roll call)
        // ============================================
        if (preg_match('/\b(attendance|percentage|present|absent|attended|sessions|roll call)\b/i', $query)) {
            $evaluations = AttendanceEvaluation::where('student_id', $studentId)
                ->orderBy('evaluation_date', 'desc')
                ->get();

            if ($evaluations->isEmpty()) {
                return "📊 <strong>No attendance data available yet.</strong><br>Please attend your classes and check back later.";
            }

            $avgAttendance = round($evaluations->avg('attendance_percentage') ?? 0, 1);
            $avgRollCall = round($evaluations->avg('roll_call_total') ?? 0, 1);
            $totalRecords = AttendanceRecord::where('student_id', $studentId)->count();
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
            $response .= "• Overall Attendance: <strong>{$avgAttendance}%</strong><br>";
            $response .= "• Average Roll Call: <strong>{$avgRollCall}/10</strong><br>";
            $response .= "• ✅ Present: {$presentSessions}<br>";
            $response .= "• ⏰ Late: {$lateSessions}<br>";
            $response .= "• ❌ Absent: {$absentSessions}<br>";
            $response .= "• 📚 Total: {$totalRecords} sessions";

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
            $evaluations = AttendanceEvaluation::where('student_id', $studentId)
                ->orderBy('evaluation_date', 'desc')
                ->get();

            if ($evaluations->isEmpty()) {
                return "📚 You don't have any attendance records yet. Attend your classes first.";
            }

            $eligible = $evaluations->where('eligibility_status', 'eligible')->count();
            $total = $evaluations->count();
            $avgAttendance = round($evaluations->avg('attendance_percentage') ?? 0, 1);

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
            $evaluations = AttendanceEvaluation::where('student_id', $studentId)
                ->orderBy('evaluation_date', 'desc')
                ->get();

            if ($evaluations->isEmpty()) {
                return "⚠️ No risk data available yet. Please attend your classes.";
            }

            $avgRisk = round($evaluations->avg('risk_score') ?? 0);
            $level = AttendanceHelper::getRiskLevel($avgRisk);

            $latest = $evaluations->first();
            $factors = $latest && $latest->risk_factors ? json_decode($latest->risk_factors, true) : [];

            $response = "⚠️ <strong>Risk Analysis</strong><br>";
            $response .= "• Risk Level: <strong>{$level}</strong><br>";
            $response .= "• Risk Score: <strong>{$avgRisk}/100</strong>";

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

            $evaluations = AttendanceEvaluation::where('student_id', $studentId)
                ->orderBy('evaluation_date', 'desc')
                ->get();

            $avgAttendance = round($evaluations->avg('attendance_percentage') ?? 0, 1);
            $avgRollCall = round($evaluations->avg('roll_call_total') ?? 0, 1);

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
        // COURSE-SPECIFIC QUERIES (with roll call)
        // ============================================
        if (preg_match('/\b([A-Z]{2,5}-\d+)\b/', $query, $matches)) {
            $courseCode = $matches[1];
            return $this->getCourseInfo($courseCode);
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
     * Get course-specific information (with KG+12 roll call)
     */
    private function getCourseInfo($courseCode)
    {
        $studentId = Auth::id();

        $eval = AttendanceEvaluation::where('student_id', $studentId)
            ->whereHas('course', function($q) use ($courseCode) {
                $q->where('course_code', $courseCode);
            })
            ->with('course', 'course.lecturer')
            ->orderBy('evaluation_date', 'desc')
            ->first();

        if (!$eval) {
            return "❌ <strong>Course not found.</strong><br>You are not enrolled in <strong>{$courseCode}</strong> or no evaluation data exists.";
        }

        $course = $eval->course;

        $response = "📚 <strong>{$course->course_code} - {$course->course_name}</strong><br>";
        $response .= "• Lecturer: " . ($course->lecturer->name ?? 'N/A') . "<br>";
        $response .= "• Department: " . ($course->department->name ?? 'N/A') . "<br>";
        $response .= "• Your Attendance: <strong>{$eval->attendance_percentage}%</strong><br>";
        $response .= "• Roll Call Components:<br>";
        $response .= "   - Consistency: <strong>{$eval->consistency_marks}/6</strong><br>";
        $response .= "   - Punctuality: <strong>{$eval->punctuality_marks}/2</strong><br>";
        $response .= "   - Participation: <strong>{$eval->participation_marks}/2</strong><br>";
        $response .= "• Total Roll Call: <strong>{$eval->roll_call_total}/10</strong><br>";
        $response .= "• Status: <strong>" . ucfirst(str_replace('_', ' ', $eval->eligibility_status)) . "</strong><br>";
        $response .= "• Risk: <strong>{$eval->risk_level}</strong> ({$eval->risk_score}/100)";

        if ($eval->attendance_percentage < 60) {
            $response .= "<br><br>⚠️ <strong>Recommendation:</strong> Your attendance is below 60%. Contact your lecturer for support.";
        } elseif ($eval->attendance_percentage < 75) {
            $response .= "<br><br>📈 <strong>Recommendation:</strong> Attend upcoming sessions to reach the 75% eligibility threshold.";
        } else {
            $response .= "<br><br>✅ <strong>Great job!</strong> You're maintaining good attendance in this course.";
        }

        return $response;
    }

    /**
     * Generate Recommendations (using KG+12 evaluations)
     */
    private function generateRecommendations($studentId, $enrollments)
    {
        $recommendations = [];

        foreach ($enrollments as $enrollment) {
            $eval = AttendanceEvaluation::where('student_id', $studentId)
                ->where('course_id', $enrollment->course_id)
                ->orderBy('evaluation_date', 'desc')
                ->first();

            if (!$eval) continue;

            $attendance = $eval->attendance_percentage;
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

            // Consecutive absences
            if ($eval->consecutive_absences >= 3) {
                $recommendations[] = [
                    'course' => $course,
                    'type' => 'warning',
                    'message' => '📚 You missed ' . $eval->consecutive_absences . ' consecutive sessions in ' . $course->course_name . ' — review missed topics before falling behind',
                    'priority' => 'high',
                ];
            }
        }

        return $recommendations;
    }

    // ============================================
    // UNUSED/LEGACY METHODS (kept for compatibility)
    // ============================================

    /**
     * @deprecated Use AttendanceEvaluation instead
     */
    private function calculateRollCallMark($percentage)
    {
        // Kept for backward compatibility
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
