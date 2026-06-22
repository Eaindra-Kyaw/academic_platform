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

    protected $description = 'Calculate roll call marks (MTU Myanmar System)';

    public function handle()
    {
        $this->info('📊 Starting Roll Call Calculation...');
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

        // Build query
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

        // Display summary
        $this->showSummary($results);
        $this->showBreakdown($results);

        return 0;
    }

    private function calculateStudentRollCall($studentId, $courseId, $month, $year, $weekly = false)
    {
        // Get timetable entries
        $timetable = TimetableEntry::where('course_id', $courseId)
            ->where('is_active', true)
            ->get();

        if ($timetable->isEmpty()) {
            return null;
        }

        $daysOfWeek = $timetable->pluck('day_of_week')->unique()->toArray();

        // Get date range
        if ($weekly) {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } else {
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();
        }

        // Get holidays
        $holidays = AcademicCalendar::whereBetween('date', [$startDate, $endDate])
            ->whereIn('type', ['holiday', 'public_holiday', 'university_closure'])
            ->pluck('date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        // Get attendance sessions
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

        // Calculate conducted periods
        $currentDate = $startDate->copy();
        $conductedPeriods = 0;
        $conductedDates = [];

        while ($currentDate <= $endDate) {
            $dayName = $currentDate->format('l');
            $dateKey = $currentDate->format('Y-m-d');

            // Skip holidays
            if (in_array($dateKey, $holidays)) {
                $currentDate->addDay();
                continue;
            }

            // Check if it's a class day
            if (in_array($dayName, $daysOfWeek)) {
                $entry = $timetable->firstWhere('day_of_week', $dayName);
                $periodCount = $entry ? $entry->period_count : 4;

                // Skip cancelled sessions
                if (in_array($dateKey, $cancelledDates)) {
                    $currentDate->addDay();
                    continue;
                }

                // If session exists, use its conducted_periods, else use default
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

        // Calculate attended periods from QR records
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

        // Calculate percentage
        $percentage = $conductedPeriods > 0
            ? round(($attendedPeriods / $conductedPeriods) * 100, 2)
            : 0;

        // Calculate roll call mark (MTU system)
        $rollCallMark = $this->calculateRollCallMark($percentage);

        // Determine eligibility
        $eligibility = $this->determineEligibility($percentage);

        return [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'period' => $weekly ? 'Weekly' : 'Monthly',
            'conducted_periods' => $conductedPeriods,
            'attended_periods' => $attendedPeriods,
            'attendance_percentage' => $percentage,
            'roll_call_mark' => $rollCallMark,
            'eligibility' => $eligibility,
            'conducted_dates' => $conductedDates,
            'attended_dates' => $attendedDates,
        ];
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

    private function determineEligibility($percentage)
    {
        if ($percentage >= 75) return '✅ Eligible';
        if ($percentage >= 60) return '⚠️ Warning';
        return '❌ Not Eligible';
    }

    private function showSummary($results)
    {
        $this->info('📊 SUMMARY');
        $this->info('═══════════════════════════════════════════');

        $table = [];
        foreach ($results as $r) {
            $table[] = [
                'Student' => $r['student_id'],
                'Course' => $r['course_id'],
                'Conducted' => $r['conducted_periods'],
                'Attended' => $r['attended_periods'],
                'Attendance %' => $r['attendance_percentage'] . '%',
                'Roll Call' => $r['roll_call_mark'] . '/10',
                'Eligibility' => $r['eligibility'],
            ];
        }

        $this->table(
            ['Student', 'Course', 'Conducted', 'Attended', 'Attendance %', 'Roll Call', 'Eligibility'],
            $table
        );
    }

    private function showBreakdown($results)
    {
        $this->newLine();
        $this->info('📋 DETAILED BREAKDOWN');
        $this->info('═══════════════════════════════════════════');

        foreach ($results as $r) {
            $this->line("\n👤 Student: {$r['student_id']} | Course: {$r['course_id']}");
            $this->line("📅 Period: {$r['period']}");

            if (!empty($r['conducted_dates'])) {
                $this->line("\n📖 Conducted Dates:");
                foreach ($r['conducted_dates'] as $date => $periods) {
                    $attended = isset($r['attended_dates'][$date]) ? '✅ Attended' : '❌ Absent';
                    $this->line("   {$date}: {$periods} periods - {$attended}");
                }
            }

            $this->line("\n📊 Total Conducted: {$r['conducted_periods']} periods");
            $this->line("✅ Total Attended: {$r['attended_periods']} periods");
            $this->line("📈 Attendance: {$r['attendance_percentage']}%");
            $this->line("🎯 Roll Call: {$r['roll_call_mark']}/10");
            $this->line("🏷️ Eligibility: {$r['eligibility']}");
            $this->line(str_repeat('─', 40));
        }
    }

    private function runTestMode()
    {
        $this->info('🧪 RUNNING TEST MODE');
        $this->info('═══════════════════════════════════════════');

        // Test student 32, course 26, June 2026
        $result = $this->calculateStudentRollCall(32, 26, 6, 2026);

        if ($result) {
            $this->info('✅ Test Result:');
            $this->line("\n👤 Student: 32 (Eaindra Kyaw)");
            $this->line("📚 Course: 26 (Machine Learning)");
            $this->line("📅 Period: Monthly (June 2026)");
            $this->line("\n📖 Conducted Dates:");
            foreach ($result['conducted_dates'] as $date => $periods) {
                $attended = isset($result['attended_dates'][$date]) ? '✅ Attended' : '❌ Absent';
                $this->line("   {$date}: {$periods} periods - {$attended}");
            }
            $this->line("\n📊 Total Conducted: {$result['conducted_periods']} periods");
            $this->line("✅ Total Attended: {$result['attended_periods']} periods");
            $this->line("📈 Attendance: {$result['attendance_percentage']}%");
            $this->line("🎯 Roll Call: {$result['roll_call_mark']}/10");
            $this->line("🏷️ Eligibility: {$result['eligibility']}");
        } else {
            $this->error('❌ No timetable found for course 26.');
            $this->info('Please insert sample timetable data first.');
        }

        $this->newLine();
        $this->info('🧪 Test completed!');
    }
}
