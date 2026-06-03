<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_returns_200(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        $response = $this->post(route('register.post'), [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'customer']);
        $this->assertAuthenticated();
    }

    public function test_registration_requires_all_fields(): void
    {
        $response = $this->post(route('register.post'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_registration_requires_unique_email(): void
    {
        \App\Models\User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('register.post'), [
            'name'                  => 'Another User',
            'email'                 => 'existing@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post(route('register.post'), [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['password']);
    }
}
