<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredItems = MenuItem::where('is_featured', true)
            ->where('is_available', true)
            ->with('category', 'allergens')
            ->limit(6)
            ->get();

        return view('home', [
            'title'         => 'Aces & Eights Pizza — London\'s Finest Italian Pizza',
            'featuredItems' => $featuredItems,
        ]);
    }
}
