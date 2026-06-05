<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirmation_email_queued_after_payment(): void
    {
        Mail::fake();

        $order = Order::factory()->pendingPayment()->create();

        $this->get(route('orders.confirmation', $order->id) . '?session_id=cs_test_fake');

        Mail::assertQueued(OrderConfirmation::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id
                && $mail->hasTo($order->customer_email);
        });
    }

    public function test_confirmation_email_not_sent_twice(): void
    {
        Mail::fake();

        // Already accepted — not pending_payment
        $order = Order::factory()->create(['status' => 'accepted']);

        $this->get(route('orders.confirmation', $order->id) . '?session_id=cs_test_fake');

        Mail::assertNothingQueued();
    }
}
