<?php

namespace Tests\Feature\Api;

use App\Models\ClassType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassTypeTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        return $this->actingAs($admin, 'sanctum');
    }

    public function test_list_class_types()
    {
        ClassType::factory()->count(2)->create();
        $this->actingAsAdmin()
            ->getJson('/api/class-types')
            ->assertOk()
            ->assertJsonStructure([['id', 'name', 'description', 'duration']]);
    }

    public function test_show_class_type()
    {
        $ct = ClassType::factory()->create();
        $this->actingAsAdmin()
            ->getJson("/api/class-types/{$ct->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $ct->id]);
    }

    public function test_show_nonexistent_class_type_returns_404()
    {
        $this->actingAsAdmin()
            ->getJson('/api/class-types/99999')
            ->assertStatus(404);
    }

    public function test_create_class_type_success()
    {
        $data = [
            'name' => 'Yoga',
            'description' => 'Stretch',
            'duration' => 60,
            'capacity' => 20,
            'level' => 'all',
            'status' => 'active',
            'color' => '#fff',
        ];
        $this->actingAsAdmin()
            ->postJson('/api/class-types', $data)
            ->assertCreated()
            ->assertJsonFragment(['name' => 'Yoga']);
    }

    public function test_create_class_type_with_invalid_data_fails()
    {
        $this->actingAsAdmin()
            ->postJson('/api/class-types', [
                'name' => '',
                'description' => '',
                'duration' => 0,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description', 'duration']);
    }

    public function test_update_class_type_success()
    {
        $ct = ClassType::factory()->create();
        $this->actingAsAdmin()
            ->putJson("/api/class-types/{$ct->id}", [
                'name' => 'Updated',
                'duration' => 90,
            ])
            ->assertOk()
            ->assertJsonFragment(['name' => 'Updated', 'duration' => 90]);
    }

    public function test_update_class_type_with_invalid_data_fails()
    {
        $ct = ClassType::factory()->create();
        $this->actingAsAdmin()
            ->putJson("/api/class-types/{$ct->id}", [
                'duration' => -1,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['duration']);
    }

    public function test_delete_class_type_success()
    {
        $ct = ClassType::factory()->create();
        $this->actingAsAdmin()
            ->deleteJson("/api/class-types/{$ct->id}")
            ->assertNoContent();
        $this->assertDatabaseMissing('class_types', ['id' => $ct->id]);
    }

    public function test_delete_nonexistent_class_type_returns_404()
    {
        $this->actingAsAdmin()
            ->deleteJson('/api/class-types/99999')
            ->assertStatus(404);
    }
}
