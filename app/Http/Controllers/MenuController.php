<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        return view('menu.index', ['title' => 'Our Menu']);
    }

    public function show(string $slug): View
    {
        return view('menu.show', ['title' => ucwords(str_replace('-', ' ', $slug)), 'slug' => $slug]);
    }
}
