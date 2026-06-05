@extends('layouts.app')
@section('content')
{{-- About Us page adapted from stitch --}}

<!-- Hero Section -->
<section class="relative bg-surface-container-low border-b-2 border-outline overflow-hidden">
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-20 md:py-32 grid grid-cols-1 md:grid-cols-12 gap-gutter items-center">
<div class="md:col-span-7 space-y-6">
<span class="font-label-bold text-label-bold text-primary uppercase tracking-widest block">EST. 2010</span>
<h1 class="font-display text-display md:text-[64px] text-primary leading-[1.1]">Authentic Industrial Italian pizza in the heart of London.</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">
    Handcrafted sourdough, fired by tradition, and served within the rugged walls of Tufnell Park. We are more than a pizzeria; we are a workshop of flavor.
</p>
<div class="pt-4 flex flex-col sm:flex-row gap-4">
<a href="https://www.acesandeightssaloonbar.com/booking/" target="_blank" rel="noopener" class="bg-primary text-on-primary font-label-bold text-label-bold px-8 py-4 uppercase tracking-wider border-b-4 border-primary-fixed-dim hover:bg-on-primary-fixed-variant transition-all active:translate-y-1 active:border-b-0 text-center">
    Book a Table
</a>
<div class="flex items-center gap-2 font-label-bold text-label-bold text-on-surface p-4 border-2 border-outline">
<span class="material-symbols-outlined text-primary">location_on</span>
    NW5 2HP, LONDON
</div>
</div>
</div>
<div class="md:col-span-5 relative">
<div class="border-2 border-primary p-2">
{{-- A cinematic, low-angle shot of a roaring wood-fired pizza oven with glowing orange embers and dancing flames --}}
<img class="w-full h-[500px] object-cover grayscale-[0.2] hover:grayscale-0 transition-all duration-700" src="https://placehold.co/800x600/1b1c1c/fcf9f8?text=Wood+Fired+Oven" alt="Wood-fired pizza oven"/>
</div>
<div class="absolute -bottom-6 -left-6 bg-secondary-container text-on-secondary-container p-6 border-2 border-outline hidden lg:block">
<p class="font-display text-headline-md leading-none">48H</p>
<p class="font-label-bold text-label-sm uppercase">Slow Ferment</p>
</div>
</div>
</div>
</section>

<!-- Divider -->
<div class="w-full h-2 border-t border-b border-outline my-0"></div>

<!-- Our Story Section: Industrial Heritage -->
<section class="bg-surface py-20">
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
<div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-start">
<div class="sticky top-32">
<h2 class="font-headline-lg text-headline-lg text-primary mb-8 underline decoration-double decoration-outline-variant underline-offset-8">Our Story</h2>
<div class="space-y-6 font-body-lg text-body-lg text-on-surface">
<p>Born from the hum of machinery and the heat of the forge, Aces &amp; Eights was founded on a simple premise: that the best food is made by hand, with tools that have stood the test of time.</p>
<p>Our journey began in 2010 on Fortess Road. We saw a kinship between the rugged, utilitarian spirit of early 20th-century American industry and the uncompromising precision of traditional Italian pizza-making.</p>
<p class="border-l-4 border-primary pl-6 italic text-on-surface-variant font-body-md">"We don't just bake; we assemble excellence from the finest raw materials."</p>
</div>
</div>
<div class="grid grid-cols-1 gap-8">
<div class="border-2 border-outline p-4 group overflow-hidden">
{{-- Interior of a modern industrial pizzeria featuring exposed brick walls, vintage Edison bulb lighting, and heavy iron-framed furniture --}}
<img class="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/700x500/e4e2e1/1b1c1c?text=Restaurant+Interior" alt="Restaurant interior"/>
<p class="mt-4 font-label-bold text-label-sm text-on-surface-variant uppercase tracking-tighter italic">The Workshop, Fortess Road</p>
</div>
<div class="bg-surface-container-high p-12 border-2 border-outline relative">
<span class="material-symbols-outlined text-[80px] text-outline-variant absolute top-4 right-4 opacity-30">history</span>
<h3 class="font-headline-md text-headline-md text-primary mb-4">A Legacy of Steam</h3>
<p class="font-body-md text-body-md text-on-surface">Our space is built on the foundations of London's industrial past. We've preserved the raw architecture to remind us that quality requires a solid foundation.</p>
</div>
</div>
</div>
</div>
</section>

