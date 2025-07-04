<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_success()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'instructor',
        ]);
        $response->assertCreated()
            ->assertJsonStructure(['id', 'name', 'email', 'role', 'token']);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'instructor']);
    }

    public function test_register_with_duplicate_email_fails()
    {
        User::factory()->create(['email' => 'dupe@example.com']);
        $response = $this->postJson('/api/register', [
            'name' => 'Another User',
            'email' => 'dupe@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_register_with_invalid_role_fails()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'User',
            'email' => 'rolefail@example.com',
            'password' => 'password123',
            'role' => 'notarole',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('role');
    }

    public function test_register_with_short_password_fails()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'User',
            'email' => 'shortpw@example.com',
            'password' => '123',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_register_with_edgecase_passwords()
    {
        $edgePasswords = [
            str_repeat('a', 255),
            '   leading trailing   ',
            'specialchars!@#$%^&*()',
            '123456',
        ];
        foreach ($edgePasswords as $pw) {
            $response = $this->postJson('/api/register', [
                'name' => 'Edge',
                'email' => uniqid().'@edge.com',
                'password' => $pw,
            ]);
            $response->assertCreated();
        }
    }

    public function test_login_success()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('secret123'),
        ]);
        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);
        $response->assertOk()
            ->assertJsonStructure(['id', 'name', 'email', 'role', 'token']);
    }

    public function test_login_with_wrong_password_fails()
    {
        $user = User::factory()->create([
            'email' => 'fail@example.com',
            'password' => Hash::make('rightpw'),
        ]);
        $response = $this->postJson('/api/login', [
            'email' => 'fail@example.com',
            'password' => 'wrongpw',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_login_with_nonexistent_email_fails()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nope@example.com',
            'password' => 'irrelevant',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_logout_deletes_token_and_returns_message()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/logout');
        $response->assertOk();
        $response->assertJson(['message' => 'Logged out successfully.']);
        // Token should be deleted
        $this->assertCount(0, $user->tokens()->get());
    }

    public function test_logout_without_token_still_returns_message()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJson(['message' => 'Logged out successfully.']);
    }
}
