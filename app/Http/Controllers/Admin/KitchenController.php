<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function index(): View
    {
        $columns = [
            'accepted'         => Order::with('items')->where('status', 'accepted')->latest()->get(),
            'cooking'          => Order::with('items')->where('status', 'cooking')->latest()->get(),
            'ready'            => Order::with('items')->where('status', 'ready')->latest()->get(),
            'out_for_delivery' => Order::with('items')->where('status', 'out_for_delivery')->latest()->get(),
        ];

        return view('admin.kitchen.index', [
            'title'   => 'Kitchen Command',
            'columns' => $columns,
        ]);
    }
}
