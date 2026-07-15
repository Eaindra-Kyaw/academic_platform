<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceSession;
use Carbon\Carbon;

class CleanupCourseSessions extends Seeder
{
    public function run()
    {
        $courseId = 30; // Machine Learning

        echo "🔍 Cleaning up sessions for course ID: {$courseId}\n";

        $sessions = AttendanceSession::where('course_id', $courseId)
            ->orderBy('session_date')
            ->get();

        $seenDates = [];
        $deleted = 0;

        foreach ($sessions as $s) {
            $date = Carbon::parse($s->session_date)->format('Y-m-d');
            if (in_array($date, $seenDates)) {
                $s->delete();
                $deleted++;
                echo "🗑️ Deleted duplicate: {$date}\n";
            } else {
                $seenDates[] = $date;
            }
        }

        $remaining = AttendanceSession::where('course_id', $courseId)->count();
        echo "\n✅ Deleted {$deleted} duplicate sessions!\n";
        echo "📊 Total sessions remaining: {$remaining}\n";

        // Update period count
        AttendanceSession::where('course_id', $courseId)->update([
            'period_count' => 1,
            'conducted_periods' => 1
        ]);

        echo "✅ Updated all sessions to period_count = 1\n";
    }
}
