<?php

namespace Tests\Feature;

use App\Mail\ContactMessage;
use App\Mail\OrderConfirmation;
use App\Mail\OrderStatusUpdate;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailableContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_confirmation_subject_includes_order_id(): void
    {
        $order = Order::factory()->create();

        (new OrderConfirmation($order))
            ->assertHasSubject('Order Confirmed — Aces & Eights Pizza #' . $order->id);
    }

    public function test_order_confirmation_html_includes_customer_name(): void
    {
        $order = Order::factory()->create(['customer_name' => 'Jane Smith']);

        (new OrderConfirmation($order))->assertSeeInHtml('Jane Smith');
    }

    public function test_order_status_update_subject_includes_order_id_and_status_label(): void
    {
        $order = Order::factory()->create(['status' => 'cooking']);

        (new OrderStatusUpdate($order))
            ->assertHasSubject('Your order #' . $order->id . ' — Cooking');
    }

    public function test_contact_message_subject_has_prefix_and_sender_name(): void
    {
        $mailable = new ContactMessage(
            senderName: 'Bob Jones',
            senderEmail: 'bob@example.com',
            messageSubject: 'Menu inquiry',
            body: 'What time do you close?',
        );

        $mailable->assertHasSubject('[Contact] Menu inquiry — Bob Jones');
    }

    public function test_contact_message_reply_to_is_sender_email(): void
    {
        $mailable = new ContactMessage(
            senderName: 'Alice',
            senderEmail: 'alice@example.com',
            messageSubject: 'Test',
            body: 'Hello.',
        );

        $mailable->assertHasReplyTo('alice@example.com');
    }
}
