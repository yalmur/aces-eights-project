<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(): View
    {
        return view('admin.menu.index', ['title' => 'Menu Management']);
    }

    public function create(): View
    {
        return view('admin.menu.edit', ['title' => 'Add Menu Item']);
    }

    public function edit(string $item): View
    {
        return view('admin.menu.edit', ['title' => 'Edit Menu Item', 'itemId' => $item]);
    }
}
