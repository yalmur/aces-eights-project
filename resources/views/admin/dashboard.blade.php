@extends('layouts.admin')
@section('content')

{{-- Header Section --}}
<section class="mb-12">
  <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-4">
    <div>
      <h1 class="font-serif text-4xl md:text-5xl font-black text-on-surface uppercase leading-none">Command Center</h1>
      <p class="font-mono text-xs font-bold text-primary mt-2 uppercase tracking-widest">SHIFT: {{ now()->format('l, g:i A') }}</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admin.menu.create') }}"
         class="gold-button px-6 py-3 font-mono text-xs font-bold uppercase flex items-center justify-center gap-2 w-full md:w-auto">
        <span class="material-symbols-outlined text-base">add</span> Add Manual Order
      </a>
    </div>
  </div>
  <div class="industrial-divider"></div>
</section>

{{-- KPI Bento Grid --}}
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
  <div class="industrial-border p-6 bg-surface-container-low flex flex-col justify-between h-44">
    <div class="flex justify-between items-start gap-2">
      <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">Today's Orders</span>
      <span class="material-symbols-outlined text-primary">confirmation_number</span>
    </div>
    <div class="text-5xl font-black font-serif">142</div>
    <div class="font-mono text-xs text-green-700 font-bold">+12% vs Yesterday</div>
  </div>
  <div class="industrial-border p-6 bg-surface-container-low flex flex-col justify-between h-44">
    <div class="flex justify-between items-start gap-2">
      <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">Live Revenue</span>
      <span class="material-symbols-outlined text-primary">payments</span>
    </div>
    <div class="font-black font-serif text-4xl">£3,240</div>
    <div class="font-mono text-xs text-[#2B2B2B]">Current Shift Projection</div>
  </div>
  <div class="industrial-border p-6 bg-surface-container-low flex flex-col justify-between h-44">
    <div class="flex justify-between items-start gap-2">
      <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">Active Deliveries</span>
      <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1">local_shipping</span>
    </div>
    <div class="text-5xl font-black font-serif text-primary">08</div>
    <div class="font-mono text-xs text-[#2B2B2B]">Avg Delivery: 24 mins</div>
  </div>
  <div class="industrial-border p-6 bg-primary text-white flex flex-col justify-between h-44 shadow-xl">
    <div class="flex justify-between items-start gap-2">
      <span class="font-mono text-xs font-bold uppercase">Kitchen Load</span>
      <span class="material-symbols-outlined text-[#D4AF37]">potted_plant</span>
    </div>
    <div class="font-black font-serif text-4xl">HIGH</div>
    <div class="font-mono text-xs uppercase font-bold text-[#D4AF37]">Slowdown Warning Active</div>
  </div>
</section>

