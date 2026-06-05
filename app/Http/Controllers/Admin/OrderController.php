<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdate;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['user', 'items'])
            ->whereNotIn('status', ['pending_payment', 'cancelled'])
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', [
            'title'  => 'Online Orders',
            'orders' => $orders,
        ]);
    }

    public function inStore(): View
    {
        return view('admin.orders.in-store', ['title' => 'In-Store Orders']);
    }

    public function show(string $order): View
    {
        $orderModel = Order::with(['user', 'items'])->findOrFail($order);

        return view('admin.orders.detail', [
            'title' => 'Order #' . $orderModel->id,
            'order' => $orderModel,
        ]);
    }

    public function updateStatus(Request $request, string $order): RedirectResponse
    {
        $orderModel = Order::findOrFail($order);
        $data = $request->validate([
            'status' => 'required|in:accepted,cooking,ready,out_for_delivery,collected,delivered,cancelled',
        ]);

        $orderModel->update(['status' => $data['status']]);

        OrderStatusUpdated::dispatch($orderModel);

        if (!empty($orderModel->customer_email)) {
            Mail::to($orderModel->customer_email)->queue(new OrderStatusUpdate($orderModel));
        }

        return back()->with('success', "Order #{$orderModel->id} updated to {$orderModel->status_label}.");
    }
}
