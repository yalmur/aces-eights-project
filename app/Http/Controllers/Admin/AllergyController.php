<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AllergyController extends Controller
{
    public function index(): View
    {
        return view('admin.allergy.index', [
            'title'         => 'Allergy Management',
            'allergens'     => Allergen::orderBy('sort_order')->get(),
            'menuItems'     => MenuItem::with('category')->orderBy('name')->get(),
            'alertsEnabled' => \App\Models\Setting::get('allergy_alerts_enabled', '1') === '1',
            'disclaimer'    => \App\Models\Setting::get('checkout_disclaimer',
                'ACES & EIGHTS PIZZA CO. TAKES FOOD SAFETY SERIOUSLY. Please be advised that our kitchen handles wheat, dairy, and eggs. While we take meticulous steps to prevent cross-contact, we cannot guarantee a 100% allergen-free environment for those with severe sensitivities. By proceeding with your order, you acknowledge these risks. Contact our floor manager for specific ingredient concerns.'
            ),
        ]);
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'allergy_alerts_enabled' => 'boolean',
            'checkout_disclaimer'    => 'nullable|string|max:2000',
        ]);

        Setting::setMany([
            'allergy_alerts_enabled' => $request->boolean('allergy_alerts_enabled') ? '1' : '0',
            'checkout_disclaimer'    => $data['checkout_disclaimer'] ?? '',
        ]);

        return redirect()->route('admin.allergy.index')->with('success', 'Allergy settings saved.');
    }

    public function saveMap(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'allergens'    => 'nullable|array',
            'allergens.*'  => 'exists:allergens,id',
        ]);

        $item = MenuItem::findOrFail($data['menu_item_id']);
        $item->allergens()->sync($data['allergens'] ?? []);

        return redirect()->route('admin.allergy.index')->with('success', "Allergen mapping saved for '{$item->name}'.");
    }

    public function toggle(string $allergen): RedirectResponse
    {
        $a = Allergen::findOrFail($allergen);
        $a->update(['is_visible' => !$a->is_visible]);
        return redirect()->route('admin.allergy.index')
            ->with('success', "'{$a->name}' " . ($a->is_visible ? 'now visible' : 'now hidden') . '.');
    }

    public function destroyAllergen(string $allergen): RedirectResponse
    {
        $a = Allergen::findOrFail($allergen);
        $name = $a->name;
        $a->delete();
        return redirect()->route('admin.allergy.index')->with('success', "'{$name}' removed.");
    }
}
