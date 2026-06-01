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
<p class="font-body-lg text-body-lg text-on-surface-variant mb-8 max-w-md border-l-4 border-primary pl-4">Forged in fire, crafted with tradition. Experience pizza built with the raw power of the industrial age and the soul of classic Italian heritage.</p>
<a class="bg-primary-container text-on-primary border-b border-[#D4AF37] px-8 py-4 font-label-bold text-label-bold uppercase tracking-wider hover:bg-primary transition-colors w-fit shadow-[inset_0_0_0_1px_rgba(255,255,255,0.2)] inline-block" href="{{ route('menu') }}">
                    Explore the Ledger
                </a>
</div>
<div class="flex-1 w-full h-full relative border-l-2 border-on-surface hidden md:block">
<!-- data-alt: A close-up, high-quality photograph of an artisan wood-fired pizza resting on a rustic, industrial metal prep table. The pizza features a perfectly charred, blistered crust, rich oxblood red tomato sauce, and melted mozzarella. The lighting is dramatic and moody, emphasizing the textures of the charred crust and the heavy industrial setting. The overall style reflects an industrial minimal aesthetic with raw materials and high contrast. -->
<img alt="Wood-fired pizza" class="w-full h-full object-cover object-center" src="https://placehold.co/800x600/e4e2e1/1b1c1c?text=Wood+Fired+Pizza"/>
<!-- Overlay for structural depth -->
<div class="absolute inset-0 border-8 border-surface pointer-events-none mix-blend-overlay opacity-50"></div>
</div>
</section>
<!-- Base of Operations (Locations/About) -->
<section class="py-margin-desktop px-margin-mobile md:px-margin-desktop border-b-4 border-double border-on-surface" id="locations">
<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter items-start">
<div class="col-span-1 md:col-span-5">
<h3 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface mb-4 border-b-2 border-on-surface pb-2">THE FOUNDRY</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-6">Our flagship location operates out of a converted 1920s steel mill. Here, we stoke the fires daily, hammering out dough and assembling ingredients with mechanical precision.</p>
<ul class="space-y-4 border-t-2 border-on-surface pt-4">
<li class="flex items-center gap-3 text-on-surface">
<span class="material-symbols-outlined text-primary-container">location_on</span>
<span class="font-label-bold text-label-bold uppercase">156 &amp; 158 Fortess Road, Tufnell Park, London, NW5 2HP</span>
</li>
<li class="flex items-center gap-3 text-on-surface">
<span class="material-symbols-outlined text-primary-container">schedule</span>
<span class="font-label-bold text-label-bold uppercase">Sun&#8211;Thu: 16:00&#8211;22:45</span>
</li>
<li class="flex items-center gap-3 text-on-surface">
<span class="material-symbols-outlined text-primary-container">schedule</span>
<span class="font-label-bold text-label-bold uppercase">Fri&#8211;Sat: 16:00&#8211;23:15</span>
</li>
</ul>
</div>
<div class="col-span-1 md:col-span-7 h-80 md:h-[400px] border-2 border-on-surface relative bg-surface-container-highest p-2">
<!-- data-alt: A wide shot of a rugged, industrial restaurant interior reminiscent of an early 20th-century workshop or foundry. The space features exposed brick walls, heavy steel beams, and vintage hanging factory lights. Diners are seated at solid wood and iron tables. The image is processed in stark black and white with high contrast, aligning with an industrial minimalist aesthetic, conveying permanence and authority. -->
<img alt="Restaurant Interior" class="w-full h-full object-cover filter grayscale contrast-125" src="https://placehold.co/700x400/e4e2e1/1b1c1c?text=Restaurant+Interior"/>
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
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
<!-- Card 1 -->
<div class="border-2 border-on-surface bg-surface flex flex-col group relative">
<div class="absolute -top-3 -right-3 bg-secondary-container text-on-secondary-container font-label-bold text-label-sm px-3 py-1 rounded-full border border-on-surface z-10">
                        FEATURED
                    </div>
