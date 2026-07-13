@extends('layouts.app')
@section('content')
{{-- About Us page adapted from stitch --}}

<!-- Hero Section -->
<section class="relative bg-surface-container-low border-b-2 border-outline overflow-hidden">
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-20 md:py-32 grid grid-cols-1 md:grid-cols-12 gap-gutter items-center">
<div class="md:col-span-7 space-y-6">
<span class="font-label-bold text-label-bold text-primary uppercase tracking-widest block">EST. 2010</span>
<h1 class="font-display text-display md:text-[64px] text-primary leading-[1.1]">Authentic Italian pizza in the heart of London.</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">
    Handcrafted sourdough, freshly prepared every day and baked to perfection in our stone-based ovens. Nestled in the heart of Tufnell Park, we are more than a pizzeria — we are a family kitchen dedicated to great flavor.
</p>
<div class="pt-4 flex flex-col sm:flex-row gap-4">
<a href="https://www.acesandeightssaloonbar.com/booking/" target="_blank" rel="noopener" class="bg-primary text-on-primary font-label-bold text-label-bold px-8 py-4 uppercase tracking-wider border-b-4 border-primary-fixed-dim hover:bg-on-primary-fixed-variant transition-all active:translate-y-1 active:border-b-0 text-center">
    Book a Table
</a>
<div class="flex items-center gap-2 font-label-bold text-label-bold text-on-surface p-4 border-2 border-outline">
<span class="material-symbols-outlined text-primary">location_on</span>
    NW5 2HP, LONDON
</div>
</div>
</div>
<div class="md:col-span-5 relative">
<div class="border-2 border-primary p-2">
{{-- Chef Murat presenting a freshly made pizza on the peel --}}
<img class="w-full h-[500px] object-cover grayscale-[0.2] hover:grayscale-0 transition-all duration-700" src="{{ asset('images/site/about-chef-murat.jpg') }}" alt="Chef Murat with a freshly made pizza"/>
</div>
<div class="absolute -bottom-6 -left-6 bg-secondary-container text-on-secondary-container p-6 border-2 border-outline hidden lg:block">
<p class="font-display text-headline-md leading-none">48H</p>
<p class="font-label-bold text-label-sm uppercase">Slow Ferment</p>
</div>
</div>
</div>
</section>

<!-- Divider -->
<div class="w-full h-2 border-t border-b border-outline my-0"></div>

<!-- Our Story Section -->
<section class="bg-surface py-20">
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
<div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-start">
<div class="sticky top-32">
<h2 class="font-headline-lg text-headline-lg text-primary mb-8 underline decoration-double decoration-outline-variant underline-offset-8">Our Story</h2>
<div class="space-y-6 font-body-lg text-body-lg text-on-surface">
<p>When we opened our doors on Fortess Road back in 2010, we had a simple obsession: bringing genuinely incredible, handmade pizza to our North West London neighbourhood — the kind of local spot we wanted to hang out in ourselves.</p>
<p>For over a decade, our pizza has been the main event. Whether you're craving a classic, thin-base Margherita or a loaded house specialty, every pie is hand-stretched fresh daily and baked to order.</p>
<p class="border-l-4 border-primary pl-6 italic text-on-surface-variant font-body-md">"Food brings people together — we're fiercely proud to be a strictly 18+, fully inclusive venue where everyone is welcome."</p>
</div>
</div>
<div class="grid grid-cols-1 gap-8">
<div class="border-2 border-outline p-4 group overflow-hidden">
{{-- Behind the counter at Aces & Eights, Fortess Road: pizzas fresh from the oven --}}
<img class="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500" src="{{ asset('images/site/about-kitchen-cooking.jpg') }}" alt="Behind the counter at Aces & Eights"/>
<p class="mt-4 font-label-bold text-label-sm text-on-surface-variant uppercase tracking-tighter italic">The Kitchen, Fortess Road</p>
</div>
<div class="bg-surface-container-high p-12 border-2 border-outline relative">
<span class="material-symbols-outlined text-[80px] text-outline-variant absolute top-4 right-4 opacity-30">music_note</span>
<h3 class="font-headline-md text-headline-md text-primary mb-4">A Rock &amp; Roll Soul</h3>
<p class="font-body-md text-body-md text-on-surface">Pair your slice with a local craft beer or classic cocktail, drop a coin in our jukebox, or catch weekend DJs spinning until late. Our basement hosts intimate live music and comedy — and it's available for private hire too.</p>
</div>
</div>
</div>
</div>
</section>

