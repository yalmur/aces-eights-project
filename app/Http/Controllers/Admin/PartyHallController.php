<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartyHallInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartyHallController extends Controller
{
    public function index(): View
    {
        $inquiries = PartyHallInquiry::latest()->paginate(20);
        return view('admin.party-hall.index', ['title' => 'Party Hall Inquiries', 'inquiries' => $inquiries]);
    }

    public function update(Request $request, PartyHallInquiry $inquiry): RedirectResponse
    {
        $inquiry->update(['status' => $request->validate(['status' => 'required|in:new,contacted,confirmed,cancelled'])['status']]);
        return back()->with('success', 'Status updated.');
    }
}