<div class="h-48 border-b-2 border-on-surface overflow-hidden p-1">
<!-- data-alt: A high-contrast, top-down view of a classic pepperoni pizza sitting on a stark white background. The pizza has a thick, charred crust and deep red pepperoni slices that glisten with oil. Initially presented in greyscale, the image emphasizes the geometric perfection of the round pie and the heavy, utilitarian style of the food presentation. -->
<img alt="Pepperoni Pizza" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Pizza"/>
</div>
<div class="p-6 flex-1 flex flex-col">
<h4 class="font-headline-md text-headline-md text-on-surface border-b border-outline-variant pb-2 mb-4">The Foreman</h4>
<p class="font-body-md text-body-md text-on-surface-variant flex-grow">Double-smoked pepperoni, crushed San Marzano tomatoes, whole milk mozzarella, hot honey drizzle.</p>
<div class="mt-6 flex justify-between items-center">
<span class="font-label-bold text-label-bold text-primary-container">XXIV</span>
<button class="text-on-surface hover:text-primary transition-colors">
<span class="material-symbols-outlined">add_circle</span>
</button>
</div>
</div>
</div>
<!-- Card 2 -->
<div class="border-2 border-on-surface bg-surface flex flex-col group md:mt-12">
<div class="h-48 border-b-2 border-on-surface overflow-hidden p-1">
<!-- data-alt: A minimalist overhead photograph of a classic Margherita pizza on a raw metal tray. The composition highlights the stark contrast between the bright white fresh mozzarella, the deep oxblood red of the tomato sauce, and the vibrant green basil leaves. The lighting is harsh and direct, casting sharp shadows that fit a brutalist, industrial aesthetic. -->
<img alt="Margherita Pizza" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Pizza"/>
</div>
<div class="p-6 flex-1 flex flex-col">
<h4 class="font-headline-md text-headline-md text-on-surface border-b border-outline-variant pb-2 mb-4">The Blueprint</h4>
<p class="font-body-md text-body-md text-on-surface-variant flex-grow">Our foundational pie. Crushed tomatoes, fresh mozzarella, torn basil, extra virgin olive oil, sea salt.</p>
<div class="mt-6 flex justify-between items-center">
<span class="font-label-bold text-label-bold text-primary-container">XVIII</span>
<button class="text-on-surface hover:text-primary transition-colors">
<span class="material-symbols-outlined">add_circle</span>
</button>
</div>
</div>
</div>
<!-- Card 3 -->
<div class="border-2 border-on-surface bg-surface flex flex-col group">
<div class="h-48 border-b-2 border-on-surface overflow-hidden p-1">
<!-- data-alt: A detailed shot of a white-sauce pizza topped with roasted wild mushrooms and truffle oil, set against a dark, textured slate background. The earthy tones of the mushrooms stand out, while the thick, bubbled crust suggests a high-heat firing process. The image conveys a sense of rich, hearty sustenance typical of a hard day's work. -->
<img alt="Mushroom Pizza" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Pizza"/>
</div>
<div class="p-6 flex-1 flex flex-col">
<h4 class="font-headline-md text-headline-md text-on-surface border-b border-outline-variant pb-2 mb-4">The Ironworker</h4>
<p class="font-body-md text-body-md text-on-surface-variant flex-grow">Roasted cremini mushrooms, caramelized onions, garlic confit, provolone, white truffle oil.</p>
<div class="mt-6 flex justify-between items-center">
<span class="font-label-bold text-label-bold text-primary-container">XXII</span>
<button class="text-on-surface hover:text-primary transition-colors">
<span class="material-symbols-outlined">add_circle</span>
</button>
</div>
</div>
</div>
</div>
</section>
<!-- Map Section -->
<section class="border-b-2 border-on-surface bg-surface h-[500px] relative w-full overflow-hidden">
<!-- Placeholder for interactive map, using a styled div to represent the industrial cartography feel -->
<div class="absolute inset-0 bg-[#e5e3df] flex items-center justify-center opacity-80" data-location="Tufnell Park, London">
<div class="w-full h-full border-4 border-on-surface opacity-20 pointer-events-none absolute inset-0 mix-blend-multiply" style="background-image: repeating-linear-gradient(45deg, #1b1c1c 25%, transparent 25%, transparent 75%, #1b1c1c 75%, #1b1c1c), repeating-linear-gradient(45deg, #1b1c1c 25%, #e5e3df 25%, #e5e3df 75%, #1b1c1c 75%, #1b1c1c); background-position: 0 0, 10px 10px; background-size: 20px 20px;"></div>
<div class="text-center z-10 p-8 border-2 border-on-surface bg-surface shadow-[4px_4px_0px_#1b1c1c]">
<span class="material-symbols-outlined text-display text-primary-container mb-2">location_on</span>
<h4 class="font-headline-md text-headline-md text-on-surface uppercase tracking-widest">Locate Us</h4>
<p class="font-label-bold text-label-bold text-on-surface-variant mt-2">156 &amp; 158 Fortess Road, Tufnell Park</p>
</div>
</div>
</section>
@endsection
