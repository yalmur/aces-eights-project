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
<section class="relative w-full h-[600px] border-b-2 border-on-surface bg-surface-container-low flex flex-col md:flex-row items-center" id="story">
<div class="flex-1 px-gutter py-margin-desktop z-10 flex flex-col justify-center h-full">
<h2 class="font-display text-display text-on-surface mb-6 uppercase tracking-tighter">Industrial<br/>Italian</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mb-8 max-w-md border-l-4 border-primary pl-4">{{ $heroText ?: 'Forged in fire, crafted with tradition. Experience pizza built with the raw power of the industrial age and the soul of classic Italian heritage.' }}</p>
<div class="flex flex-wrap items-center gap-4">
<a class="bg-primary-container text-on-primary border-b border-[#D4AF37] px-8 py-4 font-label-bold text-label-bold uppercase tracking-wider hover:bg-primary transition-colors w-fit shadow-[inset_0_0_0_1px_rgba(255,255,255,0.2)] inline-block" href="{{ route('menu') }}">
  Explore the Ledger
</a>
@if($isOpenNow)
  <span class="font-mono text-xs font-bold uppercase bg-green-700 text-white px-3 py-1 flex items-center gap-1.5">
    <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse inline-block"></span>Open Now
  </span>
@else
  <span class="font-mono text-xs font-bold uppercase bg-surface-container border border-outline-variant text-on-surface-variant px-3 py-1">Closed</span>
@endif
</div>
</div>
<div class="flex-1 w-full h-full relative border-l-2 border-on-surface hidden md:block">
<!-- data-alt: A close-up, high-quality photograph of an artisan stone-base pizza resting on a rustic, industrial metal prep table. The pizza features a perfectly charred, blistered crust, rich oxblood red tomato sauce, and melted mozzarella. The lighting is dramatic and moody, emphasizing the textures of the charred crust and the heavy industrial setting. The overall style reflects an industrial minimal aesthetic with raw materials and high contrast. -->
<img alt="Stone-base pizza" class="w-full h-full object-cover object-center" src="{{ asset('images/site/home-hero-pizza.jpg') }}"/>
<!-- Overlay for structural depth -->
<div class="absolute inset-0 border-8 border-surface pointer-events-none mix-blend-overlay opacity-50"></div>
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
