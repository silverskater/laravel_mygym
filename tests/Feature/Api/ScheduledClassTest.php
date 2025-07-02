<?php

namespace Tests\Feature\Api;

use App\Models\ScheduledClass;
use App\Models\ClassType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduledClassTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        return $this->actingAs($admin, 'sanctum');
    }

    public function test_list_scheduled_classes()
    {
        $classType = ClassType::factory()->create();
        ScheduledClass::factory()->count(2)->create([
            'class_type_id' => $classType->id,
        ]);
        $this->actingAsAdmin()
            ->getJson('/api/scheduled-classes')
            ->assertOk()
            ->assertJsonStructure([['id', 'class_type_id', 'instructor_id', 'scheduled_at']]);
    }

    public function test_show_scheduled_class()
    {
        $classType = ClassType::factory()->create();
        $sc = ScheduledClass::factory()->create([
            'class_type_id' => $classType->id,
        ]);
        $this->actingAsAdmin()
            ->getJson("/api/scheduled-classes/{$sc->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $sc->id]);
    }

    public function test_show_nonexistent_scheduled_class_returns_404()
    {
        $response = $this->actingAsAdmin()
            ->getJson('/api/scheduled-classes/99999');
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Resource not found.']);
    }

    public function test_create_scheduled_class_success()
    {
        $classType = ClassType::factory()->create();
        $instructor = User::factory()->create(['role' => 'instructor']);
        $data = [
            'class_type_id' => $classType->id,
            'instructor_id' => $instructor->id,
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'capacity' => 10,
            'status' => 'scheduled',
            'location' => 'Studio 1',
            'description' => 'Morning class',
        ];
        $this->actingAsAdmin()
            ->postJson('/api/scheduled-classes', $data)
            ->assertCreated()
            ->assertJsonFragment(['class_type_id' => $classType->id]);
    }

    public function test_create_scheduled_class_with_invalid_data_fails()
    {
        $this->actingAsAdmin()
            ->postJson('/api/scheduled-classes', [
                'class_type_id' => 999,
                'instructor_id' => 999,
                'scheduled_at' => 'notadate',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['class_type_id', 'instructor_id', 'scheduled_at']);
    }

    public function test_update_scheduled_class_success()
    {
        $classType = ClassType::factory()->create();
        $sc = ScheduledClass::factory()->create([
            'class_type_id' => $classType->id,
        ]);
        $this->actingAsAdmin()
            ->putJson("/api/scheduled-classes/{$sc->id}", [
                'capacity' => 99,
                'status' => 'completed',
            ])
            ->assertOk()
            ->assertJsonFragment(['capacity' => 99, 'status' => 'completed']);
    }

    public function test_update_scheduled_class_with_invalid_data_fails()
    {
        $classType = ClassType::factory()->create();
        $sc = ScheduledClass::factory()->create([
            'class_type_id' => $classType->id,
        ]);
        $this->actingAsAdmin()
            ->putJson("/api/scheduled-classes/{$sc->id}", [
                'capacity' => -1,
                'status' => 'notastatus',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['capacity', 'status']);
    }

    public function test_delete_scheduled_class_success()
    {
        $classType = ClassType::factory()->create();
        $sc = ScheduledClass::factory()->create([
            'class_type_id' => $classType->id,
        ]);
        $this->actingAsAdmin()
            ->deleteJson("/api/scheduled-classes/{$sc->id}")
            ->assertNoContent();
        $this->assertDatabaseMissing('scheduled_classes', ['id' => $sc->id]);
    }

    public function test_delete_nonexistent_scheduled_class_returns_404()
    {
        $response = $this->actingAsAdmin()
            ->deleteJson('/api/scheduled-classes/99999');
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Resource not found.']);
    }

    public function test_index_by_class_type()
    {
        $classType = ClassType::factory()->create();
        ScheduledClass::factory()->count(2)->create(['class_type_id' => $classType->id]);
        $this->actingAsAdmin()
            ->getJson("/api/class-types/{$classType->id}/scheduled-classes")
            ->assertOk()
            ->assertJsonCount(2);
    }

    public function test_index_by_class_type_nonexistent_returns_404_with_message()
    {
        $this->actingAsAdmin()
            ->getJson('/api/class-types/99999/scheduled-classes')
            ->assertStatus(404)
            ->assertJson(['message' => 'Resource not found.']);
    }

    public function test_user_classes_for_instructor()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $classType = \App\Models\ClassType::factory()->create();
        \App\Models\ScheduledClass::factory()->count(2)->create([
            'instructor_id' => $instructor->id,
            'class_type_id' => $classType->id,
        ]);
        $this->actingAsAdmin()
            ->getJson("/api/users/{$instructor->id}/scheduled-classes")
            ->assertOk()
            ->assertJsonCount(2);
    }

    public function test_user_classes_for_member_returns_empty()
    {
        $member = User::factory()->create(['role' => 'member']);
        // Ensure at least one class type exists for consistency
        \App\Models\ClassType::factory()->create();
        $this->actingAsAdmin()
            ->getJson("/api/users/{$member->id}/scheduled-classes")
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_update_nonexistent_scheduled_class_returns_404()
    {
        $classType = ClassType::factory()->create();
        $this->actingAsAdmin()
            ->putJson('/api/scheduled-classes/99999', [
                'capacity' => 99,
                'status' => 'completed',
                'class_type_id' => $classType->id,
            ])
            ->assertStatus(404)
            ->assertJson(['message' => 'Resource not found.']);
    }

    public function test_update_scheduled_class_requires_class_type_id_validation()
    {
        $classType = ClassType::factory()->create();
        $sc = ScheduledClass::factory()->create([
            'class_type_id' => $classType->id,
        ]);
        // Try to update with an invalid class_type_id
        $this->actingAsAdmin()
            ->putJson("/api/scheduled-classes/{$sc->id}", [
                'class_type_id' => 999999, // non-existent
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['class_type_id']);
    }

    public function test_user_classes_for_nonexistent_user_returns_404_with_message()
    {
        $this->actingAsAdmin()
            ->getJson('/api/users/99999/scheduled-classes')
            ->assertStatus(404)
            ->assertJson(['message' => 'Resource not found.']);
    }
}
