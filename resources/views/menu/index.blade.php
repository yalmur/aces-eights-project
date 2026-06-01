@extends('layouts.app')
@section('content')
<div class="max-w-container-max mx-auto">

{{-- Hero Section --}}
<section class="px-6 md:px-margin-desktop mb-12 max-w-container-max mx-auto pt-8">
    <div class="relative h-[400px] w-full overflow-hidden rounded-lg group border border-surface-variant">
        <div class="absolute inset-0 bg-gradient-to-t from-surface/80 via-transparent to-transparent z-10"></div>
        <!-- The Meat Lover Pizza hero image -->
        <img alt="The Meat Lover Pizza" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
        <div class="absolute bottom-12 left-12 z-20">
            <span class="font-label-bold text-label-bold text-primary-container mb-4 block uppercase tracking-widest">House Special</span>
            <h1 class="font-display text-display text-on-surface mb-4">THE MEAT LOVER</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">Double-fermented sourdough, San Marzano tomato, spicy salami, smoked pancetta, and fennel sausage.</p>
        </div>
    </div>
</section>

{{-- Alpine.js category filter + menu grid --}}
<div x-data="{ active: 'all', view: 'grid' }">

    {{-- Search & Filter Bar --}}
    <section class="sticky top-16 z-40 bg-surface/95 backdrop-blur-md px-6 md:px-margin-desktop py-6 border-b border-surface-variant">
        <div class="max-w-container-max mx-auto flex flex-col md:flex-row gap-6 items-center">
            {{-- Category chips --}}
            <div class="flex gap-4 overflow-x-auto no-scrollbar w-full md:w-auto flex-1">
                <button
                    @click="active = 'all'"
                    :class="active === 'all' ? 'px-6 py-2 bg-primary-container text-on-primary font-label-bold text-label-bold whitespace-nowrap' : 'px-6 py-2 bg-surface-container text-on-surface font-label-bold text-label-bold whitespace-nowrap hover:bg-surface-container-high transition-colors'">
                    All
                </button>
                <button
                    @click="active = 'pizzas'"
                    :class="active === 'pizzas' ? 'px-6 py-2 bg-primary-container text-on-primary font-label-bold text-label-bold whitespace-nowrap' : 'px-6 py-2 bg-surface-container text-on-surface font-label-bold text-label-bold whitespace-nowrap hover:bg-surface-container-high transition-colors'">
                    Pizza
                </button>
                <button
                    @click="active = 'starters'"
                    :class="active === 'starters' ? 'px-6 py-2 bg-primary-container text-on-primary font-label-bold text-label-bold whitespace-nowrap' : 'px-6 py-2 bg-surface-container text-on-surface font-label-bold text-label-bold whitespace-nowrap hover:bg-surface-container-high transition-colors'">
                    Starters
                </button>
                <button
                    @click="active = 'salads'"
                    :class="active === 'salads' ? 'px-6 py-2 bg-primary-container text-on-primary font-label-bold text-label-bold whitespace-nowrap' : 'px-6 py-2 bg-surface-container text-on-surface font-label-bold text-label-bold whitespace-nowrap hover:bg-surface-container-high transition-colors'">
                    Salads
                </button>
                <button
                    @click="active = 'pasta'"
                    :class="active === 'pasta' ? 'px-6 py-2 bg-primary-container text-on-primary font-label-bold text-label-bold whitespace-nowrap' : 'px-6 py-2 bg-surface-container text-on-surface font-label-bold text-label-bold whitespace-nowrap hover:bg-surface-container-high transition-colors'">
                    Pasta
                </button>
                <button
                    @click="active = 'desserts'"
                    :class="active === 'desserts' ? 'px-6 py-2 bg-primary-container text-on-primary font-label-bold text-label-bold whitespace-nowrap' : 'px-6 py-2 bg-surface-container text-on-surface font-label-bold text-label-bold whitespace-nowrap hover:bg-surface-container-high transition-colors'">
                    Desserts
                </button>
                <button
                    @click="active = 'drinks'"
                    :class="active === 'drinks' ? 'px-6 py-2 bg-primary-container text-on-primary font-label-bold text-label-bold whitespace-nowrap' : 'px-6 py-2 bg-surface-container text-on-surface font-label-bold text-label-bold whitespace-nowrap hover:bg-surface-container-high transition-colors'">
                    Drinks
                </button>
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

    {{-- Pizza items --}}
    <section x-show="active === 'all' || active === 'pizzas'" class="px-6 md:px-margin-desktop py-12 max-w-container-max mx-auto">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-8 uppercase tracking-wider" x-show="active === 'all'">Pizza</h2>
        <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' : view === 'list' ? 'flex flex-col gap-3' : 'grid grid-cols-2 lg:grid-cols-4 gap-4'">

            {{-- Card: Classic Margherita --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Classic Margherita -->
                    <img alt="Classic Margherita" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Classic Margherita</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£12.50</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Our signature sourdough, San Marzano D.O.P, Fior di latte, fresh basil, and extra virgin olive oil.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">MILK</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">GLUTEN</span>
                        </div>
                        <a href="{{ route('menu.show', 'classic-margherita') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card: Spicy Diavola --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Spicy Diavola -->
                    <img alt="Spicy Diavola" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Spicy Diavola</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£14.50</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">San Marzano, mozzarella, spicy Nduja from Spilinga, and Calabrese salami.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">MILK</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">GLUTEN</span>
                        </div>
                        <a href="{{ route('menu.show', 'spicy-diavola') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card: Tartufo Bianco --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Tartufo Bianco -->
                    <img alt="Tartufo Bianco" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Tartufo Bianco</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£16.00</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">White base, wild mushrooms, truffle oil, pecorino, and fresh thyme.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">MILK</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">GLUTEN</span>
                        </div>
                        <a href="{{ route('menu.show', 'tartufo-bianco') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card: Vegan Garden --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Vegan Garden -->
                    <img alt="Vegan Garden" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Vegan Garden</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£13.50</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Vegan mozzarella, fire-roasted peppers, zucchini, red onion, and balsamic glaze.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-primary text-on-primary">VEGAN</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">GLUTEN</span>
                        </div>
                        <a href="{{ route('menu.show', 'vegan-garden') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card: The Meat Lover --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- The Meat Lover -->
                    <img alt="The Meat Lover" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">The Meat Lover</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£17.00</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Double-fermented sourdough, San Marzano tomato, spicy salami, smoked pancetta, and fennel sausage.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">MILK</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">GLUTEN</span>
                        </div>
                        <a href="{{ route('menu.show', 'the-meat-lover') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Starters items --}}
    <section x-show="active === 'all' || active === 'starters'" class="px-6 md:px-margin-desktop py-12 max-w-container-max mx-auto border-t border-surface-variant">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-8 uppercase tracking-wider">Starters</h2>
        <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' : view === 'list' ? 'flex flex-col gap-3' : 'grid grid-cols-2 lg:grid-cols-4 gap-4'">

            {{-- Card: Garlic Bread --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Garlic Bread -->
                    <img alt="Garlic Bread" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Garlic Bread</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£5.50</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Sourdough flatbread, roasted garlic butter, and fresh parsley.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">MILK</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">GLUTEN</span>
                        </div>
                        <a href="{{ route('menu.show', 'garlic-bread') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card: Nocellara Olives --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Nocellara Olives -->
                    <img alt="Nocellara Olives" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Nocellara Olives</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£4.00</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Sicilian Nocellara olives marinated with chilli, lemon zest, and rosemary.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-primary text-on-primary">VEGAN</span>
                        </div>
                        <a href="{{ route('menu.show', 'nocellara-olives') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card: Burrata --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Burrata -->
                    <img alt="Burrata" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Burrata</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£8.50</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Fresh burrata, heritage tomatoes, Ligurian olive oil, and flaked sea salt.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">MILK</span>
                        </div>
                        <a href="{{ route('menu.show', 'burrata') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Salads items --}}
    <section x-show="active === 'all' || active === 'salads'" class="px-6 md:px-margin-desktop py-12 max-w-container-max mx-auto border-t border-surface-variant">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-8 uppercase tracking-wider">Salads</h2>
        <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' : view === 'list' ? 'flex flex-col gap-3' : 'grid grid-cols-2 lg:grid-cols-4 gap-4'">

            {{-- Card: Caesar Salad --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Caesar Salad -->
                    <img alt="Caesar Salad" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Caesar Salad</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£9.00</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Romaine, house Caesar dressing, pecorino, sourdough croutons, and anchovies.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">MILK</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">GLUTEN</span>
                        </div>
                        <a href="{{ route('menu.show', 'caesar-salad') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card: Rocket & Parmesan --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Rocket & Parmesan Salad -->
                    <img alt="Rocket & Parmesan Salad" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Rocket &amp; Parmesan</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£7.50</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Wild rocket, shaved Parmigiano Reggiano, balsamic reduction, and toasted pine nuts.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">MILK</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">NUTS</span>
                        </div>
                        <a href="{{ route('menu.show', 'rocket-parmesan') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Pasta items --}}
    <section x-show="active === 'all' || active === 'pasta'" class="px-6 md:px-margin-desktop py-12 max-w-container-max mx-auto border-t border-surface-variant">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-8 uppercase tracking-wider">Pasta</h2>
        <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' : view === 'list' ? 'flex flex-col gap-3' : 'grid grid-cols-2 lg:grid-cols-4 gap-4'">

            {{-- Card: Cacio e Pepe --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Cacio e Pepe -->
                    <img alt="Cacio e Pepe" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Cacio e Pepe</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£11.00</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Tonnarelli, Pecorino Romano, Parmigiano Reggiano, and cracked black pepper.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">MILK</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">GLUTEN</span>
                        </div>
                        <a href="{{ route('menu.show', 'cacio-e-pepe') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card: Amatriciana --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Amatriciana -->>
                    <img alt="Amatriciana" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Amatriciana</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£13.00</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Rigatoni, guanciale, San Marzano tomato, Pecorino Romano, and Amatrice chilli.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">MILK</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">GLUTEN</span>
                        </div>
                        <a href="{{ route('menu.show', 'amatriciana') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Desserts items --}}
    <section x-show="active === 'all' || active === 'desserts'" class="px-6 md:px-margin-desktop py-12 max-w-container-max mx-auto border-t border-surface-variant">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-8 uppercase tracking-wider">Desserts</h2>
        <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' : view === 'list' ? 'flex flex-col gap-3' : 'grid grid-cols-2 lg:grid-cols-4 gap-4'">

            {{-- Card: Tiramisu --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Tiramisu -->
                    <img alt="Tiramisu" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Tiramisu</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£7.00</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Traditional mascarpone, Savoiardi biscuits, espresso, and a dusting of Valrhona cocoa.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">MILK</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">GLUTEN</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">EGGS</span>
                        </div>
                        <a href="{{ route('menu.show', 'tiramisu') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card: Panna Cotta --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Panna Cotta -->
                    <img alt="Panna Cotta" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Panna Cotta</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£6.50</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Vanilla panna cotta with seasonal berry compote and shortbread crumb.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">MILK</span>
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">GLUTEN</span>
                        </div>
                        <a href="{{ route('menu.show', 'panna-cotta') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Drinks items --}}
    <section x-show="active === 'all' || active === 'drinks'" class="px-6 md:px-margin-desktop py-12 max-w-container-max mx-auto border-t border-surface-variant">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-8 uppercase tracking-wider">Drinks</h2>
        <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' : view === 'list' ? 'flex flex-col gap-3' : 'grid grid-cols-2 lg:grid-cols-4 gap-4'">

            {{-- Card: Moretti Draft --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- Moretti Draft -->
                    <img alt="Moretti Draft" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Moretti Draft</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£6.50</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Italian lager on draft, served in a frosted glass.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">GLUTEN</span>
                        </div>
                        <a href="{{ route('menu.show', 'moretti-draft') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card: San Pellegrino --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- San Pellegrino -->
                    <img alt="San Pellegrino" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">San Pellegrino</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£3.50</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Sparkling mineral water, 750ml bottle.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-primary text-on-primary">VEGAN</span>
                        </div>
                        <a href="{{ route('menu.show', 'san-pellegrino') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card: House Red Wine --}}
            <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'" class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
                <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'" class="overflow-hidden">
                    <!-- House Red Wine -->
                    <img alt="House Red Wine" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Menu+Item"/>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">House Red Wine</h3>
                        <span class="font-label-bold text-headline-md text-primary-container">£28.00</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">Montepulciano d'Abruzzo, medium-bodied with notes of cherry and spice. 75cl bottle.</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex gap-2">
                            <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant">SULPHITES</span>
                        </div>
                        <a href="{{ route('menu.show', 'house-red-wine') }}" class="glossy-gold w-12 h-12 flex items-center justify-center rounded-sm touch-manipulation">
                            <span class="material-symbols-outlined text-on-primary-fixed">add</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>{{-- end x-data --}}

</div>{{-- end max-w-container-max --}}
@endsection
