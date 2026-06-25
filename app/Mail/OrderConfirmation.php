<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmed — Aces & Eights Pizza #' . $this->order->id,
        );
    }

    public function content(): Content
    {
        $this->order->loadMissing('items');
        return new Content(view: 'emails.order-confirmation');
    }
}
