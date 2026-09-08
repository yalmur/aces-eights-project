<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Stripe\StripeClient;

class OrderController extends Controller
{
    public function confirmation(Request $request, string $order): View
    {
        $orderModel = Order::with('items')->findOrFail($order);

        if ($orderModel->user_id && $orderModel->user_id !== $request->user()?->id) {
            abort(403);
        }

        // Fallback for when the Stripe webhook hasn't (yet, or never will)
        // flip the order out of pending_payment — verify directly against
        // Stripe on the redirect back, so the order isn't stuck relying
        // solely on webhook delivery.
        if ($orderModel->status === 'pending_payment' && $orderModel->stripe_session_id) {
            $this->reconcileWithStripe($orderModel);
        }

        return view('orders.confirmation', [
            'title' => 'Order Confirmed — #' . $orderModel->id,
            'order' => $orderModel,
        ]);
    }

    private function reconcileWithStripe(Order $orderModel): void
    {
        try {
            $stripe  = new StripeClient(config('services.stripe.secret'));
            $session = $stripe->checkout->sessions->retrieve($orderModel->stripe_session_id);

            if ($session->payment_status === 'paid') {
                $orderModel->update([
                    'status'                   => 'accepted',
                    'stripe_payment_intent_id' => $session->payment_intent,
                ]);
                OrderStatusUpdated::dispatch($orderModel);
                Mail::to($orderModel->customer_email)->queue(new OrderConfirmation($orderModel));
            }
        } catch (\Throwable $e) {
            Log::warning('Stripe confirmation-page reconciliation failed', [
                'order_id' => $orderModel->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    public function tracking(Request $request, string $order): View
    {
        $orderModel = Order::with('items')->findOrFail($order);

        if ($orderModel->user_id && $orderModel->user_id !== $request->user()?->id) {
            abort(403);
        }

        return view('orders.tracking', [
            'title' => 'Track Order #' . $orderModel->id,
            'order' => $orderModel,
        ]);
    }
}
