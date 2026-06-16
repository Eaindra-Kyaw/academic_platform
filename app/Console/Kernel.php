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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Run attendance evaluation daily at 11:59 PM
        $schedule->command('attendance:evaluate')->dailyAt('23:59');

        // Alternative schedules you can choose from:
        // $schedule->command('attendance:evaluate')->daily(); // Runs at midnight
        // $schedule->command('attendance:evaluate')->twiceDaily(); // Runs twice daily
        // $schedule->command('attendance:evaluate')->everyFourHours(); // Runs every 4 hours
        // $schedule->command('attendance:evaluate')->weeklyOn(1, '00:00'); // Runs every Monday at midnight
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
