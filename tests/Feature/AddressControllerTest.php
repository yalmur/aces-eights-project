<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── store() ────────────────────────────────────────────────────────────

    public function test_store_requires_auth(): void
    {
        $this->post(route('account.addresses.store'), [
            'label'          => 'Home',
            'street_address' => '1 Baker Street',
            'city'           => 'London',
            'postcode'       => 'NW1 6XE',
        ])->assertRedirect(route('login'));
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('account.addresses.store'), [])
            ->assertSessionHasErrors(['label', 'street_address', 'city', 'postcode']);
    }

    public function test_store_validates_field_max_lengths(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('account.addresses.store'), [
                'label'          => str_repeat('a', 51),
                'street_address' => str_repeat('a', 256),
                'city'           => str_repeat('a', 101),
                'postcode'       => str_repeat('a', 21),
            ])
            ->assertSessionHasErrors(['label', 'street_address', 'city', 'postcode']);
    }

    public function test_store_first_address_auto_sets_is_default(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('account.addresses.store'), [
                'label'          => 'Home',
                'street_address' => '1 Baker Street',
                'city'           => 'London',
                'postcode'       => 'NW1 6XE',
            ])
            ->assertRedirect(route('account'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('user_addresses', [
            'user_id'    => $user->id,
            'label'      => 'Home',
            'is_default' => true,
        ]);
    }

    public function test_store_second_address_does_not_auto_set_is_default(): void
    {
        $user = User::factory()->create();

        UserAddress::factory()->create([
            'user_id'    => $user->id,
            'is_default' => true,
        ]);

        $this->actingAs($user)
            ->post(route('account.addresses.store'), [
                'label'          => 'Work',
                'street_address' => '2 Work Road',
                'city'           => 'London',
                'postcode'       => 'EC1A 1BB',
            ])
            ->assertRedirect(route('account'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('user_addresses', [
            'user_id'    => $user->id,
            'label'      => 'Work',
            'is_default' => false,
        ]);
    }

    // ─── destroy() ──────────────────────────────────────────────────────────

    public function test_destroy_requires_auth(): void
    {
        $address = UserAddress::factory()->create();

        $this->delete(route('account.addresses.destroy', $address))
            ->assertRedirect(route('login'));
    }

    public function test_destroy_deletes_address_and_redirects(): void
    {
        $user    = User::factory()->create();
        $address = UserAddress::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('account.addresses.destroy', $address))
            ->assertRedirect(route('account'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('user_addresses', ['id' => $address->id]);
    }

    public function test_destroy_returns_403_for_wrong_user(): void
    {
        $owner      = User::factory()->create();
        $otherUser  = User::factory()->create();
        $address    = UserAddress::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($otherUser)
            ->delete(route('account.addresses.destroy', $address))
            ->assertStatus(403);
    }

    // ─── setDefault() ───────────────────────────────────────────────────────

    public function test_set_default_requires_auth(): void
    {
        $address = UserAddress::factory()->create();

        $this->patch(route('account.addresses.default', $address))
            ->assertRedirect(route('login'));
    }

    public function test_set_default_marks_address_as_default(): void
    {
        $user    = User::factory()->create();
        $address = UserAddress::factory()->create([
            'user_id'    => $user->id,
            'is_default' => false,
        ]);

        $this->actingAs($user)
            ->patch(route('account.addresses.default', $address))
            ->assertRedirect(route('account'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('user_addresses', [
            'id'         => $address->id,
            'is_default' => true,
        ]);
    }

    public function test_set_default_resets_other_addresses_to_not_default(): void
    {
        $user     = User::factory()->create();
        $oldDefault = UserAddress::factory()->create([
            'user_id'    => $user->id,
            'is_default' => true,
        ]);
        $newDefault = UserAddress::factory()->create([
            'user_id'    => $user->id,
            'is_default' => false,
        ]);

        $this->actingAs($user)
            ->patch(route('account.addresses.default', $newDefault))
            ->assertRedirect(route('account'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('user_addresses', [
            'id'         => $oldDefault->id,
            'is_default' => false,
        ]);
        $this->assertDatabaseHas('user_addresses', [
            'id'         => $newDefault->id,
            'is_default' => true,
        ]);
    }

    public function test_set_default_returns_403_for_wrong_user(): void
    {
        $owner     = User::factory()->create();
        $otherUser = User::factory()->create();
        $address   = UserAddress::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($otherUser)
            ->patch(route('account.addresses.default', $address))
            ->assertStatus(403);
    }
}
