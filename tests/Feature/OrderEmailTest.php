<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Mail\OrderStatusUpdate;
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

        $this->actingAs($order->user)->get(route('orders.confirmation', $order->id) . '?session_id=cs_test_fake');

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

    public function test_status_update_email_queued_on_admin_change(): void
    {
        Mail::fake();

        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['status' => 'accepted', 'customer_email' => 'customer@example.com']);

        $this->actingAs($admin)
             ->patch(route('admin.orders.status', $order->id), ['status' => 'cooking']);

        Mail::assertQueued(OrderStatusUpdate::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id
                && $mail->hasTo('customer@example.com');
        });
    }

    public function test_status_email_not_sent_without_customer_email(): void
    {
        Mail::fake();

        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['status' => 'accepted', 'customer_email' => '']);

        $this->actingAs($admin)
             ->patch(route('admin.orders.status', $order->id), ['status' => 'cooking']);

        Mail::assertNothingQueued();
    }
}
