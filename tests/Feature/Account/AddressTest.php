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
}
