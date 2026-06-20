@extends('layouts.app')

@push('head')
<style>
  /* Stitch page-specific styles */
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="relative w-full min-h-[600px] lg:min-h-[700px] flex items-center justify-start overflow-hidden border-b-4 border-on-surface" id="story">
  
  {{-- Full-width Parallax Background --}}
  <div class="absolute inset-0 w-full h-full bg-surface-container-highest">
    <img alt="Stone-base pizza" class="w-full h-full object-cover object-center transform scale-105" src="{{ asset('images/site/home-hero-pizza.jpg') }}"/>
    {{-- Dynamic gradients for depth and text readability --}}
    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
  </div>

  {{-- Content Container --}}
  <div class="relative z-10 w-full max-w-container mx-auto px-6 lg:px-12 py-16">
    
    {{-- Floating 3D Glassmorphism Card --}}
    <div class="w-full max-w-xl bg-surface/80 backdrop-blur-xl border border-white/10 p-8 lg:p-12 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.6)] transform transition-transform duration-700 hover:-translate-y-2 hover:shadow-[0_30px_60px_rgba(0,0,0,0.8)]">
      
      {{-- Badge --}}
      <div class="inline-block px-4 py-1.5 mb-6 rounded-full bg-primary/20 border border-primary/30 backdrop-blur-sm shadow-inner">
        <span class="font-mono text-[11px] font-bold text-primary-container uppercase tracking-widest shadow-sm">Premium Quality</span>
      </div>

      {{-- 3D Typography Effect --}}
      <h2 class="font-display text-5xl lg:text-7xl text-white mb-6 uppercase tracking-tighter leading-[0.9] drop-shadow-[0_5px_5px_rgba(0,0,0,0.5)]">
        Industrial<br/>
        <span class="text-primary-container drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">Italian</span>
      </h2>
      
      <p class="font-body-lg text-lg text-gray-300 mb-10 max-w-md leading-relaxed drop-shadow-md">
        {{ $heroText ?: 'Forged in fire, crafted with tradition. Experience pizza built with the raw power of the industrial age and the soul of classic Italian heritage.' }}
      </p>
      
      {{-- CTA Buttons --}}
      <div class="flex flex-wrap items-center gap-6">
        
        {{-- Modern 3D Button --}}
        <a class="relative group inline-block" href="{{ route('menu') }}">
          {{-- Shadow / Depth Layer --}}
          <div class="absolute inset-0 bg-black rounded-xl"></div>
          {{-- Face Layer --}}
          <div class="relative bg-primary text-white border-2 border-black px-8 py-4 rounded-xl font-label-bold text-sm uppercase tracking-widest transform -translate-y-2 transition-transform duration-150 group-hover:-translate-y-1.5 group-active:translate-y-0 flex items-center gap-3">
            Explore the Ledger
            <span class="material-symbols-outlined text-base">local_pizza</span>
          </div>
        </a>
        
        {{-- Status Indicator --}}
        @if($isOpenNow)
          <div class="relative group">
            <div class="absolute inset-0 bg-green-900 rounded-xl"></div>
            <span class="relative font-mono text-xs font-bold uppercase bg-green-700 text-white px-5 py-3.5 rounded-xl border-2 border-green-900 flex items-center gap-2 transform -translate-y-1.5 transition-transform duration-150">
              <span class="w-2.5 h-2.5 rounded-full bg-green-400 animate-pulse shadow-[0_0_8px_rgba(74,222,128,0.8)]"></span>
              Open Now
            </span>
          </div>
        @else
          <div class="relative group">
            <div class="absolute inset-0 bg-gray-900 rounded-xl"></div>
            <span class="relative font-mono text-xs font-bold uppercase bg-surface-container text-on-surface-variant px-5 py-3.5 rounded-xl border-2 border-gray-900 flex items-center transform -translate-y-1.5 transition-transform duration-150">
              Closed
            </span>
          </div>
        @endif

      </div>
    </div>
  </div>
</section>
<!-- Base of Operations (Locations/About) -->
<section class="py-margin-desktop px-margin-mobile md:px-margin-desktop border-b-4 border-double border-on-surface" id="locations">
<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter items-start">
<div class="col-span-1 md:col-span-5">
<h3 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface mb-4 border-b-2 border-on-surface pb-2">THE FOUNDRY</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-6">{{ $storyText ?: 'Our flagship location operates out of a converted 1920s steel mill. Here, we stoke the fires daily, hammering out dough and assembling ingredients with mechanical precision.' }}</p>
<ul class="space-y-4 border-t-2 border-on-surface pt-4">
<li class="flex items-center gap-3 text-on-surface">
<span class="material-symbols-outlined text-primary-container">location_on</span>
<span class="font-label-bold text-label-bold uppercase">156 &amp; 158 Fortess Road, Tufnell Park, London, NW5 2HP</span>
</li>
<li class="flex items-center gap-3 text-on-surface">
<span class="material-symbols-outlined text-primary-container">schedule</span>
<span class="font-label-bold text-label-bold uppercase">Sun–Thu: {{ $openingSunThu }}</span>
</li>
<li class="flex items-center gap-3 text-on-surface">
<span class="material-symbols-outlined text-primary-container">schedule</span>
<span class="font-label-bold text-label-bold uppercase">Fri–Sat: {{ $openingFriSat }}</span>
</li>
</ul>
</div>
<div class="col-span-1 md:col-span-7 h-80 md:h-[400px] border-2 border-on-surface relative bg-surface-container-highest p-2">
<!-- data-alt: A wide shot of a rugged, industrial restaurant interior reminiscent of an early 20th-century workshop or foundry. The space features exposed brick walls, heavy steel beams, and vintage hanging factory lights. Diners are seated at solid wood and iron tables. The image is processed in stark black and white with high contrast, aligning with an industrial minimalist aesthetic, conveying permanence and authority. -->
<img alt="Restaurant Interior" class="w-full h-full object-cover filter grayscale contrast-125" src="{{ asset('images/site/home-restaurant-interior.jpg') }}"/>
<div class="absolute bottom-6 right-6 bg-primary-container text-on-primary rounded-full w-24 h-24 flex items-center justify-center font-display text-headline-md border border-[#D4AF37] shadow-[inset_0_2px_4px_rgba(0,0,0,0.5)]">
                        No. 1
                    </div>
