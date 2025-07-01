<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsUser()
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        return [$user, $this->actingAs($user, 'sanctum')];
    }

    public function test_show_me()
    {
        [$user, $client] = $this->actingAsUser();
        $client->getJson('/api/me')
            ->assertOk()
            ->assertJsonFragment(['id' => $user->id, 'email' => $user->email]);
    }

    public function test_update_me_success()
    {
        [$user, $client] = $this->actingAsUser();
        $client->putJson('/api/me', [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '1234567890',
        ])
            ->assertOk()
            ->assertJsonFragment(['name' => 'New Name', 'email' => 'new@example.com']);
    }

    public function test_update_me_with_invalid_email_fails()
    {
        [$user, $client] = $this->actingAsUser();
        $client->putJson('/api/me', [
            'email' => 'notanemail',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_update_password_success()
    {
        [$user, $client] = $this->actingAsUser();
        $client->putJson('/api/me/password', [
            'current_password' => 'password',
            'new_password' => 'newpassword123',
        ])
            ->assertOk()
            ->assertJsonFragment(['message' => 'Password updated successfully.']);
        $this->assertTrue(Hash::check('newpassword123', $user->refresh()->password));
    }

    public function test_update_password_with_wrong_current_password_fails()
    {
        [$user, $client] = $this->actingAsUser();
        $client->putJson('/api/me/password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
        ])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Current password is incorrect.']);
    }

    public function test_update_password_with_short_new_password_fails()
    {
        [$user, $client] = $this->actingAsUser();
        $client->putJson('/api/me/password', [
            'current_password' => 'password',
            'new_password' => '123',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['new_password']);
    }
}
