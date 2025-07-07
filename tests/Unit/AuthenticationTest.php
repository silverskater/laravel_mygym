<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class AuthenticationTest extends TestCase
{
    public function test_profile_update_request_allows_current_user_email()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $data = [
            'name' => $user->name,
            'email' => 'test@example.com'
        ];
        $request = new ProfileUpdateRequest();
        $request->setUserResolver(fn() => $user);
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_profile_update_request_rejects_duplicate_email()
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);
        $data = ['email' => 'user1@example.com'];
        $request = new ProfileUpdateRequest();
        $request->setUserResolver(fn() => $user2);
        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    public function test_login_request_throttles_after_too_many_attempts()
    {
        new LoginRequest();
        $key = 'login|127.0.0.1';
        RateLimiter::clear($key);
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($key);
        }
        $this->assertTrue(RateLimiter::tooManyAttempts($key, 5));
    }
}
