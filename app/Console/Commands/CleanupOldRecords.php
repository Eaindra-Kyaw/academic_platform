<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AuditLog;
use Carbon\Carbon;

class CleanupOldRecords extends Command
{
    protected $signature = 'attendance:cleanup {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Clean up old audit logs (keep academic records permanently)';

    public function handle()
    {
        $this->info('🧹 Starting cleanup...');
        $dryRun = $this->option('dry-run');

        // Only delete audit logs older than 5 years
        // All academic records are KEPT PERMANENTLY
        $cutoffDate = Carbon::now()->subYears(5);

        $this->info("🗑️ Audit logs older than: " . $cutoffDate->format('Y-m-d'));
        $this->info("📚 Academic records: KEPT PERMANENTLY (never deleted)");

        $oldAuditLogs = AuditLog::where('created_at', '<', $cutoffDate)->count();

        $this->line("Audit logs to delete: {$oldAuditLogs}");

        if ($dryRun) {
            $this->info('📋 Dry run - no records were deleted.');
            return 0;
        }

        if ($oldAuditLogs > 0) {
            AuditLog::where('created_at', '<', $cutoffDate)->delete();
            $this->info("✅ Deleted {$oldAuditLogs} audit logs (older than 5 years)");
        }

        $this->info('🎉 Cleanup complete! Academic records remain intact.');
        return 0;
    }
}
