<?php

namespace Tests\Feature\Account;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create(['role' => 'customer']);
    }

    public function test_customer_can_add_address(): void
    {
        $response = $this->actingAs($this->customer)->post('/account/addresses', [
            'label' => 'Home', 'street_address' => '10 Test St', 'city' => 'London', 'postcode' => 'NW5 2HP',
        ]);
        $response->assertRedirect('/account');
        $this->assertDatabaseHas('user_addresses', ['user_id' => $this->customer->id, 'street_address' => '10 Test St']);
    }

    public function test_customer_can_delete_address(): void
    {
        $addr = UserAddress::factory()->create(['user_id' => $this->customer->id]);
        $this->actingAs($this->customer)->delete("/account/addresses/{$addr->id}");
        $this->assertDatabaseMissing('user_addresses', ['id' => $addr->id]);
    }

    public function test_customer_cannot_delete_other_users_address(): void
    {
        $other = User::factory()->create();
        $addr  = UserAddress::factory()->create(['user_id' => $other->id]);
        $response = $this->actingAs($this->customer)->delete("/account/addresses/{$addr->id}");
        $response->assertStatus(403);
    }

    public function test_customer_can_set_default_address(): void
    {
        $addr1 = UserAddress::factory()->create(['user_id' => $this->customer->id, 'is_default' => true]);
        $addr2 = UserAddress::factory()->create(['user_id' => $this->customer->id, 'is_default' => false]);
        $this->actingAs($this->customer)->patch("/account/addresses/{$addr2->id}/default");
        $this->assertDatabaseHas('user_addresses', ['id' => $addr2->id, 'is_default' => true]);
        $this->assertDatabaseHas('user_addresses', ['id' => $addr1->id, 'is_default' => false]);
    }

    // ── store edge cases ──────────────────────────────────────────────────────

    public function test_first_address_stored_becomes_default_automatically(): void
    {
        $this->actingAs($this->customer)->post('/account/addresses', [
            'label'          => 'Home',
            'street_address' => '1 First Street',
            'city'           => 'London',
            'postcode'       => 'NW5 1AA',
        ]);

        $this->assertDatabaseHas('user_addresses', [
            'user_id'    => $this->customer->id,
            'is_default' => true,
        ]);
    }

    public function test_second_address_stored_is_not_default(): void
    {
        \App\Models\UserAddress::factory()->create(['user_id' => $this->customer->id, 'is_default' => true]);

        $this->actingAs($this->customer)->post('/account/addresses', [
            'label'          => 'Work',
            'street_address' => '2 Second Street',
            'city'           => 'London',
            'postcode'       => 'NW5 2BB',
        ]);

        $this->assertDatabaseHas('user_addresses', [
            'street_address' => '2 Second Street',
            'is_default'     => false,
        ]);
    }

    public function test_store_rejects_missing_required_fields(): void
    {
        $this->actingAs($this->customer)
            ->post('/account/addresses', [])
            ->assertSessionHasErrors(['label', 'street_address', 'city', 'postcode']);
    }

    public function test_guest_redirected_to_login_on_store(): void
    {
        $this->post('/account/addresses', [
            'label'          => 'Home',
            'street_address' => '1 Test St',
            'city'           => 'London',
            'postcode'       => 'NW5 1AA',
        ])->assertRedirect('/login');
    }

    // ── setDefault edge cases ─────────────────────────────────────────────────

    public function test_non_owner_gets_403_on_set_default(): void
    {
        $other = User::factory()->create();
        $addr  = \App\Models\UserAddress::factory()->create(['user_id' => $other->id]);

        $this->actingAs($this->customer)
            ->patch("/account/addresses/{$addr->id}/default")
            ->assertStatus(403);
    }

    public function test_set_default_returns_success_flash(): void
    {
        $addr = \App\Models\UserAddress::factory()->create(['user_id' => $this->customer->id]);

        $this->actingAs($this->customer)
            ->patch("/account/addresses/{$addr->id}/default")
            ->assertRedirect(route('account'))
            ->assertSessionHas('success');
    }

    public function test_guest_redirected_to_login_on_set_default(): void
    {
        $addr = \App\Models\UserAddress::factory()->create(['user_id' => $this->customer->id]);

        $this->patch("/account/addresses/{$addr->id}/default")
            ->assertRedirect('/login');
    }
}
