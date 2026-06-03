@extends('layouts.admin')
@section('content')

<!-- Page Title -->
<section class="mb-12">
<h1 class="font-display text-display text-primary tracking-tighter mb-4 text-center md:text-left">KITCHEN COMMAND</h1>
<div class="double-divider w-full"></div>
<div class="flex flex-wrap justify-between items-center mt-6 gap-4">
<div class="flex gap-4 items-center" x-data="{ s: 0 }" x-init="setInterval(() => s = (s+1)%60, 1000)">
<span class="font-label-bold text-label-bold bg-secondary-container text-on-secondary-container px-4 py-1 border border-on-surface">LIVE STATUS: ACTIVE</span>
<span class="font-label-bold text-label-bold text-on-surface-variant italic">Refreshed: <span x-text="s + 's ago'">0s ago</span></span>
</div>
<div class="flex gap-2">
<button class="border-2 border-on-surface px-6 py-2 font-label-bold text-label-bold hover:bg-surface-container-highest transition-all flex items-center gap-2">
<span class="material-symbols-outlined">print</span> PRINT BATCH
</button>
<button class="bg-primary text-on-primary border-2 border-on-surface px-6 py-2 font-label-bold text-label-bold active:scale-95 transition-all flex items-center gap-2">
<span class="material-symbols-outlined">add_task</span> NEW ORDER
</button>
</div>
</div>
</section>
<!-- Kanban Board -->
<div class="kanban-board">
<!-- Column 1: Order Queue -->
<div class="flex flex-col gap-4">
<div class="flex items-center justify-between border-b-2 border-on-surface pb-2">
<h3 class="font-headline-md text-headline-md text-primary">ORDER QUEUE</h3>
<span id="status-badge-pending_payment" class="bg-outline text-on-primary px-3 py-1 text-label-bold rounded-full transition-all duration-200">3</span>
</div>
<div class="flex flex-col gap-6 industrial-scrollbar max-h-[80vh] overflow-y-auto pr-2">
<!-- Ticket 1 -->
<div class="bg-surface border-2 border-on-surface p-4 flex flex-col gap-4 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)]">
<div class="flex justify-between items-start">
<div>
<p class="font-label-bold text-label-bold text-on-surface-variant">ORDER #1289</p>
<p class="font-headline-md text-headline-md">The "Dead Man's Hand"</p>
</div>
<span class="text-error font-label-bold animate-pulse flex items-center gap-1">
<span class="material-symbols-outlined text-[18px]">timer</span> 08:42
</span>
</div>
<div class="border-t border-outline-variant pt-2 text-body-md">
<p>• 1x Large Buffalo Pizza (Extra Spice)</p>
<p>• 2x Garlic Knots</p>
<p>• 1x Peroni 500ml</p>
</div>
<button class="w-full bg-primary text-on-primary py-3 font-label-bold text-label-bold border-b-4 border-on-primary-fixed-variant hover:brightness-110 active:translate-y-1 active:border-b-0 transition-all uppercase tracking-widest">
ACCEPT ORDER
</button>
</div>
<!-- Ticket 2 -->
<div class="bg-surface border-2 border-on-surface p-4 flex flex-col gap-4 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)]">
<div class="flex justify-between items-start">
<div>
<p class="font-label-bold text-label-bold text-on-surface-variant">ORDER #1291</p>
<p class="font-headline-md text-headline-md">Custom Supreme</p>
</div>
<span class="text-on-surface font-label-bold flex items-center gap-1">
<span class="material-symbols-outlined text-[18px]">timer</span> 02:15
</span>
</div>
<div class="border-t border-outline-variant pt-2 text-body-md">
<p>• 2x Medium Supreme Thin Crust</p>
<p>• No Bell Peppers</p>
</div>
<button class="w-full bg-primary text-on-primary py-3 font-label-bold text-label-bold border-b-4 border-on-primary-fixed-variant hover:brightness-110 active:translate-y-1 active:border-b-0 transition-all uppercase tracking-widest">
ACCEPT ORDER
</button>
</div>
</div>
</div>
<!-- Column 2: In the Kitchen -->
<div class="flex flex-col gap-4">
<div class="flex items-center justify-between border-b-2 border-on-surface pb-2">
<h3 class="font-headline-md text-headline-md text-primary">IN THE KITCHEN</h3>
<span id="status-badge-cooking" class="bg-primary text-on-primary px-3 py-1 text-label-bold rounded-full transition-all duration-200">2</span>
</div>
<div class="flex flex-col gap-6 industrial-scrollbar max-h-[80vh] overflow-y-auto pr-2">
<!-- Cooking Ticket -->
<div class="bg-surface border-2 border-on-surface p-4 flex flex-col gap-4 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)] relative overflow-hidden">
<div class="absolute top-0 right-0 p-2">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
</div>
<div class="flex justify-between items-start">
<div>
<p class="font-label-bold text-label-bold text-on-surface-variant">ORDER #1285</p>
<p class="font-headline-md text-headline-md">Meat Lovers Feast</p>
</div>
<span class="text-error font-label-bold flex items-center gap-1">
<span class="material-symbols-outlined text-[18px]">timer</span> 22:10
</span>
</div>
<div class="border-t border-outline-variant pt-2 text-body-md">
<p>• 1x XL Meat Lovers</p>
<p>• 1x Spicy Salami Plate</p>
<p class="text-primary font-bold mt-2">CHEF'S NOTE: EXTRA OVEN TIME</p>
</div>
<div class="w-full bg-surface-container-highest h-2 border border-on-surface mb-2">
<div class="bg-primary h-full w-[75%]"></div>
</div>
<button class="w-full bg-secondary text-on-secondary py-3 font-label-bold text-label-bold border-b-4 border-on-secondary-fixed-variant hover:brightness-110 active:translate-y-1 active:border-b-0 transition-all uppercase tracking-widest">
MARK AS READY
</button>
</div>
</div>
</div>
<!-- Column 3: Ready for Dispatch -->
<div class="flex flex-col gap-4">
<div class="flex items-center justify-between border-b-2 border-on-surface pb-2">
<h3 class="font-headline-md text-headline-md text-primary">READY FOR DISPATCH</h3>
<span id="status-badge-ready" class="bg-secondary text-on-secondary px-3 py-1 text-label-bold rounded-full transition-all duration-200">1</span>
</div>
<div class="flex flex-col gap-6 industrial-scrollbar max-h-[80vh] overflow-y-auto pr-2">
<!-- Dispatch Ticket -->
<div class="bg-secondary-fixed border-2 border-on-surface p-4 flex flex-col gap-4 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)]">
<div class="flex justify-between items-start">
<div>
<p class="font-label-bold text-label-bold text-on-secondary-fixed">ORDER #1280</p>
<p class="font-headline-md text-headline-md">Office Party Pack</p>
</div>
</div>
<div class="border-t border-on-secondary-fixed-variant/20 pt-2 text-body-md text-on-secondary-fixed">
<p>• 5x Large Pepperoni</p>
<p>• 4x 2L Coke</p>
</div>
<div class="flex flex-col gap-2 mt-2">
<label class="font-label-bold text-label-sm text-on-secondary-fixed uppercase opacity-70">Assign Driver</label>
<select class="w-full bg-white border-2 border-on-surface py-2 px-3 font-label-bold text-label-bold">
<option>SELECT DRIVER...</option>
<option>MARCO (ACTIVE)</option>
<option>TONY (IDLE)</option>
<option>SARAH (ON BREAK)</option>
</select>
</div>
<button class="w-full bg-on-surface text-surface py-3 font-label-bold text-label-bold border-b-4 border-primary hover:brightness-125 transition-all uppercase tracking-widest">
DISPATCH ORDER
</button>
</div>
</div>
</div>
<!-- Column 4: Out for Delivery -->
<div class="flex flex-col gap-4">
<div class="flex items-center justify-between border-b-2 border-on-surface pb-2">
<h3 class="font-headline-md text-headline-md text-primary">OUT FOR DELIVERY</h3>
<span id="status-badge-out_for_delivery" class="bg-on-surface text-surface px-3 py-1 text-label-bold rounded-full transition-all duration-200">4</span>
</div>
<div class="flex flex-col gap-6 industrial-scrollbar max-h-[80vh] overflow-y-auto pr-2">
<!-- Out for Delivery Ticket -->
<div class="bg-surface border-2 border-on-surface p-4 flex flex-col gap-4 opacity-75 grayscale-[0.5] shadow-[2px_2px_0px_0px_rgba(27,28,28,1)]">
<div class="flex justify-between items-start">
<div>
<p class="font-label-bold text-label-bold text-on-surface-variant">ORDER #1275</p>
<p class="font-headline-md text-headline-md">Vegetarian Pesto</p>
</div>
</div>
<div class="border-t border-outline-variant pt-2 text-label-sm">
<p class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">person</span> DRIVER: MARCO</p>
<p class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">location_on</span> 422 OAK ST, DOWNTOWN</p>
</div>
<button class="w-full border-2 border-on-surface py-2 font-label-bold text-label-bold hover:bg-surface-container-highest transition-all uppercase">
TRACK MAP
</button>
</div>
<div class="bg-surface border-2 border-on-surface p-4 flex flex-col gap-4 opacity-75 grayscale-[0.5] shadow-[2px_2px_0px_0px_rgba(27,28,28,1)]">
<div class="flex justify-between items-start">
<div>
<p class="font-label-bold text-label-bold text-on-surface-variant">ORDER #1272</p>
<p class="font-headline-md text-headline-md">Double Pepperoni</p>
</div>
</div>
<div class="border-t border-outline-variant pt-2 text-label-sm">
<p class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">person</span> DRIVER: TONY</p>
<p class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">location_on</span> 12B FOUNDRY BLVD</p>
</div>
<button class="w-full border-2 border-on-surface py-2 font-label-bold text-label-bold hover:bg-surface-container-highest transition-all uppercase">
TRACK MAP
</button>
</div>
</div>
</div>
</div>

{{-- Live kitchen updates via Pusher --}}
<div x-data="{
       init() {
         if (window.Echo) {
           window.Echo.private('admin.orders')
             .listen('.OrderStatusUpdated', (data) => {
               // Flash the status badge for the updated status
               const badge = document.getElementById('status-badge-' + data.status)
               if (badge) {
                 badge.classList.add('ring-2', 'ring-yellow-400', 'scale-110')
                 setTimeout(() => badge.classList.remove('ring-2', 'ring-yellow-400', 'scale-110'), 2000)
               }
             })
         }
       }
     }"
     x-init="init()">
</div>

@endsection
