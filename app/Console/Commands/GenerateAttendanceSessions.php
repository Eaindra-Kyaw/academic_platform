<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\TimetableEntry;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use Carbon\Carbon;

class GenerateAttendanceSessions extends Command
{
    protected $signature = 'attendance:generate-sessions
                            {--course= : Generate for specific course ID}
                            {--weeks=10 : Number of weeks to go back}
                            {--dry-run : Preview what would be created without saving}';

    protected $description = 'Generate attendance sessions from timetable entries for all courses';

    public function handle()
    {
        $this->info('📊 Generating Attendance Sessions from Timetable...');
        $this->info('═══════════════════════════════════════════');

        $courseId = $this->option('course');
        $weeks = (int) $this->option('weeks');
        $dryRun = $this->option('dry-run');

        // Get timetable entries
        $query = TimetableEntry::where('is_active', true);

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        $timetable = $query->with('course')->get();

        if ($timetable->isEmpty()) {
            $this->error('❌ No timetable entries found!');
            $this->info('💡 Please add timetable entries first via "Manage Timetable".');
            return 1;
        }

        $this->info("📋 Found " . $timetable->count() . " timetable entries");
        $this->newLine();

        // Calculate date range
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subWeeks($weeks);

        $this->info("📅 Date range: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
        $this->info("📅 Weeks to cover: {$weeks} weeks");
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️ DRY RUN MODE – No sessions will be created');
            $this->newLine();
        }

        $totalCreated = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        $bar = $this->output->createProgressBar($timetable->count());
        $results = [];

        foreach ($timetable as $entry) {
            $course = $entry->course;
            if (!$course) {
                $this->warn("⚠️ Course not found for timetable entry ID: {$entry->id}");
                $bar->advance();
                continue;
            }

            $result = $this->generateSessionsForCourse(
                $course,
                $entry,
                $startDate,
                $endDate,
                $dryRun
            );

            $results[] = [
                'course' => $course->course_code,
                'created' => $result['created'],
                'skipped' => $result['skipped'],
                'errors' => $result['errors'],
            ];

            $totalCreated += $result['created'];
            $totalSkipped += $result['skipped'];
            $totalErrors += $result['errors'];

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Show summary
        $this->info('📊 SUMMARY');
        $this->info('═══════════════════════════════════════════');
        $this->info("✅ Created: {$totalCreated} sessions");
        $this->info("⏭️ Skipped: {$totalSkipped} sessions (already exist)");
        if ($totalErrors > 0) {
            $this->error("❌ Errors: {$totalErrors}");
        }
        $this->newLine();

        // Show table
        $this->info('📋 Per-Course Breakdown');
        $this->table(
            ['Course', 'Created', 'Skipped', 'Errors'],
            array_map(function($r) {
                return [$r['course'], $r['created'], $r['skipped'], $r['errors']];
            }, $results)
        );

        if (!$dryRun && $totalCreated > 0) {
            $this->newLine();
            $this->info('🔄 Running attendance evaluation...');

            if ($courseId) {
                $this->call('attendance:evaluate', ['--course' => $courseId]);
            } else {
                $this->call('attendance:evaluate');
            }
        }

        return 0;
    }

    private function generateSessionsForCourse($course, $timetableEntry, $startDate, $endDate, $dryRun)
    {
        $created = 0;
        $skipped = 0;
        $errors = 0;

        $dayOfWeek = $timetableEntry->day_of_week;
        $periodCount = $timetableEntry->period_count ?? 1;
        $room = $timetableEntry->room ?? $course->room ?? 'N/A';

        $startTime = $timetableEntry->start_time ?? '08:00:00';
        $endTime = $timetableEntry->end_time ?? '08:50:00';
        $duration = $this->calculateDuration($startTime, $endTime);

        // Get total enrolled students for this course
        $totalStudents = Enrollment::where('course_id', $course->id)
            ->where('status', 'approved')
            ->count();

        // Get all dates for this day of week between start and end
        $current = $startDate->copy();
        $dates = [];

        while ($current <= $endDate) {
            if ($current->format('l') === $dayOfWeek) {
                $dates[] = $current->format('Y-m-d');
            }
            $current->addDay();
        }

        // Also check attendance records for this course to find additional dates
        $recordDates = \App\Models\AttendanceRecord::whereHas('session', function($q) use ($course) {
            $q->where('course_id', $course->id);
        })->pluck('created_at')->map(function($date) {
            return $date->format('Y-m-d');
        })->toArray();

        $allDates = array_unique(array_merge($dates, $recordDates));
        sort($allDates);

        foreach ($allDates as $date) {
            // Check if session already exists
            $exists = AttendanceSession::where('course_id', $course->id)
                ->whereDate('session_date', $date)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $created++;
                continue;
            }

            try {
                $manualCode = strtoupper(substr(md5($date . $course->id . rand()), 0, 6));
                $sessionToken = hash('sha256', $date . $course->id . uniqid());

                AttendanceSession::create([
                    'course_id' => $course->id,
                    'lecturer_id' => $timetableEntry->lecturer_id ?? $course->lecturer_id,
                    'session_date' => $date,
                    'period_count' => $periodCount,
                    'conducted_periods' => $periodCount,
                    'status' => 'ended',
                    'started_at' => $date . ' ' . $startTime,
                    'ended_at' => $date . ' ' . $endTime,
                    'expires_at' => $date . ' ' . $endTime,
                    'duration' => $duration,
                    'is_cancelled' => false,
                    'manual_code' => $manualCode,
                    'session_token' => $sessionToken,
                    'session_code' => $manualCode,
                    'room' => $room,
                    'qr_mode' => 'semester',
                    'total_students' => $totalStudents, // ✅ FIX: Set total enrolled students
                ]);

                $created++;
            } catch (\Exception $e) {
                $errors++;
                $this->error("❌ Error creating session for {$course->course_code} on {$date}: " . $e->getMessage());
            }
        }

        return [
            'created' => $created,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    private function calculateDuration($startTime, $endTime)
    {
        try {
            $start = Carbon::parse($startTime);
            $end = Carbon::parse($endTime);
            return $start->diffInMinutes($end);
        } catch (\Exception $e) {
            return 50;
        }
    }
}
