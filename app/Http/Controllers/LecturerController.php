<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\QrCodeHelper;
use App\Models\Course;
use App\Models\AttendanceSession;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Announcement;
use App\Models\TimetableEntry;
use App\Models\AttendanceEvaluation;
use App\Helpers\AttendanceHelper;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LecturerController extends Controller
{
    public function dashboard()
    {
        $lecturerId = Auth::id();
        $lecturerName = Auth::user()->name;

        // Get lecturer's courses – deduplicate by ID
        $courses = Course::where('lecturer_id', $lecturerId)
                        ->orWhere('lecturer_name', 'like', '%' . $lecturerName . '%')
                        ->where('is_active', true)
                        ->get()
                        ->unique('id');

        $courseIds = $courses->pluck('id')->toArray();

        // Get all enrolled students for manual attendance dropdown
        $students = User::whereHas('enrollments', function($query) use ($courseIds) {
            $query->whereIn('course_id', $courseIds)
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

        // ============================================================
        // CALCULATE OVERALL STATS FROM REAL EVALUATIONS
        // ============================================================
        foreach ($courses as $course) {
            $enrolledCount = Enrollment::where('course_id', $course->id)
                                      ->where('status', 'approved')
                                      ->count();
            $totalStudents += $enrolledCount;

            // Get latest evaluation for each student in this course
            $evaluations = AttendanceEvaluation::where('course_id', $course->id)
                ->where('evaluation_date', function($q) {
                    $q->selectRaw('MAX(evaluation_date)')
                        ->from('attendance_evaluations as ae2')
                        ->whereColumn('ae2.student_id', 'attendance_evaluations.student_id')
                        ->whereColumn('ae2.course_id', 'attendance_evaluations.course_id');
                })
                ->get();

            $courseAvg = $evaluations->avg('attendance_percentage') ?? 0;
            $avgAttendance += $courseAvg;

            // Count high risk students
            $highRiskCount = $evaluations->where('risk_level', 'High')->count();
            $atRiskStudents += $highRiskCount;
            if ($highRiskCount > 0) {
                $lowAlerts++;
            }

            // Store real data on the course object for the view
            $course->average_attendance = round($courseAvg, 1);
            $course->student_count = $enrolledCount;
            $course->at_risk_count = $highRiskCount;
        }

        if ($courses->count() > 0) {
            $avgAttendance = round($avgAttendance / $courses->count(), 1);
            $courseEngagement = round($avgAttendance, 0);
        }

        // ============================================================
        // At-risk students list – deduplicated by student ID
        // ============================================================
        $atRiskList = collect();
        $seenStudentIds = [];

        foreach ($courses as $course) {
            // Get all evaluations for this course with risk Medium or High
            $evaluations = AttendanceEvaluation::where('course_id', $course->id)
                ->whereIn('risk_level', ['Medium', 'High'])
                ->with('student')
                ->orderBy('risk_score', 'desc')
                ->get();

            foreach ($evaluations as $eval) {
                $studentId = $eval->student->id ?? 0;
                // Skip if we already have this student
                if (in_array($studentId, $seenStudentIds)) {
                    continue;
                }
                $seenStudentIds[] = $studentId;

                $atRiskList->push((object)[
                    'student' => $eval->student,
                    'attendance_percentage' => $eval->attendance_percentage,
                    'roll_call_total' => $eval->roll_call_total,
                    'risk_level' => $eval->risk_level,
                    'risk_score' => $eval->risk_score,
                    'consistency' => $eval->consistency_marks,
                    'punctuality' => $eval->punctuality_marks,
                    'participation' => $eval->participation_marks,
                    'consecutive_absences' => $eval->consecutive_absences,
                    'trend' => $eval->attendance_trend,
                ]);
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
        $eval = AttendanceEvaluation::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->orderBy('evaluation_date', 'desc')
            ->first();

        return $eval ? $eval->attendance_percentage : 0;
    }

    // ============================================================
    // ATTENDANCE OVERVIEW (formerly "students" page)
    // Supports period filtering: weekly, monthly, custom, overall
    // PERIOD-BASED (uses AttendanceHelper::calculateAttendanceForPeriod)
    // ============================================================
    public function monitoring(Request $request)
    {
        $lecturer = Auth::user();
        $lecturerId = $lecturer->id;
        $lecturerName = $lecturer->name;

        $period = $request->input('period', 'weekly');
        $offset = (int) $request->input('offset', 0);
        $courseId = $request->input('course_id');

        $startDate = null;
        $endDate = null;
        $periodLabel = 'This Week';

        if ($period === 'weekly') {
            $startDate = Carbon::now()->startOfWeek()->addWeeks($offset);
            $endDate = Carbon::now()->endOfWeek()->addWeeks($offset);
            $periodLabel = $startDate->format('M d, Y') . ' – ' . $endDate->format('M d, Y');
        } elseif ($period === 'monthly') {
            $startDate = Carbon::now()->startOfMonth()->addMonths($offset);
            $endDate = Carbon::now()->endOfMonth()->addMonths($offset);
            $periodLabel = $startDate->format('M d, Y') . ' – ' . $endDate->format('M d, Y');
        } elseif ($period === 'custom') {
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(7);
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
            $periodLabel = $startDate->format('M d, Y') . ' – ' . $endDate->format('M d, Y');
        } elseif ($period === 'overall') {
            $periodLabel = 'Semester';
        }

        // Get ALL lecturer's courses (for dropdown)
        $allCourses = Course::where('lecturer_id', $lecturerId)
            ->orWhere('lecturer_name', 'like', '%' . $lecturerName . '%')
            ->where('is_active', true)
            ->get()
            ->unique('id')
            ->values();

        // Filter courses if a specific one is selected
        if (!empty($courseId)) {
            $courses = $allCourses->filter(function($course) use ($courseId) {
                return $course->id == $courseId;
            });
        } else {
            $courses = $allCourses;
        }

        $courseIds = $courses->pluck('id')->toArray();

        if (empty($courseIds)) {
            $students = collect();
            $totalStudents = 0;
            $eligibleCount = 0;
            $warningCount = 0;
            $atRiskCount = 0;
            $avgAttendance = 0;

            return view('lecturer.students', compact(
                'students',
                'courses',
                'allCourses',
                'totalStudents',
                'eligibleCount',
                'warningCount',
                'atRiskCount',
                'avgAttendance',
                'period',
                'periodLabel',
                'offset',
                'courseId',
                'startDate',
                'endDate'
            ));
        }

        // Get all students enrolled in these courses
        $students = User::whereHas('enrollments', function($query) use ($courseIds) {
            $query->whereIn('course_id', $courseIds)
                  ->where('status', 'approved');
        })->with(['enrollments' => function($query) use ($courseIds) {
            $query->whereIn('course_id', $courseIds)
                  ->where('status', 'approved');
        }])->get();

        // Compute attendance and risk per student (PERIOD-BASED)
        foreach ($students as $student) {
            $totalAttendance = 0;
            $totalCourses = 0;
            $avgRollCall = 0;
            $consistencyTotal = 0;
            $punctualityTotal = 0;
            $participationTotal = 0;
            $totalRiskScore = 0;
            $highestRisk = 'Low';

            foreach ($student->enrollments as $enrollment) {
                $enrollmentCourseId = $enrollment->course_id;

                if ($period === 'overall') {
                    // Use evaluation data (already period-based)
                    $eval = AttendanceEvaluation::where('student_id', $student->id)
                        ->where('course_id', $enrollmentCourseId)
                        ->orderBy('evaluation_date', 'desc')
                        ->first();

                    if ($eval) {
                        $attPercentage = $eval->attendance_percentage;
                        $rollCallTotal = $eval->roll_call_total;
                        $consistency = $eval->consistency_marks;
                        $punctuality = $eval->punctuality_marks;
                        $participation = $eval->participation_marks;
                        $eligibility = $eval->eligibility_status;
                        $consecutiveAbsences = $eval->consecutive_absences ?? 0;
                        $trend = $eval->attendance_trend ?? 'stable';

                        $riskLevel = AttendanceHelper::getRiskLevelFromAttendance($attPercentage);
                        $riskScore = AttendanceHelper::calculateRiskScore(
                            $attPercentage,
                            $rollCallTotal,
                            $consecutiveAbsences,
                            $trend
                        );

                        $enrollment->attendance_percentage = $attPercentage;
                        $enrollment->roll_call_total = $rollCallTotal;
                        $enrollment->consistency_marks = $consistency;
                        $enrollment->punctuality_marks = $punctuality;
                        $enrollment->participation_marks = $participation;
                        $enrollment->eligibility_status = $eligibility;
                        $enrollment->risk_level = $riskLevel;
                        $enrollment->risk_score = $riskScore;

                        $totalAttendance += $attPercentage;
                        $avgRollCall += $rollCallTotal;
                        $consistencyTotal += $consistency;
                        $punctualityTotal += $punctuality;
                        $participationTotal += $participation;
                        $totalRiskScore += $riskScore;
                        $totalCourses++;

                        if ($riskLevel === 'High') $highestRisk = 'High';
                        elseif ($riskLevel === 'Medium' && $highestRisk !== 'High') $highestRisk = 'Medium';
                    } else {
                        $enrollment->attendance_percentage = 0;
                        $enrollment->roll_call_total = 0;
                        $enrollment->consistency_marks = 0;
                        $enrollment->punctuality_marks = 0;
                        $enrollment->participation_marks = 0;
                        $enrollment->eligibility_status = 'not_eligible';
                        $enrollment->risk_level = 'Low';
                        $enrollment->risk_score = 0;
                    }
                } else {
                    // PERIOD-BASED helper – sums conducted_periods
                    $data = AttendanceHelper::calculateAttendanceForPeriod(
                        $student->id,
                        $enrollmentCourseId,
                        $startDate,
                        $endDate
                    );

                    $attPercentage = $data['percentage'];
                    $lateCount = AttendanceRecord::where('student_id', $student->id)
                        ->whereHas('session', function($q) use ($enrollmentCourseId, $startDate, $endDate) {
                            $q->where('course_id', $enrollmentCourseId)
                              ->whereBetween('session_date', [$startDate, $endDate]);
                        })
                        ->where('status', 'late')
                        ->count();

                    $rollCall = AttendanceHelper::calculateRollCallMark($attPercentage, $lateCount, 1.5);
                    $eligibility = AttendanceHelper::getEligibilityStatus($attPercentage);
                    $consecutiveAbsences = AttendanceHelper::getConsecutiveAbsences($student->id, $enrollmentCourseId);
                    $trend = AttendanceHelper::getAttendanceTrend($student->id, $enrollmentCourseId);
                    $riskLevel = AttendanceHelper::getRiskLevelFromAttendance($attPercentage);
                    $riskScore = AttendanceHelper::calculateRiskScore(
                        $attPercentage,
                        $rollCall['total'],
                        $consecutiveAbsences,
                        $trend
                    );

                    $enrollment->attendance_percentage = $attPercentage;
                    $enrollment->roll_call_total = $rollCall['total'];
                    $enrollment->consistency_marks = $rollCall['consistency'];
                    $enrollment->punctuality_marks = $rollCall['punctuality'];
                    $enrollment->participation_marks = $rollCall['participation'];
                    $enrollment->eligibility_status = $eligibility;
                    $enrollment->risk_level = $riskLevel;
                    $enrollment->risk_score = $riskScore;

                    $totalAttendance += $attPercentage;
                    $avgRollCall += $rollCall['total'];
                    $consistencyTotal += $rollCall['consistency'];
                    $punctualityTotal += $rollCall['punctuality'];
                    $participationTotal += $rollCall['participation'];
                    $totalRiskScore += $riskScore;
                    $totalCourses++;

                    if ($riskLevel === 'High') $highestRisk = 'High';
                    elseif ($riskLevel === 'Medium' && $highestRisk !== 'High') $highestRisk = 'Medium';
                }
            }

            // Overall stats per student
            if ($totalCourses > 0) {
                $student->attendance_percentage = round($totalAttendance / $totalCourses, 1);
                $student->roll_call_total = round($avgRollCall / $totalCourses, 1);
                $student->consistency_marks = round($consistencyTotal / $totalCourses, 1);
                $student->punctuality_marks = round($punctualityTotal / $totalCourses, 1);
                $student->participation_marks = round($participationTotal / $totalCourses, 1);
                $student->risk_level = $highestRisk;
                $student->risk_score = round($totalRiskScore / $totalCourses, 1);
                $student->status = $student->attendance_percentage >= 75 ? 'Eligible'
                    : ($student->attendance_percentage >= 60 ? 'Warning' : 'At Risk');
            } else {
                $student->attendance_percentage = 0;
                $student->roll_call_total = 0;
                $student->consistency_marks = 0;
                $student->punctuality_marks = 0;
                $student->participation_marks = 0;
                $student->risk_level = 'Low';
                $student->risk_score = 0;
                $student->status = 'Not Evaluated';
            }
            $student->total_courses = $totalCourses;
        }

        // Statistics
        $totalStudents = $students->count();
        $eligibleCount = $students->where('status', 'Eligible')->count();
        $warningCount = $students->where('status', 'Warning')->count();
        $atRiskCount = $students->where('status', 'At Risk')->count();
        $avgAttendance = $totalStudents > 0 ? round($students->avg('attendance_percentage'), 1) : 0;

        return view('lecturer.students', compact(
            'students',
            'courses',
            'allCourses',
            'totalStudents',
            'eligibleCount',
            'warningCount',
            'atRiskCount',
            'avgAttendance',
            'period',
            'periodLabel',
            'offset',
            'courseId',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Alias for monitoring() – keeping backward compatibility
     */
    public function students(Request $request)
    {
        return $this->monitoring($request);
    }

    // ============================================================
    // PERIOD-BASED COURSE ATTENDANCE (Detailed per-course view)
    // ============================================================
    public function courseAttendancePeriod($courseId, Request $request)
    {
        $lecturer = Auth::user();

        $course = Course::where('id', $courseId)
            ->where(function($q) use ($lecturer) {
                $q->where('lecturer_id', $lecturer->id)
                  ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%');
            })
            ->firstOrFail();

        $period = $request->input('period', 'weekly');
        $offset = (int) $request->input('offset', 0);

        if ($period === 'weekly') {
            $start = Carbon::now()->startOfWeek()->addWeeks($offset);
            $end = Carbon::now()->endOfWeek()->addWeeks($offset);
        } elseif ($period === 'monthly') {
            $start = Carbon::now()->startOfMonth()->addMonths($offset);
            $end = Carbon::now()->endOfMonth()->addMonths($offset);
        } else {
            $start = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(7);
            $end = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        }

        // Get all approved students for this course
        $students = User::whereHas('enrollments', function($q) use ($courseId) {
            $q->where('course_id', $courseId)->where('status', 'approved');
        })->get();

        $studentData = [];
        foreach ($students as $student) {
            $data = AttendanceHelper::calculateAttendanceForPeriod($student->id, $courseId, $start, $end);
            $studentData[] = [
                'student' => $student,
                'attendance' => $data['percentage'],
                'attended' => $data['attended'],
                'total' => $data['total'],
                'eligibility' => AttendanceHelper::getEligibilityStatus($data['percentage']),
            ];
        }

        // Sort by attendance descending
        usort($studentData, function($a, $b) {
            return $b['attendance'] - $a['attendance'];
        });

        // Calculate roll call average for the course
        $avgRollCall = 0;
        foreach ($students as $student) {
            $lateCount = AttendanceRecord::where('student_id', $student->id)
                ->whereHas('session', function($q) use ($courseId, $start, $end) {
                    $q->where('course_id', $courseId)
                      ->whereBetween('session_date', [$start, $end]);
                })
                ->where('status', 'late')
                ->count();

            $data = AttendanceHelper::calculateAttendanceForPeriod($student->id, $courseId, $start, $end);
            $rollCall = AttendanceHelper::calculateRollCallMark($data['percentage'], $lateCount, 1.5);
            $avgRollCall += $rollCall['total'];
        }
        $avgRollCall = count($students) > 0 ? round($avgRollCall / count($students), 1) : 0;

        $courseStats = [
            'total_students' => $students->count(),
            'avg_attendance' => count($studentData) > 0 ? round(array_sum(array_column($studentData, 'attendance')) / count($studentData), 1) : 0,
            'total_periods' => array_sum(array_column($studentData, 'total')),
            'eligible' => count(array_filter($studentData, function($s) { return $s['eligibility'] === 'eligible'; })),
            'at_risk' => count(array_filter($studentData, function($s) { return $s['eligibility'] === 'not_eligible'; })),
            'avg_roll_call' => $avgRollCall,
        ];

        $periodLabel = $start->format('M d, Y') . ' – ' . $end->format('M d, Y');

        return view('lecturer.course-attendance-period', compact(
            'course',
            'studentData',
            'period',
            'offset',
            'start',
            'end',
            'periodLabel',
            'courseStats'
        ));
    }

    // ============================================================
    // QR SESSION MANAGEMENT
    // ============================================================

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

    // ============================================================
    // SCHEDULE & REPORTS
    // ============================================================

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
            ->get()
            ->unique('id');

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
            $evaluations = AttendanceEvaluation::where('course_id', $course->id)
                ->where('evaluation_date', function($q) {
                    $q->selectRaw('MAX(evaluation_date)')
                        ->from('attendance_evaluations as ae2')
                        ->whereColumn('ae2.student_id', 'attendance_evaluations.student_id')
                        ->whereColumn('ae2.course_id', 'attendance_evaluations.course_id');
                })
                ->get();

            $courseStats[$course->id] = [
                'name' => $course->course_name,
                'code' => $course->course_code,
                'sessions' => AttendanceSession::where('course_id', $course->id)->count(),
                'attendance' => round($evaluations->avg('attendance_percentage') ?? 0),
                'students' => Enrollment::where('course_id', $course->id)->where('status', 'approved')->count(),
                'avg_roll_call' => round($evaluations->avg('roll_call_total') ?? 0, 1),
                'high_risk' => $evaluations->where('risk_level', 'High')->count(),
                'eligible' => $evaluations->where('eligibility_status', 'eligible')->count(),
                'warning' => $evaluations->where('eligibility_status', 'warning')->count(),
                'not_eligible' => $evaluations->where('eligibility_status', 'not_eligible')->count(),
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

    // ============================================================
    // ANNOUNCEMENTS
    // ============================================================

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
            ->get()
            ->unique('id');

        return view('lecturer.announcements.index', compact('announcements', 'courses'));
    }

    public function showAnnouncement($id)
    {
        $user = Auth::user();

        $announcement = Announcement::findOrFail($id);

        if (!$announcement->isReadBy($user->id)) {
            $announcement->markAsRead($user->id);
        }

        $courses = Course::where('lecturer_id', Auth::id())
            ->where('is_active', true)
            ->get()
            ->unique('id');

        return view('lecturer.announcements.show', compact('announcement', 'user', 'courses'));
    }

    // ============================================================
    // TIMETABLE METHODS
    // ============================================================

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

    private function calculateStats($courses, $scheduledCourses, $timetable)
    {
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
                if ($slot !== null) $usedSlots++;
            }
        }
        $stats['free_periods'] = $totalSlots - $usedSlots;

        return $stats;
    }

    public function timetable(Request $request)
    {
        $lecturer = Auth::user();
        $lecturerId = $lecturer->id;

        $academicYear = $request->input('academic_year');
        $semester = $request->input('semester');
        $sessionType = $request->input('session_type');

        $courses = Course::where('lecturer_id', $lecturerId)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->where('is_active', true)
            ->get()
            ->unique('id');

        $timetableEntriesQuery = TimetableEntry::where('lecturer_id', $lecturerId)
            ->when($academicYear, function($q) use ($academicYear) {
                return $q->where('academic_year', $academicYear);
            })
            ->when($semester, function($q) use ($semester) {
                return $q->where('semester', $semester);
            })
            ->when($sessionType, function($q) use ($sessionType) {
                return $q->where('session_type', $sessionType);
            });

        $timetableEntries = $timetableEntriesQuery
            ->with(['course', 'course.department'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // FALLBACK: If no timetable entries, use courses table
        if ($timetableEntries->isEmpty()) {
            $scheduledCourses = Course::where('lecturer_id', $lecturerId)
                ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
                ->where('is_active', true)
                ->whereNotNull('schedule_day')
                ->whereNotNull('schedule_time')
                ->whereNotNull('schedule_end_time')
                ->get()
                ->unique('id');

            $timetableEntries = $scheduledCourses->map(function($course) {
                $entry = new TimetableEntry();
                $entry->course_id = $course->id;
                $entry->lecturer_id = $course->lecturer_id;
                $entry->department_id = $course->department_id;
                $entry->academic_year = $course->academic_year;
                $entry->semester = $course->semester;
                $entry->year_level = $course->year;
                $entry->day_of_week = $course->schedule_day;
                $entry->start_time = $course->schedule_time;
                $entry->end_time = $course->schedule_end_time;
                $entry->room = $course->room;
                $entry->session_type = 'lecture';
                $entry->course = $course;
                return $entry;
            });

            if ($academicYear) {
                $timetableEntries = $timetableEntries->filter(function($entry) use ($academicYear) {
                    return $entry->academic_year === $academicYear;
                });
            }
            if ($semester) {
                $timetableEntries = $timetableEntries->filter(function($entry) use ($semester) {
                    return $entry->semester === $semester;
                });
            }
            if ($sessionType) {
                $timetableEntries = $timetableEntries->filter(function($entry) use ($sessionType) {
                    return $entry->session_type === $sessionType;
                });
            }
        }

        // Get scheduled courses (for stats and available courses)
        $scheduledCourses = Course::where('lecturer_id', $lecturerId)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->where('is_active', true)
            ->whereNotNull('schedule_day')
            ->whereNotNull('schedule_time')
            ->whereNotNull('schedule_end_time')
            ->orderBy('schedule_day')
            ->orderBy('schedule_time')
            ->get()
            ->unique('id');

        $availableCourses = $courses;

        $days = $this->getDays();
        $timeSlots = $this->getTimeSlots();

        $today = now();
        $weekStart = $today->copy()->startOfWeek();
        $weekEnd = $today->copy()->endOfWeek();

        // Build timetable grid from timetable_entries
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
                        'year' => $matchedEntry->year_level ?? '',
                        'session_type' => $matchedEntry->session_type ?? 'lecture',
                        'entry_id' => $matchedEntry->id,
                    ];
                } else {
                    $timetable[$dayIndex][$slot['period']] = null;
                }
            }
        }

        // Fallback: Merge from courses table if no timetable_entries
        if ($timetableEntries->isEmpty()) {
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
                            $timetable[$dayIndex][$firstSlot] = [
                                'course_name' => $course->course_name ?? 'Unknown',
                                'course_code' => $course->course_code ?? 'N/A',
                                'room' => $course->room ?? 'N/A',
                                'time' => date('h:i A', strtotime($course->schedule_time)) . ' - ' .
                                         date('h:i A', strtotime($course->schedule_end_time)),
                                'year' => $course->year ?? '',
                                'session_type' => 'lecture',
                                'course_id' => $course->id,
                            ];

                            for ($i = 1; $i < count($coveredSlots); $i++) {
                                $slotPeriod = $coveredSlots[$i];
                                $timetable[$dayIndex][$slotPeriod] = 'used';
                            }
                        }
                    }
                }
            }

            // Clean up 'used' slots
            foreach ($days as $dayIndex => $day) {
                foreach ($timeSlots as $slot) {
                    if (isset($timetable[$dayIndex][$slot['period']]) && $timetable[$dayIndex][$slot['period']] === 'used') {
                        $timetable[$dayIndex][$slot['period']] = null;
                    }
                }
            }
        }

        // Next class
        $nextClass = null;
        $now = now();
        $currentDayName = $now->format('l');
        $currentTime = $now->format('H:i:s');

        $todayEntries = $timetableEntries->filter(function ($entry) use ($currentDayName) {
            return strtolower(trim($entry->day_of_week)) === strtolower(trim($currentDayName));
        })->sortBy('start_time');

        foreach ($todayEntries as $entry) {
            if ($entry->start_time > $now) {
                $nextClass = [
                    'course_name' => $entry->course->course_name ?? 'Unknown',
                    'course_code' => $entry->course->course_code ?? 'N/A',
                    'room' => $entry->room ?? 'N/A',
                    'time' => date('h:i A', strtotime($entry->start_time)) . ' - ' .
                             date('h:i A', strtotime($entry->end_time)),
                    'start_time' => $entry->start_time->toDateTimeString(),
                    'day' => $entry->day_of_week,
                ];
                break;
            }
        }

        if (!$nextClass) {
            $tomorrow = $now->copy()->addDay();
            $tomorrowDayName = $tomorrow->format('l');
            $tomorrowEntries = $timetableEntries->filter(function ($entry) use ($tomorrowDayName) {
                return strtolower(trim($entry->day_of_week)) === strtolower(trim($tomorrowDayName));
            })->sortBy('start_time');
            if ($tomorrowEntries->isNotEmpty()) {
                $entry = $tomorrowEntries->first();
                $nextClass = [
                    'course_name' => $entry->course->course_name ?? 'Unknown',
                    'course_code' => $entry->course->course_code ?? 'N/A',
                    'room' => $entry->room ?? 'N/A',
                    'time' => date('h:i A', strtotime($entry->start_time)) . ' - ' .
                             date('h:i A', strtotime($entry->end_time)),
                    'start_time' => \Carbon\Carbon::parse($entry->start_time)->addDay()->toDateTimeString(),
                    'day' => $entry->day_of_week,
                    'is_tomorrow' => true,
                ];
            }
        }

        // Statistics
        $stats = $this->calculateStats($courses, $scheduledCourses, $timetable);

        // Filter options
        $academicYears = TimetableEntry::where('lecturer_id', $lecturerId)
            ->select('academic_year')
            ->distinct()
            ->pluck('academic_year')
            ->filter()
            ->values();

        $semesters = TimetableEntry::where('lecturer_id', $lecturerId)
            ->select('semester')
            ->distinct()
            ->pluck('semester')
            ->filter()
            ->values();

        $sessionTypes = ['lecture', 'tutorial', 'lab', 'seminar', 'workshop', 'other'];

        return view('lecturer.timetable', compact(
            'courses',
            'scheduledCourses',
            'availableCourses',
            'timetableEntries',
            'timetable',
            'days',
            'timeSlots',
            'weekStart',
            'weekEnd',
            'nextClass',
            'stats',
            'academicYears',
            'semesters',
            'sessionTypes',
            'academicYear',
            'semester',
            'sessionType'
        ));
    }

    /**
     * Manage Timetable - Shows all courses and timetable entries
     */
    public function manageTimetable()
    {
        $lecturer = Auth::user();
        $lecturerId = $lecturer->id;
        $lecturerName = $lecturer->name;

        // All courses assigned to this lecturer
        $allCourses = Course::where('lecturer_id', $lecturerId)
            ->orWhere('lecturer_name', 'like', '%' . $lecturerName . '%')
            ->where('is_active', true)
            ->orderBy('course_code', 'asc')
            ->get()
            ->unique('id');

        // Get all timetable entries for this lecturer with course relation
        $scheduledEntries = TimetableEntry::where('lecturer_id', $lecturerId)
            ->with('course')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('lecturer.timetable-manage', compact('allCourses', 'scheduledEntries'));
    }

    /**
     * Add a course session to the timetable
     */
    public function addToTimetable(Request $request)
    {
        $request->validate([
            'course_code' => 'required|exists:courses,course_code',
            'schedule_day' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'schedule_time' => 'required|date_format:H:i',
            'schedule_end_time' => 'required|date_format:H:i|after:schedule_time',
            'room' => 'nullable|string|max:50',
            'session_type' => 'nullable|string|in:lecture,tutorial,lab,seminar,workshop,other',
        ]);

        if ($request->schedule_time === $request->schedule_end_time) {
            return redirect()->back()->with('error', '⚠️ Start time and end time cannot be the same!');
        }

        // Find the course by code (must belong to this lecturer)
        $course = Course::where('course_code', $request->course_code)
            ->where(function($q) {
                $q->where('lecturer_id', Auth::id())
                  ->orWhere('lecturer_name', 'like', '%' . Auth::user()->name . '%');
            })
            ->first();

        if (!$course) {
            return redirect()->back()->with('error', '❌ Course not found or not assigned to you.');
        }

        // Check for time conflict with existing timetable entries for this lecturer on that day
        $conflict = TimetableEntry::where('lecturer_id', Auth::id())
            ->where('day_of_week', $request->schedule_day)
            ->where(function($q) use ($request) {
                $q->where(function($sub) use ($request) {
                    $sub->where('start_time', '<=', $request->schedule_time)
                        ->where('end_time', '>', $request->schedule_time);
                })->orWhere(function($sub) use ($request) {
                    $sub->where('start_time', '<', $request->schedule_end_time)
                        ->where('end_time', '>=', $request->schedule_end_time);
                });
            })
            ->first();

        if ($conflict) {
            $conflictCourse = $conflict->course;
            return redirect()->back()->with('error',
                '⚠️ Time conflict with "' . ($conflictCourse ? $conflictCourse->course_name : 'Unknown') . '" on ' . $request->schedule_day
            );
        }

        // Create new timetable entry
        $entry = TimetableEntry::create([
            'course_id' => $course->id,
            'lecturer_id' => Auth::id(),
            'department_id' => $course->department_id,
            'academic_year' => $course->academic_year,
            'semester' => $course->semester,
            'year_level' => $course->year,
            'day_of_week' => $request->schedule_day,
            'start_time' => $request->schedule_time,
            'end_time' => $request->schedule_end_time,
            'room' => $request->room ?? $course->room,
            'session_type' => $request->session_type ?? 'lecture',
            'is_active' => true,
        ]);

        // Optionally update the course table if this is its first schedule (backward compatibility)
        if (is_null($course->schedule_day)) {
            $course->schedule_day = $request->schedule_day;
            $course->schedule_time = $request->schedule_time;
            $course->schedule_end_time = $request->schedule_end_time;
            $course->room = $request->room ?? $course->room;
            $course->save();
        }

        return redirect()->route('lecturer.timetable.manage')
            ->with('success', '✅ ' . $course->course_code . ' added to timetable!');
    }

    /**
     * Remove a specific timetable entry by its ID
     */
    public function removeFromTimetable($id)
    {
        try {
            $entry = TimetableEntry::where('id', $id)
                ->where('lecturer_id', Auth::id())
                ->first();

            if (!$entry) {
                return redirect()->route('lecturer.timetable.manage')
                    ->with('error', '❌ Timetable entry not found!');
            }

            $course = $entry->course;
            $courseCode = $course ? $course->course_code : 'Unknown';

            // Delete the entry
            $entry->delete();

            // If no more entries for this course, clear the course schedule fields
            $remainingEntries = TimetableEntry::where('course_id', $entry->course_id)
                ->where('lecturer_id', Auth::id())
                ->count();

            if ($remainingEntries == 0 && $course) {
                $course->schedule_day = null;
                $course->schedule_time = null;
                $course->schedule_end_time = null;
                $course->save();
            }

            return redirect()->route('lecturer.timetable.manage')
                ->with('success', '🗑️ "' . $courseCode . '" session removed from timetable!');

        } catch (\Exception $e) {
            return redirect()->route('lecturer.timetable.manage')
                ->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    public function addMultipleSessions(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'sessions' => 'required|array|min:1',
            'sessions.*.day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'sessions.*.start_time' => 'required|date_format:H:i',
            'sessions.*.end_time' => 'required|date_format:H:i|after:sessions.*.start_time',
            'sessions.*.room' => 'nullable|string|max:50',
            'sessions.*.session_type' => 'required|string|in:lecture,tutorial,lab,seminar,workshop,other',
        ]);

        $course = Course::where('lecturer_id', Auth::id())
            ->orWhere('lecturer_name', 'like', '%' . Auth::user()->name . '%')
            ->where('id', $request->course_id)
            ->firstOrFail();

        $created = [];

        foreach ($request->sessions as $sessionData) {
            $conflict = TimetableEntry::where('lecturer_id', Auth::id())
                ->where('day_of_week', $sessionData['day_of_week'])
                ->where(function($q) use ($sessionData) {
                    $q->where(function($sub) use ($sessionData) {
                        $sub->where('start_time', '<=', $sessionData['start_time'])
                            ->where('end_time', '>', $sessionData['start_time']);
                    })->orWhere(function($sub) use ($sessionData) {
                        $sub->where('start_time', '<', $sessionData['end_time'])
                            ->where('end_time', '>=', $sessionData['end_time']);
                    });
                })
                ->first();

            if ($conflict) {
                return redirect()->back()->with('error',
                    '⚠️ Time conflict with another session on ' . $sessionData['day_of_week']
                );
            }

            $entry = TimetableEntry::create([
                'course_id' => $course->id,
                'lecturer_id' => Auth::id(),
                'department_id' => $course->department_id,
                'academic_year' => $course->academic_year,
                'semester' => $course->semester,
                'year_level' => $course->year,
                'day_of_week' => $sessionData['day_of_week'],
                'start_time' => $sessionData['start_time'],
                'end_time' => $sessionData['end_time'],
                'room' => $sessionData['room'] ?? $course->room,
                'session_type' => $sessionData['session_type'],
                'is_active' => true,
            ]);

            $created[] = $entry;
        }

        // Update courses table with first session
        $firstSession = $request->sessions[0];
        $course->schedule_day = $firstSession['day_of_week'];
        $course->schedule_time = $firstSession['start_time'];
        $course->schedule_end_time = $firstSession['end_time'];
        $course->room = $firstSession['room'] ?? $course->room;
        $course->save();

        return redirect()->route('lecturer.timetable.manage')
            ->with('success', '✅ Added ' . count($created) . ' sessions for ' . $course->course_code);
    }

    public function exportTimetable(Request $request)
    {
        $lecturer = Auth::user();
        $lecturerId = $lecturer->id;

        $academicYear = $request->input('academic_year');
        $semester = $request->input('semester');

        $timetableEntries = TimetableEntry::where('lecturer_id', $lecturerId)
            ->when($academicYear, function($q) use ($academicYear) {
                return $q->where('academic_year', $academicYear);
            })
            ->when($semester, function($q) use ($semester) {
                return $q->where('semester', $semester);
            })
            ->with(['course', 'course.department'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        if ($timetableEntries->isEmpty()) {
            $timetableEntries = Course::where('lecturer_id', $lecturerId)
                ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
                ->where('is_active', true)
                ->whereNotNull('schedule_day')
                ->whereNotNull('schedule_time')
                ->whereNotNull('schedule_end_time')
                ->get()
                ->unique('id');
        }

        $filename = 'timetable_' . $lecturer->name . '_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($timetableEntries) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Course Code',
                'Course Name',
                'Day',
                'Start Time',
                'End Time',
                'Room',
                'Session Type',
                'Year Level',
                'Semester',
                'Academic Year',
            ]);

            foreach ($timetableEntries as $entry) {
                if ($entry instanceof Course) {
                    fputcsv($file, [
                        $entry->course_code ?? 'N/A',
                        $entry->course_name ?? 'N/A',
                        $entry->schedule_day ?? 'N/A',
                        date('h:i A', strtotime($entry->schedule_time ?? '00:00:00')),
                        date('h:i A', strtotime($entry->schedule_end_time ?? '00:00:00')),
                        $entry->room ?? 'N/A',
                        'lecture',
                        $entry->year ?? 'N/A',
                        $entry->semester ?? 'N/A',
                        $entry->academic_year ?? 'N/A',
                    ]);
                } else {
                    fputcsv($file, [
                        $entry->course->course_code ?? 'N/A',
                        $entry->course->course_name ?? 'N/A',
                        $entry->day_of_week ?? 'N/A',
                        date('h:i A', strtotime($entry->start_time ?? '00:00:00')),
                        date('h:i A', strtotime($entry->end_time ?? '00:00:00')),
                        $entry->room ?? 'N/A',
                        $entry->session_type ?? 'lecture',
                        $entry->year_level ?? 'N/A',
                        $entry->semester ?? 'N/A',
                        $entry->academic_year ?? 'N/A',
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportTimetablePdf(Request $request)
    {
        $lecturer = Auth::user();
        $lecturerId = $lecturer->id;

        $academicYear = $request->input('academic_year');
        $semester = $request->input('semester');

        $timetableEntries = TimetableEntry::where('lecturer_id', $lecturerId)
            ->when($academicYear, function($q) use ($academicYear) {
                return $q->where('academic_year', $academicYear);
            })
            ->when($semester, function($q) use ($semester) {
                return $q->where('semester', $semester);
            })
            ->with(['course'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        if ($timetableEntries->isEmpty()) {
            $timetableEntries = Course::where('lecturer_id', $lecturerId)
                ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
                ->where('is_active', true)
                ->whereNotNull('schedule_day')
                ->whereNotNull('schedule_time')
                ->whereNotNull('schedule_end_time')
                ->get()
                ->unique('id');
        }

        $grid = [];
        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($dayNames as $day) {
            $grid[$day] = $timetableEntries->filter(function($entry) use ($day) {
                if ($entry instanceof Course) {
                    return $entry->schedule_day === $day;
                }
                return $entry->day_of_week === $day;
            })->values();
        }

        return view('lecturer.timetable-pdf', compact('grid', 'lecturer', 'academicYear', 'semester'));
    }

    /**
     * Export report for a course (CSV)
     */
    public function exportReport(Request $request)
    {
        $courseId = $request->input('course_id');
        $type = $request->input('type', 'csv');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$courseId) {
            return redirect()->back()->with('error', 'Please select a course.');
        }

        $course = Course::where('lecturer_id', Auth::id())
            ->orWhere('lecturer_name', 'like', '%' . Auth::user()->name . '%')
            ->where('id', $courseId)
            ->firstOrFail();

        $query = AttendanceEvaluation::where('course_id', $courseId)
            ->with('student');

        if ($startDate) {
            $query->whereDate('evaluation_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('evaluation_date', '<=', $endDate);
        }

        $evaluations = $query->orderBy('evaluation_date', 'desc')->get();

        $filename = 'attendance_report_' . $course->course_code . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($evaluations, $course) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Student ID',
                'Student Name',
                'Email',
                'Attendance %',
                'Consistency (0-6)',
                'Punctuality (0-2)',
                'Participation (0-2)',
                'Total Roll Call (0-10)',
                'Eligibility',
                'Risk Level',
                'Risk Score',
                'Consecutive Absences',
                'Trend',
                'Evaluation Date'
            ]);

            foreach ($evaluations as $eval) {
                fputcsv($file, [
                    $eval->student->student_id ?? 'N/A',
                    $eval->student->name ?? 'N/A',
                    $eval->student->email ?? 'N/A',
                    $eval->attendance_percentage . '%',
                    $eval->consistency_marks ?? 0,
                    $eval->punctuality_marks ?? 0,
                    $eval->participation_marks ?? 0,
                    $eval->roll_call_total ?? 0,
                    ucfirst($eval->eligibility_status ?? 'N/A'),
                    $eval->risk_level ?? 'N/A',
                    $eval->risk_score ?? 0,
                    $eval->consecutive_absences ?? 0,
                    ucfirst($eval->attendance_trend ?? 'N/A'),
                    $eval->evaluation_date ? $eval->evaluation_date->format('Y-m-d') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export at-risk students report (CSV)
     */
    public function exportAtRiskReport(Request $request)
    {
        $courseId = $request->input('course_id');

        $query = AttendanceEvaluation::whereIn('risk_level', ['High', 'Medium'])
            ->with('student', 'course');

        if ($courseId) {
            $query->where('course_id', $courseId);
        } else {
            $courseIds = Course::where('lecturer_id', Auth::id())
                ->orWhere('lecturer_name', 'like', '%' . Auth::user()->name . '%')
                ->pluck('id')
                ->toArray();
            $query->whereIn('course_id', $courseIds);
        }

        $evaluations = $query->orderBy('risk_score', 'desc')->get();

        $filename = 'at_risk_students_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($evaluations) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Student ID',
                'Student Name',
                'Email',
                'Course',
                'Attendance %',
                'Roll Call (0-10)',
                'Consistency (0-6)',
                'Punctuality (0-2)',
                'Participation (0-2)',
                'Risk Level',
                'Risk Score',
                'Consecutive Absences',
                'Trend',
                'Eligibility'
            ]);

            foreach ($evaluations as $eval) {
                fputcsv($file, [
                    $eval->student->student_id ?? 'N/A',
                    $eval->student->name ?? 'N/A',
                    $eval->student->email ?? 'N/A',
                    $eval->course->course_code ?? 'N/A',
                    $eval->attendance_percentage . '%',
                    $eval->roll_call_total ?? 0,
                    $eval->consistency_marks ?? 0,
                    $eval->punctuality_marks ?? 0,
                    $eval->participation_marks ?? 0,
                    $eval->risk_level ?? 'N/A',
                    $eval->risk_score ?? 0,
                    $eval->consecutive_absences ?? 0,
                    ucfirst($eval->attendance_trend ?? 'N/A'),
                    ucfirst($eval->eligibility_status ?? 'N/A')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
