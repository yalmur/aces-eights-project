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

{{-- Hero --}}
<section class="relative bg-primary text-on-primary overflow-hidden">
  <div class="absolute inset-0 opacity-10">
    <div class="w-full h-full" style="background-image:repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%);background-size:24px 24px;"></div>
  </div>
  <div class="relative max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-20 md:py-32 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
    <div class="space-y-6">
      <span class="font-label-bold text-label-bold opacity-70 uppercase tracking-widest block">Private Events</span>
      <h1 class="font-display text-display md:text-[64px] leading-[1.1]">The Workshop<br>Party Hall</h1>
      <p class="font-body-lg text-body-lg opacity-80 max-w-lg">
        Capacity for 50–200 guests. Industrial-chic setting, bespoke pizza menus, and full bar service. From birthday celebrations to corporate events — we fire up the oven for you.
      </p>
      <div class="flex flex-wrap gap-6 pt-2">
        <div class="flex items-center gap-2 font-label-bold text-label-bold opacity-90">
          <span class="material-symbols-outlined">group</span> Up to 200 guests
        </div>
        <div class="flex items-center gap-2 font-label-bold text-label-bold opacity-90">
          <span class="material-symbols-outlined">local_pizza</span> Bespoke menus
        </div>
        <div class="flex items-center gap-2 font-label-bold text-label-bold opacity-90">
          <span class="material-symbols-outlined">local_bar</span> Full bar
        </div>
      </div>
    </div>
    <div class="hidden md:block">
      <div class="border-2 border-white/20 p-2">
        <img src="https://placehold.co/600x440/690008/fcf9f8?text=Party+Hall" alt="Party Hall" class="w-full h-[440px] object-cover opacity-80"/>
      </div>
    </div>
  </div>
</section>

{{-- Divider --}}
<div class="w-full h-2 border-t border-b border-outline my-0"></div>

