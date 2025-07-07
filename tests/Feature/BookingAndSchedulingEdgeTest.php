<?php

namespace Tests\Feature;

use App\Models\ClassType;
use App\Models\ScheduledClass;
use App\Models\User;
use Database\Seeders\ClassTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingAndSchedulingEdgeTest extends TestCase
{
    use RefreshDatabase;

    protected User $member;
    protected User $instructor;
    protected ClassType $classType;

    /**
     * Set up the testing environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->member = User::factory()->create(['role' => 'member']);
        $this->instructor = User::factory()->create(['role' => 'instructor']);
        $this->seed(ClassTypeSeeder::class);
        $this->classType = ClassType::first();
    }

    public function test_user_cannot_book_class_when_capacity_reached()
    {
        $scheduledClass = ScheduledClass::factory()->create([
            'class_type_id' => $this->classType->id,
            'capacity' => 1,
        ]);
        $scheduledClass->members()->attach($this->member->id);
        // Create a second member to attempt the booking
        $member2 = User::factory()->create(['role' => 'member']);
        $response = $this->actingAs($member2)->post('/member/booking', [
            'scheduled_class_id' => $scheduledClass->id,
        ]);
        $response->assertSessionHasErrors('scheduled_class_id');
    }

    public function test_instructor_cannot_schedule_class_with_invalid_class_type()
    {
        $response = $this->actingAs($this->instructor)->post('/instructor/schedule', [
            'class_type_id' => 999999, // invalid
            'date' => now()->addDay()->toDateString(),
            'time' => '10:00:00',
        ]);

        $response->assertSessionHasErrors('class_type_id');
    }

    public function test_member_cannot_access_instructor_routes()
    {
        $this->actingAs($this->member);

        $response = $this->get('/instructor/dashboard');
        $response->assertRedirect('/dashboard');

        $response = $this->get($response->headers->get('Location'));
        $response->assertRedirect('/member/dashboard');
    }

    public function test_instructor_cannot_access_member_routes()
    {
        $this->actingAs($this->instructor);

        $response = $this->get('/member/dashboard');
        $response->assertRedirect('/dashboard');

        $response = $this->get($response->headers->get('Location'));
        $response->assertRedirect('/instructor/dashboard');
    }

    public function test_deleting_user_cascades_bookings_and_scheduled_classes()
    {
        // Test instructor deletion cascades to scheduled classes
        $scheduledClass = ScheduledClass::factory()->create([
            'instructor_id' => $this->instructor->id,
            'class_type_id' => $this->classType->id,
        ]);
        $scheduledClass->members()->attach($this->member->id);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->member->id,
            'scheduled_class_id' => $scheduledClass->id,
        ]);

        $this->instructor->delete();

        $this->assertDatabaseMissing('scheduled_classes', ['id' => $scheduledClass->id]);
        $this->assertDatabaseMissing('bookings', ['scheduled_class_id' => $scheduledClass->id]);

        // Test member deletion cascades to bookings
        $scheduledClass2 = ScheduledClass::factory()->create(['class_type_id' => $this->classType->id]);
        $scheduledClass2->members()->attach($this->member->id);

        $this->member->delete();

        $this->assertDatabaseMissing('bookings', [
            'user_id' => $this->member->id,
            'scheduled_class_id' => $scheduledClass2->id,
        ]);
    }

    public function test_booking_with_missing_or_invalid_data_fails_gracefully()
    {
        $this->actingAs($this->member);

        $this->post('/member/booking', [
            // missing scheduled_class_id
        ])->assertSessionHasErrors('scheduled_class_id');

        $this->post('/member/booking', [
            'scheduled_class_id' => 999999, // invalid
        ])->assertSessionHasErrors('scheduled_class_id');
    }

    public function test_scheduling_with_missing_or_invalid_data_fails_gracefully()
    {
        $this->actingAs($this->instructor)->post('/instructor/schedule', [
            // missing class_type_id, date, time
        ])->assertSessionHasErrors(['class_type_id', 'date', 'time']);
    }
}
