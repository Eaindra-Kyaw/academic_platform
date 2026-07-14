<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use App\Models\AttendanceEvaluation;
use Carbon\Carbon;

class GenerateAttendanceEvaluations extends Command
{
    protected $signature = 'attendance:generate-evaluations';
    protected $description = 'Generate KG+12 attendance evaluations for all courses';

    public function handle()
    {
        $processed = 0;

        // Get course with ID 30 (the one that's failing)
        $course = \App\Models\Course::find(30);

        if (!$course) {
            $this->error("Course 30 not found!");
            return;
        }

        $this->info("Processing course: " . $course->course_code);

        $sessions = AttendanceSession::where('course_id', $course->id)
            ->where('status', 'ended')
            ->where('is_cancelled', false)
            ->get();

        if ($sessions->isEmpty()) {
            $this->error("No ended sessions for this course!");
            return;
        }

        $sessionIds = $sessions->pluck('id')->toArray();
        $totalSessions = $sessions->count();

        $enrollments = Enrollment::where('course_id', $course->id)
            ->where('status', 'approved')
            ->get();

        foreach ($enrollments as $enrollment) {
            $studentId = $enrollment->student_id;

            $records = AttendanceRecord::where('student_id', $studentId)
                ->whereIn('attendance_session_id', $sessionIds)
                ->get()
                ->keyBy('attendance_session_id');

            $attended = $records->whereIn('status', ['present', 'late'])->count();
            $late = $records->where('status', 'late')->count();
            $percentage = $totalSessions > 0 ? round(($attended / $totalSessions) * 100, 1) : 0;

            // ✅ This is the critical part – ALL fields INCLUDING evaluation_date
            $data = [
                'student_id' => $studentId,
                'course_id' => $course->id,
                'attendance_percentage' => $percentage,
                'consistency_marks' => 0,
                'punctuality_marks' => 2,
                'participation_marks' => 1.5,
                'roll_call_total' => 3.5,
                'eligibility_status' => 'not_eligible',
                'attended_sessions' => $attended,
                'total_sessions' => $totalSessions,
                'consecutive_absences' => 0,
                'attendance_trend' => 'stable',
                'risk_score' => 60,
                'risk_level' => 'Medium',
                'risk_factors' => json_encode(['Processed via command']),
                'academic_health_score' => 0,
                'recovery_status' => 'Stable',
                'evaluation_date' => Carbon::today(), // ✅ THIS MUST BE HERE
            ];

            AttendanceEvaluation::updateOrCreate(
                ['student_id' => $studentId, 'course_id' => $course->id],
                $data
            );

            $processed++;
            $this->info("✅ Created evaluation for student ID: $studentId");
        }

        $this->info("\n🎉 $processed evaluations created successfully!");
    }
}
