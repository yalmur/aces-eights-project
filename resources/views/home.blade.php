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
<style>
  @keyframes float-ember {
    0% { transform: translate(0, 0) scale(1); opacity: 0; }
    20% { opacity: 0.8; }
    80% { opacity: 0.6; }
    100% { transform: translate(-50px, -200px) scale(0.3); opacity: 0; }
  }
  .ember {
    position: absolute;
    border-radius: 50%;
    background: #ff5722;
    box-shadow: 0 0 10px #ff5722, 0 0 20px #ff5722;
    animation: float-ember linear infinite;
  }
  @keyframes fade-slide-up {
    0% { opacity: 0; transform: translateY(30px); }
    100% { opacity: 1; transform: translateY(0); }
  }
  @keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  @keyframes pulse-ring {
    0%, 100% { box-shadow: 0 0 40px rgba(255, 87, 34, 0.1); transform: scale(1); }
    50% { box-shadow: 0 0 80px rgba(255, 87, 34, 0.3); transform: scale(1.05); }
  }
  .animate-reveal { animation: fade-slide-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
  .delay-100 { animation-delay: 100ms; }
  .delay-200 { animation-delay: 200ms; }
  .delay-300 { animation-delay: 300ms; }
  .delay-400 { animation-delay: 400ms; }
  
  .pizza-spinner {
    clip-path: circle(48% at 50% 50%);
    animation: spin-slow 45s linear infinite;
  }
  .pizza-ring {
    animation: pulse-ring 4s ease-in-out infinite;
  }
</style>

<section class="relative w-full min-h-[600px] lg:min-h-[700px] bg-[#121212] flex items-center overflow-hidden border-b-4 border-on-surface" id="story">
  
  {{-- Floating Fire Embers --}}
  <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
    <div class="ember w-2 h-2 left-1/4 top-3/4" style="animation-duration: 4s; animation-delay: 0s;"></div>
    <div class="ember w-3 h-3 left-1/2 top-[90%]" style="animation-duration: 5s; animation-delay: 1s;"></div>
    <div class="ember w-1.5 h-1.5 left-3/4 top-2/3" style="animation-duration: 3s; animation-delay: 2s;"></div>
    <div class="ember w-2.5 h-2.5 left-[60%] top-[80%]" style="animation-duration: 6s; animation-delay: 0.5s;"></div>
    <div class="ember w-4 h-4 left-1/3 top-[85%]" style="animation-duration: 4.5s; animation-delay: 1.5s;"></div>
    <div class="ember w-1 h-1 left-[80%] top-[95%]" style="animation-duration: 3.5s; animation-delay: 0.8s;"></div>
    <div class="ember w-2 h-2 left-[15%] top-[70%]" style="animation-duration: 5.5s; animation-delay: 2.5s;"></div>
  </div>

  {{-- Heat/Glow Gradient Background --}}
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_50%,rgba(139,0,0,0.15),transparent_60%)] z-0"></div>

  <div class="relative z-10 w-full max-w-container mx-auto px-6 lg:px-12 py-16 flex flex-col md:flex-row items-center gap-12">
    
    {{-- Left: Staggered Animated Typography --}}
    <div class="flex-1 w-full max-w-xl text-left">
      <div class="inline-block px-4 py-1 mb-6 rounded-full border border-primary/50 bg-primary/10 animate-reveal">
        <span class="font-mono text-xs font-bold text-primary uppercase tracking-widest">Premium Quality</span>
      </div>

      <h2 class="font-display text-5xl lg:text-7xl text-white mb-6 uppercase tracking-tighter leading-[0.9] drop-shadow-lg">
        <div class="animate-reveal delay-100">Industrial</div>
        <div class="text-primary-container animate-reveal delay-200">Italian</div>
      </h2>
      
      <p class="font-body-lg text-lg text-gray-300 mb-10 max-w-md leading-relaxed animate-reveal delay-300">
        {{ $heroText ?: 'Forged in fire, crafted with tradition. Experience pizza built with the raw power of the industrial age and the soul of classic Italian heritage.' }}
      </p>
      
      <div class="flex flex-wrap items-center gap-6 animate-reveal delay-400">
        <a class="relative group inline-block" href="{{ route('menu') }}">
          <div class="absolute inset-0 bg-primary-container translate-y-1.5 rounded-lg transition-transform duration-200 group-hover:translate-y-2"></div>
          <div class="relative bg-primary text-white border border-primary-container px-8 py-4 rounded-lg font-label-bold text-sm uppercase tracking-wider transition-transform duration-200 group-hover:translate-y-0.5 active:translate-y-1.5 flex items-center gap-2">
            Explore the Ledger
            <span class="material-symbols-outlined text-sm transition-transform duration-300 group-hover:translate-x-1.5">arrow_forward</span>
          </div>
        </a>
        
        @if($isOpenNow)
          <span class="font-mono text-xs font-bold uppercase text-white px-4 py-3 flex items-center gap-2 bg-transparent border border-green-500 rounded-lg shadow-[0_0_15px_rgba(74,222,128,0.15)]">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-[ping_1.5s_cubic-bezier(0,0,0.2,1)_infinite]"></span>
            Open Now
          </span>
        @else
          <span class="font-mono text-xs font-bold uppercase text-on-surface-variant px-4 py-3 border border-outline-variant rounded-lg bg-surface/50">Closed</span>
        @endif
      </div>
    </div>

    {{-- Right: Visual Effect Spinning Pizza --}}
    <div class="flex-1 w-full relative flex items-center justify-center animate-reveal delay-300 hidden md:flex min-h-[400px]">
      {{-- Glowing pulsating ring behind the pizza --}}
      <div class="absolute w-[380px] lg:w-[450px] h-[380px] lg:h-[450px] rounded-full border border-[#ff5722]/30 pizza-ring"></div>
      
      {{-- The Spinning Pizza Image (masked into a circle) --}}
      <div class="w-[360px] lg:w-[420px] h-[360px] lg:h-[420px] relative z-10 pizza-spinner shadow-2xl">
        <img alt="Stone-base pizza" class="w-full h-full object-cover object-center scale-110" src="{{ asset('images/site/home-hero-pizza.jpg') }}"/>
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
