<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceEvaluation;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Helpers\AttendanceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceEvaluationController extends Controller
{
    public function evaluateStudentCourse(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $studentId = $validated['student_id'];
        $courseId = $validated['course_id'];

        return response()->json($this->evaluateAndStore($studentId, $courseId));
    }

    public function evaluateCourse($courseId)
    {
        $course = Course::findOrFail($courseId);

        $enrollments = Enrollment::where('course_id', $courseId)
            ->where('status', 'approved')
            ->with('student')
            ->get();

        if ($enrollments->isEmpty()) {
            return response()->json([
                'success' => true,
                'course' => $course->course_name,
                'total_students' => 0,
                'results' => [],
                'message' => 'No students enrolled in this course.'
            ]);
        }

        $results = [];

        foreach ($enrollments as $enrollment) {
            $student = $enrollment->student;
            $evaluation = $this->evaluateAndStore($student->id, $courseId);
            $results[] = [
                'student_name' => $student->name,
                'student_id' => $student->student_id,
                'attendance' => $evaluation['attendance_percentage'] ?? 0,
                'roll_call' => $evaluation['roll_call_total'] ?? 0,
                'consistency' => $evaluation['consistency_marks'] ?? 0,
                'punctuality' => $evaluation['punctuality_marks'] ?? 0,
                'participation' => $evaluation['participation_marks'] ?? 0,
                'eligibility' => $evaluation['eligibility_status'] ?? 'not_eligible',
                'risk_level' => $evaluation['risk_level'] ?? 'Low',
                'risk_score' => $evaluation['risk_score'] ?? 0,
                'consecutive_absences' => $evaluation['consecutive_absences'] ?? 0,
                'trend' => $evaluation['attendance_trend'] ?? 'stable',
                'risk_factors' => json_decode($evaluation['risk_factors'] ?? '[]', true),
            ];
        }

        return response()->json([
            'success' => true,
            'course' => $course->course_name,
            'total_students' => count($results),
            'results' => $results,
            'message' => 'Course evaluation completed with KG+12 roll call and full risk.'
        ]);
    }

    private function evaluateAndStore($studentId, $courseId)
    {
        $sessions = AttendanceSession::where('course_id', $courseId)
            ->where('status', 'ended')
            ->where('is_cancelled', false)
            ->orderBy('session_date', 'asc')
            ->get();

        $totalSessions = $sessions->count();
        $sessionIds = $sessions->pluck('id')->toArray();

        $records = AttendanceRecord::where('student_id', $studentId)
            ->whereIn('attendance_session_id', $sessionIds)
            ->get()
            ->keyBy('attendance_session_id');

        $attendedCount = $records->whereIn('status', ['present', 'late'])->count();
        $lateCount = $records->where('status', 'late')->count();

        // ----- All calculations using AttendanceHelper -----
        $attendancePercentage = AttendanceHelper::calculateAttendance($attendedCount, $totalSessions);
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

        $data = [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'total_sessions' => $totalSessions,
            'attended_sessions' => $attendedCount,
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
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('attendance_evaluations')->updateOrInsert(
            ['student_id' => $studentId, 'course_id' => $courseId],
            $data
        );

        return $data;
    }

    public function studentSummary($studentId)
    {
        $student = User::findOrFail($studentId);
        $evaluations = DB::table('attendance_evaluations')
            ->where('student_id', $studentId)
            ->get();

        return response()->json([
            'student_name' => $student->name,
            'student_id' => $student->student_id,
            'total_courses' => $evaluations->count(),
            'overall_attendance' => round($evaluations->avg('attendance_percentage'), 1),
            'overall_roll_call' => round($evaluations->avg('roll_call_total'), 1),
            'overall_risk_score' => round($evaluations->avg('risk_score'), 1),
            'courses' => $evaluations,
        ]);
    }

    public function courseSummary($courseId)
    {
        $course = Course::findOrFail($courseId);
        $evaluations = DB::table('attendance_evaluations')
            ->where('course_id', $courseId)
            ->get();

        return response()->json([
            'course_name' => $course->course_name,
            'course_code' => $course->course_code,
            'total_students' => $evaluations->count(),
            'average_attendance' => round($evaluations->avg('attendance_percentage'), 1),
            'average_roll_call' => round($evaluations->avg('roll_call_total'), 1),
            'students' => $evaluations,
        ]);
    }

    public function riskDistribution($departmentId = null, $year = null)
    {
        $query = DB::table('attendance_evaluations');

        if ($departmentId) {
            $query->whereExists(function($q) use ($departmentId) {
                $q->select(DB::raw(1))
                    ->from('courses')
                    ->whereColumn('courses.id', 'attendance_evaluations.course_id')
                    ->where('courses.department_id', $departmentId);
            });
        }

        if ($year) {
            $query->whereExists(function($q) use ($year) {
                $q->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'attendance_evaluations.student_id')
                    ->where('users.current_year', $year);
            });
        }

        $evaluations = $query->get();

        return response()->json([
            'total_students' => $evaluations->count(),
            'low_risk' => $evaluations->where('risk_level', 'Low')->count(),
            'medium_risk' => $evaluations->where('risk_level', 'Medium')->count(),
            'high_risk' => $evaluations->where('risk_level', 'High')->count(),
            'eligible' => $evaluations->where('eligibility_status', 'eligible')->count(),
            'warning' => $evaluations->where('eligibility_status', 'warning')->count(),
            'not_eligible' => $evaluations->where('eligibility_status', 'not_eligible')->count(),
        ]);
    }

    public function batchEvaluate()
    {
        $courses = Course::where('is_active', true)->get();
        $results = [];
        $totalProcessed = 0;

        foreach ($courses as $course) {
            $enrollments = Enrollment::where('course_id', $course->id)
                ->where('status', 'approved')
                ->get();

            if ($enrollments->isEmpty()) {
                $results[] = [
                    'course' => $course->course_name,
                    'status' => 'skipped',
                    'students' => 0,
                    'message' => 'No students enrolled'
                ];
                continue;
            }

            $processed = 0;
            foreach ($enrollments as $enrollment) {
                $this->evaluateAndStore($enrollment->student_id, $course->id);
                $processed++;
                $totalProcessed++;
            }

            $results[] = [
                'course' => $course->course_name,
                'status' => 'success',
                'students' => $processed,
            ];
        }

        return response()->json([
            'success' => true,
            'total_courses' => count($results),
            'total_students_processed' => $totalProcessed,
            'results' => $results,
            'message' => "Batch evaluation completed! $totalProcessed evaluations created/updated."
        ]);
    }

    /**
     * Evaluate by date range (Weekly/Monthly)
     */
    public function evaluateByDateRange(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $courseId = $request->course_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $sessions = AttendanceSession::where('course_id', $courseId)
            ->where('status', 'ended')
            ->where('is_cancelled', false)
            ->whereBetween('session_date', [$startDate, $endDate])
            ->orderBy('session_date', 'asc')
            ->get();

        if ($sessions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No ended sessions found in this date range.'
            ]);
        }

        $enrollments = Enrollment::where('course_id', $courseId)
            ->where('status', 'approved')
            ->get();

        $results = [];
        $sessionIds = $sessions->pluck('id')->toArray();
        $totalSessions = $sessions->count();

        foreach ($enrollments as $enrollment) {
            $studentId = $enrollment->student_id;

            $records = AttendanceRecord::where('student_id', $studentId)
                ->whereIn('attendance_session_id', $sessionIds)
                ->get()
                ->keyBy('attendance_session_id');

            $attendedCount = $records->whereIn('status', ['present', 'late'])->count();
            $lateCount = $records->where('status', 'late')->count();

            $attendancePercentage = AttendanceHelper::calculateAttendance($attendedCount, $totalSessions);
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

            $results[] = [
                'student_id' => $studentId,
                'student_name' => $enrollment->student->name ?? 'Unknown',
                'attendance' => $attendancePercentage,
                'roll_call' => $rollCall['total'],
                'risk_level' => $riskLevel,
                'eligibility' => $eligibility,
            ];
        }

        return response()->json([
            'success' => true,
            'course_id' => $courseId,
            'date_range' => ['start' => $startDate, 'end' => $endDate],
            'total_sessions' => $totalSessions,
            'total_students' => count($results),
            'results' => $results,
            'message' => 'Evaluation completed for date range.'
        ]);
    }

    /**
 * Get attendance data for a specific student (weekly trend + risk summary)
 */