{{-- Main workspace --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

  {{-- Live Production Queue --}}
  <section class="lg:col-span-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
      <h3 class="font-serif text-xl font-bold uppercase tracking-tight">Live Production Queue</h3>
      <div x-data="{ tab: 'all' }" class="flex gap-2 p-1 bg-surface-container industrial-border w-full sm:w-auto">
        <button @click="tab = 'all'" :class="tab==='all' ? 'bg-primary text-white' : 'hover:bg-surface-variant'"
                class="flex-1 sm:flex-none px-4 py-1 font-mono text-xs font-bold uppercase transition-colors">All</button>
        <button @click="tab = 'online'" :class="tab==='online' ? 'bg-primary text-white' : 'hover:bg-surface-variant'"
                class="flex-1 sm:flex-none px-4 py-1 font-mono text-xs font-bold uppercase transition-colors">Online</button>
        <button @click="tab = 'instore'" :class="tab==='instore' ? 'bg-primary text-white' : 'hover:bg-surface-variant'"
                class="flex-1 sm:flex-none px-4 py-1 font-mono text-xs font-bold uppercase transition-colors">In-Store</button>
      </div>
    </div>
    <div class="space-y-4">

      {{-- Order Card 1 --}}
      <div class="industrial-border overflow-hidden bg-white">
        <div class="bg-surface-container-highest px-6 py-3 flex flex-wrap justify-between items-center border-b border-[#2B2B2B] gap-2">
          <div class="flex items-center gap-4">
            <span class="font-mono text-sm font-bold">ORD-7721</span>
            <span class="bg-primary text-white text-[10px] px-2 py-0.5 font-bold uppercase rounded-full">Online</span>
          </div>
          <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">Received 4m ago</span>
        </div>
        <div class="p-6 flex flex-col md:flex-row justify-between gap-6">
          <div class="flex-1">
            <h4 class="font-serif text-lg font-bold mb-2">James Henderson</h4>
            <ul class="font-sans text-sm space-y-1">
              <li class="flex justify-between"><span>2x The Meat Lover (Large)</span><span class="font-bold">£34.00</span></li>
              <li class="flex justify-between"><span>1x Garlic Bread</span><span class="font-bold">£5.50</span></li>
              <li class="flex justify-between border-t border-dotted border-[#2B2B2B] mt-2 pt-1 font-bold"><span>Total</span><span>£39.50</span></li>
            </ul>
          </div>
          <div class="w-full md:w-64 shrink-0">
            <p class="font-mono text-xs uppercase text-[#2B2B2B] mb-3">Status: Accepted</p>
            <div class="w-full h-2 bg-surface-container mb-4 industrial-border overflow-hidden">
              <div class="h-full bg-primary w-1/5"></div>
            </div>
            <button class="w-full gold-button py-2 font-mono text-xs uppercase flex items-center justify-center gap-2">
              Fire To Oven <span class="material-symbols-outlined text-sm">local_fire_department</span>
            </button>
          </div>
        </div>
      </div>

      {{-- Order Card 2 --}}
      <div class="industrial-border overflow-hidden bg-white">
        <div class="bg-surface-container-highest px-6 py-3 flex flex-wrap justify-between items-center border-b border-[#2B2B2B] gap-2">
          <div class="flex items-center gap-4">
            <span class="font-mono text-sm font-bold">ORD-7718</span>
            <span class="bg-[#2B2B2B] text-white text-[10px] px-2 py-0.5 font-bold uppercase rounded-full">In-Store</span>
          </div>
          <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">Received 12m ago</span>
        </div>
        <div class="p-6 flex flex-col md:flex-row justify-between gap-6">
          <div class="flex-1">
            <h4 class="font-serif text-lg font-bold mb-2">Table 04 — Sarah P.</h4>
            <ul class="font-sans text-sm space-y-1">
              <li class="flex justify-between"><span>1x Classic Margherita (12")</span><span class="font-bold">£12.50</span></li>
              <li class="flex justify-between"><span>2x Moretti Draft</span><span class="font-bold">£13.00</span></li>
              <li class="flex justify-between border-t border-dotted border-[#2B2B2B] mt-2 pt-1 font-bold"><span>Total</span><span>£25.50</span></li>
            </ul>
          </div>
          <div class="w-full md:w-64 shrink-0">
            <p class="font-mono text-xs uppercase text-[#2B2B2B] mb-3">Status: Cooking</p>
            <div class="w-full h-2 bg-surface-container mb-4 industrial-border overflow-hidden">
              <div class="h-full bg-primary w-3/5"></div>
            </div>
            <button class="w-full gold-button py-2 font-mono text-xs uppercase flex items-center justify-center gap-2">
              Mark As Ready <span class="material-symbols-outlined text-sm">check_circle</span>
            </button>
          </div>
        </div>
      </div>

      {{-- Order Card 3 (Ready) --}}
      <div class="industrial-border overflow-hidden bg-white/50 border-dashed">
        <div class="bg-green-100 px-6 py-3 flex flex-wrap justify-between items-center border-b border-[#2B2B2B] gap-2">
          <div class="flex items-center gap-4 text-green-800">
            <span class="font-mono text-sm font-bold">ORD-7712</span>
            <span class="bg-green-800 text-white text-[10px] px-2 py-0.5 font-bold uppercase rounded-full">Online</span>
          </div>
          <span class="font-mono text-xs font-bold text-green-800 uppercase">Ready for Pickup</span>
        </div>
        <div class="p-6 flex flex-col md:flex-row justify-between gap-6">
          <div class="flex-1">
            <h4 class="font-serif text-lg font-bold mb-2">Marco V. (Driver Assigned)</h4>
            <p class="font-sans text-sm text-[#2B2B2B] italic">"Leave at gate, code 1234."</p>
          </div>
          <div class="w-full md:w-64 shrink-0">
            <p class="font-mono text-xs uppercase text-[#2B2B2B] mb-3">Status: Ready</p>
            <div class="w-full h-2 bg-surface-container mb-4 industrial-border overflow-hidden">
              <div class="h-full bg-green-600 w-full"></div>
            </div>
            <button class="w-full bg-[#2B2B2B] text-white py-2 font-mono text-xs uppercase flex items-center justify-center gap-2">
              Handed to Driver <span class="material-symbols-outlined text-sm">moped</span>
            </button>
          </div>
        </div>
      </div>

    </div>
  </section>

  {{-- Sidebar Contextual Controls --}}
  <aside class="lg:col-span-4 space-y-6">

    {{-- Station Load --}}
    <div class="industrial-border bg-white p-6">
      <h3 class="font-mono text-xs font-bold uppercase border-b border-[#2B2B2B] pb-2 mb-4">Station Load</h3>
      <div class="space-y-4 font-sans text-sm">
        <div class="flex items-center justify-between">
          <span>Stone Oven 1</span>
          <span class="font-mono font-bold text-primary">FULL</span>
        </div>
        <div class="flex items-center justify-between">
          <span>Stone Oven 2</span>
          <span class="font-mono font-bold text-primary">80%</span>
        </div>
        <div class="flex items-center justify-between">
          <span>Prep Station</span>
          <span class="font-mono font-bold text-green-700">MODERATE</span>
        </div>
      </div>
    </div>

    {{-- Delivery Map Placeholder --}}
    <div class="industrial-border overflow-hidden relative group">
      <img src="https://placehold.co/400x200/e4e2e1/1b1c1c?text=Delivery+Map"
           alt="Delivery Map" class="w-full h-48 object-cover grayscale brightness-75 group-hover:brightness-100 transition-all">
      <div class="absolute inset-0 bg-primary/20 flex items-center justify-center">
        <a href="{{ route('admin.delivery.index') }}"
           class="bg-[#2B2B2B] text-white px-4 py-2 font-mono text-xs font-bold uppercase shadow-xl">
          Expand Delivery Map
        </a>
      </div>
    </div>

    {{-- Shift Personnel --}}
    <div class="industrial-border bg-surface-container-low p-6">
      <h3 class="font-mono text-xs font-bold uppercase border-b border-[#2B2B2B] pb-2 mb-4">Shift Personnel</h3>
      <div class="space-y-3">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-[#2B2B2B] text-white flex items-center justify-center text-[10px] font-bold">AL</div>
          <div class="flex-1">
            <p class="font-mono text-xs font-bold leading-none">Antonio L. <span class="text-primary">•</span></p>
            <p class="font-mono text-[9px] uppercase text-[#2B2B2B]">Head Pizzaiolo</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-[#2B2B2B] text-white flex items-center justify-center text-[10px] font-bold">RM</div>
          <div class="flex-1">
            <p class="font-mono text-xs font-bold leading-none">Rosa M.</p>
            <p class="font-mono text-[9px] uppercase text-[#2B2B2B]">Floor Manager</p>
          </div>
        </div>
      </div>
      <button class="w-full mt-6 py-2 industrial-border font-mono text-xs font-bold uppercase hover:bg-[#2B2B2B] hover:text-white transition-colors">
        Manage Rota
      </button>
    </div>

  </aside>
</div>

@endsection