</div>
</div>
</section>
<!-- Signatures (Bento Grid) -->
<section class="py-margin-desktop px-margin-mobile md:px-margin-desktop bg-surface-container-low border-b-2 border-on-surface" id="menu">
<div class="text-center mb-12">
<h3 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface uppercase tracking-widest">The Ledger</h3>
<div class="h-1 w-24 bg-primary-container mx-auto mt-4"></div>
</div>
@if($featuredItems->isNotEmpty())
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
@foreach($featuredItems as $loop_item)
@php $isFirst = $loop->first; $isSecond = $loop->index === 1; @endphp
<div class="border-2 border-on-surface bg-surface flex flex-col group relative {{ $isSecond ? 'md:mt-12' : '' }}">
@if($isFirst)
<div class="absolute -top-3 -right-3 bg-secondary-container text-on-secondary-container font-label-bold text-label-sm px-3 py-1 rounded-full border border-on-surface z-10">FEATURED</div>
@endif
<div class="h-48 border-b-2 border-on-surface overflow-hidden p-1">
@if($loop_item->hasStoredImage())
<img alt="{{ $loop_item->name }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" src="{{ asset('storage/' . $loop_item->image_path) }}"/>
@else
<img alt="{{ $loop_item->name }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text={{ urlencode($loop_item->category->name ?? 'Item') }}"/>
@endif
</div>
<div class="p-6 flex-1 flex flex-col">
<h4 class="font-headline-md text-headline-md text-on-surface border-b border-outline-variant pb-2 mb-4">{{ $loop_item->name }}</h4>
<p class="font-body-md text-body-md text-on-surface-variant flex-grow">{{ $loop_item->description }}</p>
<div class="mt-6 flex justify-between items-center">
<span class="font-label-bold text-label-bold text-primary-container">£{{ number_format($loop_item->base_price, 2) }}</span>
<button class="text-on-surface hover:text-primary transition-colors"
        @click="$store.cart.openDrawer({ id: {{ json_encode($loop_item->slug) }}, name: {{ json_encode($loop_item->name) }}, category: {{ json_encode($loop_item->category->slug ?? '') }}, basePrice: {{ (float) $loop_item->base_price }}, allergens: {{ $loop_item->allergens->where('is_visible', true)->pluck('name')->values()->toJson() }} })"
        aria-label="Add {{ $loop_item->name }} to cart">
<span class="material-symbols-outlined">add_circle</span>
</button>
</div>
</div>
</div>
@endforeach
</div>
<div class="text-center mt-10">
<a href="{{ route('menu') }}" class="inline-flex items-center gap-2 px-8 py-3 border-2 border-on-surface font-label-bold text-label-bold uppercase tracking-widest hover:bg-on-surface hover:text-surface transition-colors">
View Full Menu <span class="material-symbols-outlined text-base">arrow_forward</span>
</a>
</div>
@else
<div class="text-center py-12 max-w-md mx-auto">
<p class="font-body-md text-on-surface-variant mb-6">Our kitchen is preparing something special. Check our full menu to see all available items.</p>
<a href="{{ route('menu') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-primary text-on-primary font-label-bold text-label-bold uppercase tracking-widest hover:brightness-110 transition-all">
View Full Menu <span class="material-symbols-outlined text-base">arrow_forward</span>
</a>
</div>
@endif
</section>
<!-- Map Section -->
<section class="border-b-2 border-on-surface bg-surface h-[500px] relative w-full overflow-hidden">
<iframe
  src="https://maps.google.com/maps?q=156+Fortess+Road+London+NW5+2HP&output=embed"
  class="w-full h-full grayscale hover:grayscale-0 transition-all duration-700" style="border:0;" allowfullscreen loading="lazy"
  referrerpolicy="no-referrer-when-downgrade"
  title="Aces &amp; Eights Pizza location map"></iframe>
<div class="absolute bottom-6 right-6 bg-primary text-on-primary p-4 shadow-xl flex items-center gap-3 border border-[#D4AF37] pointer-events-none">
  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">location_on</span>
  <div>
    <p class="font-label-bold text-label-bold uppercase tracking-widest text-sm">Locate Us</p>
    <p class="font-mono text-[10px] text-on-primary-container">156 &amp; 158 Fortess Road, Tufnell Park</p>
  </div>
</div>
</section>
@endsection
