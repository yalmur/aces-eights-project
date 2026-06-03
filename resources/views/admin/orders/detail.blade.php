@extends('layouts.admin')
@section('content')

{{-- Order Header --}}
<section class="flex flex-col gap-6 mb-10">
  <div class="flex justify-between items-end">
    <div>
      <h2 class="font-serif text-3xl font-bold text-on-surface">ORDER #AE-9842</h2>
      <p class="font-sans text-sm text-on-surface-variant mt-1">Customer: <span class="font-bold text-on-surface">Dominic Vitale</span></p>
    </div>
    <div class="bg-secondary-container border-2 border-on-surface px-6 py-2 flex items-center gap-3">
      <span class="material-symbols-outlined text-on-secondary-fixed-variant" style="font-variation-settings:'FILL' 1">restaurant</span>
      <span class="font-mono text-xs font-bold text-on-secondary-fixed-variant uppercase">PREPARING</span>
    </div>
  </div>
  <div class="industrial-divider w-full"></div>
</section>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

  {{-- Left: Order details --}}
  <div class="lg:col-span-8 flex flex-col gap-10">

    {{-- Lifecycle stepper --}}
    <section class="bg-surface-container border-2 border-on-surface p-8 flex flex-col gap-10">
      <h3 class="font-mono text-sm font-bold text-primary uppercase tracking-widest">Order Lifecycle</h3>
      <div class="relative flex items-center justify-between w-full px-4">
        <div class="absolute top-1/2 left-0 w-full h-1 bg-outline-variant -translate-y-1/2 z-0"></div>
        <div class="absolute top-1/2 left-0 w-1/3 h-1 bg-primary -translate-y-1/2 z-0"></div>
        @foreach([
          ['icon'=>'check', 'label'=>'Accepted', 'done'=>true],
          ['icon'=>'restaurant', 'label'=>'Cooking', 'done'=>true, 'active'=>true],
          ['icon'=>'local_shipping', 'label'=>'Dispatch', 'done'=>false],
          ['icon'=>'home', 'label'=>'Delivered', 'done'=>false],
        ] as $step)
        <div class="flex flex-col items-center gap-2 z-10">
          <div class="{{ ($step['done'] ?? false) ? 'bg-primary text-white border-on-surface' : 'bg-surface-container-highest text-on-surface-variant border-outline' }} w-12 h-12 rounded-full border-2 flex items-center justify-center shadow-lg">
            <span class="material-symbols-outlined text-[24px]">{{ $step['icon'] }}</span>
          </div>
          <span class="font-mono text-[10px] {{ ($step['active'] ?? false) ? 'text-primary font-bold' : 'text-on-surface-variant' }} uppercase">{{ $step['label'] }}</span>
        </div>
        @endforeach
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
        <button class="bg-primary-container text-on-primary font-mono text-xs font-bold py-4 px-4 border-b-2 border-secondary-fixed active:scale-95 transition-all hover:bg-primary uppercase">ACCEPT ORDER</button>
        <button class="bg-surface border-2 border-on-surface text-on-surface font-mono text-xs font-bold py-4 px-4 active:scale-95 transition-all hover:bg-surface-container-low uppercase">START COOKING</button>
        <button class="bg-surface border-2 border-on-surface text-on-surface font-mono text-xs font-bold py-4 px-4 active:scale-95 transition-all hover:bg-surface-container-low uppercase">OUT FOR DELIVERY</button>
        <button class="bg-brand-error text-white font-mono text-xs font-bold py-4 px-4 active:scale-95 transition-all hover:opacity-90 uppercase">CANCEL ORDER</button>
      </div>
    </section>

    {{-- Order breakdown --}}
    <section class="border-2 border-on-surface overflow-hidden">
      <div class="bg-on-surface text-surface px-6 py-4 flex justify-between items-center">
        <h3 class="font-serif text-lg font-bold uppercase">Order Breakdown</h3>
        <span class="font-mono text-sm font-bold">Items: 3</span>
      </div>
      <div class="p-8 flex flex-col gap-6 bg-surface-container-lowest">
        <div class="flex justify-between items-start border-b border-outline-variant pb-6">
          <div class="flex flex-col gap-2">
            <span class="font-mono text-sm font-bold">1x THE FULL HOUSE PIZZA (15")</span>
            <div class="flex gap-2">
              <span class="bg-on-primary-fixed-variant text-white text-[10px] px-3 py-1 font-bold">EXTRA SPICY</span>
              <span class="bg-on-primary-fixed-variant text-white text-[10px] px-3 py-1 font-bold">NO OLIVES</span>
            </div>
          </div>
          <span class="font-serif text-xl font-bold">£22.50</span>
        </div>
        <div class="flex justify-between items-start border-b border-outline-variant pb-6">
          <span class="font-mono text-sm font-bold">2x GARLIC BREAD</span>
          <span class="font-serif text-xl font-bold">£11.00</span>
        </div>
        <div class="flex justify-between items-start border-b border-outline-variant pb-6">
          <span class="font-mono text-sm font-bold">1x SAN PELLEGRINO (750ml)</span>
          <span class="font-serif text-xl font-bold">£3.50</span>
        </div>
        <div class="flex flex-col gap-4 pt-4 ml-auto w-full max-w-xs">
          <div class="flex justify-between text-on-surface-variant font-sans text-sm"><span>Subtotal</span><span>£37.00</span></div>
          <div class="flex justify-between text-on-surface-variant font-sans text-sm"><span>Delivery Fee</span><span>£3.50</span></div>
          <div class="flex justify-between pt-4 border-t-2 border-on-surface">
            <span class="font-serif text-lg font-bold uppercase">TOTAL</span>
            <span class="font-serif text-lg font-bold text-primary">£40.50</span>
          </div>
        </div>
      </div>
    </section>
  </div>

  {{-- Right: Sidebar --}}
  <aside class="lg:col-span-4 flex flex-col gap-8">

    {{-- Customer Info --}}
    <div class="border-2 border-on-surface p-6 flex flex-col gap-6 bg-surface shadow-[4px_4px_0px_#1b1c1c]">
      <div class="flex items-center justify-between">
        <h3 class="font-mono text-xs font-bold text-primary uppercase tracking-widest">Customer Info</h3>
        <span class="font-mono text-[10px] text-on-surface-variant underline cursor-pointer">CUSTOMER HISTORY</span>
      </div>
      <div class="flex flex-col gap-4">
        <div class="flex items-start gap-4">
          <span class="material-symbols-outlined text-primary mt-1">location_on</span>
          <div>
            <p class="font-sans text-sm font-bold">Shipping Address</p>
            <p class="font-sans text-sm text-on-surface-variant">42 Industrial Way, London, NW5 2HP</p>
          </div>
        </div>
        <div class="flex items-center justify-between bg-surface-container-low p-4 border border-outline-variant">
          <div class="flex items-center gap-4">
            <span class="material-symbols-outlined text-primary">phone</span>
            <p class="font-sans text-sm font-bold">+44 7700 900123</p>
          </div>
          <button class="bg-primary text-on-primary p-2 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-[20px]">call</span>
          </button>
        </div>
      </div>
    </div>

    {{-- Dispatch Control --}}
    <div class="border-2 border-on-surface p-6 flex flex-col gap-6 bg-surface shadow-[4px_4px_0px_#1b1c1c]">
      <h3 class="font-mono text-xs font-bold text-primary uppercase tracking-widest">Dispatch Control</h3>
      <div class="flex flex-col gap-5">
        <div class="flex flex-col gap-2">
          <label class="font-mono text-[10px] text-on-surface-variant uppercase tracking-wider">Assign Driver</label>
          <select class="industrial-border-b w-full font-sans text-lg font-bold py-3">
            <option>Not Assigned</option>
            <option>Marco Rossi</option>
            <option>Luca Moretti</option>
          </select>
        </div>
        <div class="flex justify-between items-center bg-surface-container-low p-4">
          <label class="font-mono text-[10px] text-on-surface-variant uppercase tracking-wider">Est. Delivery</label>
          <div class="flex items-center gap-4" x-data="{ mins: 45 }">
            <button @click="mins = Math.max(15, mins - 5)" class="w-8 h-8 flex items-center justify-center border-2 border-on-surface hover:bg-on-surface hover:text-surface transition-colors">
              <span class="material-symbols-outlined text-[18px]">remove</span>
            </button>
            <span class="font-serif text-xl font-bold min-w-[70px] text-center" x-text="mins + ' MIN'"></span>
            <button @click="mins = Math.min(90, mins + 5)" class="w-8 h-8 flex items-center justify-center border-2 border-on-surface hover:bg-on-surface hover:text-surface transition-colors">
              <span class="material-symbols-outlined text-[18px]">add</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Admin Notes --}}
    <section class="border-2 border-on-surface p-6 flex flex-col gap-4 bg-surface shadow-[4px_4px_0px_#1b1c1c]">
      <h3 class="font-mono text-xs font-bold text-primary uppercase tracking-widest">Admin & Kitchen Notes</h3>
      <textarea class="industrial-border-b w-full min-h-[120px] font-sans text-sm resize-none p-2 italic bg-surface-container-lowest" placeholder="Add notes for the kitchen or driver..."></textarea>
      <div class="flex justify-end">
        <button class="font-mono text-xs font-bold text-on-surface border-b-2 border-primary hover:text-primary transition-colors uppercase py-1">Save Note</button>
      </div>
    </section>

  </aside>
</div>

@endsection
