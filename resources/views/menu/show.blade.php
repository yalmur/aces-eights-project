@extends('layouts.app')
@section('content')
{{-- Pizza customisation page - adapted from stitch design --}}
<div x-data="{
    size: 'medium',
    qty: 1,
    toppings: { calabrese: 0, pancetta: 0 },
    get basePrice() {
        const p = { small: 12.50, medium: 15.00, large: 18.50 };
        return p[this.size];
    },
    get toppingCost() {
        return (this.toppings.calabrese + this.toppings.pancetta) * 2.00;
    },
    get total() { return ((this.basePrice + this.toppingCost) * this.qty).toFixed(2); }
}" class="max-w-container-max mx-auto">
<div class="lg:grid-cols-12 lg:gap-12 lg:grid">
<!-- Left Column: Customization -->
<div class="lg:col-span-8 space-y-12">
<!-- Hero Section -->
<section class="space-y-6">
<div class="aspect-video lg:aspect-[21/9] overflow-hidden border-4 border-on-surface shadow-[4px_4px_0px_0px_rgba(27,28,28,1)]">
<!-- photo: The Ironworks Margherita pizza -->
<img alt="The Ironworks Margherita pizza" class="w-full h-full object-cover" src="https://placehold.co/600x400/e4e2e1/1b1c1c?text=The+Ironworks+Margherita"/>
</div>
<div>
<h2 class="font-headline-lg text-headline-lg text-primary uppercase tracking-tight">The Ironworks Margherita</h2>
<p class="font-body-lg text-on-surface-variant mt-2 italic max-w-2xl">A legacy blend of San Marzano tomatoes, fresh pulled mozzarella, and heritage basil on a 48-hour fermented crust.</p>
</div>
</section>
<!-- Allergy Info -->
<section>
<div class="bg-surface-container-high border border-outline p-4 flex gap-3 items-start">
<span class="material-symbols-outlined text-primary">warning</span>
<div>
<p class="font-label-bold text-label-bold uppercase">Allergy Information</p>
<p class="font-label-sm text-label-sm mt-1">This item contains: Gluten, Dairy. Our facility handles nuts and soy.</p>
</div>
</div>
</section>
<div class="industrial-divider"></div>
<!-- Smart Filters -->
<section class="space-y-4">
<p class="font-label-bold text-label-bold uppercase tracking-widest text-on-surface-variant">Forging Preferences</p>
<div class="flex items-center gap-3 mb-4 py-2">
<span class="font-label-sm uppercase tracking-wider text-on-surface-variant">Single Build</span>
<button class="relative inline-flex h-6 w-11 items-center rounded-full bg-outline-variant transition-colors hover:bg-outline">
<span class="inline-block h-4 w-4 transform rounded-full bg-surface transition-transform translate-x-1"></span>
</button>
<span class="font-label-sm uppercase tracking-wider text-primary">Half-and-Half Foundry</span>
</div>
<div class="flex gap-3 overflow-x-auto pb-2 no-scrollbar">
<button class="flex-none px-6 py-2 bg-secondary-container border border-on-surface rounded-full flex items-center gap-2 stamped-effect hover:bg-secondary-fixed">
<span class="material-symbols-outlined text-on-secondary-fixed text-[18px]">eco</span>
<span class="font-label-bold text-label-bold">All-Natural Toppings</span>
</button>
<button class="flex-none px-6 py-2 border border-outline rounded-full flex items-center gap-2 stamped-effect hover:bg-surface-container">
<span class="material-symbols-outlined text-on-surface-variant text-[18px]">fitness_center</span>
<span class="font-label-bold text-label-bold">High Protein Add-ons</span>
</button>
</div>
</section>
<!-- Size Choice -->
<section class="mt-8">
<p class="font-label-bold text-label-bold uppercase tracking-widest text-on-surface-variant mb-4">Select Gauge (Size)</p>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
<button
    @click="size = 'small'"
    :class="size === 'small' ? 'border-primary bg-primary-container text-on-primary' : 'border-on-surface bg-surface'"
    class="relative flex flex-col items-center p-6 border-2 hover:bg-surface-container transition-colors cursor-pointer">
