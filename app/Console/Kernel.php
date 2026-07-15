<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\EvaluateAttendance::class,
        \App\Console\Commands\GenerateAttendanceSessions::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // ============================================================
        // DAILY TASKS
        // ============================================================

        // Run attendance evaluation daily at 11:59 PM
        $schedule->command('attendance:evaluate')->dailyAt('23:59');

        // ============================================================
        // WEEKLY TASKS
        // ============================================================

        // Weekly roll call calculation every Monday at 6:00 AM
        // (Calculate for the previous week)
        $schedule->command('attendance:evaluate --weekly')->weekly()->mondays()->at('06:00');

        // ============================================================
        // MONTHLY TASKS
        // ============================================================

        // Monthly roll call calculation on the 1st of each month at 1:00 AM
        // (Calculate for the previous month)
        $schedule->command('attendance:evaluate --monthly')->monthlyOn(1, '01:00');

        // ============================================================
        // RECURRING TASKS (Optional)
        // ============================================================

        // Cache warmup for dashboards daily at 3:00 AM
        // $schedule->command('cache:clear')->dailyAt('03:00');

        // Clean up old audit logs monthly (keep last 5 years)
        // $schedule->command('attendance:cleanup')->monthly();

        // ============================================================
        // EXPLANATION OF SCHEDULES
        // ============================================================
        //
        // 1. Daily: attendance:evaluate
        //    - Calculates attendance percentage for all students
        //    - Updates eligibility status
        //    - Runs at 11:59 PM every day
        //
        // 2. Weekly: attendance:evaluate --weekly
        //    - Calculates weekly attendance and roll call
        //    - Considers 4 or 5 class periods per week
        //    - Runs every Monday at 6:00 AM
        //
        // 3. Monthly: attendance:evaluate --monthly
        //    - Calculates monthly attendance and roll call
        //    - Accounts for holidays, cancellations
        //    - Considers 4 Mondays vs 5 Mondays
        //    - Runs on the 1st of each month at 1:00 AM
        //
        // ============================================================
        // MTU (Myanmar) Specific Logic
        // ============================================================
        //
        // For the monthly calculation:
        // - Gets all weekdays in the month (e.g., all Mondays)
        // - Checks if each day is a holiday (from academic_calendar)
        // - Checks if class was cancelled (from attendance_sessions)
        // - Calculates: Conducted Periods = Scheduled Periods - (Holidays + Cancelled)
        // - Calculates: Attendance % = Attended Periods ÷ Conducted Periods × 100
        // - Determines: Roll Call Mark (0-10) based on MTU system
        // - Determines: Eligibility (>=75% = Eligible, >=60% = Warning, <60% = Not Eligible)
        //
        // ============================================================
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
