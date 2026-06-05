<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdate;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
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
        $orders = Order::with('items')
            ->whereIn('type', ['eat_in', 'collection'])
            ->whereNotIn('status', ['pending_payment', 'cancelled'])
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $menuItems = MenuItem::with('category')
            ->where('is_available', true)
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->get();

        return view('admin.orders.in-store', [
            'title'     => 'In-Store Orders',
            'orders'    => $orders,
            'menuItems' => $menuItems,
        ]);
    }

    public function storeInStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'customer_email' => 'nullable|email|max:255',
            'order_type'     => 'required|in:eat_in,collection',
            'notes'          => 'nullable|string|max:1000',
            'items'          => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.qty'    => 'required|integer|min:1',
        ]);

        $subtotal = 0;
        $orderItems = [];

        foreach ($data['items'] as $line) {
            $menuItem  = MenuItem::findOrFail($line['menu_item_id']);
            $lineTotal = $menuItem->base_price * $line['qty'];
            $subtotal += $lineTotal;
            $orderItems[] = [
                'menu_item_id' => $menuItem->id,
                'name'         => $menuItem->name,
                'qty'          => $line['qty'],
                'unit_price'   => $menuItem->base_price,
                'line_total'   => $lineTotal,
                'size'         => null,
                'crust'        => null,
                'size_extra'   => 0,
                'crust_extra'  => 0,
            ];
        }

        $order = Order::create([
            'type'           => $data['order_type'],
            'status'         => 'accepted',
            'subtotal'       => $subtotal,
            'delivery_fee'   => 0,
            'total'          => $subtotal,
            'customer_name'  => $data['customer_name'],
            'customer_email' => $data['customer_email'] ?? null,
            'customer_phone' => $data['customer_phone'] ?? null,
            'notes'          => $data['notes'] ?? null,
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        OrderStatusUpdated::dispatch($order);

        return redirect()->route('admin.orders.in-store')
            ->with('success', "Order #{$order->id} created for {$order->customer_name}.");
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