<span class="font-label-bold text-label-bold">SMALL</span>
<span class="font-body-md text-on-surface-variant">10"</span>
<span class="font-label-bold text-primary mt-1">£12.50</span>
</button>
<button
    @click="size = 'medium'"
    :class="size === 'medium' ? 'border-primary bg-primary-container text-on-primary' : 'border-outline bg-surface'"
    class="relative flex flex-col items-center p-6 border-2 hover:bg-surface-container transition-colors cursor-pointer">
<span class="font-label-bold text-label-bold">MEDIUM</span>
<span class="font-body-md text-on-surface-variant">12"</span>
<span class="font-label-bold text-primary mt-1">£15.00</span>
</button>
<button
    @click="size = 'large'"
    :class="size === 'large' ? 'border-primary bg-primary-container text-on-primary' : 'border-outline bg-surface'"
    class="relative flex flex-col items-center p-6 border-2 hover:bg-surface-container transition-colors cursor-pointer">
<span class="font-label-bold text-label-bold">LARGE</span>
<span class="font-body-md text-on-surface-variant">14"</span>
<span class="font-label-bold text-primary mt-1">£18.50</span>
</button>
</div>
</section>
<!-- Base Choice -->
<section class="mt-8">
<p class="font-label-bold text-label-bold uppercase tracking-widest text-on-surface-variant mb-4">Crust Foundry</p>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<button class="flex justify-between items-center p-4 border-2 border-on-surface bg-primary text-on-primary stamped-effect hover:bg-primary-container hover:text-on-primary-container">
<span class="font-label-bold">48hr Sourdough</span>
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</button>
<button class="flex justify-between items-center p-4 border-2 border-outline bg-surface stamped-effect hover:bg-surface-container">
<span class="font-label-bold">Gluten-Free (+£2.00)</span>
<span class="material-symbols-outlined text-outline">radio_button_unchecked</span>
</button>
<button class="flex justify-between items-center p-4 border-2 border-outline bg-surface stamped-effect hover:bg-surface-container">
<span class="font-label-bold">Cauliflower (+£2.50)</span>
<span class="material-symbols-outlined text-outline">radio_button_unchecked</span>
</button>
</div>
</section>
<!-- Custom Toppings -->
<section class="mt-8">
<div class="flex justify-between items-center mb-6">
<p class="font-label-bold text-label-bold uppercase tracking-widest text-on-surface-variant">The Foundry Toppings (Meats)</p>
<span class="font-label-sm text-primary underline cursor-pointer hover:text-primary-container">See All</span>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<!-- Salami Card -->
<div class="flex items-center gap-6 p-6 border border-outline-variant bg-surface-container-low shadow-sm">
<!-- photo: Thin slices of premium spicy calabrese salami -->
<img alt="Thin slices of premium spicy calabrese salami" class="w-20 h-20 object-cover border border-on-surface" src="https://placehold.co/600x400/e4e2e1/1b1c1c?text=The+Ironworks+Margherita"/>
<div class="flex-grow">
<p class="font-label-bold">Calabrese Salami</p>
<p class="text-label-sm text-on-surface-variant italic">Spicy, cured in-house</p>
<div class="flex items-center gap-3 mt-3">
<button @click="if(toppings.calabrese > 0) toppings.calabrese--" class="w-8 h-8 rounded-full border border-outline flex items-center justify-center stamped-effect hover:bg-surface-container"><span class="material-symbols-outlined text-[18px]">remove</span></button>
<span class="font-label-bold" x-text="toppings.calabrese">0</span>
<button @click="toppings.calabrese++" class="w-8 h-8 rounded-full border border-on-surface bg-primary text-on-primary flex items-center justify-center stamped-effect hover:bg-primary-container hover:text-on-primary-container"><span class="material-symbols-outlined text-[18px]">add</span></button>
</div>
</div>
</div>
<!-- Pancetta Card -->
<div class="flex items-center gap-6 p-6 border border-outline-variant bg-surface-container-low shadow-sm">
<!-- photo: Strips of thick-cut smoked pancetta -->
<img alt="Strips of thick-cut smoked pancetta" class="w-20 h-20 object-cover border border-on-surface" src="https://placehold.co/600x400/e4e2e1/1b1c1c?text=The+Ironworks+Margherita"/>
<div class="flex-grow">
<p class="font-label-bold">Smoked Pancetta</p>
<p class="text-label-sm text-on-surface-variant italic">+£2.50</p>
<div class="flex items-center gap-3 mt-3">
<button @click="if(toppings.pancetta > 0) toppings.pancetta--" class="w-8 h-8 rounded-full border border-outline flex items-center justify-center stamped-effect hover:bg-surface-container"><span class="material-symbols-outlined text-[18px]">remove</span></button>
<span class="font-label-bold" x-text="toppings.pancetta">0</span>
<button @click="toppings.pancetta++" class="w-8 h-8 rounded-full border border-on-surface bg-primary text-on-primary flex items-center justify-center stamped-effect hover:bg-primary-container hover:text-on-primary-container"><span class="material-symbols-outlined text-[18px]">add</span></button>
</div>
</div>
</div>
</div>
</section>
<!-- Mobile-only Summary/Manifest -->
<div class="lg:hidden">
<div class="bg-surface-container-low border-2 border-dashed border-outline p-8 mt-12">
<div class="flex items-center gap-2 mb-4 text-primary">
<span class="material-symbols-outlined">analytics</span>
<p class="font-label-bold text-label-bold uppercase tracking-widest">Macro-Nutrient Calculator</p>
</div>
<div class="grid grid-cols-4 gap-4 text-center">
<div class="space-y-1">
<p class="text-[10px] text-on-surface-variant uppercase font-bold">Calories</p>
<p class="font-headline-md text-headline-md text-on-surface">840</p>
</div>
<div class="space-y-1">
<p class="text-[10px] text-on-surface-variant uppercase font-bold">Protein</p>
<p class="font-headline-md text-headline-md text-on-surface">32g</p>
</div>
<div class="space-y-1">
<p class="text-[10px] text-on-surface-variant uppercase font-bold">Carbs</p>
<p class="font-headline-md text-headline-md text-on-surface">98g</p>
</div>
<div class="space-y-1">
<p class="text-[10px] text-on-surface-variant uppercase font-bold">Fat</p>
<p class="font-headline-md text-headline-md text-on-surface">38g</p>
</div>
</div>
<p class="text-[10px] text-on-surface-variant italic mt-4 text-center">*Estimates based on current forging selections</p>
</div>
<div class="bg-surface border-2 border-on-surface p-8 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)] mt-12">
<p class="font-label-bold text-label-bold uppercase tracking-widest text-on-surface-variant mb-6">Forge Manifest</p>
<div class="space-y-4 mb-8">
<div class="flex justify-between font-body-md">
<span>Ironworks Margherita (12")</span>
<span>£15.00</span>
</div>
<div class="flex justify-between font-body-md text-on-surface-variant italic">
<span>+ 48hr Sourdough</span>
<span>Included</span>
</div>
<div class="flex justify-between font-body-md text-on-surface-variant italic">
<span>+ Calabrese Salami</span>
<span>Included</span>
</div>
</div>
<!-- Quantity stepper -->
<div class="flex items-center gap-4 mb-6">
<p class="font-label-bold text-label-bold uppercase tracking-widest text-on-surface-variant">Qty</p>
<button @click="if(qty > 1) qty--" class="w-8 h-8 rounded-full border border-outline flex items-center justify-center stamped-effect hover:bg-surface-container"><span class="material-symbols-outlined text-[18px]">remove</span></button>
<span class="font-label-bold" x-text="qty">1</span>
<button @click="qty++" class="w-8 h-8 rounded-full border border-on-surface bg-primary text-on-primary flex items-center justify-center stamped-effect hover:bg-primary-container hover:text-on-primary-container"><span class="material-symbols-outlined text-[18px]">add</span></button>
</div>
<div class="border-t-2 border-dashed border-outline-variant pt-6 mb-2">
<div class="flex justify-between items-end">
<div>
<p class="text-label-sm text-on-surface-variant uppercase font-bold">Total Estimate</p>
<p class="font-display text-headline-lg text-primary" x-text="'£' + total">£15.00</p>
</div>
<a href="{{ route('checkout') }}" class="bg-primary text-on-primary py-4 px-8 flex items-center gap-4 border-b-4 border-primary-container stamped-effect forge-button-ritual hover:bg-primary-container hover:text-on-primary-container transition-colors">
<span class="font-label-bold uppercase text-[16px]">Forge My Pizza</span>
<span class="material-symbols-outlined">arrow_forward</span>
</a>
</div>
</div>
</div>
</div>
</div>
<!-- Right Column: Sidebar (Summary & Pairings) -->
<aside class="hidden lg:block lg:col-span-4 mt-12 lg:mt-0">
<div class="lg:top-28 space-y-8 sticky">
<!-- Macro-Nutrient Calculator -->
<div class="bg-surface-container-low border-2 border-dashed border-outline p-8">
<div class="flex items-center gap-2 mb-4 text-primary">
<span class="material-symbols-outlined">analytics</span>
<p class="font-label-bold text-label-bold uppercase tracking-widest">Macro-Nutrient Calculator</p>
</div>
<div class="grid grid-cols-4 gap-4 text-center">
<div class="space-y-1">
<p class="text-[10px] text-on-surface-variant uppercase font-bold">Calories</p>
<p class="font-headline-md text-headline-md text-on-surface">840</p>
</div>
<div class="space-y-1">
<p class="text-[10px] text-on-surface-variant uppercase font-bold">Protein</p>
<p class="font-headline-md text-headline-md text-on-surface">32g</p>
</div>
<div class="space-y-1">
<p class="text-[10px] text-on-surface-variant uppercase font-bold">Carbs</p>
<p class="font-headline-md text-headline-md text-on-surface">98g</p>
</div>
<div class="space-y-1">
<p class="text-[10px] text-on-surface-variant uppercase font-bold">Fat</p>
<p class="font-headline-md text-headline-md text-on-surface">38g</p>
</div>
</div>
<p class="text-[10px] text-on-surface-variant italic mt-4 text-center">*Estimates based on current forging selections</p>
</div>
<!-- Forge Manifest -->
<div class="bg-surface border-2 border-on-surface p-8 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)]">
<p class="font-label-bold text-label-bold uppercase tracking-widest text-on-surface-variant mb-6">Forge Manifest</p>
<div class="space-y-4 mb-8">
<div class="flex justify-between font-body-md">
<span>Ironworks Margherita (12")</span>
<span>£15.00</span>
</div>
<div class="flex justify-between font-body-md text-on-surface-variant italic">
<span>+ 48hr Sourdough</span>
<span>Included</span>
</div>
<div class="flex justify-between font-body-md text-on-surface-variant italic">
<span>+ Calabrese Salami</span>
<span>Included</span>
</div>
</div>
<!-- Quantity stepper -->
<div class="flex items-center gap-4 mb-6">
<p class="font-label-bold text-label-bold uppercase tracking-widest text-on-surface-variant">Qty</p>
<button @click="if(qty > 1) qty--" class="w-8 h-8 rounded-full border border-outline flex items-center justify-center stamped-effect hover:bg-surface-container"><span class="material-symbols-outlined text-[18px]">remove</span></button>
<span class="font-label-bold" x-text="qty">1</span>
<button @click="qty++" class="w-8 h-8 rounded-full border border-on-surface bg-primary text-on-primary flex items-center justify-center stamped-effect hover:bg-primary-container hover:text-on-primary-container"><span class="material-symbols-outlined text-[18px]">add</span></button>
</div>
<div class="border-t-2 border-dashed border-outline-variant pt-6 mb-2">
<div class="flex justify-between items-end">
<div>
<p class="text-label-sm text-on-surface-variant uppercase font-bold">Total Estimate</p>
<p class="font-display text-headline-lg text-primary" x-text="'£' + total">£15.00</p>
</div>
<a href="{{ route('checkout') }}" class="bg-primary text-on-primary py-4 px-8 flex items-center gap-4 border-b-4 border-primary-container stamped-effect forge-button-ritual hover:bg-primary-container hover:text-on-primary-container transition-colors">
<span class="font-label-bold uppercase text-[16px]">Forge My Pizza</span>
<span class="material-symbols-outlined">arrow_forward</span>
</a>
</div>
</div>
</div>
<!-- Expert Pairings -->
<div class="bg-surface-container p-6 border border-on-surface">
<p class="font-label-bold text-label-bold uppercase tracking-widest text-primary mb-6">Expert Pairings</p>
<div class="space-y-4">
<div class="bg-surface border border-outline flex gap-4 p-3 group cursor-pointer hover:border-on-surface transition-colors items-center">
<!-- photo: A cold beer -->
<img alt="A cold beer" class="w-20 h-20 object-cover" src="https://placehold.co/600x400/e4e2e1/1b1c1c?text=The+Ironworks+Margherita"/>
<div class="flex-grow">
<p class="font-label-bold">Foundry Pale Ale</p>
<p class="text-label-sm text-on-surface-variant mb-3">Pairs perfectly with crust</p>
<button class="bg-primary text-on-primary px-3 py-1.5 font-label-bold uppercase text-[10px] stamped-effect hover:bg-primary-container hover:text-on-primary-container transition-colors shadow-sm">+ Add £5.50</button>
</div>
</div>
<div class="bg-surface border border-outline flex gap-4 p-3 group cursor-pointer hover:border-on-surface transition-colors items-center">
<!-- photo: Garlic knots -->
<img alt="Garlic knots" class="w-20 h-20 object-cover" src="https://placehold.co/600x400/e4e2e1/1b1c1c?text=The+Ironworks+Margherita"/>
<div class="flex-grow">
<p class="font-label-bold">Garlic Knots (4)</p>
<p class="text-label-sm text-on-surface-variant mb-3">Customer favorite</p>
<button class="bg-primary text-on-primary px-3 py-1.5 font-label-bold uppercase text-[10px] stamped-effect hover:bg-primary-container hover:text-on-primary-container transition-colors shadow-sm">+ Add £5.00</button>
</div>
</div>
</div>
</div>
</div>
</aside>
</div>
<!-- Mobile Sticky Checkout -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 p-4 bg-surface border-t-2 border-on-surface z-50">
<div class="max-w-md mx-auto flex items-center gap-4">
<div class="flex-grow">
<p class="text-label-sm text-on-surface-variant uppercase font-bold">Total Estimate</p>
<p class="font-headline-md text-primary" x-text="'£' + total">£15.00</p>
</div>
<a href="{{ route('checkout') }}" class="flex-[2] bg-primary text-on-primary py-4 px-6 flex items-center justify-between border-b-[3px] border-primary-container stamped-effect hover:bg-primary-container hover:text-on-primary-container transition-colors">
<span class="font-label-bold uppercase text-[16px]">Forge My Pizza</span>
<span class="material-symbols-outlined">arrow_forward</span>
</a>
</div>
</div>
</div>
@endsection
