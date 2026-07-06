<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topping;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToppingController extends Controller
{
    public function index(): View
    {
        return view('admin.toppings.index', [
            'title'    => 'Topping Management',
            'toppings' => Topping::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'       => 'required|string|max:60|unique:toppings,name',
            'price'      => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        Topping::create([
            'name'         => $data['name'],
            'price'        => $data['price'],
            'sort_order'   => $data['sort_order'] ?? 0,
            'is_available' => true,
        ]);

        return redirect()->route('admin.toppings.index')->with('success', "'{$data['name']}' added.");
    }

    public function update(Request $request, string $topping): RedirectResponse
    {
        $t = Topping::findOrFail($topping);

        $data = $request->validate([
            'name'       => 'required|string|max:60|unique:toppings,name,' . $t->id,
            'price'      => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $t->update([
            'name'       => $data['name'],
            'price'      => $data['price'],
            'sort_order' => $data['sort_order'] ?? $t->sort_order,
        ]);

        return redirect()->route('admin.toppings.index')->with('success', "'{$t->name}' updated.");
    }

    public function toggle(string $topping): RedirectResponse
    {
        $t = Topping::findOrFail($topping);
        $t->update(['is_available' => !$t->is_available]);

        return redirect()->route('admin.toppings.index')
            ->with('success', "'{$t->name}' " . ($t->is_available ? 'now available' : 'now hidden') . '.');
    }

    public function destroy(string $topping): RedirectResponse
    {
        $t = Topping::findOrFail($topping);
        $name = $t->name;
        $t->delete();

        return redirect()->route('admin.toppings.index')->with('success', "'{$name}' removed.");
    }
}
