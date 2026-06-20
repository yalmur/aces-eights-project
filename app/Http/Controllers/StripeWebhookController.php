<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload    = $request->getContent();
        $sigHeader  = $request->header('Stripe-Signature');
        $secret     = config('services.stripe.webhook_secret');

        if (!$secret) {
            return response('Webhook secret not configured.', 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            return response('Webhook signature verification failed.', 400);
        }

        $eventType       = $event->type;
        $sessionId       = $event->data->object->id ?? null;
        $paymentIntentId = $event->data->object->payment_intent ?? null;

        if ($eventType === 'checkout.session.completed' && $sessionId) {
            $order = Order::where('stripe_session_id', $sessionId)
                ->where('status', 'pending_payment')
                ->first();
            if ($order) {
                $order->update(['status' => 'accepted', 'stripe_payment_intent_id' => $paymentIntentId]);
                OrderStatusUpdated::dispatch($order);
                Mail::to($order->customer_email)->queue(new OrderConfirmation($order));
            }
        }

        if ($eventType === 'checkout.session.expired' && $sessionId) {
            Order::where('stripe_session_id', $sessionId)
                ->where('status', 'pending_payment')
                ->update(['status' => 'cancelled']);
        }

        return response('OK', 200);
    }
}
