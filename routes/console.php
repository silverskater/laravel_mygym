<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule a command to remind members to book classes
Schedule::command('app:remind-members')
    ->daily()
    ->withoutOverlapping()
    ->onFailure(function () {
        // Log the failure or send a notification
        Log::error('Failed to remind members to book classes.');
    });

// Schedule a command to clean up old scheduled classes
Schedule::command('app:cleanup-scheduled-classes')
    ->weekly()
    ->withoutOverlapping()
    ->onFailure(function () {
        // Log the failure or send a notification
        Log::error('Failed to clean up old scheduled classes.');
    });
