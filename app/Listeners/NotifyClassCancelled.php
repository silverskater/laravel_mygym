<?php

namespace App\Listeners;

use App\Events\ClassCancelled;
use App\Jobs\NotifyClassCancelledJob;
use App\Mail\ClassCancelledMail;
use App\Notifications\ClassCancelledNotification;
use Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Notification;

class NotifyClassCancelled
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ClassCancelled $event): void
    {
        $scheduledClass = $event->scheduledClass;

        Log::info('Class cancelled', [
            'scheduled_class_id' => $scheduledClass->id,
            'scheduled_at' => $scheduledClass->scheduled_at,
            'instructor_id' => $scheduledClass->instructor_id,
            'user_id' => Auth::id(),
            'members_count' => $scheduledClass->members()->count(),
        ]);

        /*$scheduledClass->members->each(function ($member) use ($scheduledClass) {
            Mail::to($member)->send(new ClassCancelledMail($scheduledClass));
        });*/
        NotifyClassCancelledJob::dispatch(
            $scheduledClass->members,
            $scheduledClass
        );
    }
}
