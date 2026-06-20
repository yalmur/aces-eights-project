<?php

namespace Tests\Feature;

use App\Models\DeliveryZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DeliveryZoneModelTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // extractDistrict
    // -------------------------------------------------------------------------

    public function test_extract_district_handles_two_letter_area_code(): void
    {
        $this->assertSame('NW5', DeliveryZone::extractDistrict('NW5 2HP'));
    }

    public function test_extract_district_handles_four_char_district(): void
    {
        $this->assertSame('EC1A', DeliveryZone::extractDistrict('EC1A 1BB'));
    }

    public function test_extract_district_handles_sw1w(): void
    {
        $this->assertSame('SW1W', DeliveryZone::extractDistrict('SW1W 0NY'));
    }

    public function test_extract_district_handles_w1a(): void
    {
        $this->assertSame('W1A', DeliveryZone::extractDistrict('W1A 0AX'));
    }

    public function test_extract_district_handles_single_letter_area_code(): void
    {
        $this->assertSame('N7', DeliveryZone::extractDistrict('N7 8QL'));
    }

    public function test_extract_district_falls_back_to_first_word_on_invalid_postcode(): void
    {
        $this->assertSame('INVALID', DeliveryZone::extractDistrict('INVALID'));
    }

    public function test_extract_district_trims_and_uppercases_input(): void
    {
        $this->assertSame('NW5', DeliveryZone::extractDistrict('  nw5 2hp  '));
    }

    // -------------------------------------------------------------------------
    // findByPostcode
    // -------------------------------------------------------------------------

    public function test_find_by_postcode_returns_matching_active_zone(): void
    {
        Cache::flush();
        $zone = DeliveryZone::factory()->create(['postcodes' => 'NW5, N7', 'is_active' => true]);

        $found = DeliveryZone::findByPostcode('NW5 2HP');

        $this->assertNotNull($found);
        $this->assertSame($zone->id, $found->id);
    }

    public function test_find_by_postcode_returns_null_when_no_zone_covers_district(): void
    {
        Cache::flush();
        DeliveryZone::factory()->create(['postcodes' => 'SW1W, EC1A', 'is_active' => true]);

        $found = DeliveryZone::findByPostcode('NW5 2HP');

        $this->assertNull($found);
    }

    public function test_find_by_postcode_returns_null_for_inactive_zone(): void
    {
        Cache::flush();
        DeliveryZone::factory()->create(['postcodes' => 'NW5', 'is_active' => false]);

        $found = DeliveryZone::findByPostcode('NW5 2HP');

        $this->assertNull($found);
    }

    public function test_find_by_postcode_matching_is_case_insensitive(): void
    {
        Cache::flush();
        $zone = DeliveryZone::factory()->create(['postcodes' => 'NW5', 'is_active' => true]);

        $found = DeliveryZone::findByPostcode('nw5 2hp');

        $this->assertNotNull($found);
        $this->assertSame($zone->id, $found->id);
    }

    public function test_find_by_postcode_returns_null_when_no_zones_exist(): void
    {
        Cache::flush();

        $this->assertNull(DeliveryZone::findByPostcode('NW5 2HP'));
    }

    // -------------------------------------------------------------------------
    // getPostcodeListAttribute
    // -------------------------------------------------------------------------

    public function test_postcode_list_returns_cleaned_array_from_comma_separated_string(): void
    {
        $zone = DeliveryZone::factory()->make(['postcodes' => 'NW5, N7, N19']);

        $this->assertSame(['NW5', 'N7', 'N19'], $zone->postcode_list);
    }

    public function test_postcode_list_returns_empty_array_when_postcodes_is_null(): void
    {
        $zone = DeliveryZone::factory()->make(['postcodes' => null]);

        $this->assertSame([], $zone->postcode_list);
    }

    public function test_postcode_list_returns_empty_array_when_postcodes_is_empty_string(): void
    {
        $zone = DeliveryZone::factory()->make(['postcodes' => '']);

        $this->assertSame([], $zone->postcode_list);
    }

    public function test_postcode_list_trims_whitespace_from_each_district(): void
    {
        $zone = DeliveryZone::factory()->make(['postcodes' => '  NW5 ,  N7 ']);

        $this->assertSame(['NW5', 'N7'], $zone->postcode_list);
    }

    public function test_postcode_list_uppercases_districts(): void
    {
        $zone = DeliveryZone::factory()->make(['postcodes' => 'nw5,n7']);

        $this->assertSame(['NW5', 'N7'], $zone->postcode_list);
    }
}
