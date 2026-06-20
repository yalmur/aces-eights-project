<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function confirmation(Request $request, string $order): View
    {
        $orderModel = Order::with('items')->findOrFail($order);

        if ($orderModel->user_id && $orderModel->user_id !== $request->user()?->id) {
            abort(403);
        }

        // Fallback for when Stripe redirect arrives before the webhook fires.
        if ($request->query('session_id') && $orderModel->status === 'pending_payment' && $orderModel->customer_email) {
            Mail::to($orderModel->customer_email)->queue(new OrderConfirmation($orderModel));
        }

        return view('orders.confirmation', [
            'title' => 'Order Confirmed — #' . $orderModel->id,
            'order' => $orderModel,
        ]);
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
