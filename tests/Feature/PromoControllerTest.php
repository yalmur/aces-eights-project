<?php

namespace Tests\Feature;

use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoControllerTest extends TestCase
{
    use RefreshDatabase;

    // 1. Valid fixed promo returns discount
    public function test_valid_fixed_promo_returns_discount(): void
    {
        Promotion::factory()->fixed(5)->create(['code' => 'SAVE5']);

        $response = $this->postJson('/promo/check', ['code' => 'SAVE5', 'subtotal' => 20.00]);

        $response->assertOk()->assertJson(['valid' => true, 'discount' => 5.00]);
    }

    // 2. Valid percentage promo returns correct discount
    public function test_valid_percentage_promo_returns_discount(): void
    {
        Promotion::factory()->percentage(10)->create(['code' => 'TEN']);

        $response = $this->postJson('/promo/check', ['code' => 'TEN', 'subtotal' => 50.00]);

        $response->assertOk()->assertJson(['valid' => true, 'discount' => 5.00]);
    }

    // 3. Unknown code returns valid:false
    public function test_invalid_code_returns_valid_false(): void
    {
        $response = $this->postJson('/promo/check', ['code' => 'NOSUCHCODE', 'subtotal' => 30.00]);

        $response->assertOk()->assertJson(['valid' => false]);
    }

    // 4. Expired promo returns valid:false
    public function test_expired_promo_returns_valid_false(): void
    {
        Promotion::factory()->expired()->create(['code' => 'OLDCODE']);

        $response = $this->postJson('/promo/check', ['code' => 'OLDCODE', 'subtotal' => 30.00]);

        $response->assertOk()->assertJson(['valid' => false]);
    }

    // 5. Subtotal below minimum order returns valid:false
    public function test_subtotal_below_minimum_returns_valid_false(): void
    {
        Promotion::factory()->fixed(5)->create(['code' => 'MINORD', 'min_order_amount' => 25.00]);

        $response = $this->postJson('/promo/check', ['code' => 'MINORD', 'subtotal' => 10.00]);

        $response->assertOk()->assertJson(['valid' => false]);
    }

    // 6. Missing code field returns 422
    public function test_validation_fails_without_code(): void
    {
        $response = $this->postJson('/promo/check', ['subtotal' => 20.00]);

        $response->assertStatus(422)->assertJsonValidationErrors(['code']);
    }

    // 7. Missing subtotal field returns 422
    public function test_validation_fails_without_subtotal(): void
    {
        $response = $this->postJson('/promo/check', ['code' => 'SAVE5']);

        $response->assertStatus(422)->assertJsonValidationErrors(['subtotal']);
    }

    // 8. Code lookup is case-insensitive
    public function test_code_lookup_is_case_insensitive(): void
    {
        Promotion::factory()->fixed(5)->create(['code' => 'SAVE5']);

        $response = $this->postJson('/promo/check', ['code' => 'save5', 'subtotal' => 20.00]);

        $response->assertOk()->assertJson(['valid' => true, 'discount' => 5.00]);
    }

    // 9. free_delivery returns delivery_fee as discount
    public function test_free_delivery_promo_returns_delivery_fee_as_discount(): void
    {
        Promotion::factory()->freeDelivery()->create(['code' => 'FREEDEL']);

        $this->postJson('/promo/check', ['code' => 'FREEDEL', 'subtotal' => 25.00, 'delivery_fee' => 3.50])
            ->assertOk()
            ->assertJson(['valid' => true, 'discount' => 3.50]);
    }

    // 10. free_delivery with zero fee returns zero discount
    public function test_free_delivery_promo_with_zero_fee_returns_zero_discount(): void
    {
        Promotion::factory()->freeDelivery()->create(['code' => 'FREEDEL2']);

        $this->postJson('/promo/check', ['code' => 'FREEDEL2', 'subtotal' => 25.00, 'delivery_fee' => 0])
            ->assertOk()
            ->assertJson(['valid' => true, 'discount' => 0]);
    }

    // 11. inactive promo returns valid=false
    public function test_inactive_promo_returns_valid_false(): void
    {
        Promotion::factory()->inactive()->create(['code' => 'INACTIVE']);

        $this->postJson('/promo/check', ['code' => 'INACTIVE', 'subtotal' => 20.00])
            ->assertOk()
            ->assertJson(['valid' => false]);
    }

    // 12. whitespace trimmed and matched
    public function test_code_with_whitespace_is_trimmed_and_matched(): void
    {
        Promotion::factory()->fixed(5)->create(['code' => 'TRIM5']);

        $this->postJson('/promo/check', ['code' => ' TRIM5 ', 'subtotal' => 20.00])
            ->assertOk()
            ->assertJson(['valid' => true]);
    }

    // 13. max_uses exhausted returns valid=false
    public function test_max_uses_exhausted_returns_valid_false(): void
    {
        Promotion::factory()->fixed(5)->create(['code' => 'MAXED', 'max_uses' => 1, 'current_uses' => 1]);

        $this->postJson('/promo/check', ['code' => 'MAXED', 'subtotal' => 20.00])
            ->assertOk()
            ->assertJson(['valid' => false]);
    }

    // 14. BOGO returns half subtotal as discount
    public function test_bogo_promo_returns_half_subtotal_as_discount(): void
    {
        Promotion::factory()->create(['code' => 'BOGO', 'type' => 'buy_one_get_one', 'value' => 0]);

        $this->postJson('/promo/check', ['code' => 'BOGO', 'subtotal' => 20.00])
            ->assertOk()
            ->assertJson(['valid' => true, 'discount' => 10.00]);
    }
}
