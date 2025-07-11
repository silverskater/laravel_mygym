<?php

namespace App\Policies;

use App\Models\ScheduledClass;
use App\Models\User;

class ScheduledClassPolicy
{
    public function delete(User $user, ScheduledClass $scheduledClass)
    {
        // Check if the user is admin or the instructor of the scheduled class.
        return ($user->id === $scheduledClass->instructor_id || $user->role === 'admin')
            //&& $scheduledClass->scheduled_at->isFuture()
            && $scheduledClass->scheduled_at > now()->addHours(2);
    }
}
