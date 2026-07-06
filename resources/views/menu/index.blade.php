@extends('layouts.app')
@push('head')
<style>
.menu-masthead { border-bottom: 4px double #690008; padding-bottom: 2rem; margin-bottom: 3rem; }
.menu-masthead-rule { width:100%; height:1px; background:linear-gradient(90deg,transparent,#690008 20%,#690008 80%,transparent); margin:0.75rem 0; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
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

    {{-- ===== FILTER BAR ===== --}}
    <section class="sticky top-16 z-40 bg-surface/98 backdrop-blur-md border-b-2 border-[#690008]/30 shadow-sm">

        {{-- Row 1: Category tabs (horizontally scrollable) --}}
        <div class="border-b border-surface-variant">
            <div class="max-w-container-max mx-auto px-6 md:px-margin-desktop">
                <div class="flex overflow-x-auto scrollbar-hide -mb-px">
                    <button @click="active = 'all'"
                            :class="active === 'all'
                                ? 'border-b-2 border-[#690008] text-[#690008] font-bold'
                                : 'border-b-2 border-transparent text-on-surface-variant hover:text-on-surface'"
                            class="px-4 py-3.5 font-mono text-[11px] uppercase tracking-widest whitespace-nowrap transition-colors flex-shrink-0">
                        All
                    </button>
                    @foreach($categories as $category)
                    <button @click="active = '{{ $category->slug }}'"
                            :class="active === '{{ $category->slug }}'
                                ? 'border-b-2 border-[#690008] text-[#690008] font-bold'
                                : 'border-b-2 border-transparent text-on-surface-variant hover:text-on-surface'"
                            class="px-4 py-3.5 font-mono text-[11px] uppercase tracking-widest whitespace-nowrap transition-colors flex-shrink-0">
                        {{ $category->name }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Row 2: Search + Diet + Allergens + View --}}
        <div class="max-w-container-max mx-auto px-6 md:px-margin-desktop py-3 flex items-center gap-3 flex-wrap md:flex-nowrap">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[180px] md:max-w-xs">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
                <input x-model="search" @keydown.escape="search = ''"
                       class="w-full bg-surface-container-low rounded-full border border-surface-variant pl-9 pr-8 py-2 text-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:border-[#690008] focus:ring-1 focus:ring-[#690008]/30 transition-colors"
                       placeholder="Search menu…" type="text"/>
                <button x-show="search" x-cloak @click="search = ''"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>

            {{-- Divider --}}
            <div class="hidden md:block w-px h-6 bg-surface-variant flex-shrink-0"></div>

            {{-- Dietary toggles --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <button @click="diet = diet === 'vegetarian' ? '' : 'vegetarian'"
                        :class="diet === 'vegetarian'
                            ? 'bg-green-700 text-white border-green-700'
                            : 'bg-transparent text-on-surface-variant border-surface-variant hover:border-green-600 hover:text-green-700'"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-[11px] font-mono uppercase tracking-wide transition-all">
                    <span class="material-symbols-outlined text-[14px]">eco</span>
                    Vegetarian
                </button>
                <button @click="diet = diet === 'vegan' ? '' : 'vegan'"
                        :class="diet === 'vegan'
                            ? 'bg-green-800 text-white border-green-800'
                            : 'bg-transparent text-on-surface-variant border-surface-variant hover:border-green-700 hover:text-green-800'"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-[11px] font-mono uppercase tracking-wide transition-all">
                    <span class="material-symbols-outlined text-[14px]">grass</span>
                    Vegan
                </button>
            </div>

            {{-- Divider --}}
            <div class="hidden md:block w-px h-6 bg-surface-variant flex-shrink-0"></div>

            {{-- Allergen filter button --}}
            <button @click="showFilters = !showFilters" type="button"
                    :class="showFilters || excludedAllergens.length
                        ? 'bg-[#690008] text-white border-[#690008]'
                        : 'bg-transparent border-surface-variant text-on-surface-variant hover:border-[#690008] hover:text-[#690008]'"
                    class="relative flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-[11px] font-mono uppercase tracking-wide transition-all flex-shrink-0">
                <span class="material-symbols-outlined text-[14px]">tune</span>
                Allergens
                <span x-show="excludedAllergens.length" x-cloak
                      x-text="excludedAllergens.length"
                      class="ml-0.5 w-4 h-4 rounded-full bg-white text-[#690008] text-[9px] font-bold flex items-center justify-center"></span>
            </button>

            {{-- Spacer --}}
            <div class="flex-1 hidden md:block"></div>

            {{-- View toggle --}}
            <div class="flex items-center gap-1 bg-surface-container-low rounded-lg p-0.5 border border-surface-variant flex-shrink-0">
                <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-surface shadow text-on-surface' : 'text-on-surface-variant hover:text-on-surface'"
                        class="p-2 rounded-md transition-all" title="Grid view">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="square" d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z"/>
                    </svg>
                </button>
                <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-surface shadow text-on-surface' : 'text-on-surface-variant hover:text-on-surface'"
                        class="p-2 rounded-md transition-all" title="List view">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="square" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <button @click="view = 'compact'"
                        :class="view === 'compact' ? 'bg-surface shadow text-on-surface' : 'text-on-surface-variant hover:text-on-surface'"
                        class="p-2 rounded-md transition-all" title="Compact view">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="square" d="M3 3h5v5H3zM9 3h5v5H9zM15 3h5v5h-5zM3 9h5v5H3zM9 9h5v5H9zM15 9h5v5h-5zM3 15h5v5H3zM9 15h5v5H9zM15 15h5v5h-5z"/>
                    </svg>
                </button>
            </div>

        </div>

        {{-- Allergen dropdown panel --}}
        <div x-show="showFilters" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="border-t border-surface-variant bg-surface-container-lowest">
            <div class="max-w-container-max mx-auto px-6 md:px-margin-desktop py-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant">Exclude allergens:</span>
                    <button x-show="excludedAllergens.length" x-cloak @click="excludedAllergens = []"
                            class="font-mono text-[10px] text-[#690008] hover:underline uppercase tracking-widest">Clear all</button>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($allergens as $allergen)
                    <button @click="excludedAllergens = excludedAllergens.includes({{ json_encode($allergen->name) }}) ? excludedAllergens.filter(a => a !== {{ json_encode($allergen->name) }}) : [...excludedAllergens, {{ json_encode($allergen->name) }}]"
                            :class="excludedAllergens.includes({{ json_encode($allergen->name) }})
                                ? 'bg-[#690008]/10 border-[#690008] text-[#690008] font-bold'
                                : 'bg-surface border-surface-variant text-on-surface-variant hover:border-on-surface-variant'"
                            type="button"
                            class="px-3 py-1.5 rounded-full border text-[11px] font-mono uppercase tracking-wide transition-all flex items-center gap-1.5">
                        <span x-show="excludedAllergens.includes({{ json_encode($allergen->name) }})"
                              class="material-symbols-outlined text-[13px]">block</span>
                        {{ $allergen->name }}
                    </button>
                    @endforeach
                </div>
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
                        availableToppings: {{ json_encode($item->toppingsPayload()) }},
                        sizes: {{ json_encode($item->sizesPayload()) }},
                        crusts: {{ json_encode($item->crustsPayload()) }}
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

{{-- ── DEALS SECTION ── --}}
@if($deals->isNotEmpty())
<section class="bg-surface-container-low border-t-2 border-[#690008]/20 py-16 px-6 md:px-margin-desktop">
  <div class="max-w-container-max mx-auto">
    <div class="flex items-end justify-between mb-8">
      <div>
        <p class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-1">Save more</p>
        <h2 class="font-serif text-3xl font-black uppercase text-on-surface">Today's Deals</h2>
      </div>
    </div>
    <div class="double-divider mb-8"></div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($deals as $deal)
      @php $payload = $deal->toFrontendPayload(); @endphp
      <div class="bg-surface border border-surface-variant hover:border-[#690008]/40 transition-all shadow-sm overflow-hidden group">
        @if($deal->image_path)
        <div class="h-44 overflow-hidden">
          <img src="{{ asset('storage/' . $deal->image_path) }}" alt="{{ $deal->name }}"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        </div>
        @else
        <div class="h-44 bg-gradient-to-br from-[#690008]/10 to-[#690008]/5 flex items-center justify-center">
          <span class="material-symbols-outlined text-[64px] text-[#690008]/30">local_offer</span>
        </div>
        @endif

        <div class="p-6">
          <div class="flex justify-between items-start mb-2">
            <h3 class="font-serif text-xl font-bold text-on-surface">{{ $deal->name }}</h3>
            <span class="font-mono text-sm font-bold text-primary whitespace-nowrap ml-4">
              {{ $deal->type_label }}
            </span>
          </div>
          @if($deal->description)
          <p class="font-sans text-sm text-on-surface-variant mb-4">{{ $deal->description }}</p>
          @endif

          @if($deal->hasSlots())
          <div class="flex flex-wrap gap-1.5 mb-5">
            @foreach($deal->slots as $slot)
            <span class="font-mono text-[10px] uppercase tracking-wide px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">
              {{ $slot->min_qty === $slot->max_qty ? $slot->max_qty . 'x' : $slot->min_qty . '–' . $slot->max_qty . 'x' }}
              {{ $slot->label }}
              @if($slot->is_free)<span class="text-green-700"> FREE</span>@endif
            </span>
            @endforeach
          </div>
          @endif

          <button @click="$store.dealCart.openDeal({{ json_encode($payload) }})"
                  class="btn-primary w-full flex items-center justify-center gap-2 py-3 font-mono text-xs uppercase font-bold">
            <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
            {{ $deal->deal_type === 'bundle' || $deal->deal_type === 'bogo' ? 'Build This Deal' : 'Add to Order' }}
          </button>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

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
