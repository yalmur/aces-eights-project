<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    private function mockSocialiteUser(string $id, string $email, string $name = 'Test User'): void
    {
        $socialUser = \Mockery::mock(\Laravel\Socialite\Two\User::class);
        $socialUser->shouldReceive('getId')->andReturn($id);
        $socialUser->shouldReceive('getEmail')->andReturn($email);
        $socialUser->shouldReceive('getName')->andReturn($name);
        $socialUser->shouldReceive('getNickname')->andReturn(null);
        $socialUser->shouldReceive('getAvatar')->andReturn(null);

        Socialite::shouldReceive('driver->user')->andReturn($socialUser);
    }

    public function test_new_social_user_created_on_first_login(): void
    {
        $this->mockSocialiteUser('google-123', 'new@example.com');
        $this->get('/auth/google/callback')->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => 'new@example.com', 'google_id' => 'google-123']);
    }

    public function test_existing_social_user_logged_in_by_id(): void
    {
        User::factory()->create(['email' => 'existing@example.com', 'google_id' => 'google-456']);
        $this->mockSocialiteUser('google-456', 'existing@example.com');
        $this->get('/auth/google/callback')->assertRedirect('/');
    }

    public function test_email_collision_with_password_account_rejected(): void
    {
        User::factory()->create(['email' => 'taken@example.com', 'google_id' => null]);
        $this->mockSocialiteUser('google-789', 'taken@example.com');
        $this->get('/auth/google/callback')
            ->assertRedirect('/login');
        $this->assertDatabaseMissing('users', ['google_id' => 'google-789']);
    }

    public function test_social_redirect_returns_redirect(): void
    {
        Socialite::shouldReceive('driver->redirect')->andReturn(redirect('https://accounts.google.com'));
        $this->get('/auth/google/redirect')->assertRedirect();
    }

    public function test_invalid_provider_returns_404(): void
    {
        $this->get('/auth/twitter/redirect')->assertStatus(404);
    }

    public function test_callback_exception_redirects_to_login_with_error(): void
    {
        Socialite::shouldReceive('driver->user')
            ->andThrow(new \Exception('OAuth failed'));

        $this->get('/auth/google/callback')
            ->assertRedirect('/login');
    }

    public function test_redirect_without_configured_client_id_redirects_to_login(): void
    {
        config(['services.google.client_id' => null]);

        $this->get('/auth/google/redirect')
            ->assertRedirect('/login');
    }

    public function test_callback_with_invalid_provider_returns_404(): void
    {
        $this->get('/auth/twitter/callback')->assertStatus(404);
    }
}
