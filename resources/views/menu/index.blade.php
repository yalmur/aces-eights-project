@extends('layouts.app')
@push('head')
<style>
.menu-masthead { border-bottom: 4px double #690008; padding-bottom: 2rem; margin-bottom: 0; }
.menu-masthead-rule { width:100%; height:1px; background:linear-gradient(90deg,transparent,#690008 20%,#690008 80%,transparent); margin:0.75rem 0; }
.filter-sidebar { width: 260px; flex-shrink: 0; }
</style>
@endpush
@section('content')

{{-- Masthead --}}
<div class="max-w-container-max mx-auto px-6 lg:px-16 pt-20 pb-6">
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

{{-- Main layout: sidebar + content --}}
<div x-data="{
    active: 'all',
    view: 'grid',
    search: '',
    diet: '',
    excludedAllergens: [],
    sidebarOpen: false,
    clearAll() {
        this.active = 'all';
        this.diet = '';
        this.excludedAllergens = [];
        this.search = '';
    },
    get activeCount() {
        let n = 0;
        if (this.active !== 'all') n++;
        if (this.diet) n++;
        n += this.excludedAllergens.length;
        return n;
    }
}" class="max-w-container-max mx-auto px-4 lg:px-16 py-8 flex gap-8 items-start relative">

    {{-- Mobile sidebar backdrop --}}
    <div x-show="sidebarOpen" x-cloak
         class="fixed inset-0 z-40 bg-on-surface/40 backdrop-blur-sm lg:hidden"
         @click="sidebarOpen = false"></div>

    {{-- ===== FILTER SIDEBAR ===== --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="filter-sidebar fixed top-0 left-0 h-full z-50 bg-surface border-r border-surface-variant overflow-y-auto
                  lg:static lg:h-auto lg:translate-x-0 lg:border lg:border-surface-variant lg:bg-surface-container-lowest
                  transition-transform duration-300 flex-shrink-0">

        {{-- Sidebar header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-surface-variant sticky top-0 bg-surface lg:bg-surface-container-lowest z-10">
            <span class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant">Filters</span>
            <div class="flex items-center gap-3">
                <button x-show="activeCount > 0" x-cloak
                        @click="clearAll()"
                        class="font-mono text-[10px] uppercase tracking-widest text-primary hover:underline">
                    Clear all
                </button>
                <button @click="sidebarOpen = false" class="lg:hidden text-on-surface-variant hover:text-on-surface p-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="square" d="M6 6l12 12M6 18L18 6"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-5 space-y-8">

            {{-- Search --}}
            <div>
                <p class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-3">Search</p>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                    <input x-model="search" @keydown.escape="search = ''"
                           class="w-full bg-surface-container border border-surface-variant pl-9 pr-8 py-2.5 font-body-md text-body-md text-on-surface focus:ring-1 focus:ring-primary-container focus:border-primary-container text-sm"
                           placeholder="Search menu…" type="text"/>
                    <button x-show="search" x-cloak @click="search = ''"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>
            </div>

            {{-- Category --}}
            <div>
                <p class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-3">Category</p>
                <div class="space-y-1">
                    <button @click="active = 'all'"
                            :class="active === 'all' ? 'bg-primary text-on-primary' : 'text-on-surface hover:bg-surface-container'"
                            class="w-full text-left px-3 py-2 font-mono text-xs uppercase tracking-wide transition-colors flex items-center justify-between">
                        <span>All items</span>
                        <span x-show="active === 'all'" class="material-symbols-outlined text-sm">check</span>
                    </button>
                    @foreach($categories as $category)
                    <button @click="active = '{{ $category->slug }}'"
                            :class="active === '{{ $category->slug }}' ? 'bg-primary text-on-primary' : 'text-on-surface hover:bg-surface-container'"
                            class="w-full text-left px-3 py-2 font-mono text-xs uppercase tracking-wide transition-colors flex items-center justify-between">
                        <span>{{ $category->name }}</span>
                        <span x-show="active === '{{ $category->slug }}'" class="material-symbols-outlined text-sm">check</span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Dietary --}}
            <div>
                <p class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-3">Dietary</p>
                <div class="space-y-1">
                    <button @click="diet = diet === 'vegetarian' ? '' : 'vegetarian'"
                            :class="diet === 'vegetarian' ? 'bg-green-700 text-white' : 'text-on-surface hover:bg-surface-container'"
                            class="w-full text-left px-3 py-2 font-mono text-xs uppercase tracking-wide transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">eco</span>
                        <span>Vegetarian</span>
                    </button>
                    <button @click="diet = diet === 'vegan' ? '' : 'vegan'"
                            :class="diet === 'vegan' ? 'bg-green-800 text-white' : 'text-on-surface hover:bg-surface-container'"
                            class="w-full text-left px-3 py-2 font-mono text-xs uppercase tracking-wide transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">grass</span>
                        <span>Vegan</span>
                    </button>
                </div>
            </div>

            {{-- Allergen exclusion --}}
            @if($allergens->isNotEmpty())
            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant">Exclude Allergens</p>
                    <button x-show="excludedAllergens.length" x-cloak
                            @click="excludedAllergens = []"
                            class="font-mono text-[9px] text-primary hover:underline uppercase">Clear</button>
                </div>
                <div class="space-y-1">
                    @foreach($allergens as $allergen)
                    <button @click="excludedAllergens = excludedAllergens.includes({{ json_encode($allergen->name) }})
                                ? excludedAllergens.filter(a => a !== {{ json_encode($allergen->name) }})
                                : [...excludedAllergens, {{ json_encode($allergen->name) }}]"
                            :class="excludedAllergens.includes({{ json_encode($allergen->name) }})
                                ? 'bg-brand-error/10 border-brand-error text-brand-error'
                                : 'border-surface-variant text-on-surface hover:border-outline'"
                            class="w-full text-left px-3 py-2 font-mono text-xs uppercase tracking-wide border transition-colors flex items-center justify-between">
                        <span>{{ $allergen->name }}</span>
                        <span x-show="excludedAllergens.includes({{ json_encode($allergen->name) }})"
                              class="material-symbols-outlined text-sm">block</span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </aside>

    {{-- ===== CONTENT AREA ===== --}}
    <div class="flex-1 min-w-0">

        {{-- Top bar: mobile filter toggle + view toggle --}}
        <div class="flex items-center justify-between mb-6 gap-4">
            <div class="flex items-center gap-3">
                {{-- Mobile: open sidebar button --}}
                <button @click="sidebarOpen = true"
                        class="lg:hidden flex items-center gap-2 px-4 py-2 border border-surface-variant bg-surface-container font-mono text-xs uppercase tracking-wide hover:bg-surface-container-high transition-colors relative">
                    <span class="material-symbols-outlined text-sm">tune</span>
                    Filters
                    <span x-show="activeCount > 0" x-cloak
                          x-text="activeCount"
                          class="absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-primary text-white text-[10px] font-bold flex items-center justify-center"></span>
                </button>
                {{-- Active filter summary --}}
                <div x-show="search" x-cloak class="flex items-center gap-2">
                    <span class="font-mono text-xs text-on-surface-variant">Results for</span>
                    <span class="font-mono text-xs font-bold text-primary" x-text="'&quot;' + search + '&quot;'"></span>
                </div>
            </div>

            {{-- View toggle --}}
            <div class="flex border border-surface-variant overflow-hidden flex-shrink-0">
                <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-primary-container text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'"
                        class="p-2.5 transition-colors" title="Grid view">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="square" d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z"/>
                    </svg>
                </button>
                <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-primary-container text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'"
                        class="p-2.5 border-l border-surface-variant transition-colors" title="List view">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="square" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <button @click="view = 'compact'"
                        :class="view === 'compact' ? 'bg-primary-container text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'"
                        class="p-2.5 border-l border-surface-variant transition-colors" title="Compact view">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="square" d="M3 3h5v5H3zM9 3h5v5H9zM15 3h5v5h-5zM3 9h5v5H3zM9 9h5v5H9zM15 9h5v5h-5zM3 15h5v5H3zM9 15h5v5H9zM15 15h5v5h-5z"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Menu sections --}}
        @foreach($categories as $category)
        @php
            $catSearchStr = $category->availableItems->map(fn($i) => strtolower($i->name . ' ' . ($item->description ?? '')))->join(' ||| ');
            $catHasVeg    = $category->availableItems->where('is_vegetarian', true)->count() > 0;
            $catHasVegan  = $category->availableItems->where('is_vegan', true)->count() > 0;
        @endphp
        <section x-data="{ catText: {{ json_encode($category->availableItems->map(fn($i) => strtolower($i->name . ' ' . ($i->description ?? '')))->join(' ||| ')) }} }"
                 x-show="(search ? catText.split(' ||| ').some(t => t.includes(search.toLowerCase())) : (active === 'all' || active === '{{ $category->slug }}')) && (!diet || (diet === 'vegetarian' && {{ $catHasVeg ? 'true' : 'false' }}) || (diet === 'vegan' && {{ $catHasVegan ? 'true' : 'false' }}))"
                 class="mb-12 {{ !$loop->first ? 'pt-8 border-t border-surface-variant' : '' }}">

            <h2 class="menu-section-heading" x-show="active === 'all' || search">{{ $category->name }}</h2>

            <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6' : view === 'list' ? 'flex flex-col gap-3' : 'grid grid-cols-2 lg:grid-cols-3 gap-4'">

                @foreach($category->availableItems as $item)
                <div x-show="(!search || '{{ strtolower($item->name . ' ' . ($item->description ?? '')) }}'.includes(search.toLowerCase())) && (!diet || (diet === 'vegetarian' && {{ $item->is_vegetarian ? 'true' : 'false' }}) || (diet === 'vegan' && {{ $item->is_vegan ? 'true' : 'false' }})) && !excludedAllergens.some(a => {{ json_encode($item->allergens->pluck('name')->values()->all()) }}.includes(a))"
                     :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'"
                     class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                    <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-36 overflow-hidden' : 'h-56 overflow-hidden'"
                         class="overflow-hidden">
                        <img alt="{{ $item->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             src="{{ $item->hasStoredImage() ? asset('storage/' . $item->image_path) : 'https://placehold.co/400x300/e4e2e1/1b1c1c?text=' . urlencode($item->name) }}"/>
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $item->name }}</h3>
                            <span class="font-label-bold text-headline-md text-primary-container flex-shrink-0 ml-2">{{ $item->formatted_price }}</span>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface-variant mb-5 flex-1">{{ $item->description }}</p>
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
                                        allergens: {{ $item->allergens->where('is_visible', true)->pluck('name')->toJson() }},
                                        image: {{ $item->hasStoredImage() ? json_encode(asset('storage/' . $item->image_path)) : json_encode('https://placehold.co/400x300/e4e2e1/1b1c1c?text=' . urlencode($item->name)) }},
                                        ingredients: {{ $item->baseIngredients->pluck('name')->values()->toJson() }},
                                        relatedItems: {{ json_encode($item->relatedItemsPayload()) }},
                                        isCustomizable: {{ json_encode($item->is_customizable) }},
                                        availableToppings: {{ json_encode($item->toppingsPayload()) }},
                                        sizes: {{ json_encode($item->sizesPayload()) }},
                                        crusts: {{ json_encode($item->crustsPayload()) }}
                                      })"
                                  class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation flex-shrink-0">
                                <span class="material-symbols-outlined text-white text-[20px]">add</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </section>
        @endforeach

    </div>{{-- end content area --}}

</div>{{-- end flex wrapper --}}

@once
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.store('cart').baseIngredients = @json($categories->flatMap(function($cat) {
      return $cat->availableItems->filter(fn($i) => $i->category->slug === 'pizza')->mapWithKeys(fn($item) => [
        $item->slug => $item->baseIngredients->pluck('name')->values()
      ]);
    })->all());
  });
</script>
@endonce

@endsection
