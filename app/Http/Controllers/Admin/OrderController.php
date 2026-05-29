<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('admin.orders.index', ['title' => 'Online Orders']);
    }

    public function inStore(): View
    {
        return view('admin.orders.in-store', ['title' => 'In-Store Orders']);
    }

    public function show(string $order): View
    {
        return view('admin.orders.detail', ['title' => 'Order #' . $order, 'orderId' => $order]);
    }
}
