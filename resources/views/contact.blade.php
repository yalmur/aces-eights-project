@extends('layouts.app')
@section('content')
@if(session('success'))
<div class="max-w-container-max mx-auto px-4 lg:px-16 pt-8">
  <div class="bg-secondary-container border-2 border-on-surface px-6 py-4 flex items-center gap-3">
    <span class="material-symbols-outlined text-on-surface">check_circle</span>
    <p class="font-label-bold text-label-bold uppercase">{{ session('success') }}</p>
  </div>
</div>
@endif
{{-- adapted stitch content --}}

<main class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-12">
<!-- Hero Section -->
<header class="mb-16 text-center">
<div class="inline-block border-2 border-primary px-6 py-2 mb-6">
<span class="font-label-bold text-label-bold uppercase tracking-[0.2em] text-primary">Est. London NW5</span>
</div>
<h1 class="font-display text-display md:text-[64px] mb-4 text-primary uppercase leading-none">Get in Touch</h1>
<p class="max-w-2xl mx-auto font-body-lg text-body-lg text-on-surface-variant italic">
    Questions about our sourdough, booking a large table, or just want to talk shop? Drop us a line below. We're always heating the oven.
</p>
<div class="double-divider mt-12 mx-auto max-w-sm"></div>
</header>
<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
<!-- Contact Form (Bento Style Card) -->
<section class="lg:col-span-7 bg-surface-container-low industrial-border-thin p-8 md:p-12">
<h2 class="font-headline-md text-headline-md mb-8 flex items-center gap-3">
<span class="material-symbols-outlined text-primary">mail</span>
    Send a Message
</h2>
<form action="{{ route('contact.send') }}" method="POST" class="space-y-8">
@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
<div class="flex flex-col">
<label class="font-label-bold text-label-bold text-primary uppercase mb-1" for="name">Full Name</label>
<input class="ledger-input p-2 font-body-md" id="name" name="name" placeholder="Vito Corleone" required type="text"/>
</div>
<div class="flex flex-col">
<label class="font-label-bold text-label-bold text-primary uppercase mb-1" for="email">Email Address</label>
<input class="ledger-input p-2 font-body-md" id="email" name="email" placeholder="vito@thefamily.com" required type="email"/>
</div>
</div>
<div class="flex flex-col">
<label class="font-label-bold text-label-bold text-primary uppercase mb-1" for="subject">Subject</label>
<select class="ledger-input p-2 font-body-md bg-transparent" id="subject" name="subject">
<option>Table Reservation</option>
<option>General Inquiry</option>
<option>Private Events</option>
<option>Career Opportunities</option>
</select>
</div>
<div class="flex flex-col">
<label class="font-label-bold text-label-bold text-primary uppercase mb-1" for="message">Message</label>
<textarea class="ledger-input p-2 font-body-md" id="message" name="message" placeholder="Tell us what's on your mind..." required rows="4"></textarea>
</div>
<button class="gold-metallic-button w-full md:w-auto px-12 py-4 font-label-bold text-label-bold text-white uppercase tracking-widest" type="submit">
    Send Message
</button>
</form>
</section>
<!-- Sidebar Info -->
<aside class="lg:col-span-5 space-y-gutter">
<!-- Direct Contact Info -->
<div class="bg-primary text-white p-8 industrial-border">
<h3 class="font-headline-md text-headline-md mb-6 border-b border-white/20 pb-4">Direct Lines</h3>
<ul class="space-y-6">
<li class="flex items-start gap-4">
<span class="material-symbols-outlined mt-1">location_on</span>
<div>
<p class="font-label-bold text-label-bold uppercase opacity-70">Headquarters</p>
<p class="font-body-lg text-body-lg">{{ \App\Models\Setting::get('store_address', '156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP') }}</p>
</div>
</li>
<li class="flex items-start gap-4">
<span class="material-symbols-outlined mt-1">call</span>
<div>
<p class="font-label-bold text-label-bold uppercase opacity-70">The Kitchen</p>
<p class="font-body-lg text-body-lg">{{ \App\Models\Setting::get('store_phone', '+44 020 7485 4033') }}</p>
</div>
</li>
<li class="flex items-start gap-4">
<span class="material-symbols-outlined mt-1">alternate_email</span>
<div>
<p class="font-label-bold text-label-bold uppercase opacity-70">Correspondence</p>
<p class="font-body-lg text-body-lg">{{ \App\Models\Setting::get('store_email', 'nw5pizza@gmail.com') }}</p>
</div>
</li>
</ul>
</div>
<!-- Opening Hours -->
<div class="bg-surface-container industrial-border-thin p-8">
<h3 class="font-headline-md text-headline-md mb-6 text-primary flex items-center gap-3">
<span class="material-symbols-outlined">schedule</span>
    Service Hours
</h3>
<div class="space-y-4">
<div class="flex justify-between items-end border-b border-outline-variant pb-2">
<span class="font-label-bold text-label-bold uppercase">Sun – Thu</span>
<span class="font-body-lg text-body-lg">{{ \App\Models\Setting::get('opening_sun_thu', '16:00 – 22:45') }}</span>
</div>
<div class="flex justify-between items-end border-b border-outline-variant pb-2">
<span class="font-label-bold text-label-bold uppercase text-primary">Fri – Sat</span>
<span class="font-body-lg text-body-lg font-bold">{{ \App\Models\Setting::get('opening_fri_sat', '16:00 – 23:15') }}</span>
</div>
</div>
<p class="mt-4 text-label-sm text-on-surface-variant italic">* Kitchen closes 30 mins before end of service.</p>
</div>
</aside>
</div>
<!-- Map Section -->
<section class="mt-gutter">
<div class="relative h-[400px] w-full industrial-border overflow-hidden">
  <iframe
    src="https://maps.google.com/maps?q=156+Fortess+Road+London+NW5+2HP&output=embed"
    width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"
    referrerpolicy="no-referrer-when-downgrade"
    title="Aces &amp; Eights Pizza location map"></iframe>
  <a href="https://maps.google.com/?q=156+Fortess+Road+London+NW5+2HP" target="_blank" rel="noopener"
     class="absolute bottom-4 right-4 font-mono text-[10px] font-bold uppercase bg-surface border border-outline px-3 py-1.5 hover:bg-primary hover:text-on-primary transition-colors">
    Open in Google Maps ↗
  </a>
</div>
</section>
</main>
@endsection
