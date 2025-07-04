<?php

namespace App\Console\Commands;

use App\Models\ScheduledClass;
use Illuminate\Console\Command;

class IncrementDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:increment-date {--days=1 : Number of days to increment the scheduled classes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Increment the date for all the scheduled classes by one or more days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        if ($days !== 0) {
            $this->info("Incrementing scheduled classes by {$days} day(s).");
        } else {
            $this->error('Invalid number of days specified. Please provide a number.');
            return;
        }
        ScheduledClass::latest('scheduled_at')->get()->each(function ($class) use ($days) {
            $class->scheduled_at = $class->scheduled_at->addDays($days); // Increment by the specified number of days
            $class->save();
            $this->info("Incremented date for class ID: {$class->id} to {$class->scheduled_at}");
        });
    }
}
