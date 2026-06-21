<?php

namespace Tests\Unit;

use App\Models\Promotion;
use Carbon\Carbon;
use Tests\TestCase;

class PromotionModelTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Promotion::isValid()
    // -------------------------------------------------------------------------

    public function test_is_valid_returns_false_when_inactive(): void
    {
        $promo = new Promotion(['is_active' => false]);

        $this->assertFalse($promo->isValid(50.00));
    }

    public function test_is_valid_returns_true_when_active_with_no_limits(): void
    {
        $promo = new Promotion([
            'is_active'        => true,
            'expires_at'       => null,
            'max_uses'         => null,
            'current_uses'     => 0,
            'min_order_amount' => null,
        ]);

        $this->assertTrue($promo->isValid(50.00));
    }

    public function test_is_valid_returns_false_when_expired(): void
    {
        $promo = new Promotion([
            'is_active'  => true,
            'expires_at' => Carbon::yesterday(),
        ]);

        $this->assertFalse($promo->isValid(50.00));
    }

    public function test_is_valid_returns_true_when_not_yet_expired(): void
    {
        $promo = new Promotion([
            'is_active'  => true,
            'expires_at' => Carbon::tomorrow(),
        ]);

        $this->assertTrue($promo->isValid(50.00));
    }

    public function test_is_valid_returns_false_when_max_uses_reached(): void
    {
        $promo = new Promotion([
            'is_active'    => true,
            'expires_at'   => null,
            'max_uses'     => 10,
            'current_uses' => 10,
        ]);

        $this->assertFalse($promo->isValid(50.00));
    }

    public function test_is_valid_returns_true_when_max_uses_is_null(): void
    {
        $promo = new Promotion([
            'is_active'    => true,
            'expires_at'   => null,
            'max_uses'     => null,
            'current_uses' => 999,
        ]);

        $this->assertTrue($promo->isValid(50.00));
    }

    public function test_is_valid_returns_true_when_max_uses_not_yet_reached(): void
    {
        $promo = new Promotion([
            'is_active'    => true,
            'expires_at'   => null,
            'max_uses'     => 10,
            'current_uses' => 9,
        ]);

        $this->assertTrue($promo->isValid(50.00));
    }

    public function test_is_valid_returns_false_when_subtotal_below_minimum(): void
    {
        $promo = new Promotion([
            'is_active'        => true,
            'expires_at'       => null,
            'max_uses'         => null,
            'min_order_amount' => 20.00,
        ]);

        $this->assertFalse($promo->isValid(19.99));
    }

    public function test_is_valid_returns_true_when_subtotal_equals_minimum(): void
    {
        $promo = new Promotion([
            'is_active'        => true,
            'expires_at'       => null,
            'max_uses'         => null,
            'min_order_amount' => 20.00,
        ]);

        $this->assertTrue($promo->isValid(20.00));
    }

    public function test_is_valid_returns_true_when_min_order_amount_is_null(): void
    {
        $promo = new Promotion([
            'is_active'        => true,
            'expires_at'       => null,
            'max_uses'         => null,
            'min_order_amount' => null,
        ]);

        $this->assertTrue($promo->isValid(0.01));
    }

    // -------------------------------------------------------------------------
    // Promotion::calculateDiscount()
    // -------------------------------------------------------------------------

    public function test_calculate_discount_percentage_ten_percent_of_forty(): void
    {
        $promo = new Promotion(['type' => 'percentage', 'value' => '10.00']);

        $this->assertSame(4.00, $promo->calculateDiscount(40.00, 3.50));
    }

    public function test_calculate_discount_percentage_rounds_correctly(): void
    {
        $promo = new Promotion(['type' => 'percentage', 'value' => '15.00']);

        $this->assertSame(5.00, $promo->calculateDiscount(33.33, 3.50));
    }

    public function test_calculate_discount_fixed_amount_within_subtotal(): void
    {
        $promo = new Promotion(['type' => 'fixed_amount', 'value' => '5.00']);

        $this->assertSame(5.00, $promo->calculateDiscount(20.00, 3.50));
    }

    public function test_calculate_discount_fixed_amount_capped_at_subtotal(): void
    {
        $promo = new Promotion(['type' => 'fixed_amount', 'value' => '30.00']);

        $this->assertSame(20.00, $promo->calculateDiscount(20.00, 3.50));
    }

    public function test_calculate_discount_free_delivery_returns_delivery_fee(): void
    {
        $promo = new Promotion(['type' => 'free_delivery', 'value' => '0.00']);

        $this->assertSame(3.50, $promo->calculateDiscount(40.00, 3.50));
    }

    public function test_calculate_discount_buy_one_get_one_returns_half_subtotal(): void
    {
        $promo = new Promotion(['type' => 'buy_one_get_one', 'value' => '0.00']);

        $this->assertSame(15.00, $promo->calculateDiscount(30.00, 3.50));
    }

    public function test_calculate_discount_multi_buy_returns_one_third_subtotal(): void
    {
        $promo = new Promotion(['type' => 'multi_buy', 'value' => '0.00']);

        $this->assertSame(10.00, $promo->calculateDiscount(30.00, 3.50));
    }

    public function test_calculate_discount_unknown_type_returns_zero(): void
    {
        $promo = new Promotion(['type' => 'mystery', 'value' => '0.00']);

        $this->assertSame(0.0, $promo->calculateDiscount(40.00, 3.50));
    }

    // -------------------------------------------------------------------------
    // Promotion::typeLabel (getTypeLabelAttribute)
    // -------------------------------------------------------------------------

    public function test_type_label_percentage_shows_value_and_percent_off(): void
    {
        $promo = new Promotion(['type' => 'percentage', 'value' => '10.00']);

        $this->assertSame('10% off', $promo->typeLabel);
    }

    public function test_type_label_fixed_amount_shows_pound_value_off(): void
    {
        $promo = new Promotion(['type' => 'fixed_amount', 'value' => '5.00']);

        $this->assertSame('£5.00 off', $promo->typeLabel);
    }

    public function test_type_label_free_delivery(): void
    {
        $promo = new Promotion(['type' => 'free_delivery', 'value' => '0.00']);

        $this->assertSame('Free delivery', $promo->typeLabel);
    }

    public function test_type_label_buy_one_get_one(): void
    {
        $promo = new Promotion(['type' => 'buy_one_get_one', 'value' => '0.00']);

        $this->assertSame('Buy 1 Get 1 Free', $promo->typeLabel);
    }

    public function test_type_label_multi_buy(): void
    {
        $promo = new Promotion(['type' => 'multi_buy', 'value' => '0.00']);

        $this->assertSame('3 for 2', $promo->typeLabel);
    }

    public function test_type_label_unknown_type_returns_type_string(): void
    {
        $promo = new Promotion(['type' => 'mystery', 'value' => '0.00']);

        $this->assertSame('mystery', $promo->typeLabel);
    }
}
