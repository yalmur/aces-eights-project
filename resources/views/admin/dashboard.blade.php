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
    <div class="text-5xl font-black font-serif">{{ $todayOrders }}</div>
    <div class="font-mono text-xs {{ $orderGrowth === null ? 'text-[#2B2B2B]' : ($orderGrowth >= 0 ? 'text-green-700' : 'text-error') }} font-bold">
      @if($orderGrowth !== null)
        {{ $orderGrowth >= 0 ? '+' : '' }}{{ $orderGrowth }}% vs Yesterday
      @else
        No data for yesterday
      @endif
    </div>
  </div>
  <div class="industrial-border p-6 bg-surface-container-low flex flex-col justify-between h-44">
    <div class="flex justify-between items-start gap-2">
      <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">Today's Revenue</span>
      <span class="material-symbols-outlined text-primary">payments</span>
    </div>
    <div class="font-black font-serif text-4xl">£{{ number_format($todayRevenue, 2) }}</div>
    <div class="font-mono text-xs text-[#2B2B2B]">Paid orders today</div>
  </div>
  <div class="industrial-border p-6 bg-surface-container-low flex flex-col justify-between h-44">
    <div class="flex justify-between items-start gap-2">
      <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">Active Deliveries</span>
      <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1">local_shipping</span>
    </div>
    <div class="text-5xl font-black font-serif text-primary">{{ str_pad($activeDeliveries, 2, '0', STR_PAD_LEFT) }}</div>
    <div class="font-mono text-xs text-[#2B2B2B]">Out for delivery now</div>
  </div>
  <div class="industrial-border p-6 bg-primary text-white flex flex-col justify-between h-44 shadow-xl">
    <div class="flex justify-between items-start gap-2">
      <span class="font-mono text-xs font-bold uppercase">Kitchen Load</span>
      <span class="material-symbols-outlined text-[#D4AF37]">potted_plant</span>
    </div>
    <div class="font-black font-serif text-4xl">{{ $kitchenLoad }}</div>
    <div class="font-mono text-xs uppercase font-bold text-[#D4AF37]">{{ $kitchenQueue }} order{{ $kitchenQueue !== 1 ? 's' : '' }} in queue</div>
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

      @forelse($recentOrders as $order)
      @php
        $statusProgress = match($order->status) {
          'accepted'         => 'w-1/5',
          'cooking'          => 'w-3/5',
          'ready'            => 'w-full',
          'out_for_delivery' => 'w-full',
          default            => 'w-0',
        };
        $statusBg = in_array($order->status, ['ready', 'out_for_delivery']) ? 'bg-green-600' : 'bg-primary';
        $headerBg = in_array($order->status, ['ready', 'out_for_delivery']) ? 'bg-green-100' : 'bg-surface-container-highest';
        $typeLabel = match($order->type) { 'delivery' => 'Online', 'eat_in' => 'Eat-In', default => 'Collection' };
      @endphp
      <div class="industrial-border overflow-hidden bg-white {{ in_array($order->status, ['ready','out_for_delivery']) ? 'border-dashed' : '' }}">
        <div class="{{ $headerBg }} px-6 py-3 flex flex-wrap justify-between items-center border-b border-[#2B2B2B] gap-2">
          <div class="flex items-center gap-4">
            <span class="font-mono text-sm font-bold">ORD-{{ $order->id }}</span>
            <span class="bg-primary text-white text-[10px] px-2 py-0.5 font-bold uppercase rounded-full">{{ $typeLabel }}</span>
          </div>
          <span class="font-mono text-xs font-bold text-[#2B2B2B] uppercase">{{ $order->created_at->diffForHumans() }}</span>
        </div>
        <div class="p-6 flex flex-col md:flex-row justify-between gap-6">
          <div class="flex-1">
            <h4 class="font-serif text-lg font-bold mb-2">{{ $order->customer_name }}</h4>
            <ul class="font-sans text-sm space-y-1">
              @foreach($order->items as $item)
              <li class="flex justify-between"><span>{{ $item->qty }}x {{ $item->name }}</span><span class="font-bold">£{{ number_format($item->line_total, 2) }}</span></li>
              @endforeach
              <li class="flex justify-between border-t border-dotted border-[#2B2B2B] mt-2 pt-1 font-bold"><span>Total</span><span>£{{ number_format($order->total, 2) }}</span></li>
            </ul>
          </div>
          <div class="w-full md:w-48 shrink-0">
            <p class="font-mono text-xs uppercase text-[#2B2B2B] mb-3">{{ $order->status_label }}</p>
            <div class="w-full h-2 bg-surface-container mb-4 industrial-border overflow-hidden">
              <div class="h-full {{ $statusBg }} {{ $statusProgress }}"></div>
            </div>
            <a href="{{ route('admin.kitchen.index') }}" class="w-full gold-button py-2 font-mono text-xs uppercase flex items-center justify-center gap-2">
              Kitchen View <span class="material-symbols-outlined text-sm">open_in_new</span>
            </a>
          </div>
        </div>
      </div>
      @empty
      <div class="industrial-border p-12 text-center bg-surface-container-low">
        <span class="material-symbols-outlined text-4xl text-on-surface-variant mb-4 block">receipt_long</span>
        <p class="font-mono text-xs uppercase text-on-surface-variant tracking-widest">No active orders right now</p>
        <a href="{{ route('admin.orders.in-store') }}" class="inline-block mt-4 gold-button px-6 py-2 font-mono text-xs uppercase">Create In-Store Order</a>
      </div>
      @endforelse

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
