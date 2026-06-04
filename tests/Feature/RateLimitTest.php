<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_throttled_after_5_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'test@test.com', 'password' => 'wrong']);
        }
        $response = $this->post('/login', ['email' => 'test@test.com', 'password' => 'wrong']);
        $response->assertStatus(429);
    }

    public function test_registration_is_throttled_after_3_attempts(): void
    {
        // Make 4 identical requests (with invalid data) to hit the throttle limit
        for ($i = 0; $i < 4; $i++) {
            $this->post(route('register.post'), [
                'name' => 'User', 'email' => 'test@test.com',
                'password' => 'password123', 'password_confirmation' => 'password123',
            ]);
        }
        // 5th request should be throttled
        $response = $this->post(route('register.post'), [
            'name' => 'User', 'email' => 'test@test.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ]);
        $response->assertStatus(429);
    }
}
