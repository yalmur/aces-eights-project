<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use App\Models\BaseIngredient;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(Request $request): View
    {
        $query = MenuItem::with(['category', 'allergens'])
            ->orderBy('category_id')
            ->orderBy('sort_order');

        if ($search = $request->query('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"));
        }

        if ($categorySlug = $request->query('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        $items      = $query->paginate(20)->withQueryString();
        $categories = \App\Models\Category::orderBy('sort_order')->get(['id', 'name', 'slug']);

        return view('admin.menu.index', [
            'title'      => 'Menu Management',
            'items'      => $items,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.menu.edit', [
            'title'      => 'Add Menu Item',
            'item'       => null,
            'categories' => Category::orderBy('sort_order')->get(),
            'allergens'  => Allergen::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255|unique:menu_items,name',
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'nullable|string',
            'base_price'    => 'required|numeric|min:0',
            'is_available'   => 'boolean',
            'is_featured'    => 'boolean',
            'is_vegetarian'  => 'boolean',
            'is_vegan'       => 'boolean',
            'allergens'     => 'nullable|array',
            'allergens.*'   => 'exists:allergens,id',
            'ingredients'   => 'nullable|array',
            'ingredients.*' => 'string|max:100',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu', 'public');
        }

        $item = MenuItem::create([
            'category_id'   => $data['category_id'],
            'name'          => $data['name'],
            'slug'          => Str::slug($data['name']),
            'description'   => $data['description'] ?? null,
            'base_price'    => $data['base_price'],
            'is_available'  => $request->boolean('is_available'),
            'is_featured'   => $request->boolean('is_featured'),
            'is_vegetarian' => $request->boolean('is_vegetarian'),
            'is_vegan'      => $request->boolean('is_vegan'),
            'sort_order'   => MenuItem::max('sort_order') + 1,
            'image_path'   => $imagePath,
        ]);

        $item->allergens()->sync($data['allergens'] ?? []);
        if ($request->has('ingredients')) {
            $this->syncIngredients($item, $data['ingredients'] ?? []);
        }

        return redirect()->route('admin.menu.index')
            ->with('success', "'{$item->name}' added to menu.");
    }

    public function edit(string $item): View
    {
        $menuItem = MenuItem::with(['allergens', 'baseIngredients'])->findOrFail($item);

        return view('admin.menu.edit', [
            'title'      => 'Edit: ' . $menuItem->name,
            'item'       => $menuItem,
            'categories' => Category::orderBy('sort_order')->get(),
            'allergens'  => Allergen::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, string $item): RedirectResponse
    {
        $menuItem = MenuItem::findOrFail($item);

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'nullable|string',
            'base_price'    => 'required|numeric|min:0',
            'is_available'   => 'boolean',
            'is_featured'    => 'boolean',
            'is_vegetarian'  => 'boolean',
            'is_vegan'       => 'boolean',
            'allergens'     => 'nullable|array',
            'allergens.*'   => 'exists:allergens,id',
            'ingredients'   => 'nullable|array',
            'ingredients.*' => 'string|max:100',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($menuItem->image_path) {
                Storage::disk('public')->delete($menuItem->image_path);
            }
            $menuItem->image_path = $request->file('image')->store('menu', 'public');
        }

        $menuItem->update([
            'category_id'   => $data['category_id'],
            'name'          => $data['name'],
            'slug'          => Str::slug($data['name']),
            'description'   => $data['description'] ?? null,
            'base_price'    => $data['base_price'],
            'is_available'  => $request->boolean('is_available'),
            'is_featured'   => $request->boolean('is_featured'),
            'is_vegetarian' => $request->boolean('is_vegetarian'),
            'is_vegan'      => $request->boolean('is_vegan'),
            'image_path'   => $menuItem->image_path,
        ]);

        $menuItem->allergens()->sync($data['allergens'] ?? []);
        if ($request->has('ingredients')) {
            $this->syncIngredients($menuItem, $data['ingredients'] ?? []);
        }

        return redirect()->route('admin.menu.index')
            ->with('success', "'{$menuItem->name}' updated.");
    }

    public function destroy(string $item): RedirectResponse
    {
        $menuItem = MenuItem::findOrFail($item);
        $name = $menuItem->name;
        $menuItem->delete();

        return redirect()->route('admin.menu.index')
            ->with('success', "'{$name}' removed from menu.");
    }

    public function toggleAvailability(string $item): \Illuminate\Http\JsonResponse
    {
        $menuItem = MenuItem::findOrFail($item);
        $menuItem->update(['is_available' => !$menuItem->is_available]);
        return response()->json(['available' => $menuItem->is_available]);
    }

    private function syncIngredients(MenuItem $item, array $ingredients): void
    {
        $item->baseIngredients()->delete();
        foreach (array_values(array_filter($ingredients)) as $i => $name) {
            BaseIngredient::create([
                'menu_item_id' => $item->id,
                'name'         => $name,
                'sort_order'   => $i + 1,
            ]);
        }
    }
}
