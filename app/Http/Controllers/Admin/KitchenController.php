<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function index(): View
    {
        return view('admin.kitchen.index', [
            'title'      => 'Kitchen Dashboard',
            'preparing'  => Order::with('items')->whereIn('status', ['accepted', 'cooking'])->latest()->get(),
            'ready'      => Order::with('items')->where('status', 'ready')->latest()->get(),
            'dispatched' => Order::with('items')->where('status', 'out_for_delivery')->latest()->get(),
        ]);
    }
}
