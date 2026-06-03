<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function index(): View
    {
        return view('admin.kitchen.index', ['title' => 'Kitchen Command']);
    }
}
