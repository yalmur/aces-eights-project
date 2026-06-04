<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $promotions = Promotion::latest()->paginate(20);
        return view('admin.promotions.index', ['title' => 'Promotions', 'promotions' => $promotions]);
    }

    public function create(): View
    {
        return view('admin.promotions.edit', ['title' => 'Add Promotion', 'promo' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Promotion::create($data);
        return redirect()->route('admin.promotions.index')->with('success', "Promotion '{$data['code']}' created.");
    }

    public function edit(string $promo): View
    {
        $promotion = Promotion::findOrFail($promo);
        return view('admin.promotions.edit', ['title' => 'Edit Promotion', 'promo' => $promotion]);
    }

    public function update(Request $request, string $promo): RedirectResponse
    {
        $promotion = Promotion::findOrFail($promo);
        $data = $this->validated($request, $promotion->id);
        $promotion->update($data);
        return redirect()->route('admin.promotions.index')->with('success', "Promotion '{$promotion->code}' updated.");
    }

    public function destroy(string $promo): RedirectResponse
    {
        $promotion = Promotion::findOrFail($promo);
        $code = $promotion->code;
        $promotion->delete();
        return redirect()->route('admin.promotions.index')->with('success', "Promotion '{$code}' deleted.");
    }

    private function validated(Request $request, ?int $excludeId = null): array
    {
        $data = $request->validate([
            'code'             => 'required|string|max:50|unique:promotions,code' . ($excludeId ? ",{$excludeId}" : ''),
            'name'             => 'required|string|max:255',
            'type'             => 'required|in:percentage,fixed_amount,free_delivery',
            'value'            => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses'         => 'nullable|integer|min:1',
            'is_active'        => 'boolean',
            'expires_at'       => 'nullable|date|after:today',
        ]);
        $data['code']      = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
