<?php

namespace Tests\Feature;

use App\Models\Promotion;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DealsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_deals_page_loads(): void
    {
        $response = $this->get('/deals');
        $response->assertStatus(200);
        $response->assertSee('Deals');
    }

    public function test_deals_page_shows_active_promotions(): void
    {
        $promo = Promotion::create([
            'code' => 'TESTDEAL',
            'name' => 'Test Deal',
            'type' => 'percentage',
            'value' => 15,
            'is_active' => true,
        ]);

        $response = $this->get('/deals');
        $response->assertStatus(200);
        $response->assertSee('TESTDEAL');
        $response->assertSee('Test Deal');
        $response->assertSee('15% OFF');
    }

    public function test_deals_page_hides_inactive_promotions(): void
    {
        Promotion::create([
            'code' => 'EXPIRED',
            'name' => 'Expired Deal',
            'type' => 'fixed_amount',
            'value' => 5,
            'is_active' => false,
        ]);

        $response = $this->get('/deals');
        $response->assertStatus(200);
        $response->assertDontSee('EXPIRED');
    }

    public function test_deals_page_shows_empty_state_when_no_deals(): void
    {
        $response = $this->get('/deals');
        $response->assertStatus(200);
        $response->assertSee('No Active Deals Right Now');
    }
}
