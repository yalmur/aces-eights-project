<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('about', ['title' => 'About Us']);
    }

    public function contact(): View
    {
        return view('contact', ['title' => 'Contact Us']);
    }

    public function sendContact(Request $request): RedirectResponse
    {
        return back()->with('success', 'Message received. We\'ll be in touch!');
    }
}
