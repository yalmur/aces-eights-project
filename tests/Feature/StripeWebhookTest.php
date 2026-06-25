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

    public function test_missing_webhook_secret_returns_500(): void
    {
        config(['services.stripe.webhook_secret' => null]);

        $response = $this->call('POST', '/stripe/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['type' => 'checkout.session.completed']));

        $response->assertStatus(500);
    }

    public function test_invalid_signature_returns_400(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_testsecret']);

        $response = $this->call('POST', '/stripe/webhook', [], [], [], [
            'CONTENT_TYPE'          => 'application/json',
            'HTTP_STRIPE-SIGNATURE' => 't=123,v1=badsig',
        ], json_encode(['type' => 'checkout.session.completed']));

        $response->assertStatus(400);
    }

    public function test_completed_event_on_already_accepted_order_is_idempotent(): void
    {
        $secret = 'whsec_testsecret';
        config(['services.stripe.webhook_secret' => $secret]);

        $order = Order::factory()->create([
            'status'            => 'accepted',
            'stripe_session_id' => 'cs_test_already',
            'customer_email'    => 'test@example.com',
        ]);

        $this->signedWebhookCall($secret, [
            'type' => 'checkout.session.completed',
            'data' => ['object' => ['id' => 'cs_test_already', 'payment_intent' => 'pi_test_999']],
        ])->assertStatus(200);

        $this->assertSame('accepted', $order->fresh()->status);
    }

    public function test_completed_event_with_unmatched_session_id_returns_200_and_no_db_change(): void
    {
        $secret = 'whsec_testsecret';
        config(['services.stripe.webhook_secret' => $secret]);

        $this->signedWebhookCall($secret, [
            'type' => 'checkout.session.completed',
            'data' => ['object' => ['id' => 'cs_test_ghost', 'payment_intent' => 'pi_test_000']],
        ])->assertStatus(200);

        $this->assertDatabaseMissing('orders', ['stripe_session_id' => 'cs_test_ghost']);
    }

    public function test_unknown_event_type_returns_200(): void
    {
        $secret = 'whsec_testsecret';
        config(['services.stripe.webhook_secret' => $secret]);

        $this->signedWebhookCall($secret, [
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_test_xyz']],
        ])->assertStatus(200);
    }

    public function test_completed_event_queues_order_confirmation_email(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        $secret = 'whsec_testsecret';
        config(['services.stripe.webhook_secret' => $secret]);

        $order = Order::factory()->pendingPayment()->create([
            'stripe_session_id' => 'cs_test_mail',
            'customer_email'    => 'customer@example.com',
        ]);

        $this->signedWebhookCall($secret, [
            'type' => 'checkout.session.completed',
            'data' => ['object' => ['id' => 'cs_test_mail', 'payment_intent' => 'pi_mail_test']],
        ])->assertStatus(200);

        \Illuminate\Support\Facades\Mail::assertQueued(
            \App\Mail\OrderConfirmation::class,
            fn ($mail) => $mail->hasTo('customer@example.com')
        );
    }

    public function test_completed_event_dispatches_order_status_updated_event(): void
    {
        \Illuminate\Support\Facades\Event::fake([\App\Events\OrderStatusUpdated::class]);
        $secret = 'whsec_testsecret';
        config(['services.stripe.webhook_secret' => $secret]);

        $order = Order::factory()->pendingPayment()->create([
            'stripe_session_id' => 'cs_test_event',
            'customer_email'    => 'ev@example.com',
        ]);

        $this->signedWebhookCall($secret, [
            'type' => 'checkout.session.completed',
            'data' => ['object' => ['id' => 'cs_test_event', 'payment_intent' => 'pi_ev']],
        ])->assertStatus(200);

        \Illuminate\Support\Facades\Event::assertDispatched(
            \App\Events\OrderStatusUpdated::class,
            fn ($e) => $e->order->id === $order->id
        );
    }
}
