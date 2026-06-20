<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function signedWebhookCall(string $secret, array $payload): \Illuminate\Testing\TestResponse
    {
        $body      = json_encode($payload);
        $timestamp = time();
        $sig       = hash_hmac('sha256', "{$timestamp}.{$body}", $secret);

        return $this->call('POST', '/stripe/webhook', [], [], [], [
            'CONTENT_TYPE'          => 'application/json',
            'HTTP_STRIPE-SIGNATURE' => "t={$timestamp},v1={$sig}",
        ], $body);
    }

    public function test_checkout_session_completed_marks_order_accepted_and_stores_payment_intent(): void
    {
        $secret = 'whsec_testsecret';
        config(['services.stripe.webhook_secret' => $secret]);

        $order = Order::factory()->pendingPayment()->create([
            'stripe_session_id' => 'cs_test_123',
            'stripe_payment_intent_id' => null,
        ]);

        $payload   = json_encode([
            'type' => 'checkout.session.completed',
            'data' => ['object' => ['id' => 'cs_test_123', 'payment_intent' => 'pi_test_456']],
        ]);
        $timestamp = time();
        $sig       = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);

        $response = $this->call('POST', '/stripe/webhook', [], [], [], [
            'CONTENT_TYPE'          => 'application/json',
            'HTTP_STRIPE-SIGNATURE' => "t={$timestamp},v1={$sig}",
        ], $payload);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertSame('accepted', $order->status);
        $this->assertSame('pi_test_456', $order->stripe_payment_intent_id);
    }

    public function test_checkout_session_expired_cancels_pending_payment_order(): void
    {
        $secret = 'whsec_testsecret';
        config(['services.stripe.webhook_secret' => $secret]);

        $order = Order::factory()->pendingPayment()->create([
            'stripe_session_id' => 'cs_test_expired_abc',
        ]);

        $this->signedWebhookCall($secret, [
            'type' => 'checkout.session.expired',
            'data' => ['object' => ['id' => 'cs_test_expired_abc']],
        ])->assertStatus(200);

        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_checkout_session_expired_ignores_unmatched_session_id(): void
    {
        $secret = 'whsec_testsecret';
        config(['services.stripe.webhook_secret' => $secret]);

        $order = Order::factory()->pendingPayment()->create([
            'stripe_session_id' => 'cs_test_other',
        ]);

        $this->signedWebhookCall($secret, [
            'type' => 'checkout.session.expired',
            'data' => ['object' => ['id' => 'cs_test_no_match']],
        ])->assertStatus(200);

        $this->assertSame('pending_payment', $order->fresh()->status);
    }
}
