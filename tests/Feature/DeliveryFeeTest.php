<?php

namespace Tests\Feature;

use App\Models\DeliveryZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DeliveryFeeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_returns_not_covered_when_no_postcode_given(): void
    {
        $response = $this->getJson('/delivery-fee');

        $response->assertOk()
                 ->assertJson([
                     'covered' => false,
                     'fee'     => 0,
                     'message' => 'Enter your postcode',
                 ]);
    }

    public function test_returns_not_covered_for_unknown_postcode(): void
    {
        DeliveryZone::factory()->create([
            'postcodes' => 'NW5,N7',
            'is_active' => true,
        ]);

        $response = $this->getJson('/delivery-fee?postcode=SW1A');

        $response->assertOk()
                 ->assertJson(['covered' => false]);
    }

    public function test_returns_covered_with_fee_for_known_postcode(): void
    {
        DeliveryZone::factory()->create([
            'name'      => 'Zone A',
            'postcodes' => 'NW5,N7',
            'fee'       => 2.50,
            'is_active' => true,
        ]);

        $response = $this->getJson('/delivery-fee?postcode=NW5');

        $response->assertOk()
                 ->assertJson([
                     'covered' => true,
                     'fee'     => 2.5,
                     'zone'    => 'Zone A',
                 ]);
    }

    public function test_returns_zero_fee_in_not_covered_response(): void
    {
        DeliveryZone::factory()->create([
            'postcodes' => 'NW5,N7',
            'is_active' => true,
        ]);

        $response = $this->getJson('/delivery-fee?postcode=SW1A');

        $response->assertOk()
                 ->assertJson(['fee' => 0]);
    }

    public function test_full_postcode_matches_zone_by_district(): void
    {
        // extractDistrict normalises "NW5 2HP" → "NW5", so a full postcode
        // should match a zone whose postcodes list contains the district "NW5".
        DeliveryZone::factory()->create([
            'name'      => 'Zone A',
            'postcodes' => 'NW5,N7',
            'fee'       => 2.50,
            'is_active' => true,
        ]);

        $response = $this->getJson('/delivery-fee?postcode=NW5+2HP');

        $response->assertOk()
                 ->assertJson([
                     'covered' => true,
                     'fee'     => 2.5,
                     'zone'    => 'Zone A',
                 ]);
    }
}
