<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class OurMenuController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('availableItems')->orderBy('sort_order')->get();

        $sections = $categories
            ->filter(fn ($cat) => $cat->availableItems->isNotEmpty())
            ->map(fn ($cat) => [
                'slug'    => $cat->slug,
                'heading' => $cat->name,
                'italian' => $cat->name,
                'items'   => $cat->availableItems->map(fn ($item) => [
                    'id'       => $item->slug,
                    'category' => $cat->slug,
                    'name'     => $item->name,
                    'desc'     => $item->description ?? '',
                    'price'    => number_format((float) $item->base_price, 2),
                    'basePrice'=> (float) $item->base_price,
                ])->all(),
            ])
            ->values()
            ->all();

        return view('our-menu', ['title' => 'Menu', 'sections' => $sections]);
    }
}