{{-- What's Included --}}
<section class="bg-surface py-16">
  <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
    <h2 class="font-display text-display text-primary uppercase text-center mb-12">What's Included</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
      <div class="bg-surface-container-low industrial-border p-8 text-center space-y-4">
        <span class="material-symbols-outlined text-4xl text-primary" style="font-variation-settings:'FILL' 1">restaurant</span>
        <h3 class="font-headline-md text-headline-md text-primary">Dedicated Menu</h3>
        <p class="font-body-md text-body-md text-on-surface-variant">Work with our team to design a bespoke sharing menu — from stone-base pizzas and sides to desserts and canapés.</p>
      </div>
      <div class="bg-surface-container-low industrial-border p-8 text-center space-y-4">
        <span class="material-symbols-outlined text-4xl text-primary" style="font-variation-settings:'FILL' 1">celebration</span>
        <h3 class="font-headline-md text-headline-md text-primary">Full Venue Hire</h3>
        <p class="font-body-md text-body-md text-on-surface-variant">Private use of our industrial-chic hall. AV setup, decorative lighting, and flexible table layouts all available.</p>
      </div>
      <div class="bg-surface-container-low industrial-border p-8 text-center space-y-4">
        <span class="material-symbols-outlined text-4xl text-primary" style="font-variation-settings:'FILL' 1">local_bar</span>
        <h3 class="font-headline-md text-headline-md text-primary">Bar Service</h3>
        <p class="font-body-md text-body-md text-on-surface-variant">Pre-paid drinks packages or tab bar. Craft beers, wines, cocktails, and non-alcoholic options for every guest.</p>
      </div>
    </div>
  </div>
</section>

{{-- Inquiry Form + Info --}}
<section class="bg-surface-container-lowest border-t-2 border-outline py-16">
  <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">

      {{-- Form --}}
      <div class="lg:col-span-7 bg-surface industrial-border-thin p-8 md:p-12">
        <h2 class="font-headline-md text-headline-md mb-2 flex items-center gap-3">
          <span class="material-symbols-outlined text-primary">mail</span>
          Make an Inquiry
        </h2>
        <p class="font-body-md text-body-md text-on-surface-variant mb-8">Tell us about your event. We'll get back to you within 24 hours with availability and a quote.</p>

        <form action="{{ route('party-hall.submit') }}" method="POST" class="space-y-6">
          @csrf
          @if($errors->any())
            <div class="bg-error-container border border-error px-4 py-3">
              <ul class="text-xs font-mono space-y-1">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col">
              <label class="font-label-bold text-label-bold text-primary uppercase mb-1" for="name">Full Name *</label>
              <input class="ledger-input p-2 font-body-md" id="name" name="name" value="{{ old('name') }}" placeholder="Your name" required type="text"/>
            </div>
            <div class="flex flex-col">
              <label class="font-label-bold text-label-bold text-primary uppercase mb-1" for="email">Email Address *</label>
              <input class="ledger-input p-2 font-body-md" id="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required type="email"/>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col">
              <label class="font-label-bold text-label-bold text-primary uppercase mb-1" for="phone">Phone Number *</label>
              <input class="ledger-input p-2 font-body-md" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+44 7700 900000" required type="tel"/>
            </div>
            <div class="flex flex-col">
              <label class="font-label-bold text-label-bold text-primary uppercase mb-1" for="event_date">Event Date *</label>
              <input class="ledger-input p-2 font-body-md" id="event_date" name="event_date" value="{{ old('event_date') }}" required type="date" min="{{ date('Y-m-d', strtotime('+1 day')) }}"/>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col">
              <label class="font-label-bold text-label-bold text-primary uppercase mb-1" for="guests">Number of Guests *</label>
              <input class="ledger-input p-2 font-body-md" id="guests" name="guests" value="{{ old('guests') }}" placeholder="e.g. 80" min="50" max="500" required type="number"/>
              <span class="text-xs text-on-surface-variant mt-1">Minimum 50 guests</span>
            </div>
            <div class="flex flex-col">
              <label class="font-label-bold text-label-bold text-primary uppercase mb-1" for="event_type">Event Type *</label>
              <select class="ledger-input p-2 font-body-md bg-transparent" id="event_type" name="event_type">
                <option value="" disabled {{ old('event_type') ? '' : 'selected' }}>Select type...</option>
                <option {{ old('event_type') === 'Birthday Party'   ? 'selected' : '' }}>Birthday Party</option>
                <option {{ old('event_type') === 'Corporate Event'  ? 'selected' : '' }}>Corporate Event</option>
                <option {{ old('event_type') === 'Wedding Reception' ? 'selected' : '' }}>Wedding Reception</option>
                <option {{ old('event_type') === 'Anniversary'      ? 'selected' : '' }}>Anniversary</option>
                <option {{ old('event_type') === 'Charity Event'    ? 'selected' : '' }}>Charity Event</option>
                <option {{ old('event_type') === 'Other'            ? 'selected' : '' }}>Other</option>
              </select>
            </div>
          </div>

          <div class="flex flex-col">
            <label class="font-label-bold text-label-bold text-primary uppercase mb-1" for="message">Additional Requirements</label>
            <textarea class="ledger-input p-2 font-body-md" id="message" name="message" placeholder="Dietary requirements, AV needs, decoration requests, timing details..." rows="4">{{ old('message') }}</textarea>
          </div>

          <button class="gold-metallic-button w-full md:w-auto px-12 py-4 font-label-bold text-label-bold text-white uppercase tracking-widest" type="submit">
            Send Inquiry
          </button>
        </form>
      </div>

      {{-- Sidebar --}}
      <aside class="lg:col-span-5 space-y-gutter">
        <div class="bg-primary text-white p-8 industrial-border">
          <h3 class="font-headline-md text-headline-md mb-6 border-b border-white/20 pb-4">Venue Details</h3>
          <ul class="space-y-5">
            <li class="flex items-start gap-4">
              <span class="material-symbols-outlined mt-1">location_on</span>
              <div>
                <p class="font-label-bold text-label-bold uppercase opacity-70">Address</p>
                <p class="font-body-lg text-body-lg">{{ \App\Models\Setting::get('store_address', '156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP') }}</p>
              </div>
            </li>
            <li class="flex items-start gap-4">
              <span class="material-symbols-outlined mt-1">group</span>
              <div>
                <p class="font-label-bold text-label-bold uppercase opacity-70">Capacity</p>
                <p class="font-body-lg text-body-lg">50–200 guests (seated or standing)</p>
              </div>
            </li>
            <li class="flex items-start gap-4">
              <span class="material-symbols-outlined mt-1">schedule</span>
              <div>
                <p class="font-label-bold text-label-bold uppercase opacity-70">Hire Hours</p>
                <p class="font-body-lg text-body-lg">Evening hire from 18:00. Daytime available on request.</p>
              </div>
            </li>
            <li class="flex items-start gap-4">
              <span class="material-symbols-outlined mt-1">call</span>
              <div>
                <p class="font-label-bold text-label-bold uppercase opacity-70">Enquiries</p>
                <p class="font-body-lg text-body-lg">
                  <a href="tel:{{ preg_replace('/\s+/', '', \App\Models\Setting::get('store_phone', '+44 020 7485 4033')) }}" class="hover:opacity-80 transition-opacity">
                    {{ \App\Models\Setting::get('store_phone', '+44 020 7485 4033') }}
                  </a>
                </p>
              </div>
            </li>
          </ul>
        </div>

        <div class="bg-surface-container industrial-border-thin p-8">
          <h3 class="font-headline-md text-headline-md mb-4 text-primary flex items-center gap-3">
            <span class="material-symbols-outlined">info</span> Good to Know
          </h3>
          <ul class="space-y-3 font-body-md text-body-md text-on-surface-variant">
            <li class="flex items-start gap-2"><span class="material-symbols-outlined text-sm text-primary mt-0.5">check</span> Fully licensed bar until late</li>
            <li class="flex items-start gap-2"><span class="material-symbols-outlined text-sm text-primary mt-0.5">check</span> Vegan &amp; halal options available</li>
            <li class="flex items-start gap-2"><span class="material-symbols-outlined text-sm text-primary mt-0.5">check</span> Ground floor — wheelchair accessible</li>
            <li class="flex items-start gap-2"><span class="material-symbols-outlined text-sm text-primary mt-0.5">check</span> Dedicated event coordinator</li>
            <li class="flex items-start gap-2"><span class="material-symbols-outlined text-sm text-primary mt-0.5">check</span> PA system &amp; projector available</li>
            <li class="flex items-start gap-2"><span class="material-symbols-outlined text-sm text-primary mt-0.5">check</span> Bespoke cake service on request</li>
          </ul>
        </div>
      </aside>

    </div>
  </div>
</section>

@endsection
