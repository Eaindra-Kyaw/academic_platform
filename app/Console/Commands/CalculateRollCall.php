<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\TimetableEntry;
use App\Models\AcademicCalendar;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\AttendanceEvaluation;
use App\Helpers\AttendanceHelper;
use Carbon\Carbon;

class CalculateRollCall extends Command
{
    protected $signature = 'rollcall:calculate
                            {--student= : Calculate for specific student}
                            {--course= : Calculate for specific course}
                            {--month= : Month to calculate (1-12)}
                            {--year= : Year to calculate}
                            {--weekly : Calculate weekly instead of monthly}
                            {--test : Run in test mode with sample output}';

    protected $description = 'Calculate roll call marks using KG+12 Myanmar System';

    public function handle()
    {
        $this->info('📊 Starting KG+12 Roll Call Calculation...');
        $this->info('═══════════════════════════════════════════');

        $studentId = $this->option('student');
        $courseId = $this->option('course');
        $month = $this->option('month') ?? Carbon::now()->month;
        $year = $this->option('year') ?? Carbon::now()->year;
        $weekly = $this->option('weekly');
        $test = $this->option('test');

        if ($test) {
            $this->runTestMode();
            return 0;
        }

        $query = Enrollment::where('status', 'approved');

        if ($studentId) {
            $query->where('student_id', $studentId);
            $this->info("👤 Filtering by student: {$studentId}");
        }

        if ($courseId) {
            $query->where('course_id', $courseId);
            $this->info("📚 Filtering by course: {$courseId}");
        }

        $enrollments = $query->get();
        $total = $enrollments->count();

        if ($total === 0) {
            $this->error('❌ No enrollments found.');
            return 1;
        }

        $this->info("📋 Processing {$total} enrollment(s) for {$month}/{$year}...");
        $this->newLine();

        $bar = $this->output->createProgressBar($total);
        $results = [];

        foreach ($enrollments as $enrollment) {
            $result = $this->calculateStudentRollCall(
                $enrollment->student_id,
                $enrollment->course_id,
                $month,
                $year,
                $weekly
            );

            if ($result) {
                $results[] = $result;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->showSummary($results);
        $this->showBreakdown($results);

        return 0;
    }

    private function calculateStudentRollCall($studentId, $courseId, $month, $year, $weekly = false)
    {
        $timetable = TimetableEntry::where('course_id', $courseId)
            ->where('is_active', true)
            ->get();

        if ($timetable->isEmpty()) {
            return null;
        }

        $daysOfWeek = $timetable->pluck('day_of_week')->unique()->toArray();

        if ($weekly) {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } else {
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();
        }

        $holidays = AcademicCalendar::whereBetween('date', [$startDate, $endDate])
            ->whereIn('type', ['holiday', 'public_holiday', 'university_closure'])
            ->pluck('date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        $sessions = AttendanceSession::where('course_id', $courseId)
            ->whereBetween('session_date', [$startDate, $endDate])
            ->get();

        $sessionDates = $sessions->pluck('session_date')->map(function($date) {
            return Carbon::parse($date)->format('Y-m-d');
        })->toArray();

        $cancelledDates = $sessions->where('is_cancelled', true)
            ->pluck('session_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })->toArray();

        $currentDate = $startDate->copy();
        $conductedPeriods = 0;
        $conductedDates = [];

        while ($currentDate <= $endDate) {
            $dayName = $currentDate->format('l');
            $dateKey = $currentDate->format('Y-m-d');

            if (in_array($dateKey, $holidays)) {
                $currentDate->addDay();
                continue;
            }

            if (in_array($dayName, $daysOfWeek)) {
                $entry = $timetable->firstWhere('day_of_week', $dayName);
                $periodCount = $entry ? $entry->period_count : 4;

                if (in_array($dateKey, $cancelledDates)) {
                    $currentDate->addDay();
                    continue;
                }

                if (in_array($dateKey, $sessionDates)) {
                    $session = $sessions->firstWhere('session_date', $currentDate->format('Y-m-d'));
                    $conducted = $session ? $session->conducted_periods : $periodCount;
                } else {
                    $conducted = $periodCount;
                }

                $conductedPeriods += $conducted;
                $conductedDates[$dateKey] = $conducted;
            }

            $currentDate->addDay();
        }

        $attendedPeriods = 0;
        $attendedDates = [];

        foreach ($conductedDates as $date => $periods) {
            $record = AttendanceRecord::where('student_id', $studentId)
                ->whereDate('scanned_at', $date)
                ->whereHas('session', function($q) use ($courseId) {
                    $q->where('course_id', $courseId);
                })
                ->first();

            if ($record) {
                $attendedPeriods += $periods;
                $attendedDates[$date] = $periods;
            }
        }

        $percentage = $conductedPeriods > 0
            ? round(($attendedPeriods / $conductedPeriods) * 100, 2)
            : 0;

        // ============================================================
        // KG+12 ROLL CALL CALCULATION
        // ============================================================

        $lateCount = AttendanceRecord::where('student_id', $studentId)
            ->whereHas('session', function($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })
            ->where('status', 'late')
            ->count();

        $participationMark = 1.5; // Default moderate participation
        $rollCall = AttendanceHelper::calculateRollCallMark($percentage, $lateCount, $participationMark);

        $eligibility = AttendanceHelper::getEligibilityStatus($percentage);

        $consecutiveAbsences = AttendanceHelper::getConsecutiveAbsences($studentId, $courseId);
        $trend = AttendanceHelper::getAttendanceTrend($studentId, $courseId);

        $riskScore = AttendanceHelper::calculateRiskScore(
            $percentage,
            $rollCall['total'],
            $consecutiveAbsences,
            $trend
        );
        $riskLevel = AttendanceHelper::getRiskLevel($riskScore);
        $riskExplanation = AttendanceHelper::getRiskExplanation(
            $percentage,
            $rollCall['total'],
            $consecutiveAbsences,
            $trend,
            $riskLevel
        );

        // Save to AttendanceEvaluation
        AttendanceEvaluation::updateOrCreate(
            [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'evaluation_date' => Carbon::today(),
            ],
            [
                'total_sessions' => count($conductedDates),
                'attended_sessions' => count($attendedDates),
                'attendance_percentage' => $percentage,
                'consistency_marks' => $rollCall['consistency'],
                'punctuality_marks' => $rollCall['punctuality'],
                'participation_marks' => $rollCall['participation'],
                'roll_call_total' => $rollCall['total'],
                'eligibility_status' => $eligibility,
                'consecutive_absences' => $consecutiveAbsences,
                'attendance_trend' => $trend,
                'risk_score' => $riskScore,
                'risk_level' => $riskLevel,
                'risk_factors' => json_encode($riskExplanation),
                'academic_health_score' => 0,
                'recovery_status' => 'Stable',
            ]
        );

        return [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'period' => $weekly ? 'Weekly' : 'Monthly',
            'conducted_periods' => $conductedPeriods,
            'attended_periods' => $attendedPeriods,
            'attendance_percentage' => $percentage,
            'roll_call_total' => $rollCall['total'],
            'consistency' => $rollCall['consistency'],
            'punctuality' => $rollCall['punctuality'],
            'participation' => $rollCall['participation'],
            'eligibility' => $eligibility,
            'risk_level' => $riskLevel,
            'risk_score' => $riskScore,
            'conducted_dates' => $conductedDates,
            'attended_dates' => $attendedDates,
        ];
    }

    private function showSummary($results)
    {
        $this->info('📊 KG+12 SUMMARY');
        $this->info('═══════════════════════════════════════════');

        $table = [];
        foreach ($results as $r) {
            $eligibilityLabel = match($r['eligibility']) {
                'eligible' => '✅ Eligible',
                'warning' => '⚠️ Warning',
                'not_eligible' => '❌ Not Eligible',
                default => $r['eligibility'],
            };

            $table[] = [
                'Student' => $r['student_id'],
                'Course' => $r['course_id'],
                'Attendance %' => $r['attendance_percentage'] . '%',
                'Roll Call' => $r['roll_call_total'] . '/10',
                'Consistency' => $r['consistency'] . '/6',
                'Punctuality' => $r['punctuality'] . '/2',
                'Participation' => $r['participation'] . '/2',
                'Risk' => $r['risk_level'],
                'Eligibility' => $eligibilityLabel,
            ];
        }

        $this->table(
            ['Student', 'Course', 'Attendance %', 'Roll Call', 'Consistency', 'Punctuality', 'Participation', 'Risk', 'Eligibility'],
            $table
        );
    }

    private function showBreakdown($results)
    {
        $this->newLine();
        $this->info('📋 DETAILED KG+12 BREAKDOWN');
        $this->info('═══════════════════════════════════════════');

        foreach ($results as $r) {
            $this->line("\n👤 Student: {$r['student_id']} | Course: {$r['course_id']}");
            $this->line("📅 Period: {$r['period']}");

            $this->line("\n📊 KG+12 Roll Call Components:");
            $this->line("   • Attendance Consistency: {$r['consistency']}/6");
            $this->line("   • Punctuality: {$r['punctuality']}/2");
            $this->line("   • Participation: {$r['participation']}/2");
            $this->line("   • Total Roll Call: {$r['roll_call_total']}/10");

            $this->line("\n📊 Total Conducted: {$r['conducted_periods']} periods");
            $this->line("✅ Total Attended: {$r['attended_periods']} periods");
            $this->line("📈 Attendance: {$r['attendance_percentage']}%");
            $this->line("🎯 Risk Score: {$r['risk_score']} ({$r['risk_level']})");
            $this->line("🏷️ Eligibility: {$r['eligibility']}");
            $this->line(str_repeat('─', 50));
        }
    }

    private function runTestMode()
    {
        $this->info('🧪 RUNNING TEST MODE (KG+12)');
        $this->info('═══════════════════════════════════════════');

        $result = $this->calculateStudentRollCall(32, 26, 6, 2026);

        if ($result) {
            $this->info('✅ Test Result:');
            $this->line("\n👤 Student: 32 (Eaindra Kyaw)");
            $this->line("📚 Course: 26 (Machine Learning)");
            $this->line("📅 Period: Monthly (June 2026)");

            $this->line("\n📊 KG+12 Roll Call Components:");
            $this->line("   • Attendance Consistency: {$result['consistency']}/6");
            $this->line("   • Punctuality: {$result['punctuality']}/2");
            $this->line("   • Participation: {$result['participation']}/2");
            $this->line("   • Total Roll Call: {$result['roll_call_total']}/10");

            $this->line("\n📊 Total Conducted: {$result['conducted_periods']} periods");
            $this->line("✅ Total Attended: {$result['attended_periods']} periods");
            $this->line("📈 Attendance: {$result['attendance_percentage']}%");
            $this->line("🎯 Risk Score: {$result['risk_score']} ({$result['risk_level']})");
            $this->line("🏷️ Eligibility: {$result['eligibility']}");
        } else {
            $this->error('❌ No timetable found for course 26.');
            $this->info('Please insert sample timetable data first.');
        }

        $this->newLine();
        $this->info('🧪 Test completed!');
    }
}
