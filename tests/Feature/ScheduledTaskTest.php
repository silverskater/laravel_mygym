<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ClassType;
use App\Models\ScheduledClass;
use App\Notifications\RemindMembersNotification;
use Database\Seeders\ClassTypeSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduledTaskTest extends TestCase
{
    use RefreshDatabase;

    protected $instructor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->instructor = User::factory()->create([
            'role' => 'instructor',
        ]);
        $this->seed(ClassTypeSeeder::class);
    }

    public function test_remind_members_command_only_notifies_correct_users()
    {
        Notification::fake();

        $classType = ClassType::first();

        $memberToRemind = User::factory()->create(['role' => 'member']);
        $memberWithFutureBooking = User::factory()->create(['role' => 'member']);
        $memberRecentlyBooked = User::factory()->create(['role' => 'member']);

        // Member with future booking
        $futureClass = ScheduledClass::factory()->create([
            'scheduled_at' => now()->addDays(2),
            'class_type_id' => $classType->id,
            'instructor_id' => $this->instructor->id,
        ]);
        $futureClass->members()->attach($memberWithFutureBooking->id);

        // Member who booked recently (within 7 days)
        $recentClass = ScheduledClass::factory()->create([
            'scheduled_at' => now()->subDays(1),
            'class_type_id' => $classType->id,
            'instructor_id' => $this->instructor->id,
        ]);
        $recentClass->members()->attach($memberRecentlyBooked->id, [
            'created_at' => now()->subDays(2),
        ]);

        // Member to remind: no future bookings and no recent bookings
        // (no bookings at all)

        Artisan::call('app:remind-members');

        Notification::assertSentTo($memberToRemind, RemindMembersNotification::class);
        Notification::assertNotSentTo($memberWithFutureBooking, RemindMembersNotification::class);
        Notification::assertNotSentTo($memberRecentlyBooked, RemindMembersNotification::class);
    }

    public function test_cleanup_scheduled_classes_command_deletes_only_old_classes()
    {
        $classType = ClassType::first();

        $oldClass = ScheduledClass::factory()->create([
            'scheduled_at' => now()->subDays(11),
            'class_type_id' => $classType->id,
            'instructor_id' => $this->instructor->id,
        ]);
        $recentClass = ScheduledClass::factory()->create([
            'scheduled_at' => now()->subDays(5),
            'class_type_id' => $classType->id,
            'instructor_id' => $this->instructor->id,
        ]);

        Artisan::call('app:cleanup-scheduled-classes');

        $this->assertDatabaseMissing('scheduled_classes', ['id' => $oldClass->id]);
        $this->assertDatabaseHas('scheduled_classes', ['id' => $recentClass->id]);
    }
}
