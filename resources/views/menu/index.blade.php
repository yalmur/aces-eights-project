@extends('layouts.app')
@push('head')
<style>
.menu-masthead { border-bottom: 4px double #690008; padding-bottom: 2rem; margin-bottom: 3rem; }
.menu-masthead-rule { width:100%; height:1px; background:linear-gradient(90deg,transparent,#690008 20%,#690008 80%,transparent); margin:0.75rem 0; }
</style>
@endpush
@section('content')
{{-- Masthead — same container structure as our-menu --}}
<div class="max-w-container-max mx-auto px-6 lg:px-16 pt-20">
    <div class="menu-masthead text-center">
        <p class="font-mono text-[0.65rem] tracking-[0.3em] uppercase text-on-surface-variant mb-4">
            156 &amp; 158 Fortess Road &middot; Tufnell Park &middot; London, NW5 2HP
        </p>
        <div class="menu-masthead-rule"></div>
        <h1 class="font-serif font-black uppercase tracking-[0.18em] text-4xl lg:text-5xl text-on-surface my-4">
            Aces &amp; Eights Pizza
        </h1>
        <div class="menu-masthead-rule"></div>
        <p class="font-mono text-[0.65rem] tracking-[0.3em] uppercase text-on-surface-variant mt-4">
            Est. 2010 &nbsp;&middot;&nbsp; Authentic Italian Takeaway
        </p>
    </div>
</div>

{{-- Alpine.js category filter + menu grid --}}
<div x-data="{ active: 'all', view: 'grid', search: '', diet: '', showFilters: false, excludedAllergens: [] }">

    {{-- Search & Filter Bar --}}
    <section class="sticky top-16 z-40 bg-surface/95 backdrop-blur-md px-6 md:px-margin-desktop py-6 border-b border-surface-variant">
        <div class="max-w-container-max mx-auto flex flex-col md:flex-row gap-6 items-center">
            {{-- Category chips --}}
            <div class="flex flex-wrap gap-2 w-full md:w-auto flex-1 pb-1">
                <button @click="active = 'all'" :class="{ 'active': active === 'all' }" class="chip whitespace-nowrap">All</button>
                @foreach($categories as $category)
                <button @click="active = '{{ $category->slug }}'"
                        :class="{ 'active': active === '{{ $category->slug }}' }"
                        class="chip whitespace-nowrap">{{ $category->name }}</button>
                @endforeach
                <div class="w-px h-6 bg-surface-variant self-center mx-1 flex-shrink-0"></div>
                <button @click="diet = diet === 'vegetarian' ? '' : 'vegetarian'"
                        :class="{ 'active': diet === 'vegetarian' }"
                        class="chip whitespace-nowrap">Vegetarian</button>
                <button @click="diet = diet === 'vegan' ? '' : 'vegan'"
                        :class="{ 'active': diet === 'vegan' }"
                        class="chip whitespace-nowrap">Vegan</button>
            </div>
            {{-- Smart Search --}}
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="relative flex-1 md:w-80">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                    <input x-model="search" @keydown.escape="search = ''"
                           class="w-full bg-surface-container border border-surface-variant px-10 py-3 font-body-md text-body-md text-on-surface focus:ring-1 focus:ring-primary-container focus:border-primary-container" placeholder="Search our soul..." type="text"/>
                    <button x-show="search" x-cloak @click="search = ''"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>
                <button @click="showFilters = !showFilters" type="button"
                        :class="showFilters || excludedAllergens.length ? 'bg-primary-container' : 'bg-surface-container hover:bg-surface-container-high'"
                        class="relative p-3 border border-surface-variant transition-colors">
                    <span class="material-symbols-outlined" :class="showFilters || excludedAllergens.length ? 'text-on-primary' : 'text-on-surface'">tune</span>
                    <span x-show="excludedAllergens.length" x-cloak
                          x-text="excludedAllergens.length"
                          class="absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-primary text-white text-[10px] font-bold flex items-center justify-center"></span>
                </button>
                {{-- View toggle --}}
                <div class="flex border border-surface-variant overflow-hidden flex-shrink-0">
                    <button @click="view = 'grid'"
                            :class="view === 'grid' ? 'bg-primary-container text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'"
                            class="p-3 transition-colors" title="Grid view">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="square" d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z"/>
                        </svg>
                    </button>
                    <button @click="view = 'list'"
                            :class="view === 'list' ? 'bg-primary-container text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'"
                            class="p-3 border-l border-surface-variant transition-colors" title="List view">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="square" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <button @click="view = 'compact'"
                            :class="view === 'compact' ? 'bg-primary-container text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'"
                            class="p-3 border-l border-surface-variant transition-colors" title="Compact view">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="square" d="M3 3h5v5H3zM9 3h5v5H9zM15 3h5v5h-5zM3 9h5v5H3zM9 9h5v5H9zM15 9h5v5h-5zM3 15h5v5H3zM9 15h5v5H9zM15 15h5v5h-5z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Allergen filter panel --}}
        <div x-show="showFilters" x-cloak
             class="max-w-container-max mx-auto mt-4 p-4 bg-surface-container border border-surface-variant">
            <div class="flex items-center justify-between mb-3">
                <span class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant">Exclude items containing:</span>
                <button x-show="excludedAllergens.length" x-cloak @click="excludedAllergens = []"
                        class="font-mono text-[10px] text-primary hover:underline uppercase tracking-widest">Clear</button>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($allergens as $allergen)
                <button @click="excludedAllergens = excludedAllergens.includes({{ json_encode($allergen->name) }}) ? excludedAllergens.filter(a => a !== {{ json_encode($allergen->name) }}) : [...excludedAllergens, {{ json_encode($allergen->name) }}]"
                        :class="excludedAllergens.includes({{ json_encode($allergen->name) }}) ? 'active' : ''"
                        type="button" class="chip whitespace-nowrap">{{ $allergen->name }}</button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Search active indicator --}}
    <div x-show="search" x-cloak
         class="px-6 md:px-margin-desktop pt-6 max-w-container-max mx-auto flex items-center gap-3">
        <span class="font-mono text-xs text-on-surface-variant uppercase tracking-widest">
            Showing results for:
        </span>
        <span class="font-mono text-xs font-bold text-primary uppercase tracking-widest" x-text="'&quot;' + search + '&quot;'"></span>
        <button @click="search = ''" class="font-mono text-[10px] text-on-surface-variant hover:text-primary underline uppercase tracking-widest">
            Clear
        </button>
    </div>

    {{-- Dynamic menu sections --}}
    @foreach($categories as $category)
    @php
        $catSearchStr = $category->availableItems->map(fn($i) => strtolower($i->name . ' ' . ($i->description ?? '')))->join(' ||| ');
        $catHasVeg    = $category->availableItems->where('is_vegetarian', true)->count() > 0;
        $catHasVegan  = $category->availableItems->where('is_vegan',       true)->count() > 0;
    @endphp
    <section x-data="{ catText: {{ json_encode($catSearchStr) }} }"
             x-show="(search ? catText.split(' ||| ').some(t => t.includes(search.toLowerCase())) : (active === 'all' || active === '{{ $category->slug }}')) && (!diet || (diet === 'vegetarian' && {{ $catHasVeg ? 'true' : 'false' }}) || (diet === 'vegan' && {{ $catHasVegan ? 'true' : 'false' }}))"
             class="px-6 md:px-margin-desktop py-12 max-w-container-max mx-auto {{ !$loop->first ? 'border-t border-surface-variant' : '' }}">

      <h2 class="menu-section-heading" x-show="active === 'all' || search">{{ $category->name }}</h2>

      <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' : view === 'list' ? 'flex flex-col gap-3' : 'grid grid-cols-2 lg:grid-cols-4 gap-4'">

        @foreach($category->availableItems as $item)
        <div x-show="(!search || '{{ strtolower($item->name . ' ' . ($item->description ?? '')) }}'.includes(search.toLowerCase())) && (!diet || (diet === 'vegetarian' && {{ $item->is_vegetarian ? 'true' : 'false' }}) || (diet === 'vegan' && {{ $item->is_vegan ? 'true' : 'false' }})) && !excludedAllergens.some(a => {{ json_encode($item->allergens->pluck('name')->values()->all()) }}.includes(a))"
             :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'"
             class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
          <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'"
               class="overflow-hidden">
            <img alt="{{ $item->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                 src="{{ $item->hasStoredImage() ? asset('storage/' . $item->image_path) : 'https://placehold.co/400x300/e4e2e1/1b1c1c?text=' . urlencode($item->name) }}"/>
          </div>
          <div class="p-6 flex flex-col flex-1">
            <div class="flex justify-between items-start mb-2">
              <h3 class="font-headline-md text-headline-md text-on-surface">{{ $item->name }}</h3>
              <span class="font-label-bold text-headline-md text-primary-container">{{ $item->formatted_price }}</span>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">{{ $item->description }}</p>
            <div class="flex items-center justify-between mt-auto">
              <div class="flex gap-2 flex-wrap">
                @if($item->is_vegan)
                  <span class="font-label-sm text-label-sm px-2 py-1 bg-green-100 text-green-800 border border-green-300 uppercase">Vegan</span>
                @elseif($item->is_vegetarian)
                  <span class="font-label-sm text-label-sm px-2 py-1 bg-green-50 text-green-700 border border-green-200 uppercase">Veggie</span>
                @endif
                @foreach($item->allergens->take(3) as $allergen)
                  <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant uppercase">
                    {{ strtoupper(substr($allergen->name, 0, 5)) }}
                  </span>
                @endforeach
              </div>
              <button @click="$store.cart.openDrawer({
                        id: '{{ $item->slug }}',
                        name: {{ json_encode($item->name) }},
                        category: '{{ $item->category->slug }}',
                        basePrice: {{ $item->base_price }},
                        allergens: {{ $item->allergens->where('is_visible', true)->pluck('name') ->toJson() }},
                        image: {{ $item->hasStoredImage() ? json_encode(asset('storage/' . $item->image_path)) : json_encode('https://placehold.co/400x300/e4e2e1/1b1c1c?text=' . urlencode($item->name)) }},
                        ingredients: {{ $item->baseIngredients->pluck('name')->values()->toJson() }},
                        relatedItems: {{ json_encode($item->relatedItemsPayload()) }},
                        isCustomizable: {{ json_encode($item->is_customizable) }},
                        availableToppings: {{ json_encode($item->toppingsPayload()) }}
                      })"
                      class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
                <span class="material-symbols-outlined text-white text-[20px]">add</span>
              </button>
            </div>
          </div>
        </div>
        @endforeach

      </div>
    </section>
    @endforeach

</div>{{-- end x-data --}}

@once
<script>
  document.addEventListener('alpine:init', () => {
    // Overwrite hardcoded base ingredients with DB data
    Alpine.store('cart').baseIngredients = @json($categories->flatMap(function($cat) {
      return $cat->availableItems->filter(fn($i) => $i->category->slug === 'pizza')->mapWithKeys(fn($item) => [
        $item->slug => $item->baseIngredients->pluck('name')->values()
      ]);
    })->all());
  });
</script>
@endonce

@endsection
