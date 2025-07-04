<?php

namespace App\Jobs;

use App\Models\ScheduledClass;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ClassCancelledNotification;

class NotifyClassCancelledJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $members, public ScheduledClass $scheduledClass)
    {
        $this->scheduledClass = $scheduledClass;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Notification::send($this->members, new ClassCancelledNotification($this->scheduledClass));
    }
}
