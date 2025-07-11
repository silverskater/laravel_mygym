<?php

namespace App\Console\Commands;

use App;
use Notification;
use App\Models\User;
use Illuminate\Console\Command;
use App\Notifications\RemindMembersNotification;

class RemindMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remind-members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remind members to book a class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $members = User::where('role', 'member')
            // Get members who have no future bookings.
            ->whereDoesntHave('bookings', function ($query) {
                $query->where('scheduled_at', '>=', now());
            })
            // AND who have not made a booking in the last 7 days.
            ->whereDoesntHave('bookings', function ($query) {
                $query->where('bookings.created_at', '>=', now()->subDays(7));
            })
            ->select('id', 'name', 'email')
            ->get();
        if ($members->isEmpty()) {
            $this->info('No members found who need reminders.');
            return;
        }
        if (config('app.debug')) {
            $this->table(['ID', 'Name', 'Email'], $members->toArray());
        }
        Notification::send($members, new RemindMembersNotification());
        $this->info('Reminders sent to inactive members (no future bookings and no classes booked in the last 7 days).');
    }
}
