<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── AccountController@index ─────────────────────────────────────────────

    public function test_account_redirects_unauthenticated_users_to_login(): void
    {
        $this->get(route('account'))
            ->assertRedirect(route('login'));
    }

    public function test_account_returns_200_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account'))
            ->assertOk();
    }

    public function test_account_passes_orders_to_view(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account'))
            ->assertViewHas('orders');
    }

    public function test_account_passes_addresses_to_view(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account'))
            ->assertViewHas('addresses');
    }

    public function test_account_only_shows_own_orders(): void
    {
        $user      = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownOrder   = Order::factory()->create(['user_id' => $user->id]);
        $otherOrder = Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('account'));

        $orders = $response->viewData('orders');

        $this->assertTrue($orders->contains('id', $ownOrder->id));
        $this->assertFalse($orders->contains('id', $otherOrder->id));
    }

    // ─── PasswordController@update ───────────────────────────────────────────

    public function test_password_update_succeeds_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
            ->post(route('account.password'), [
                'current_password'      => 'password123',
                'password'              => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertRedirect(route('account'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }

    public function test_password_update_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
            ->post(route('account.password'), [
                'current_password'      => 'wrongpassword',
                'password'              => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors(['current_password']);
    }

    public function test_password_update_fails_when_new_password_too_short(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
            ->post(route('account.password'), [
                'current_password'      => 'password123',
                'password'              => 'short',
                'password_confirmation' => 'short',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors(['password']);
    }

    public function test_password_update_fails_when_confirmation_mismatches(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
            ->post(route('account.password'), [
                'current_password'      => 'password123',
                'password'              => 'newpassword',
                'password_confirmation' => 'differentpassword',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors(['password']);
    }

    public function test_password_update_fails_when_current_password_missing(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('account.password'), [
                'password'              => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors(['current_password']);
    }

    public function test_password_update_redirects_guest_to_login(): void
    {
        $this->post(route('account.password'), [
            'current_password'      => 'password123',
            'password'              => 'newpassword',
            'password_confirmation' => 'newpassword',
        ])->assertRedirect(route('login'));
    }

    public function test_account_passes_user_to_view(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account'))
            ->assertViewHas('user', fn ($u) => $u->id === $user->id);
    }
}
