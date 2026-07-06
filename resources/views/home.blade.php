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
<!-- Hero Section -->
<style>
  @import url('https://fonts.googleapis.com/css2?family=Anton&family=Great+Vibes&family=Oswald:wght@500;700&display=swap');
  
  .font-script { font-family: 'Great Vibes', cursive; }
  .font-oswald { font-family: 'Oswald', sans-serif; }
  .font-anton { font-family: 'Anton', sans-serif; }

  /* Fade and slide up animations */
  @keyframes fade-slide-up {
    0% { opacity: 0; transform: translateY(30px); }
    100% { opacity: 1; transform: translateY(0); }
  }
  .animate-entrance {
    opacity: 0;
    animation: fade-slide-up 1.2s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
  }
  
  /* Subtle text glow animation for script */
  @keyframes pulse-glow {
    0%, 100% { text-shadow: 0 4px 15px rgba(0,0,0,0.9), 0 0 10px rgba(212,175,55,0.4); }
    50% { text-shadow: 0 4px 15px rgba(0,0,0,0.9), 0 0 25px rgba(212,175,55,0.8); }
  }
  .animate-pulse-glow { animation: pulse-glow 4s ease-in-out infinite; }

  /* Magma & Fire Text Effect (Matches the image exactly) */
  @keyframes magma-flow {
    0% { background-position: 0% 50%; filter: drop-shadow(0 0 15px rgba(255, 60, 0, 0.6)); }
    50% { background-position: 100% 50%; filter: drop-shadow(0 0 35px rgba(255, 120, 0, 0.9)); }
    100% { background-position: 0% 50%; filter: drop-shadow(0 0 15px rgba(255, 60, 0, 0.6)); }
  }
  .text-magma-fire {
    font-family: 'Impact', 'Anton', sans-serif;
    /* Create a cracked rock look with glowing lava underneath */
    background: 
      repeating-linear-gradient(45deg, transparent, transparent 12px, rgba(20,20,20,0.9) 12px, rgba(20,20,20,0.9) 15px),
      repeating-linear-gradient(-45deg, transparent, transparent 18px, rgba(20,20,20,0.9) 18px, rgba(20,20,20,0.9) 20px),
      linear-gradient(90deg, #111 0%, #330c00 20%, #ff4400 40%, #ffcc00 50%, #ff4400 60%, #330c00 80%, #111 100%);
    background-size: 300% 300%;
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    
    /* The bright golden/orange edge glow from the image */
    -webkit-text-stroke: 2px rgba(255, 180, 0, 0.9);
    
    animation: magma-flow 5s ease-in-out infinite;
    letter-spacing: -0.02em;
  }

  /* Metallic button gradients */
  .btn-metallic-red {
    background: linear-gradient(to bottom, #ff4b4b 0%, #aa0000 100%);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.4), 0 5px 15px rgba(0,0,0,0.5);
    border: 1px solid #7a0000;
  }
  .btn-metallic-red:hover { background: linear-gradient(to bottom, #ff6b6b 0%, #cc0000 100%); }
  
  .btn-metallic-gold {
    background: linear-gradient(to bottom, #f3d47f 0%, #a67c00 100%);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.6), 0 5px 15px rgba(0,0,0,0.5);
    border: 1px solid #6b5000;
  }
  .btn-metallic-gold:hover { background: linear-gradient(to bottom, #ffe89f 0%, #c49600 100%); }
</style>

<section class="relative w-full min-h-screen flex items-center justify-center border-b-4 border-on-surface pt-24 pb-16" id="story">
  
  {{-- Video Background --}}
  <div class="absolute inset-0 w-full h-full bg-black z-0">
    <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover saturate-[1.5] contrast-[1.1] brightness-[1.1]" poster="{{ asset('images/site/home-hero-pizza.jpg') }}">
      <source src="{{ asset('videos/hero-pizza.mp4') }}" type="video/mp4">
    </video>
    {{-- Dark vignette to make the golden text pop --}}
    <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-transparent to-black/80"></div>
    <div class="absolute inset-0 bg-black/40"></div>
  </div>

  {{-- Content Container --}}
  <div class="relative z-10 w-full max-w-7xl mx-auto px-4 flex flex-col items-center text-center">
    
    {{-- Massive Boxed Headline (Matches Badge Style) --}}
    <div class="border-[3px] border-[#d4af37]/80 px-8 sm:px-12 py-6 sm:py-8 mb-8 shadow-[0_5px_15px_rgba(0,0,0,0.5)] bg-black/50 backdrop-blur-sm w-full max-w-5xl mx-auto">
      <h2 class="font-oswald text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-[#f2e3c6] tracking-[0.15em] leading-tight drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)] uppercase">
        BORN IN FIRE. <br class="hidden sm:block"/> BUILT FOR FLAVOR.
      </h2>
    </div>

    {{-- Top Framed Badge --}}
    <div class="border-[3px] border-[#d4af37]/80 px-8 py-3 mb-8 shadow-[0_5px_15px_rgba(0,0,0,0.5)] bg-black/50 backdrop-blur-sm">
      <h1 class="font-oswald text-xl md:text-2xl lg:text-3xl font-bold text-[#f2e3c6] tracking-[0.15em] drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)]">
        ACES & EIGHTS PIZZA — EST. 2012
      </h1>
    </div>

    {{-- Elegant Cursive Sub-Headline --}}
    <h3 class="font-script text-4xl sm:text-5xl md:text-6xl lg:text-[80px] leading-[1.1] text-transparent bg-clip-text bg-gradient-to-b from-[#ffe082] to-[#cba052] drop-shadow-[0_4px_10px_rgba(0,0,0,0.9)] animate-pulse-glow mb-12 px-4" style="-webkit-text-stroke: 1px rgba(0,0,0,0.5);">
      Forged in the Fire of Tradition,<br/>
      Crafted with Industrial Flavor
    </h3>
    
    {{-- CTA Buttons --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 w-full px-4">
      
      {{-- Red Metallic CTA --}}
      <a href="{{ route('menu') }}" class="btn-metallic-red text-white px-10 py-4 rounded-md font-oswald text-xl lg:text-2xl tracking-wider transition-all duration-300 transform hover:scale-105 active:scale-95 flex items-center gap-3">
        <span class="text-2xl drop-shadow-md">🔥</span> <span class="font-bold drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">ORDER NOW</span>
      </a>
      
      {{-- Gold Metallic CTA --}}
      <a href="{{ route('our-menu') }}" class="btn-metallic-gold text-black px-10 py-4 rounded-md font-oswald text-xl lg:text-2xl tracking-wider transition-all duration-300 transform hover:scale-105 active:scale-95 flex items-center gap-3">
        <span class="font-bold drop-shadow-[0_1px_2px_rgba(255,255,255,0.6)]">VIEW OUR MENU</span>
      </a>
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
        @click="$store.cart.openDrawer({ id: {{ json_encode($loop_item->slug) }}, name: {{ json_encode($loop_item->name) }}, category: {{ json_encode($loop_item->category->slug ?? '') }}, basePrice: {{ (float) $loop_item->base_price }}, allergens: {{ $loop_item->allergens->where('is_visible', true)->pluck('name')->values()->toJson() }}, image: {{ $loop_item->hasStoredImage() ? json_encode(asset('storage/' . $loop_item->image_path)) : json_encode('https://placehold.co/400x300/e4e2e1/1b1c1c?text=' . urlencode($loop_item->category->name ?? 'Item')) }}, ingredients: {{ $loop_item->baseIngredients->pluck('name')->values()->toJson() }}, relatedItems: {{ json_encode($loop_item->relatedItemsPayload()) }}, isCustomizable: {{ json_encode($loop_item->is_customizable) }} })"
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
