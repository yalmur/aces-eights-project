<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Deal;
use App\Models\DealSlot;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DealController extends Controller
{
    public function index(): View
    {
        $deals = Deal::withCount('slots')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.deals.index', ['title' => 'Deals', 'deals' => $deals]);
    }

    public function create(): View
    {
        return view('admin.deals.edit', [
            'title'      => 'New Deal',
            'deal'       => null,
            'categories' => Category::orderBy('sort_order')->get(),
            'menuItems'  => MenuItem::where('is_available', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('deals', 'public');
        }
        $deal = Deal::create($data);
        if ($deal->hasSlots()) {
            $this->syncSlots($deal, $request->input('slots', []));
        }
        return redirect()->route('admin.deals.index')->with('success', "Deal '{$deal->name}' created.");
    }

    public function edit(Deal $deal): View
    {
        $deal->load(['slots.categories', 'slots.menuItems']);
        return view('admin.deals.edit', [
            'title'      => 'Edit Deal — ' . $deal->name,
            'deal'       => $deal,
            'categories' => Category::orderBy('sort_order')->get(),
            'menuItems'  => MenuItem::where('is_available', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Deal $deal): RedirectResponse
    {
        $data = $this->validated($request, $deal->id);
        if ($request->hasFile('image')) {
            if ($deal->image_path) Storage::disk('public')->delete($deal->image_path);
            $data['image_path'] = $request->file('image')->store('deals', 'public');
        } elseif ($request->boolean('remove_image') && $deal->image_path) {
            Storage::disk('public')->delete($deal->image_path);
            $data['image_path'] = null;
        }
        $deal->update($data);
        if ($deal->hasSlots()) {
            $this->syncSlots($deal, $request->input('slots', []));
        } else {
            $deal->slots()->delete();
        }
        return redirect()->route('admin.deals.index')->with('success', "Deal '{$deal->name}' updated.");
    }

    public function destroy(Deal $deal): RedirectResponse
    {
        $name = $deal->name;
        if ($deal->image_path) Storage::disk('public')->delete($deal->image_path);
        $deal->delete();
        return redirect()->route('admin.deals.index')->with('success', "Deal '{$name}' deleted.");
    }

    public function toggle(Deal $deal): RedirectResponse
    {
        $deal->update(['is_active' => !$deal->is_active]);
        return back()->with('success', "Deal '{$deal->name}' " . ($deal->is_active ? 'activated' : 'deactivated') . '.');
    }

    private function validated(Request $request, ?int $excludeId = null): array
    {
        $data = $request->validate([
            'name'           => 'required|string|max:120',
            'slug'           => 'nullable|string|max:120|unique:deals,slug' . ($excludeId ? ",{$excludeId}" : ''),
            'description'    => 'nullable|string|max:500',
            'deal_type'      => 'required|in:bundle,bogo,percentage_off,fixed_off',
            'price'          => 'nullable|numeric|min:0',
            'discount_value' => 'nullable|numeric|min:0',
            'is_active'      => 'boolean',
            'sort_order'     => 'integer|min:0',
            'starts_at'      => 'nullable|date',
            'ends_at'        => 'nullable|date|after_or_equal:starts_at',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_image'   => 'nullable|boolean',
        ]);
        unset($data['image'], $data['remove_image']);

        $data['is_active']   = $request->boolean('is_active');
        $data['slug']        = ($data['slug'] ?? null) ?: Str::slug($data['name']);
        $data['sort_order']  = (int) ($data['sort_order'] ?? 0);
        return $data;
    }

    private function syncSlots(Deal $deal, array $slots): void
    {
        $deal->slots()->delete();

        foreach ($slots as $i => $s) {
            if (empty(trim($s['label'] ?? ''))) continue;

            $slot = DealSlot::create([
                'deal_id'     => $deal->id,
                'label'       => substr(trim($s['label']), 0, 80),
                'min_qty'     => max(1, (int) ($s['min_qty'] ?? 1)),
                'max_qty'     => max(1, (int) ($s['max_qty'] ?? 1)),
                'is_required' => !empty($s['is_required']),
                'is_free'     => !empty($s['is_free']),
                'sort_order'  => $i,
            ]);

            if (!empty($s['category_ids'])) {
                $slot->categories()->sync(array_filter((array) $s['category_ids'], 'is_numeric'));
            }
            if (!empty($s['item_ids'])) {
                $slot->menuItems()->sync(array_filter((array) $s['item_ids'], 'is_numeric'));
            }
        }
    }
}
