<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload    = $request->getContent();
        $sigHeader  = $request->header('Stripe-Signature');
        $secret     = config('services.stripe.webhook_secret');

        if ($secret) {
            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $secret);
            } catch (SignatureVerificationException $e) {
                return response('Webhook signature verification failed.', 400);
            }
            $eventType = $event->type;
            $sessionId = $event->data->object->id ?? null;
        } else {
            $body      = json_decode($payload, true);
            $eventType = $body['type'] ?? '';
            $sessionId = $body['data']['object']['id'] ?? null;
        }

        if ($eventType === 'checkout.session.completed' && $sessionId) {
            $order = Order::where('stripe_session_id', $sessionId)
                ->where('status', 'pending_payment')
                ->first();
            if ($order) {
                $order->update(['status' => 'accepted']);
                OrderStatusUpdated::dispatch($order);
            }
        }

        return response('OK', 200);
    }
}
