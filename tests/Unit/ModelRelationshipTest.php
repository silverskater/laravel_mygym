<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\ClassType;
use App\Models\ScheduledClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ClassTypeSeeder::class);
    }

    public function test_instructor_scheduled_classes_relationship_returns_correct_classes()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $otherInstructor = User::factory()->create(['role' => 'instructor']);
        $classType = ClassType::first();

        $class1 = ScheduledClass::factory()->create([
            'instructor_id' => $instructor->id,
            'class_type_id' => $classType->id,
        ]);
        $class2 = ScheduledClass::factory()->create([
            'instructor_id' => $instructor->id,
            'class_type_id' => $classType->id,
        ]);
        $class3 = ScheduledClass::factory()->create([
            'instructor_id' => $otherInstructor->id,
            'class_type_id' => $classType->id,
        ]);

        $scheduledClasses = $instructor->scheduledClasses;
        $this->assertCount(2, $scheduledClasses);
        $this->assertTrue($scheduledClasses->contains($class1));
        $this->assertTrue($scheduledClasses->contains($class2));
        $this->assertFalse($scheduledClasses->contains($class3));
    }

    public function test_member_bookings_relationship_returns_correct_classes()
    {
        $member = User::factory()->create(['role' => 'member']);
        $otherMember = User::factory()->create(['role' => 'member']);
        $classType = ClassType::first();

        $class1 = ScheduledClass::factory()->create(['class_type_id' => $classType->id]);
        $class2 = ScheduledClass::factory()->create(['class_type_id' => $classType->id]);
        $class3 = ScheduledClass::factory()->create(['class_type_id' => $classType->id]);

        // Attach bookings
        $member->bookings()->attach($class1->id);
        $member->bookings()->attach($class2->id);
        $otherMember->bookings()->attach($class3->id);

        $bookings = $member->bookings;
        $this->assertCount(2, $bookings);
        $this->assertTrue($bookings->contains($class1));
        $this->assertTrue($bookings->contains($class2));
        $this->assertFalse($bookings->contains($class3));
    }

    public function test_scheduled_class_members_relationship_returns_correct_users()
    {
        $member1 = User::factory()->create(['role' => 'member']);
        $member2 = User::factory()->create(['role' => 'member']);
        $otherMember = User::factory()->create(['role' => 'member']);
        $classType = ClassType::first();

        $scheduledClass = ScheduledClass::factory()->create(['class_type_id' => $classType->id]);
        $otherClass = ScheduledClass::factory()->create(['class_type_id' => $classType->id]);

        $scheduledClass->members()->attach([$member1->id, $member2->id]);
        $otherClass->members()->attach($otherMember->id);

        $members = $scheduledClass->members;
        $this->assertCount(2, $members);
        $this->assertTrue($members->contains($member1));
        $this->assertTrue($members->contains($member2));
        $this->assertFalse($members->contains($otherMember));
    }

    public function test_scope_upcoming_returns_only_future_classes()
    {
        $classType = ClassType::first();
        $pastClass = ScheduledClass::factory()->create([
            'scheduled_at' => now()->subDay(),
            'class_type_id' => $classType->id,
        ]);
        $futureClass1 = ScheduledClass::factory()->create([
            'scheduled_at' => now()->addHour(),
            'class_type_id' => $classType->id,
        ]);
        $futureClass2 = ScheduledClass::factory()->create([
            'scheduled_at' => now()->addDays(2),
            'class_type_id' => $classType->id,
        ]);

        $upcoming = ScheduledClass::upcoming()->get();
        $this->assertTrue($upcoming->contains($futureClass1));
        $this->assertTrue($upcoming->contains($futureClass2));
        $this->assertFalse($upcoming->contains($pastClass));
    }

    public function test_scope_upcoming_returns_empty_when_no_future_classes()
    {
        $classType = ClassType::first();
        ScheduledClass::factory()->create([
            'scheduled_at' => now()->subDay(),
            'class_type_id' => $classType->id,
        ]);
        $upcoming = ScheduledClass::upcoming()->get();
        $this->assertCount(0, $upcoming);
    }
}
