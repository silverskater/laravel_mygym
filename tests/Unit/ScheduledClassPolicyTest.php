<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\ClassType;
use App\Models\ScheduledClass;
use App\Policies\ScheduledClassPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduledClassPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ClassTypeSeeder::class);
    }

    public function test_instructor_can_delete_class_more_than_two_hours_before_start()
    {
        $user = User::factory()->create(['role' => 'instructor']);
        $scheduledClass = ScheduledClass::factory()->create([
            'instructor_id' => $user->id,
            'scheduled_at' => now()->addHours(3),
            'class_type_id' => ClassType::first()->id,
        ]);
        $policy = new ScheduledClassPolicy();
        $this->assertTrue($policy->delete($user, $scheduledClass));
    }

    public function test_instructor_cannot_delete_class_less_than_two_hours_before_start()
    {
        $user = User::factory()->create(['role' => 'instructor']);
        $scheduledClass = ScheduledClass::factory()->create([
            'instructor_id' => $user->id,
            'scheduled_at' => now()->addHour(),
            'class_type_id' => ClassType::first()->id,
        ]);
        $policy = new ScheduledClassPolicy();
        $this->assertFalse($policy->delete($user, $scheduledClass));
    }

    public function test_admin_can_delete_class_more_than_two_hours_before_start()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $scheduledClass = ScheduledClass::factory()->create([
            'scheduled_at' => now()->addHours(3),
            'class_type_id' => ClassType::first()->id,
        ]);
        $policy = new ScheduledClassPolicy();
        $this->assertTrue($policy->delete($user, $scheduledClass));
    }

    public function test_admin_cannot_delete_class_less_than_two_hours_before_start()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $scheduledClass = ScheduledClass::factory()->create([
            'scheduled_at' => now()->addHour(),
            'class_type_id' => ClassType::first()->id,
        ]);
        $policy = new ScheduledClassPolicy();
        $this->assertFalse($policy->delete($user, $scheduledClass));
    }

    public function test_other_users_cannot_delete_class()
    {
        $user = User::factory()->create(['role' => 'member']);
        $scheduledClass = ScheduledClass::factory()->create([
            'scheduled_at' => now()->addHours(3),
            'class_type_id' => ClassType::first()->id,
        ]);
        $policy = new ScheduledClassPolicy();
        $this->assertFalse($policy->delete($user, $scheduledClass));
    }
}
