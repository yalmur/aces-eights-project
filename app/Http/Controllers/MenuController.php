<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Topping;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        [$categories, $toppings] = Cache::remember('public.menu.index', 300, fn () => [
            Category::with([
                'availableItems.allergens',
                'availableItems.baseIngredients',
                'availableItems.relatedItems',
            ])
            ->orderBy('sort_order')
            ->get(),
            Topping::available()->get(),
        ]);

        return view('menu.index', [
            'title'      => 'Order Now',
            'categories' => $categories,
            'toppings'   => $toppings,
        ]);
    }

    public function show(string $slug): View
    {
        $item = MenuItem::with(['category', 'allergens', 'baseIngredients', 'relatedItems'])
            ->where('slug', $slug)
            ->where('is_available', true)
            ->firstOrFail();

        $related = MenuItem::with(['allergens', 'category', 'baseIngredients', 'relatedItems'])
            ->where('category_id', $item->category_id)
            ->where('id', '!=', $item->id)
            ->where('is_available', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('menu.show', [
            'title'   => $item->name,
            'item'    => $item,
            'related' => $related,
        ]);
    }
}
