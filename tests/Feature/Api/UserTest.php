<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        return $this->actingAs($admin, 'sanctum');
    }

    public function test_list_users()
    {
        User::factory()->count(2)->create();
        $this->actingAsAdmin()
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonStructure([['id', 'name', 'email', 'role']]);
    }

    public function test_show_user()
    {
        $user = User::factory()->create();
        $this->actingAsAdmin()
            ->getJson("/api/users/{$user->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $user->id]);
    }

    public function test_show_nonexistent_user_returns_404()
    {
        $response = $this->actingAsAdmin()
            ->getJson('/api/users/99999');
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Resource not found.']);
    }

    public function test_update_user_success()
    {
        $user = User::factory()->create();
        $this->actingAsAdmin()
            ->putJson("/api/users/{$user->id}", [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'role' => 'instructor',
                'phone' => '1234567890',
            ])
            ->assertOk()
            ->assertJsonFragment(['name' => 'Updated Name', 'role' => 'instructor']);
    }

    public function test_update_user_with_invalid_data_fails()
    {
        $user = User::factory()->create();
        $this->actingAsAdmin()
            ->putJson("/api/users/{$user->id}", [
                'email' => 'notanemail',
                'role' => 'notarole',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'role']);
    }

    public function test_delete_user_success()
    {
        $user = User::factory()->create();
        $this->actingAsAdmin()
            ->deleteJson("/api/users/{$user->id}")
            ->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_delete_nonexistent_user_returns_404()
    {
        $response = $this->actingAsAdmin()
            ->deleteJson('/api/users/99999');
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Resource not found.']);
    }

    public function test_update_nonexistent_user_returns_404()
    {
        $this->actingAsAdmin()
            ->putJson('/api/users/99999', [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'role' => 'instructor',
                'phone' => '1234567890',
            ])
            ->assertStatus(404)
            ->assertJson(['message' => 'Resource not found.']);
    }
}
