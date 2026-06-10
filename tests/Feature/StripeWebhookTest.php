<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_session_completed_marks_order_accepted_and_stores_payment_intent(): void
    {
        config(['services.stripe.webhook_secret' => null]);

        $order = Order::factory()->pendingPayment()->create([
            'stripe_session_id' => 'cs_test_123',
            'stripe_payment_intent_id' => null,
        ]);

        $response = $this->postJson('/stripe/webhook', [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_123',
                    'payment_intent' => 'pi_test_456',
                ],
            ],
        ]);

        $response->assertStatus(200);

        $order->refresh();

        $this->assertSame('accepted', $order->status);
        $this->assertSame('pi_test_456', $order->stripe_payment_intent_id);
    }
}
