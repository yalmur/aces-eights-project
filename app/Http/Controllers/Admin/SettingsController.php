<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'title'    => 'Settings',
            'settings' => [
                'store_name'      => Setting::get('store_name',      'Aces & Eights Pizza'),
                'store_address'   => Setting::get('store_address',   '156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP'),
                'store_phone'     => Setting::get('store_phone',     '+44 020 7485 4033'),
                'store_email'     => Setting::get('store_email',     'nw5pizza@gmail.com'),
                'opening_sun_thu' => Setting::get('opening_sun_thu', '16:00 – 22:45'),
                'opening_fri_sat' => Setting::get('opening_fri_sat', '16:00 – 23:15'),
                'hero_text'              => Setting::get('hero_text',              ''),
                'story_text'             => Setting::get('story_text',             ''),
                'size_large_extra'       => Setting::get('size_large_extra',       '4.00'),
                'crust_gluten_free_extra'=> Setting::get('crust_gluten_free_extra','2.00'),
                'crust_cauliflower_extra'=> Setting::get('crust_cauliflower_extra','2.50'),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name'      => 'required|string|max:100',
            'store_address'   => 'required|string|max:255',
            'store_phone'     => 'required|string|max:30',
            'store_email'     => 'required|email|max:100',
            'opening_sun_thu' => 'required|string|max:50',
            'opening_fri_sat' => 'required|string|max:50',
            'hero_text'               => 'nullable|string|max:500',
            'story_text'              => 'nullable|string|max:1000',
            'size_large_extra'        => 'required|numeric|min:0|max:50',
            'crust_gluten_free_extra' => 'required|numeric|min:0|max:50',
            'crust_cauliflower_extra' => 'required|numeric|min:0|max:50',
        ]);

        Setting::setMany($data);

        return redirect()->route('admin.settings.index')->with('success', 'Settings saved successfully.');
    }
}
