<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        $data['name']    = str_replace(["\r", "\n"], '', $data['name']);
        $data['subject'] = str_replace(["\r", "\n"], '', $data['subject']);

        $to = Setting::get('store_email', 'nw5pizza@gmail.com');

        Mail::to($to)->queue(new ContactMessage(
            senderName:  $data['name'],
            senderEmail: $data['email'],
            subject:     $data['subject'],
            body:        $data['message'],
        ));

        return back()->with('success', 'Message received. We\'ll be in touch soon!');
    }
}
