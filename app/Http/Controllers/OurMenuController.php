<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class OurMenuController extends Controller
{
    public function index(): View
    {
        $sections = Cache::remember('public.our-menu.sections', 300, function () {
            $categories = Category::with('availableItems.allergens', 'availableItems.baseIngredients', 'availableItems.relatedItems', 'availableItems.toppings')->orderBy('sort_order')->get();
            return $categories
                ->filter(fn ($cat) => $cat->availableItems->isNotEmpty())
                ->map(fn ($cat) => [
                    'slug'    => $cat->slug,
                    'heading' => $cat->name,
                    'italian' => $cat->name,
                    'items'   => $cat->availableItems->map(fn ($item) => [
                        'id'        => $item->slug,
                        'category'  => $cat->slug,
                        'name'      => $item->name,
                        'desc'      => $item->description ?? '',
                        'price'     => number_format((float) $item->base_price, 2),
                        'basePrice' => (float) $item->base_price,
                        'image'     => $item->hasStoredImage()
                            ? asset('storage/' . $item->image_path)
                            : 'https://placehold.co/400x300/e4e2e1/1b1c1c?text=' . urlencode($item->name),
                        'allergens' => $item->allergens->where('is_visible', true)->pluck('name')->values()->all(),
                        'ingredients'     => $item->baseIngredients->pluck('name')->values()->all(),
                        'relatedItems'    => $item->relatedItemsPayload(),
                        'isCustomizable'  => $item->is_customizable,
                        'availableToppings' => $item->toppingsPayload(),
                    ])->all(),
                ])
                ->values()
                ->all();
        });

        return view('our-menu', ['title' => 'Menu', 'sections' => $sections]);
    }
}
