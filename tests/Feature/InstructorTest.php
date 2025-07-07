<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ClassType;
use App\Models\ScheduledClass;
use Database\Seeders\ClassTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InstructorTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => 'instructor',
        ]);
    }

    public function test_instructor_redirection_to_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertRedirectToRoute('instructor.dashboard');
        $this->followRedirects($response)
            ->assertSeeText('Hey instructor')
            ->assertViewIs('instructor.dashboard');
    }

    public function test_instructor_can_schedule_classes(): void
    {
        $this->seed(ClassTypeSeeder::class);
        $datetime = now()->addDays(1)->setTime(7, 0);
        $response = $this->actingAs($this->user)
            ->post('/instructor/schedule', [
                'class_type_id' => ClassType::where('name', 'Tai Chi')->first()->id,
                'date' => $datetime->toDateString(),
                'time' => $datetime->toTimeString(),
            ]
        );
        $this->assertDatabaseHas('scheduled_classes', [
            'class_type_id' => ClassType::where('name', 'Tai Chi')->first()->id,
            'scheduled_at' => $datetime,
            'instructor_id' => $this->user->id,
        ]);
        $response->assertRedirectToRoute('schedule.index');
    }

    public function test_instructor_can_cancel_classes(): void
    {
        $this->seed(ClassTypeSeeder::class);
        $scheduledClass = ScheduledClass::factory()->create([
            'class_type_id' => ClassType::where('name', 'Mobility Training')->first()->id,
            'scheduled_at' => now()->addDays(1)->setTime(9, 0),
            'instructor_id' => $this->user->id,
        ]);
        $response = $this->actingAs($this->user)
            ->delete('/instructor/schedule/' . $scheduledClass->id);

        $this->assertDatabaseMissing('scheduled_classes', [
            'id' => $scheduledClass->id,
        ]);
        $response->assertRedirectToRoute('schedule.index');
    }

    public function test_instructor_cannot_cancel_class_two_hours_before_start(): void
    {
        $this->seed(ClassTypeSeeder::class);
        $datetime = now()->addHours(1)->minute(0)->second(0);
        ScheduledClass::factory()->create([
            'class_type_id' => ClassType::first()->id,
            'scheduled_at' => $datetime,
            'instructor_id' => $this->user->id,
        ]);
        $this->actingAs($this->user)
            ->get('/instructor/schedule')
            ->assertDontSeeText('Cancel');
    }
}
