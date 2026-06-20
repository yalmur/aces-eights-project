<?php

namespace App\Http\Controllers;

use App\Mail\PartyHallInquiry;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PartyHallController extends Controller
{
    public function index(): View
    {
        return view('party-hall', ['title' => 'Party Hall Hire | Aces & Eights Pizza']);
    }

    public function submit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|max:150',
            'phone'      => 'required|string|max:30',
            'event_date' => 'required|date|after:today',
            'guests'     => 'required|integer|min:50|max:500',
            'event_type' => 'required|string|max:60',
            'message'    => 'nullable|string|max:1000',
        ]);

        $adminEmail = Setting::get('store_email', 'nw5pizza@gmail.com');
        Mail::to($adminEmail)->queue(new PartyHallInquiry($data));

        return redirect()->route('party-hall')
            ->with('success', 'Inquiry sent! We\'ll be in touch within 24 hours.');
    }
}
