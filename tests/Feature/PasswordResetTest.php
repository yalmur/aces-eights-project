<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_renders(): void
    {
        $r = $this->get('/forgot-password');
        $r->assertStatus(200);
        $r->assertSee('Reset Password');
    }

    public function test_reset_link_sent_for_existing_email(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $r = $this->post('/forgot-password', ['email' => $user->email]);

        $r->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_no_error_for_unknown_email(): void
    {
        $r = $this->post('/forgot-password', ['email' => 'nobody@example.com']);
        $r->assertSessionHasErrors('email');
    }

    public function test_reset_password_form_renders(): void
    {
        $user  = User::factory()->create();
        $token = Password::createToken($user);

        $r = $this->get("/reset-password/{$token}?email={$user->email}");
        $r->assertStatus(200);
        $r->assertSee('New Password');
    }

    public function test_password_updated_with_valid_token(): void
    {
        $user  = User::factory()->create();
        $token = Password::createToken($user);

        $r = $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'newpassword99',
            'password_confirmation' => 'newpassword99',
        ]);

        $r->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('newpassword99', $user->fresh()->password));
    }

    public function test_invalid_token_rejected(): void
    {
        $user = User::factory()->create();

        $r = $this->post('/reset-password', [
            'token'                 => 'bad-token',
            'email'                 => $user->email,
            'password'              => 'newpassword99',
            'password_confirmation' => 'newpassword99',
        ]);

        $r->assertSessionHasErrors('email');
    }

    public function test_reset_rejects_password_shorter_than_8_chars(): void
    {
        $user  = User::factory()->create();
        $token = Password::createToken($user);

        $r = $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'short',
            'password_confirmation' => 'short',
        ]);

        $r->assertSessionHasErrors('password');
        $this->assertFalse(Hash::check('short', $user->fresh()->password));
    }

    public function test_reset_rejects_password_confirmation_mismatch(): void
    {
        $user  = User::factory()->create();
        $token = Password::createToken($user);

        $r = $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'newpassword99',
            'password_confirmation' => 'different99',
        ]);

        $r->assertSessionHasErrors('password');
    }

    public function test_reset_requires_all_fields(): void
    {
        $this->post('/reset-password', [])
             ->assertSessionHasErrors(['token', 'email', 'password']);
    }
}
