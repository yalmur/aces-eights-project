@extends('layouts.admin')
@section('content')

<!-- Dashboard Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
<div class="flex-1">
<h1 class="font-display text-display text-on-surface leading-tight">IN-STORE ORDERS</h1>
<div class="double-divider max-w-2xl"></div>
<p class="text-on-surface-variant font-body-lg italic">Command center for the daily service ledger.</p>
</div>
<div class="flex flex-col md:flex-row items-stretch md:items-center gap-4">
<div class="relative group">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
<input class="ledger-input pl-10 pr-4 py-2 w-full md:w-64 font-body-md" placeholder="Search order # or table..." type="text"/>
</div>
<button class="bg-primary text-on-primary font-label-bold py-3 px-6 industrial-border flex items-center justify-center gap-2 hover:bg-on-primary-fixed-variant transition-colors active:scale-95 duration-150">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add</span>
                    NEW ORDER
                </button>
</div>
</div>
<!-- Orders Grid (Bento Style for Desktop, List for Mobile) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
<!-- Order 402 -->
<div class="lg:col-span-6 xl:col-span-4 bg-surface industrial-border p-6 flex flex-col gap-4 hover:bg-surface-container transition-colors">
<div class="flex justify-between items-start">
<div>
<span class="font-label-bold text-secondary text-label-sm uppercase tracking-widest">Order #402</span>
<h3 class="font-headline-md text-on-surface">TABLE 12 — Giovanni M.</h3>
</div>
<span class="bg-secondary-container text-on-secondary-container font-label-bold text-[10px] px-2 py-1 industrial-border uppercase">Cooking</span>
</div>
<div class="border-y border-outline-variant py-4 space-y-2">
<div class="flex justify-between text-body-md">
<span>1x The Industrial Queen (Margherita)</span>
<span class="font-bold">$18.00</span>
</div>
<div class="flex justify-between text-body-md">
<span>2x Steel Mill Garlic Knots</span>
<span class="font-bold">$18.00</span>
</div>
</div>
<div class="flex justify-between items-center mt-auto">
<span class="font-display text-headline-md text-primary">$36.00</span>
<div class="flex gap-2">
<button class="p-2 border border-outline hover:bg-surface-variant transition-colors" title="View Receipt">
<span class="material-symbols-outlined text-on-surface">receipt_long</span>
</button>
<button class="bg-on-surface text-surface px-4 py-2 font-label-bold hover:bg-primary transition-colors">
                            UPDATE STATUS
                        </button>
</div>
</div>
</div>
<!-- Order 403 -->
<div class="lg:col-span-6 xl:col-span-4 bg-surface industrial-border p-6 flex flex-col gap-4 hover:bg-surface-container transition-colors">
<div class="flex justify-between items-start">
<div>
<span class="font-label-bold text-secondary text-label-sm uppercase tracking-widest">Order #403</span>
<h3 class="font-headline-md text-on-surface">BAR 04 — Sarah L.</h3>
</div>
<span class="bg-primary text-on-primary font-label-bold text-[10px] px-2 py-1 industrial-border uppercase">Accepted</span>
</div>
<div class="border-y border-outline-variant py-4 space-y-2">
<div class="flex justify-between text-body-md text-on-surface">
<span>1x The Ironclad Pepperoni</span>
<span class="font-bold">$21.00</span>
</div>
<p class="text-label-sm text-error font-bold">* NO HONEY DRIZZLE</p>
</div>
<div class="flex justify-between items-center mt-auto">
<span class="font-display text-headline-md text-primary">$21.00</span>
<div class="flex gap-2">
<button class="p-2 border border-outline hover:bg-surface-variant transition-colors" title="View Receipt">
<span class="material-symbols-outlined text-on-surface">receipt_long</span>
</button>
<button class="bg-on-surface text-surface px-4 py-2 font-label-bold hover:bg-primary transition-colors">
                            UPDATE STATUS
                        </button>
</div>
</div>
</div>
<!-- Order 401 -->
<div class="lg:col-span-6 xl:col-span-4 bg-tertiary-fixed industrial-border p-6 flex flex-col gap-4 opacity-80">
<div class="flex justify-between items-start">
<div>
<span class="font-label-bold text-on-tertiary-fixed-variant text-label-sm uppercase tracking-widest">Order #401</span>
<h3 class="font-headline-md text-on-tertiary-fixed">TABLE 08 — Foundry Group</h3>
</div>
<span class="bg-tertiary-container text-on-tertiary-fixed font-label-bold text-[10px] px-2 py-1 industrial-border uppercase">Ready</span>
</div>
<div class="border-y border-on-tertiary-fixed py-4 space-y-2">
<div class="flex justify-between text-body-md">
<span>3x Signature Cheese</span>
<span class="font-bold">$48.00</span>
</div>
<div class="flex justify-between text-body-md">
<span>1x Pitcher Industrial Lager</span>
<span class="font-bold">$16.00</span>
</div>
</div>
<div class="flex justify-between items-center mt-auto">
<span class="font-display text-headline-md text-on-tertiary-fixed">$64.00</span>
<div class="flex gap-2">
<button class="p-2 border border-on-tertiary-fixed hover:bg-tertiary-fixed-dim transition-colors">
<span class="material-symbols-outlined text-on-tertiary-fixed">check_circle</span>
</button>
<button class="bg-on-tertiary-fixed text-tertiary-fixed px-4 py-2 font-label-bold hover:bg-primary hover:text-on-primary transition-colors">
                            COMPLETE
                        </button>
</div>
</div>
</div>
<!-- Quick Stats Section -->
<div class="lg:col-span-12 xl:col-span-4 grid grid-cols-2 gap-4">
<div class="industrial-border p-4 bg-surface text-center">
<span class="font-label-bold text-[10px] text-on-surface-variant block uppercase">Active Orders</span>
<span class="font-display text-4xl text-primary">12</span>
</div>
<div class="industrial-border p-4 bg-surface text-center">
<span class="font-label-bold text-[10px] text-on-surface-variant block uppercase">Avg. Prep Time</span>
<span class="font-display text-4xl text-secondary">18m</span>
</div>
</div>
<!-- Staff Messages/Log -->
<div class="lg:col-span-12 xl:col-span-8 bg-surface-container-highest industrial-border p-6">
<h4 class="font-label-bold text-on-surface mb-4 border-b border-on-surface inline-block uppercase">Service Announcements</h4>
<div class="space-y-3">
<div class="flex gap-3 text-body-md">
<span class="font-bold text-primary">[18:24]</span>
<p><span class="font-bold">KITCHEN:</span> "The Ironclad Pepperoni" low stock on spicy salami.</p>
</div>
<div class="flex gap-3 text-body-md">
<span class="font-bold text-primary">[17:45]</span>
<p><span class="font-bold">MANAGER:</span> Shift change at 19:00. Briefing in the back.</p>
</div>
</div>
</div>
</div>

@endsection
