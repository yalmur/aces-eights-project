<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    use RefreshDatabase;

    // ── isDelivery ──────────────────────────────────────────────────────────────

    public function test_is_delivery_true_for_delivery_type(): void
    {
        $order = Order::factory()->make(['type' => 'delivery']);
        $this->assertTrue($order->isDelivery());
    }

    public function test_is_delivery_false_for_collection_type(): void
    {
        $order = Order::factory()->make(['type' => 'collection']);
        $this->assertFalse($order->isDelivery());
    }

    public function test_is_delivery_false_for_eat_in_type(): void
    {
        $order = Order::factory()->make(['type' => 'eat_in']);
        $this->assertFalse($order->isDelivery());
    }

    // ── isPaid ──────────────────────────────────────────────────────────────────

    public function test_is_paid_true_for_accepted(): void
    {
        $this->assertTrue(Order::factory()->make(['status' => 'accepted'])->isPaid());
    }

    public function test_is_paid_true_for_cooking(): void
    {
        $this->assertTrue(Order::factory()->make(['status' => 'cooking'])->isPaid());
    }

    public function test_is_paid_true_for_delivered(): void
    {
        $this->assertTrue(Order::factory()->make(['status' => 'delivered'])->isPaid());
    }

    public function test_is_paid_true_for_collected(): void
    {
        $this->assertTrue(Order::factory()->make(['status' => 'collected'])->isPaid());
    }

    public function test_is_paid_false_for_pending_payment(): void
    {
        $this->assertFalse(Order::factory()->make(['status' => 'pending_payment'])->isPaid());
    }

    public function test_is_paid_false_for_cancelled(): void
    {
        $this->assertFalse(Order::factory()->make(['status' => 'cancelled'])->isPaid());
    }
}
