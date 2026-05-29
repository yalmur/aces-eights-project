<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        return view('menu.index', ['title' => 'Our Menu']);
    }
}