<!-- Our Craft Section: Bento Grid -->
<section class="bg-surface-container-lowest py-20 border-y-[3px] border-double border-outline">
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
<div class="text-center mb-16">
<h2 class="font-display text-display text-primary uppercase">The Components of Quality</h2>
<div class="w-24 h-1 bg-primary mx-auto mt-4"></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
<!-- Component 1: Flour -->
<div class="md:col-span-8 bg-surface border-2 border-outline p-8 flex flex-col md:flex-row gap-8 items-center group">
<div class="w-full md:w-1/2 overflow-hidden border border-outline">
{{-- Close-up macro photography of double-fermented sourdough pizza crust showing intricate air bubbles and a perfectly charred leopard spot pattern --}}
<img class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Ingredient" alt="Sourdough crust"/>
</div>
<div class="w-full md:w-1/2">
<h3 class="font-headline-md text-headline-md text-primary mb-2">Double-Fermented Sourdough</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Our signature dough undergoes a rigorous 48-hour cold fermentation process. This patience yields a complex flavor profile and a crust that is both airy and resilient.</p>
</div>
</div>
<!-- Component 2: Tomatoes -->
<div class="md:col-span-4 bg-primary text-on-primary p-8 flex flex-col justify-between border-2 border-primary">
<span class="material-symbols-outlined text-4xl">verified</span>
<div>
<h3 class="font-headline-md text-headline-md mb-2">San Marzano D.O.P</h3>
<p class="font-body-md text-body-md text-on-primary-container">Grown in the volcanic soil of Mount Vesuvius, our tomatoes are hand-picked and crushed to preserve their vibrant acidity and sweetness.</p>
</div>
</div>
<!-- Component 3: The Fire -->
<div class="md:col-span-4 bg-surface-container border-2 border-outline p-8 group">
<div class="border-b-2 border-outline pb-4 mb-4 flex justify-between items-end">
<h3 class="font-headline-md text-headline-md text-primary">400°C Fire</h3>
<span class="material-symbols-outlined text-primary">local_fire_department</span>
</div>
<p class="font-body-md text-body-md text-on-surface">We blast our pizzas at extreme temperatures for precisely 90 seconds, sealing in moisture while achieving the perfect char.</p>
</div>
<!-- Component 4: Mozzarella -->
<div class="md:col-span-8 bg-surface border-2 border-outline p-8 flex flex-col md:flex-row-reverse gap-8 items-center group">
<div class="w-full md:w-1/2 overflow-hidden border border-outline">
{{-- Studio photography of fresh Buffalo Mozzarella being torn by hand, revealing its creamy, fibrous interior --}}
<img class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Ingredient" alt="Fior di latte mozzarella"/>
</div>
<div class="w-full md:w-1/2 text-right md:text-left">
<h3 class="font-headline-md text-headline-md text-primary mb-2">Artisan Fior di Latte</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Delivered fresh, our mozzarella provides the creamy, clean finish that balances the bold acidity of our tomato base.</p>
</div>
</div>
</div>
</div>
</section>

<!-- Our Location Section -->
<section class="bg-surface-container-high py-20">
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
<div class="bg-surface border-2 border-primary overflow-hidden grid grid-cols-1 lg:grid-cols-2">
<div class="p-12 md:p-16 flex flex-col justify-center">
<h2 class="font-display text-headline-lg text-primary uppercase mb-4">The Fortess Road Shop</h2>
<div class="space-y-6">
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary mt-1">location_on</span>
<div>
<p class="font-label-bold text-label-bold">Address</p>
<p class="font-body-lg text-body-lg">156 &amp; 158 Fortess Road, Tufnell Park<br/>London, NW5 2HP</p>
</div>
</div>
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary mt-1">schedule</span>
<div>
<p class="font-label-bold text-label-bold">Opening Hours</p>
<p class="font-body-lg text-body-lg">Sun–Thu: 16:00–22:45<br/>Fri–Sat: 16:00–23:15</p>
</div>
</div>
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary mt-1">call</span>
<div>
<p class="font-label-bold text-label-bold">Phone</p>
<p class="font-body-lg text-body-lg"><a href="tel:+4402074854033" class="hover:text-primary transition-colors">+44 020 7485 4033</a></p>
</div>
</div>
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary mt-1">mail</span>
<div>
<p class="font-label-bold text-label-bold">Email</p>
<p class="font-body-lg text-body-lg"><a href="mailto:nw5pizza@gmail.com" class="hover:text-primary transition-colors">nw5pizza@gmail.com</a></p>
</div>
</div>
<a href="https://maps.google.com/?q=156+Fortess+Road+London+NW5+2HP" target="_blank" rel="noopener" class="inline-block w-full md:w-auto bg-primary text-on-primary font-label-bold text-label-bold px-10 py-4 uppercase tracking-widest hover:opacity-90 transition-opacity text-center">
    Get Directions
</a>
</div>
</div>
<div class="h-[400px] lg:h-auto min-h-[500px] border-l-0 lg:border-l-2 border-primary relative grayscale">
<div class="absolute inset-0 bg-primary/10 mix-blend-multiply"></div>
{{-- Location map placeholder --}}
<img alt="Location Map" class="w-full h-full object-cover" src="https://placehold.co/600x400/e4e2e1/1b1c1c?text=Photo"/>
<div class="absolute inset-0 flex items-center justify-center">
<div class="bg-primary text-on-primary p-4 rounded-full shadow-xl">
<span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">location_on</span>
</div>
</div>
</div>
</div>
</div>
</section>

@endsection
