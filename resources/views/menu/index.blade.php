@extends('layouts.app')
@section('content')
<div class="max-w-container-max mx-auto">

{{-- Hero Section --}}
<section class="px-6 md:px-margin-desktop mb-12 max-w-container-max mx-auto pt-8">
    <div class="relative h-[400px] w-full overflow-hidden rounded-lg group border border-surface-variant">
        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-transparent z-10"></div>
        <!-- The Meat Lover Pizza hero image -->
        <img alt="The Meat Lover Pizza" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="https://placehold.co/1200x400/2a2a2a/666666?text=Wood+Fired+Pizza"/>
        <div class="absolute bottom-12 left-12 z-20">
            <span class="font-label-bold text-label-bold text-secondary-fixed-dim mb-4 block uppercase tracking-widest">House Special</span>
            <h1 class="font-display text-display text-white mb-4" style="text-shadow:0 2px 8px rgba(0,0,0,0.6)">THE MEAT LOVER</h1>
            <p class="font-body-lg text-body-lg text-white/75 max-w-xl">Double-fermented sourdough, San Marzano tomato, spicy salami, smoked pancetta, and fennel sausage.</p>
        </div>
    </div>
</section>

{{-- Alpine.js category filter + menu grid --}}
<div x-data="{ active: 'all', view: 'grid' }"
     x-init="$store.cart.allToppings = {{ $toppings->map(fn($t) => ['name' => $t->name, 'price' => (float)$t->price])->toJson() }}">

    {{-- Search & Filter Bar --}}
    <section class="sticky top-16 z-40 bg-surface/95 backdrop-blur-md px-6 md:px-margin-desktop py-6 border-b border-surface-variant">
        <div class="max-w-container-max mx-auto flex flex-col md:flex-row gap-6 items-center">
            {{-- Category chips --}}
            <div class="flex gap-2 overflow-x-auto no-scrollbar w-full md:w-auto flex-1 pb-1">
                <button @click="active = 'all'" :class="{ 'active': active === 'all' }" class="chip whitespace-nowrap">All</button>
                @foreach($categories as $category)
                <button @click="active = '{{ $category->slug }}'"
                        :class="{ 'active': active === '{{ $category->slug }}' }"
                        class="chip whitespace-nowrap">{{ $category->name }}</button>
                @endforeach
            </div>
            {{-- Smart Search --}}
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="relative flex-1 md:w-80">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                    <input class="w-full bg-surface-container border border-surface-variant px-10 py-3 font-body-md text-body-md text-on-surface focus:ring-1 focus:ring-primary-container focus:border-primary-container" placeholder="Search our soul..." type="text"/>
                </div>
                <button class="p-3 bg-surface-container border border-surface-variant hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-on-surface">tune</span>
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
    </section>

    {{-- Dynamic menu sections --}}
    @foreach($categories as $category)
    <section x-show="active === 'all' || active === '{{ $category->slug }}'"
             class="px-6 md:px-margin-desktop py-12 max-w-container-max mx-auto {{ !$loop->first ? 'border-t border-surface-variant' : '' }}">

      <h2 class="menu-section-heading" x-show="active === 'all'">{{ $category->name }}</h2>

      <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' : view === 'list' ? 'flex flex-col gap-3' : 'grid grid-cols-2 lg:grid-cols-4 gap-4'">

        @foreach($category->availableItems as $item)
        <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'"
             class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
          <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'"
               class="overflow-hidden">
            <img alt="{{ $item->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                 src="{{ $item->image_path ? asset('storage/' . $item->image_path) : 'https://placehold.co/400x300/e4e2e1/1b1c1c?text=' . urlencode($item->name) }}"/>
          </div>
          <div class="p-6 flex flex-col flex-1">
            <div class="flex justify-between items-start mb-2">
              <h3 class="font-headline-md text-headline-md text-on-surface">{{ $item->name }}</h3>
              <span class="font-label-bold text-headline-md text-primary-container">{{ $item->formatted_price }}</span>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">{{ $item->description }}</p>
            <div class="flex items-center justify-between mt-auto">
              <div class="flex gap-2 flex-wrap">
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
                        basePrice: {{ $item->base_price }}
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

</div>{{-- end max-w-container-max --}}

@once
<script>
  document.addEventListener('alpine:init', () => {
    // Overwrite hardcoded base ingredients with DB data
    Alpine.store('cart').baseIngredients = {!! $categories->flatMap(function($cat) {
      return $cat->availableItems->filter(fn($i) => $i->category->slug === 'pizza')->mapWithKeys(fn($item) => [
        $item->slug => $item->baseIngredients->pluck('name')->values()
      ]);
    })->toJson() !!};
  });
</script>
@endonce

@endsection
