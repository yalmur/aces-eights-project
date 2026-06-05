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

        // Mark as accepted when arriving from Stripe success — send email once
        if ($request->session_id && $orderModel->status === 'pending_payment') {
            $orderModel->update(['status' => 'accepted']);
            Mail::to($orderModel->customer_email)->queue(new OrderConfirmation($orderModel));
        }

        return view('orders.confirmation', [
            'title' => 'Order Confirmed — #' . $orderModel->id,
            'order' => $orderModel,
        ]);
    }

    public function tracking(string $order): View
    {
        $orderModel = Order::with('items')->findOrFail($order);

        return view('orders.tracking', [
            'title' => 'Track Order #' . $orderModel->id,
            'order' => $orderModel,
        ]);
    }
}
