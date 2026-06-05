<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    public function index(): View
    {
        $zones = DeliveryZone::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.delivery.index', ['title' => 'Delivery Zones', 'zones' => $zones]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        DeliveryZone::create($data);
        return redirect()->route('admin.delivery.index')->with('success', 'Delivery zone added.');
    }

    public function update(Request $request, string $zone): RedirectResponse
    {
        $zoneModel = DeliveryZone::findOrFail($zone);
        $zoneModel->update($this->validated($request));
        return redirect()->route('admin.delivery.index')->with('success', "Zone '{$zoneModel->name}' updated.");
    }

    public function destroy(string $zone): RedirectResponse
    {
        DeliveryZone::findOrFail($zone)->delete();
        return redirect()->route('admin.delivery.index')->with('success', 'Zone deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'min_km'     => 'required|numeric|min:0',
            'max_km'     => 'required|numeric|min:0',
            'postcodes'  => 'nullable|string|max:500',
            'fee'        => 'required|numeric|min:0',
            'is_active'  => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);
        // Normalise: uppercase, strip extra spaces around commas
        if (!empty($data['postcodes'])) {
            $parts = array_map('trim', explode(',', strtoupper($data['postcodes'])));
            $data['postcodes'] = implode(', ', array_filter($parts));
        }
        return $data;
    }
}
