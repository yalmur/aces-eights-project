<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class OrderController extends Controller
{
    public function confirmation(string $order): View
    {
        return view('orders.confirmation', ['title' => 'Order Confirmed', 'orderId' => $order]);
    }

    public function tracking(string $order): View
    {
        return view('orders.tracking', ['title' => 'Track Your Order', 'orderId' => $order]);
    }
}
