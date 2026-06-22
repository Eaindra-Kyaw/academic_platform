<?php

namespace App\Services;

use App\Models\Course;
use App\Models\TimetableEntry;
use App\Models\AcademicCalendar;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\RollCallSubmission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RollCallCalculator
{
    /**
     * Calculate roll call for a student in a course for a specific month
     */
    public function calculateStudentRollCall($studentId, $courseId, $month, $year)
    {
        // 1. Get timetable entries for this course
        $timetable = TimetableEntry::where('course_id', $courseId)
            ->where('is_active', true)
            ->get();

        if ($timetable->isEmpty()) {
            Log::warning("No timetable found for course: {$courseId}");
            return null;
        }

        // 2. Get the day names from timetable
        $daysOfWeek = $timetable->pluck('day_of_week')->unique()->toArray();

        // 3. Get all weekdays in this month
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // 4. Calculate expected periods (from timetable)
        $expectedPeriods = 0;
        $conductedPeriods = 0;

        // 5. Get academic calendar holidays for this month
        $holidays = AcademicCalendar::whereBetween('date', [$startDate, $endDate])
            ->whereIn('type', ['holiday', 'public_holiday', 'university_closure'])
            ->pluck('date')
            ->toArray();

        $holidayDates = array_map(function($date) {
            return Carbon::parse($date)->format('Y-m-d');
        }, $holidays);

        // 6. Get attendance sessions for this course in this month
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

        // 7. Calculate conducted periods
        $currentDate = $startDate->copy();
        $conductedPeriodsByDate = [];

        while ($currentDate <= $endDate) {
            $dayName = $currentDate->format('l');
            $dateKey = $currentDate->format('Y-m-d');

            // Check if it's a holiday
            if (in_array($dateKey, $holidayDates)) {
                $currentDate->addDay();
                continue;
            }

            // Check if it's a scheduled class day
            if (in_array($dayName, $daysOfWeek)) {
                // Get the period count for this day
                $entry = $timetable->firstWhere('day_of_week', $dayName);
                $periodCount = $entry ? $entry->period_count : 4;

                // Check if this session was cancelled
                if (in_array($dateKey, $cancelledDates)) {
                    // It was cancelled, skip
                    $currentDate->addDay();
                    continue;
                }

                // Check if this date has an attendance session (class was conducted)
                if (in_array($dateKey, $sessionDates)) {
                    $session = $sessions->firstWhere('session_date', $currentDate->format('Y-m-d'));
                    $conductedPeriods += $session ? $session->conducted_periods : $periodCount;
                    $conductedPeriodsByDate[$dateKey] = $session ? $session->conducted_periods : $periodCount;
                } else {
                    // No session = class was conducted but no QR? Still count it
                    // (This would mean lecturer didn't start QR, should be handled)
                    $conductedPeriods += $periodCount;
                    $conductedPeriodsByDate[$dateKey] = $periodCount;
                }
            }

            $currentDate->addDay();
        }

        // 8. Calculate attended periods from QR records
        $attendedPeriods = 0;

        foreach ($conductedPeriodsByDate as $date => $periods) {
            // Check if student attended on this date
            $record = AttendanceRecord::where('student_id', $studentId)
                ->whereDate('scanned_at', $date)
                ->whereHas('session', function($q) use ($courseId) {
                    $q->where('course_id', $courseId);
                })
                ->first();

            if ($record) {
                // Student attended all periods for that day
                $attendedPeriods += $periods;
            }
        }

        // 9. Calculate attendance percentage
        $percentage = $conductedPeriods > 0
            ? round(($attendedPeriods / $conductedPeriods) * 100, 2)
            : 0;

        // 10. Calculate roll call mark (MTU system)
        $rollCallMark = $this->calculateRollCallMark($percentage);

        // 11. Determine eligibility
        $eligibility = $this->determineEligibility($percentage);

        // 12. Return result
        return [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'month' => $month,
            'year' => $year,
            'total_conducted_periods' => $conductedPeriods,
            'total_attended_periods' => $attendedPeriods,
            'attendance_percentage' => $percentage,
            'roll_call_mark' => $rollCallMark,
            'eligibility_status' => $eligibility,
            'breakdown' => [
                'days_of_week' => $daysOfWeek,
                'holidays' => $holidayDates,
                'cancelled_dates' => $cancelledDates,
                'conducted_dates' => array_keys($conductedPeriodsByDate),
                'conducted_periods_by_date' => $conductedPeriodsByDate,
            ],
        ];
    }

    /**
     * Calculate roll call mark (MTU Myanmar System)
     */
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

    /**
     * Determine eligibility (MTU Myanmar System)
     */
    private function determineEligibility($percentage)
    {
        if ($percentage >= 75) return 'eligible';
        if ($percentage >= 60) return 'warning';
        return 'not_eligible';
    }

    /**
     * Calculate roll call for all students in a course
     */
    public function calculateCourseRollCall($courseId, $month, $year)
    {
        $students = \App\Models\Enrollment::where('course_id', $courseId)
            ->where('status', 'approved')
            ->pluck('student_id')
            ->toArray();

        $results = [];
        foreach ($students as $studentId) {
            $results[$studentId] = $this->calculateStudentRollCall($studentId, $courseId, $month, $year);
        }

        return $results;
    }

    /**
     * Calculate roll call for all students in a department
     */
    public function calculateDepartmentRollCall($departmentId, $month, $year)
    {
        $courses = \App\Models\Course::where('department_id', $departmentId)
            ->pluck('id')
            ->toArray();

        $results = [];
        foreach ($courses as $courseId) {
            $results[$courseId] = $this->calculateCourseRollCall($courseId, $month, $year);
        }

        return $results;
    }

    /**
     * Get monthly summary for a student
     */
    public function getStudentMonthlySummary($studentId, $month, $year)
    {
        $submissions = RollCallSubmission::where('student_id', $studentId)
            ->where('month', $month)
            ->where('year', $year)
            ->with(['course', 'course.department'])
            ->get();

        return $submissions;
    }
}
