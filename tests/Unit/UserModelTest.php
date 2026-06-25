<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // User::defaultAddress()
    // -------------------------------------------------------------------------

    public function test_default_address_returns_address_marked_as_default(): void
    {
        $user = User::factory()->create();
        UserAddress::factory()->create(['user_id' => $user->id, 'is_default' => false]);
        $default = UserAddress::factory()->create(['user_id' => $user->id, 'is_default' => true]);

        $result = $user->defaultAddress();

        $this->assertNotNull($result);
        $this->assertSame($default->id, $result->id);
    }

    public function test_default_address_returns_null_when_no_default_set(): void
    {
        $user = User::factory()->create();
        UserAddress::factory()->create(['user_id' => $user->id, 'is_default' => false]);

        $this->assertNull($user->defaultAddress());
    }

    public function test_default_address_returns_null_when_user_has_no_addresses(): void
    {
        $user = User::factory()->create();

        $this->assertNull($user->defaultAddress());
    }

    // -------------------------------------------------------------------------
    // User::orders() relationship ordering
    // -------------------------------------------------------------------------

    public function test_orders_relationship_returns_latest_first(): void
    {
        $user   = User::factory()->create();
        $older  = \App\Models\Order::factory()->create(['user_id' => $user->id, 'created_at' => now()->subHour()]);
        $newer  = \App\Models\Order::factory()->create(['user_id' => $user->id, 'created_at' => now()]);

        $ids = $user->orders()->pluck('id')->all();

        $this->assertSame([$newer->id, $older->id], $ids);
    }
}
