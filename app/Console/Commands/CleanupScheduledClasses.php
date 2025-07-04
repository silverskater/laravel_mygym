<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupScheduledClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-scheduled-classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old scheduled classes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Delete old scheduled classes
        $deleted = \App\Models\ScheduledClass::where('scheduled_at', '<', now()->subDays(10))->delete();
        $this->info("Cleaned up {$deleted} old scheduled classes.");
    }
}
