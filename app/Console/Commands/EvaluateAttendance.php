<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\AttendanceEvaluation;
use Carbon\Carbon;

class EvaluateAttendance extends Command
{
    protected $signature = 'attendance:evaluate {--student= : Evaluate specific student} {--course= : Evaluate specific course}';
    protected $description = 'Calculate attendance evaluations for all students';

    public function handle()
    {
        $this->info('📊 Starting Attendance Evaluation...');

        $studentsQuery = User::where('role_id', 3);

        if ($this->option('student')) {
            $studentsQuery->where('id', $this->option('student'));
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
        $sessions = AttendanceSession::where('course_id', $course->id)
            ->where('status', 'ended')
            ->get();

        $totalSessions = $sessions->count();

        if ($totalSessions == 0) {
            return;
        }

        $records = AttendanceRecord::where('student_id', $student->id)
            ->whereIn('attendance_session_id', $sessions->pluck('id'))
            ->get();

        $attended = $records->count();
        $present = $records->where('status', 'present')->count();
        $late = $records->where('status', 'late')->count();

        $attendancePercentage = round(($attended / max($totalSessions, 1)) * 100);

        // Calculate roll call score (0-10)
        $rollCallScore = $this->calculateRollCallScore($attendancePercentage);

        // Determine eligibility (match your table's enum)
        if ($attendancePercentage >= 75) {
            $eligibilityStatus = 'eligible';
        } elseif ($attendancePercentage >= 60) {
            $eligibilityStatus = 'warning';
        } else {
            $eligibilityStatus = 'not_eligible';
        }

        $consecutiveAbsences = $this->getConsecutiveAbsences($student->id, $course->id);
        $streaks = $this->getStreaks($student->id, $course->id);
        $currentStreak = $streaks['current'];
        $longestStreak = $streaks['longest'];

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
            } else {
                $recoveryStatus = 'Stable';
            }
        }

        $trend = $this->getTrend($student->id, $course->id);
        $riskScore = $this->calculateRiskScore($attendancePercentage, $rollCallScore, $consecutiveAbsences, $trend);

        // Match your table's risk_level format
        if ($riskScore < 40) {
            $riskLevel = 'Low';
        } elseif ($riskScore < 70) {
            $riskLevel = 'Medium';
        } else {
            $riskLevel = 'High';
        }

        $academicHealthScore = $this->calculateAcademicHealthScore(
            $attendancePercentage,
            $rollCallScore,
            $currentStreak,
            $trend
        );

        $notes = $this->generateNotes(
            $attendancePercentage,
            $eligibilityStatus,
            $riskLevel,
            $consecutiveAbsences,
            $recoveryStatus
        );

