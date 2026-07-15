<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\AttendanceEvaluation;
use App\Helpers\AttendanceHelper;
use Carbon\Carbon;

class EvaluateAttendance extends Command
{
    protected $signature = 'attendance:evaluate {--student= : Evaluate specific student} {--course= : Evaluate specific course}';
    protected $description = 'Calculate attendance evaluations with KG+12 roll call and risk (PERIOD-BASED)';

    public function handle()
    {
        $this->info('📊 Starting KG+12 Period-Based Evaluation...');

        $studentsQuery = User::where('role_id', 3);

        if ($this->option('student')) {
            $studentsQuery->where('id', $this->option('student'));
        }

        if ($this->option('course')) {
            $courseId = $this->option('course');
            $studentIds = Enrollment::where('course_id', $courseId)
                ->where('status', 'approved')
                ->pluck('student_id');
            $studentsQuery->whereIn('id', $studentIds);
        }

        $students = $studentsQuery->get();

        if ($students->isEmpty()) {
            $this->error('No students found!');
            return 1;
        }

        $this->info("Found {$students->count()} students to evaluate");
        $bar = $this->output->createProgressBar($students->count());

        $evaluated = 0;
        $errors = 0;

        foreach ($students as $student) {
            try {
                $this->evaluateStudent($student);
                $evaluated++;
            } catch (\Exception $e) {
                $this->error("Error evaluating student {$student->id}: {$e->getMessage()}");
                $errors++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("✅ Evaluation completed!");
        $this->info("   - Students evaluated: {$evaluated}");
        $this->info("   - Errors: {$errors}");

        return 0;
    }

    private function evaluateStudent($student)
    {
        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with('course')
            ->get();

        foreach ($enrollments as $enrollment) {
            $this->evaluateStudentCourse($student, $enrollment->course);
        }
    }

    private function evaluateStudentCourse($student, $course)
    {
        // Get all ended, non-cancelled sessions for this course
        $sessions = AttendanceSession::where('course_id', $course->id)
            ->where('status', 'ended')
            ->where('is_cancelled', false)
            ->get();

        if ($sessions->isEmpty()) {
            return;
        }

        // ✅ PERIOD-BASED CALCULATION
        $totalPeriods = $sessions->sum('conducted_periods');
        if ($totalPeriods == 0) {
            return;
        }

        $attendedPeriods = 0;
        $lateCount = 0;

        foreach ($sessions as $session) {
            $record = AttendanceRecord::where('student_id', $student->id)
                ->where('attendance_session_id', $session->id)
                ->first();

            if ($record && in_array($record->status, ['present', 'late'])) {
                // ✅ Add the session's conducted periods
                $attendedPeriods += $session->conducted_periods;
                if ($record->status === 'late') {
                    $lateCount++;
                }
            }
        }

        // ✅ Attendance % = (attendedPeriods / totalPeriods) * 100
        $attendancePercentage = round(($attendedPeriods / max($totalPeriods, 1)) * 100, 1);

        // === KG+12 ROLL CALL (uses period percentage for consistency) ===
        $rollCall = AttendanceHelper::calculateRollCallMark($attendancePercentage, $lateCount, 1.5);

        $eligibilityStatus = AttendanceHelper::getEligibilityStatus($attendancePercentage);

        $consecutiveAbsences = AttendanceHelper::getConsecutiveAbsences($student->id, $course->id);
        $trend = AttendanceHelper::getAttendanceTrend($student->id, $course->id);

        $riskScore = AttendanceHelper::calculateRiskScore(
            $attendancePercentage,
            $rollCall['total'],
            $consecutiveAbsences,
            $trend
        );
        $riskLevel = AttendanceHelper::getRiskLevel($riskScore);
        $riskExplanation = AttendanceHelper::getRiskExplanation(
            $attendancePercentage,
            $rollCall['total'],
            $consecutiveAbsences,
            $trend,
            $riskLevel
        );

        $previous = AttendanceEvaluation::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->latest('evaluation_date')
            ->first();

        $recoveryStatus = 'Stable';
        if ($previous) {
            if ($attendancePercentage > $previous->attendance_percentage) {
                $recoveryStatus = 'Recovering';
            } elseif ($attendancePercentage < $previous->attendance_percentage) {
                $recoveryStatus = 'Declining';
            }
        }

        $academicHealthScore = $this->calculateAcademicHealthScore(
            $attendancePercentage,
            $rollCall['total'],
            0,
            $trend
        );

        AttendanceEvaluation::updateOrCreate(
            [
                'student_id' => $student->id,
                'course_id' => $course->id,
            ],
            [
                'evaluation_date' => Carbon::today()->toDateString(),
                'attendance_percentage' => $attendancePercentage,
                'consistency_marks' => $rollCall['consistency'],
                'punctuality_marks' => $rollCall['punctuality'],
                'participation_marks' => $rollCall['participation'],
                'roll_call_total' => $rollCall['total'],
                'eligibility_status' => $eligibilityStatus,
                'attended_sessions' => $attendedPeriods,
                'total_sessions' => $totalPeriods,
                'consecutive_absences' => $consecutiveAbsences,
                'attendance_trend' => $trend,
                'risk_score' => $riskScore,
                'risk_level' => $riskLevel,
                'risk_factors' => json_encode($riskExplanation),
                'academic_health_score' => $academicHealthScore,
                'recovery_status' => $recoveryStatus,
                'evaluation_notes' => implode(' | ', $riskExplanation),
            ]
        );
    }

    private function calculateAcademicHealthScore($attendance, $rollCall, $streak, $trend)
    {
        $trendScore = match($trend) {
            'improving' => 100,
            'stable'    => 75,
            'declining' => 50,
            'critical'  => 25,
            default     => 50,
        };

        $streakScore = match(true) {
            $streak >= 13 => 100,
            $streak >= 9  => 80,
            $streak >= 6  => 60,
            $streak >= 3  => 40,
            default      => 20,
        };

        $rollCallScore = $rollCall * 10;

        return round(
            ($attendance * 0.40) +
            ($rollCallScore * 0.25) +
            ($streakScore * 0.20) +
            ($trendScore * 0.15)
        );
    }
}
