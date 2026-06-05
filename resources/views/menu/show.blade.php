@extends('layouts.app')
@section('content')

@php
  $allergens    = $item->allergens->where('is_visible', true);
  $ingredients  = $item->baseIngredients;
  $imageUrl     = $item->image_path
      ? asset('storage/' . $item->image_path)
      : 'https://placehold.co/800x450/e4e2e1/1b1c1c?text=' . urlencode($item->name);
  $allergenList = $allergens->pluck('name')->values()->toJson();
@endphp

<div class="max-w-container mx-auto px-4 lg:px-16 py-12">

  {{-- Breadcrumb --}}
  <nav class="font-mono text-[10px] uppercase text-on-surface-variant mb-8 flex items-center gap-2">
    <a href="{{ route('menu') }}" class="hover:text-primary transition-colors">Menu</a>
    <span>/</span>
    <a href="{{ route('menu') }}#{{ $item->category->slug }}" class="hover:text-primary transition-colors">{{ $item->category->name }}</a>
    <span>/</span>
    <span class="text-on-surface">{{ $item->name }}</span>
  </nav>

  <div class="lg:grid lg:grid-cols-12 lg:gap-12 items-start">

    {{-- Left: Item detail --}}
    <div class="lg:col-span-8 space-y-10">

      {{-- Hero --}}
      <section>
        <div class="aspect-video overflow-hidden border-4 border-on-surface shadow-[4px_4px_0px_0px_rgba(27,28,28,1)] mb-6">
          <img src="{{ $imageUrl }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
        </div>
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <h1 class="font-serif text-4xl font-black text-on-surface uppercase tracking-tight leading-none">{{ $item->name }}</h1>
            <p class="font-mono text-xs text-primary uppercase tracking-widest mt-1">{{ $item->category->name }}</p>
          </div>
          <div class="flex flex-wrap gap-2">
            @if($item->is_vegetarian)
              <span class="font-mono text-[10px] font-bold uppercase bg-green-100 border border-green-400 text-green-800 px-3 py-1">Vegetarian</span>
            @endif
            @if($item->is_vegan)
              <span class="font-mono text-[10px] font-bold uppercase bg-green-100 border border-green-600 text-green-900 px-3 py-1">Vegan</span>
            @endif
          </div>
        </div>
        @if($item->description)
          <p class="font-sans text-base text-on-surface-variant mt-4 italic leading-relaxed">{{ $item->description }}</p>
        @endif
      </section>

      {{-- Allergy info --}}
      @if($allergens->count())
      <section class="bg-amber-50 border border-amber-300 p-5 flex gap-4 items-start">
        <span class="material-symbols-outlined text-amber-700 mt-0.5" style="font-variation-settings:'FILL' 1">warning</span>
        <div>
          <p class="font-mono text-[10px] font-bold uppercase text-amber-800 mb-2">Allergen Information</p>
          <div class="flex flex-wrap gap-2">
            @foreach($allergens as $allergen)
              <span class="font-mono text-[10px] font-bold uppercase bg-amber-100 border border-amber-300 text-amber-900 px-2 py-0.5">{{ $allergen->name }}</span>
            @endforeach
          </div>
        </div>
      </section>
      @else
      <section class="bg-surface-container border border-outline-variant p-5 flex gap-4 items-start">
        <span class="material-symbols-outlined text-on-surface-variant mt-0.5">info</span>
        <p class="font-sans text-sm text-on-surface-variant">No major allergen information recorded for this item. Ask a member of staff for full details.</p>
      </section>
      @endif

      {{-- Ingredients --}}
      @if($ingredients->count())
      <section>
        <p class="font-mono text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-4">What's Inside</p>
        <div class="flex flex-wrap gap-2">
          @foreach($ingredients as $ingredient)
            <span class="font-mono text-xs border border-outline-variant bg-surface-container-low px-3 py-1 text-on-surface-variant">{{ $ingredient->name }}</span>
          @endforeach
        </div>
      </section>
      @endif

    </div>

    {{-- Right: Add to cart sidebar --}}
    <aside class="lg:col-span-4 mt-10 lg:mt-0 lg:sticky lg:top-28 space-y-6">

      {{-- Price + add to cart --}}
      <div class="border-2 border-on-surface p-6 bg-surface shadow-[4px_4px_0px_0px_rgba(27,28,28,1)]">
        <p class="font-mono text-[10px] uppercase text-on-surface-variant tracking-widest mb-1">Starting from</p>
        <p class="font-serif text-4xl font-black text-primary mb-6">{{ $item->formatted_price }}</p>

        <button
          @click="$store.cart.openDrawer({
            id: {{ json_encode($item->slug) }},
            name: {{ json_encode($item->name) }},
            category: {{ json_encode($item->category->slug) }},
            basePrice: {{ (float) $item->base_price }},
            allergens: {{ $allergenList }}
          })"
          class="w-full bg-primary text-on-primary font-mono text-sm font-bold uppercase py-4 px-6 border-b-4 border-[#1b1c1c] hover:bg-primary/90 active:scale-[.99] transition-all flex items-center justify-center gap-3">
          <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
          Add to Order
        </button>

        <a href="{{ route('menu') }}" class="mt-3 block text-center font-mono text-[10px] uppercase text-on-surface-variant hover:text-primary transition-colors">← Back to Menu</a>
      </div>

      {{-- Related items --}}
      @if($related->count())
      <div class="border-2 border-on-surface p-6 bg-surface">
        <p class="font-mono text-[10px] font-bold uppercase tracking-widest text-primary mb-5">More from {{ $item->category->name }}</p>
        <div class="space-y-4">
          @foreach($related as $rel)
          @php $relAllergens = $rel->allergens->where('is_visible', true)->pluck('name')->values()->toJson(); @endphp
          <div class="flex gap-3 items-center border border-outline-variant p-3 hover:border-on-surface transition-colors">
            <div class="w-16 h-16 flex-shrink-0 overflow-hidden bg-surface-container">
              <img src="{{ $rel->image_path ? asset('storage/'.$rel->image_path) : 'https://placehold.co/64x64/e4e2e1/1b1c1c?text=+' }}"
                   alt="{{ $rel->name }}" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 min-w-0">
              <a href="{{ route('menu.show', $rel->slug) }}" class="font-mono text-xs font-bold text-on-surface hover:text-primary transition-colors block truncate">{{ $rel->name }}</a>
              <p class="font-mono text-xs text-primary font-bold mt-0.5">{{ $rel->formatted_price }}</p>
            </div>
            <button
              @click="$store.cart.openDrawer({
                id: {{ json_encode($rel->slug) }},
                name: {{ json_encode($rel->name) }},
                category: {{ json_encode($rel->category->slug ?? $item->category->slug) }},
                basePrice: {{ (float) $rel->base_price }},
                allergens: {{ $relAllergens }}
              })"
              class="flex-shrink-0 w-9 h-9 flex items-center justify-center bg-surface-container border border-outline hover:bg-primary hover:text-on-primary hover:border-primary transition-colors">
              <span class="material-symbols-outlined text-[18px]">add</span>
            </button>
          </div>
          @endforeach
        </div>
      </div>
      @endif

    </aside>
  </div>

</div>

@endsection
