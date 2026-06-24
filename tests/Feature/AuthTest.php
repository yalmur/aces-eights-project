<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_login_with_valid_credentials_redirects_home(): void
    {
        $user = User::factory()->create([
            'email'    => 'customer@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'customer',
        ]);

        $this->post('/login', [
            'email'    => 'customer@example.com',
            'password' => 'password123',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('adminpass'),
            'role'     => 'admin',
        ]);

        $this->post('/login', [
            'email'    => 'admin@example.com',
            'password' => 'adminpass',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_with_wrong_password_returns_error(): void
    {
        User::factory()->create([
            'email'    => 'user@example.com',
            'password' => bcrypt('correct'),
        ]);

        $this->post('/login', [
            'email'    => 'user@example.com',
            'password' => 'wrong',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_with_unknown_email_returns_error(): void
    {
        $this->post('/login', [
            'email'    => 'nobody@example.com',
            'password' => 'whatever',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_logout_clears_auth_and_redirects_home(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
