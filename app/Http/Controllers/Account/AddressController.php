<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label'          => 'required|string|max:50',
            'street_address' => 'required|string|max:255',
            'city'           => 'required|string|max:100',
            'postcode'       => 'required|string|max:20',
        ]);

        $user    = Auth::user();
        $isFirst = $user->addresses()->count() === 0;

        $user->addresses()->create(array_merge($data, ['is_default' => $isFirst]));

        return redirect()->route('account')->with('success', 'Address saved.');
    }

    public function destroy(string $address): RedirectResponse
    {
        $addr = UserAddress::findOrFail($address);

        if ($addr->user_id !== Auth::id()) {
            abort(403);
        }

        $addr->delete();

        return redirect()->route('account')->with('success', 'Address removed.');
    }

    public function setDefault(string $address): RedirectResponse
    {
        $addr = UserAddress::findOrFail($address);

        if ($addr->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->addresses()->update(['is_default' => false]);
        $addr->update(['is_default' => true]);

        return redirect()->route('account')->with('success', 'Default address updated.');
    }
}