        AttendanceEvaluation::updateOrCreate(
            [
                'student_id' => $student->id,
                'course_id' => $course->id,
                'evaluation_date' => Carbon::today(),
            ],
            [
                'attendance_percentage' => $attendancePercentage,
                'roll_call_score' => $rollCallScore,
                'eligibility_status' => $eligibilityStatus,
                'sessions_attended' => $attended,
                'total_sessions' => $totalSessions,
                'consecutive_absences' => $consecutiveAbsences,
                'current_streak' => $currentStreak,
                'longest_streak' => $longestStreak,
                'risk_score' => $riskScore,
                'risk_level' => $riskLevel,
                'academic_health_score' => $academicHealthScore,
                'recovery_status' => $recoveryStatus,
                'evaluation_notes' => $notes,
            ]
        );
    }

    private function calculateRollCallScore($percentage)
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

    private function getConsecutiveAbsences($studentId, $courseId)
    {
        $sessions = AttendanceSession::where('course_id', $courseId)
            ->where('status', 'ended')
            ->orderBy('session_date', 'desc')
            ->limit(5)
            ->get();

        $consecutive = 0;
        foreach ($sessions as $session) {
            $record = AttendanceRecord::where('student_id', $studentId)
                ->where('attendance_session_id', $session->id)
                ->first();

            if (!$record || $record->status == 'absent') {
                $consecutive++;
            } else {
                break;
            }
        }

        return $consecutive;
    }

    private function getStreaks($studentId, $courseId)
    {
        $sessions = AttendanceSession::where('course_id', $courseId)
            ->where('status', 'ended')
            ->orderBy('session_date', 'asc')
            ->get();

        $currentStreak = 0;
        $longestStreak = 0;

        foreach ($sessions as $session) {
            $record = AttendanceRecord::where('student_id', $studentId)
                ->where('attendance_session_id', $session->id)
                ->first();

            if ($record && in_array($record->status, ['present', 'late'])) {
                $currentStreak++;
                if ($currentStreak > $longestStreak) {
                    $longestStreak = $currentStreak;
                }
            } else {
                $currentStreak = 0;
            }
        }

        return ['current' => $currentStreak, 'longest' => $longestStreak];
    }

    private function getTrend($studentId, $courseId)
    {
        $sessions = AttendanceSession::where('course_id', $courseId)
            ->where('status', 'ended')
            ->orderBy('session_date', 'desc')
            ->limit(10)
            ->get();

        if ($sessions->count() < 4) {
            return 'Stable';
        }

        $recent = 0;
        $older = 0;
        $half = ceil($sessions->count() / 2);

        foreach ($sessions as $index => $session) {
            $record = AttendanceRecord::where('student_id', $studentId)
                ->where('attendance_session_id', $session->id)
                ->first();

            $attended = $record && in_array($record->status, ['present', 'late']);

            if ($index < $half) {
                if ($attended) $recent++;
            } else {
                if ($attended) $older++;
            }
        }

        $recentRate = $half > 0 ? ($recent / $half) * 100 : 0;
        $olderRate = $half > 0 ? ($older / $half) * 100 : 0;

        if ($recentRate > $olderRate + 10) return 'Improving';
        if ($recentRate < $olderRate - 10) return 'Declining';
        return 'Stable';
    }

    private function calculateRiskScore($attendance, $rollCall, $absences, $trend)
    {
        $attendanceRisk = $attendance < 75 ? 75 : ($attendance < 85 ? 50 : 25);
        $rollCallRisk = $rollCall < 5 ? 75 : ($rollCall < 7 ? 50 : 25);
        $absenceRisk = $absences >= 3 ? 75 : ($absences >= 1 ? 50 : 25);
        $trendRisk = $trend == 'Declining' ? 75 : ($trend == 'Stable' ? 50 : 25);

        return round(($attendanceRisk * 0.40) + ($rollCallRisk * 0.25) + ($absenceRisk * 0.20) + ($trendRisk * 0.15));
    }

    private function calculateAcademicHealthScore($attendance, $rollCall, $streak, $trend)
    {
        $trendScore = match($trend) {
            'Improving' => 100,
            'Stable' => 75,
            'Declining' => 50,
            default => 50,
        };

        $streakScore = match(true) {
            $streak >= 13 => 100,
            $streak >= 9 => 80,
            $streak >= 6 => 60,
            $streak >= 3 => 40,
            default => 20,
        };

        return round(
            ($attendance * 0.40) +
            ($rollCall * 10 * 0.25) +
            ($streakScore * 0.20) +
            ($trendScore * 0.15)
        );
    }

    private function generateNotes($attendance, $eligibility, $risk, $absences, $recovery)
    {
        $notes = [];

        if ($attendance >= 90) {
            $notes[] = "Excellent attendance maintained";
        } elseif ($attendance >= 75) {
            $notes[] = "Good attendance, maintaining eligibility";
        } elseif ($attendance >= 60) {
            $notes[] = "Attendance needs improvement";
        } else {
            $notes[] = "Critical attendance - immediate action required";
        }

        if ($eligibility == 'eligible') {
            $notes[] = "Student is eligible for examination";
        } elseif ($eligibility == 'warning') {
            $notes[] = "Attendance warning - needs to improve";
        } else {
            $notes[] = "Not eligible - attendance below threshold";
        }

        if ($risk == 'High') {
            $notes[] = "High risk detected - requires intervention";
        } elseif ($risk == 'Medium') {
            $notes[] = "Medium risk - monitor attendance";
        }

        if ($absences >= 3) {
            $notes[] = "Multiple consecutive absences detected";
        }

        if ($recovery == 'Recovering') {
            $notes[] = "Recovery trend detected - improving attendance";
        } elseif ($recovery == 'Declining') {
            $notes[] = "Declining trend - attendance decreasing";
        }

        return implode(' | ', $notes);
    }
}
