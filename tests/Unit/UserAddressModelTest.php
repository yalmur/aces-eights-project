<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAddressModelTest extends TestCase
{
    // -------------------------------------------------------------------------
    // getFullAddressAttribute — no DB required
    // -------------------------------------------------------------------------

    public function test_full_address_formats_street_city_postcode(): void
    {
        $address = new UserAddress([
            'street_address' => '123 Baker St',
            'city'           => 'London',
            'postcode'       => 'NW1 6XE',
        ]);

        $this->assertSame('123 Baker St, London, NW1 6XE', $address->full_address);
    }

    public function test_full_address_uses_comma_space_separator(): void
    {
        $address = new UserAddress([
            'street_address' => '45 High Road',
            'city'           => 'Manchester',
            'postcode'       => 'M1 2AB',
        ]);

        $this->assertSame('45 High Road, Manchester, M1 2AB', $address->full_address);
    }

    public function test_full_address_order_is_street_then_city_then_postcode(): void
    {
        $address = new UserAddress([
            'street_address' => '7 Oak Lane',
            'city'           => 'Bristol',
            'postcode'       => 'BS1 4DJ',
        ]);

        $parts = explode(', ', $address->full_address);

        $this->assertSame('7 Oak Lane', $parts[0]);
        $this->assertSame('Bristol',    $parts[1]);
        $this->assertSame('BS1 4DJ',   $parts[2]);
    }

    // -------------------------------------------------------------------------
    // scopeDefault — DB required
    // -------------------------------------------------------------------------

    use RefreshDatabase;

    public function test_scope_default_returns_only_default_address(): void
    {
        $user = User::factory()->create();

        UserAddress::create([
            'user_id'        => $user->id,
            'label'          => 'Home',
            'street_address' => '1 Main St',
            'city'           => 'London',
            'postcode'       => 'E1 1AA',
            'is_default'     => true,
        ]);

        UserAddress::create([
            'user_id'        => $user->id,
            'label'          => 'Work',
            'street_address' => '2 Side St',
            'city'           => 'London',
            'postcode'       => 'E1 2BB',
            'is_default'     => false,
        ]);

        $defaults = UserAddress::default()->get();

        $this->assertCount(1, $defaults);
        $this->assertSame('1 Main St', $defaults->first()->street_address);
    }

    public function test_scope_default_returns_empty_when_no_defaults_exist(): void
    {
        $user = User::factory()->create();

        UserAddress::create([
            'user_id'        => $user->id,
            'label'          => 'Work',
            'street_address' => '2 Side St',
            'city'           => 'London',
            'postcode'       => 'E1 2BB',
            'is_default'     => false,
        ]);

        $this->assertCount(0, UserAddress::default()->get());
    }

    public function test_scope_default_returns_all_defaults_when_multiple_exist(): void
    {
        $user = User::factory()->create();

        UserAddress::create([
            'user_id'        => $user->id,
            'label'          => 'Home',
            'street_address' => '1 Main St',
            'city'           => 'London',
            'postcode'       => 'E1 1AA',
            'is_default'     => true,
        ]);

        UserAddress::create([
            'user_id'        => $user->id,
            'label'          => 'Work',
            'street_address' => '2 Side St',
            'city'           => 'London',
            'postcode'       => 'E1 2BB',
            'is_default'     => true,
        ]);

        $this->assertCount(2, UserAddress::default()->get());
    }
}