<!-- Our Craft Section: Bento Grid -->
<section class="bg-surface-container-lowest py-20 border-y-[3px] border-double border-outline">
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
<div class="text-center mb-16">
<h2 class="font-display text-display text-primary uppercase">The Components of Quality</h2>
<div class="w-24 h-1 bg-primary mx-auto mt-4"></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
<!-- Component 1: Dough -->
<div class="md:col-span-8 bg-surface border-2 border-outline p-8 flex flex-col md:flex-row gap-8 items-center group">
<div class="w-full md:w-1/2 overflow-hidden border border-outline">
{{-- Hand-stretched dough, topped fresh, ready for the oven --}}
<img class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500" src="{{ asset('images/site/about-dough-stretch.jpg') }}" alt="Hand-stretched pizza dough"/>
</div>
<div class="w-full md:w-1/2">
<h3 class="font-headline-md text-headline-md text-primary mb-2">Double-Fermented, Hand-Stretched Dough</h3>
<p class="font-body-md text-body-md text-on-surface-variant">We never rush our dough — a slow, two-stage fermentation breaks down the heavy starches for a light, airy, easy-to-digest base. Every one is hand-stretched to order by our pizzaiolos, never a rolling pin or press.</p>
</div>
</div>
<!-- Component 2: Drinks -->
<div class="md:col-span-4 bg-primary text-on-primary p-8 flex flex-col justify-between border-2 border-primary">
<span class="material-symbols-outlined text-4xl">local_bar</span>
<div>
<h3 class="font-headline-md text-headline-md mb-2">Local Brews &amp; Classic Cocktails</h3>
<p class="font-body-md text-body-md text-on-primary-container">We partner with standout London and independent breweries, and our cocktails are made to order with premium spirits, fresh citrus and proper bar technique — no shortcuts, no pre-mixed bottles.</p>
</div>
</div>
<!-- Component 3: Atmosphere -->
<div class="md:col-span-4 bg-surface-container border-2 border-outline p-8 group">
<div class="border-b-2 border-outline pb-4 mb-4 flex justify-between items-end">
<h3 class="font-headline-md text-headline-md text-primary">The Soundtrack</h3>
<span class="material-symbols-outlined text-primary">music_note</span>
</div>
<p class="font-body-md text-body-md text-on-surface">A classic jukebox in the main bar, late-night weekend DJs, and an intimate basement built for live music and stand-up comedy — also available for private hire.</p>
</div>
<!-- Component 4: Toppings & Bake -->
<div class="md:col-span-8 bg-surface border-2 border-outline p-8 flex flex-col md:flex-row-reverse gap-8 items-center group">
<div class="w-full md:w-1/2 overflow-hidden border border-outline">
{{-- Fresh, premium toppings on a hand-stretched base --}}
<img class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500" src="{{ asset('storage/menu-items/saporita.jpg') }}" alt="Premium pizza toppings"/>
</div>
<div class="w-full md:w-1/2 text-right md:text-left">
<h3 class="font-headline-md text-headline-md text-primary mb-2">Premium Toppings &amp; The Perfect Bake</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Rich tomato sauce and creamy mozzarella, topped with fresh ingredients that honour authentic Italian tradition. Baked for a slight, smoky char on the edge and a sturdy bite in the centre.</p>
</div>
</div>
</div>
</div>
</section>

<!-- Our Location Section -->
<section class="bg-surface-container-high py-20">
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
<div class="bg-surface border-2 border-primary overflow-hidden grid grid-cols-1 lg:grid-cols-2">
<div class="p-12 md:p-16 flex flex-col justify-center">
<h2 class="font-display text-headline-lg text-primary uppercase mb-4">The Fortess Road Shop</h2>
<div class="space-y-6">
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary mt-1">location_on</span>
<div>
<p class="font-label-bold text-label-bold">Address</p>
<p class="font-body-lg text-body-lg">{{ \App\Models\Setting::get('store_address', '156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP') }}</p>
</div>
</div>
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary mt-1">schedule</span>
<div>
<p class="font-label-bold text-label-bold">Opening Hours</p>
<p class="font-body-lg text-body-lg">
    Sun–Thu: {{ \App\Models\Setting::get('opening_sun_thu', '16:00 – 22:45') }}<br/>
    Fri–Sat: {{ \App\Models\Setting::get('opening_fri_sat', '16:00 – 23:15') }}
</p>
</div>
</div>
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary mt-1">call</span>
<div>
<p class="font-label-bold text-label-bold">Phone</p>
<p class="font-body-lg text-body-lg"><a href="tel:{{ preg_replace('/\s+/', '', \App\Models\Setting::get('store_phone', '+44 020 7485 4033')) }}" class="hover:text-primary transition-colors">{{ \App\Models\Setting::get('store_phone', '+44 020 7485 4033') }}</a></p>
</div>
</div>
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary mt-1">mail</span>
<div>
<p class="font-label-bold text-label-bold">Email</p>
<p class="font-body-lg text-body-lg"><a href="mailto:{{ \App\Models\Setting::get('store_email', 'nw5pizza@gmail.com') }}" class="hover:text-primary transition-colors">{{ \App\Models\Setting::get('store_email', 'nw5pizza@gmail.com') }}</a></p>
</div>
</div>
<a href="https://maps.google.com/?q=156+Fortess+Road+London+NW5+2HP" target="_blank" rel="noopener" class="inline-block w-full md:w-auto bg-primary text-on-primary font-label-bold text-label-bold px-10 py-4 uppercase tracking-widest hover:opacity-90 transition-opacity text-center">
    Get Directions
</a>
</div>
</div>
<div class="h-[400px] lg:h-auto min-h-[500px] border-l-0 lg:border-l-2 border-primary relative grayscale">
<div class="absolute inset-0 bg-primary/10 mix-blend-multiply"></div>
<iframe
  src="https://maps.google.com/maps?q=156+Fortess+Road+London+NW5+2HP&output=embed"
  class="w-full h-full" style="border:0;" allowfullscreen loading="lazy"
  referrerpolicy="no-referrer-when-downgrade"
  title="Aces &amp; Eights Pizza location map"></iframe>
<div class="absolute inset-0 flex items-center justify-center">
<div class="bg-primary text-on-primary p-4 rounded-full shadow-xl">
<span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">location_on</span>
</div>
</div>
</div>
</div>
</div>
</section>

@endsection
