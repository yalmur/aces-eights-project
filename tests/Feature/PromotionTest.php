<?php

namespace Tests\Feature;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_promotions_page_returns_200(): void
    {
        $this->actingAs($this->admin)->get('/admin/promotions')->assertStatus(200);
    }

    public function test_admin_can_create_promotion(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/promotions', [
            'code' => 'SAVE10', 'name' => '10% off', 'type' => 'percentage', 'value' => '10', 'is_active' => '1',
        ]);
        $response->assertRedirect('/admin/promotions');
        $this->assertDatabaseHas('promotions', ['code' => 'SAVE10', 'type' => 'percentage']);
    }

    public function test_promo_code_is_forced_uppercase(): void
    {
        $this->actingAs($this->admin)->post('/admin/promotions', [
            'code' => 'lowercase', 'name' => 'Test', 'type' => 'fixed_amount', 'value' => '5',
        ]);
        $this->assertDatabaseHas('promotions', ['code' => 'LOWERCASE']);
    }

    public function test_admin_can_update_promotion(): void
    {
        $promo = Promotion::factory()->create(['name' => 'Old Name']);
        $this->actingAs($this->admin)->put("/admin/promotions/{$promo->id}", [
            'code' => $promo->code, 'name' => 'New Name', 'type' => $promo->type, 'value' => $promo->value,
        ]);
        $this->assertDatabaseHas('promotions', ['id' => $promo->id, 'name' => 'New Name']);
    }

    public function test_admin_can_delete_promotion(): void
    {
        $promo = Promotion::factory()->create();
        $this->actingAs($this->admin)->delete("/admin/promotions/{$promo->id}");
        $this->assertDatabaseMissing('promotions', ['id' => $promo->id]);
    }

    public function test_promotion_is_valid_when_conditions_met(): void
    {
        $promo = Promotion::factory()->percentage(10)->create(['min_order_amount' => 20]);
        $this->assertTrue($promo->isValid(25.00));
        $this->assertFalse($promo->isValid(15.00));
    }

    public function test_promotion_calculates_correct_discount(): void
    {
        $pct   = Promotion::factory()->percentage(10)->create();
        $fixed = Promotion::factory()->fixed(5)->create();
        $free  = Promotion::factory()->freeDelivery()->create();
        $this->assertEquals(2.50, $pct->calculateDiscount(25.00, 3.50));
        $this->assertEquals(5.00, $fixed->calculateDiscount(25.00, 3.50));
        $this->assertEquals(3.50, $free->calculateDiscount(25.00, 3.50));
    }

    public function test_expired_promotion_is_not_valid(): void
    {
        $promo = Promotion::factory()->expired()->create();
        $this->assertFalse($promo->isValid(50.00));
    }

    public function test_promo_check_endpoint_returns_valid_discount(): void
    {
        Promotion::factory()->percentage(10)->create(['code' => 'SAVE10']);

        $r = $this->postJson('/promo/check', ['code' => 'SAVE10', 'subtotal' => 30.00, 'delivery_fee' => 3.50]);

        $r->assertOk()->assertJson(['valid' => true, 'discount' => 3.0]);
    }

    public function test_promo_check_endpoint_returns_invalid_for_bad_code(): void
    {
        $r = $this->postJson('/promo/check', ['code' => 'FAKECODE', 'subtotal' => 30.00]);

        $r->assertOk()->assertJson(['valid' => false]);
    }

    public function test_promo_check_endpoint_returns_invalid_when_min_order_not_met(): void
    {
        Promotion::factory()->percentage(10)->create(['code' => 'BIG10', 'min_order_amount' => 50.00]);

        $r = $this->postJson('/promo/check', ['code' => 'BIG10', 'subtotal' => 20.00]);

        $r->assertOk()->assertJson(['valid' => false]);
    }

    public function test_promo_check_is_case_insensitive(): void
    {
        Promotion::factory()->fixed(5)->create(['code' => 'FIVER']);

        $r = $this->postJson('/promo/check', ['code' => 'fiver', 'subtotal' => 20.00]);

        $r->assertOk()->assertJson(['valid' => true]);
    }

    public function test_bogo_calculates_half_subtotal_as_discount(): void
    {
        $promo = Promotion::factory()->create(['type' => 'buy_one_get_one', 'value' => 0]);
        $this->assertEquals(12.50, $promo->calculateDiscount(25.00, 3.50));
    }

    public function test_multi_buy_calculates_one_third_subtotal_as_discount(): void
    {
        $promo = Promotion::factory()->create(['type' => 'multi_buy', 'value' => 0]);
        $this->assertEquals(round(25.00 / 3, 2), $promo->calculateDiscount(25.00, 3.50));
    }
}