public function studentAttendanceData($studentId)
{
    $student = User::with('department')->findOrFail($studentId);

    // Get all evaluations for this student
    $evaluations = DB::table('attendance_evaluations')
        ->where('student_id', $studentId)
        ->get();

    // --- Weekly attendance trend (last 12 weeks) ---
    $weeks = 12;
    $trend = [];
    $startDate = Carbon::now()->subWeeks($weeks);

    // Get all attendance sessions that the student has records for (across all courses)
    $sessionIds = AttendanceRecord::where('student_id', $studentId)
        ->pluck('attendance_session_id')
        ->unique()
        ->toArray();

    $sessions = AttendanceSession::whereIn('id', $sessionIds)
        ->where('created_at', '>=', $startDate)
        ->orderBy('session_date', 'asc')
        ->get();

    for ($i = $weeks; $i >= 0; $i--) {
        $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
        $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
        $label = $weekStart->format('d M');

        $weekSessions = $sessions->filter(function($s) use ($weekStart, $weekEnd) {
            return Carbon::parse($s->session_date)->between($weekStart, $weekEnd);
        });

        $weekRecords = AttendanceRecord::where('student_id', $studentId)
            ->whereIn('attendance_session_id', $weekSessions->pluck('id')->toArray())
            ->whereIn('status', ['present', 'late'])
            ->count();

        $totalPossible = $weekSessions->count();
        $attendance = $totalPossible > 0 ? round(($weekRecords / $totalPossible) * 100, 1) : 0;

        $trend[] = [
            'label' => $label,
            'attendance' => $attendance,
        ];
    }

    // --- Overall risk summary ---
    $avgAttendance = $evaluations->avg('attendance_percentage') ?? 0;
    $avgRollCall = $evaluations->avg('roll_call_total') ?? 0;
    $overallRisk = $evaluations->avg('risk_score') ?? 0;
    $riskLevel = \App\Helpers\AttendanceHelper::getRiskLevel($overallRisk);
    $eligibility = \App\Helpers\AttendanceHelper::getEligibilityStatus($avgAttendance);

    // Collect risk factors from all evaluations (unique)
    $allFactors = [];
    foreach ($evaluations as $eval) {
        $factors = json_decode($eval->risk_factors ?? '[]', true);
        if (is_array($factors)) {
            $allFactors = array_merge($allFactors, $factors);
        }
    }
    $uniqueFactors = array_values(array_unique($allFactors));

    // Per-course breakdown
    $courses = [];
    foreach ($evaluations as $eval) {
        $course = Course::find($eval->course_id);
        if ($course) {
            $courses[] = [
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'attendance' => round($eval->attendance_percentage, 1),
                'eligibility' => $eval->eligibility_status,
                'risk_level' => $eval->risk_level,
                'roll_call' => round($eval->roll_call_total, 1),
            ];
        }
    }

    return response()->json([
        'success' => true,
        'student' => [
            'id' => $student->id,
            'name' => $student->name,
            'student_id' => $student->student_id,
        ],
        'trend' => $trend,
        'summary' => [
            'average_attendance' => round($avgAttendance, 1),
            'average_roll_call' => round($avgRollCall, 1),
            'overall_risk_score' => round($overallRisk, 1),
            'risk_level' => $riskLevel,
            'eligibility_status' => $eligibility,
            'risk_factors' => $uniqueFactors,
        ],
        'courses' => $courses,
    ]);
}
}
