<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;

/*
 * Customers can listen to their own order channel.
 */
Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    $order = Order::find($orderId);
    return $order && $order->user_id === $user->id;
});

/*
 * Admin staff can listen to the shared orders channel.
 */
Broadcast::channel('admin.orders', function ($user) {
    return $user->role === 'admin';
});
