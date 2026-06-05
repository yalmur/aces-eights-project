@extends('layouts.admin')
@section('content')

<!-- Page Title -->
<section class="mb-12">
<h1 class="font-display text-display text-primary tracking-tighter mb-4 text-center md:text-left">KITCHEN COMMAND</h1>
<div class="double-divider w-full"></div>
<div class="flex flex-wrap justify-between items-center mt-6 gap-4">
<div class="flex gap-4 items-center" x-data="{ s: 0 }" x-init="setInterval(() => { s++; if(s >= 30){ window.location.reload(); } }, 1000)">
<span class="font-label-bold text-label-bold bg-secondary-container text-on-secondary-container px-4 py-1 border border-on-surface">LIVE STATUS: ACTIVE</span>
<span class="font-label-bold text-label-bold text-on-surface-variant italic">Auto-refresh: <span x-text="(30 - s) + 's'">30s</span></span>
</div>
<div class="flex gap-2">
<a href="{{ route('admin.orders.in-store') }}" class="bg-primary text-on-primary border-2 border-on-surface px-6 py-2 font-label-bold text-label-bold active:scale-95 transition-all flex items-center gap-2">
<span class="material-symbols-outlined">add_task</span> NEW ORDER
</a>
</div>
</div>
</section>

@if(session('success'))
<div class="mb-6 bg-secondary-container border-2 border-on-surface px-6 py-3 font-label-bold text-label-bold uppercase flex items-center gap-2">
  <span class="material-symbols-outlined text-sm">check_circle</span>{{ session('success') }}
</div>
@endif

<!-- Kanban Board -->
<div class="kanban-board">

{{-- ===== COLUMN 1: ORDER QUEUE (accepted) ===== --}}
<div class="flex flex-col gap-4">
<div class="flex items-center justify-between border-b-2 border-on-surface pb-2">
<h3 class="font-headline-md text-headline-md text-primary">ORDER QUEUE</h3>
<span class="bg-outline text-on-primary px-3 py-1 text-label-bold rounded-full">{{ $columns['accepted']->count() }}</span>
</div>
<div class="flex flex-col gap-6 industrial-scrollbar max-h-[80vh] overflow-y-auto pr-2">
@forelse($columns['accepted'] as $order)
<div class="bg-surface border-2 border-on-surface p-4 flex flex-col gap-4 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)]">
  <div class="flex justify-between items-start">
    <div>
      <p class="font-label-bold text-label-bold text-on-surface-variant">ORDER #{{ $order->id }}</p>
      <p class="font-headline-md text-headline-md">{{ $order->customer_name }}</p>
      <p class="font-label-bold text-label-sm text-on-surface-variant uppercase">{{ $order->type === 'delivery' ? 'Delivery' : ($order->type === 'eat_in' ? 'Eat-In' : 'Collection') }}</p>
    </div>
    <span class="text-error font-label-bold flex items-center gap-1 text-xs">
      <span class="material-symbols-outlined text-[16px]">timer</span>
      {{ $order->created_at->diffForHumans(null, true) }}
    </span>
  </div>
  <div class="border-t border-outline-variant pt-2 text-body-md space-y-1">
    @foreach($order->items as $item)
    <p>• {{ $item->qty }}x {{ $item->name }}</p>
    @endforeach
    @if($order->notes)
    <p class="text-primary font-bold mt-2">NOTE: {{ $order->notes }}</p>
    @endif
  </div>
  <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
    @csrf @method('PATCH')
    <input type="hidden" name="status" value="cooking">
    <button type="submit" class="w-full bg-primary text-on-primary py-3 font-label-bold text-label-bold border-b-4 border-on-primary-fixed-variant hover:brightness-110 active:translate-y-1 active:border-b-0 transition-all uppercase tracking-widest">
      FIRE TO KITCHEN <span class="material-symbols-outlined text-sm align-middle">local_fire_department</span>
    </button>
  </form>
</div>
@empty
<p class="font-mono text-xs text-on-surface-variant text-center py-8 uppercase tracking-widest">Queue clear</p>
@endforelse
</div>
</div>

{{-- ===== COLUMN 2: IN THE KITCHEN (cooking) ===== --}}
<div class="flex flex-col gap-4">
<div class="flex items-center justify-between border-b-2 border-on-surface pb-2">
<h3 class="font-headline-md text-headline-md text-primary">IN THE KITCHEN</h3>
<span class="bg-primary text-on-primary px-3 py-1 text-label-bold rounded-full">{{ $columns['cooking']->count() }}</span>
</div>
<div class="flex flex-col gap-6 industrial-scrollbar max-h-[80vh] overflow-y-auto pr-2">
@forelse($columns['cooking'] as $order)
<div class="bg-surface border-2 border-on-surface p-4 flex flex-col gap-4 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)] relative overflow-hidden">
  <div class="absolute top-0 right-0 p-2">
    <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
  </div>
  <div class="flex justify-between items-start">
    <div>
      <p class="font-label-bold text-label-bold text-on-surface-variant">ORDER #{{ $order->id }}</p>
      <p class="font-headline-md text-headline-md">{{ $order->customer_name }}</p>
      <p class="font-label-bold text-label-sm text-on-surface-variant uppercase">{{ $order->type === 'delivery' ? 'Delivery' : ($order->type === 'eat_in' ? 'Eat-In' : 'Collection') }}</p>
    </div>
    <span class="text-error font-label-bold flex items-center gap-1 text-xs">
      <span class="material-symbols-outlined text-[16px]">timer</span>
      {{ $order->created_at->diffForHumans(null, true) }}
    </span>
  </div>
  <div class="border-t border-outline-variant pt-2 text-body-md space-y-1">
    @foreach($order->items as $item)
    <p>• {{ $item->qty }}x {{ $item->name }}</p>
    @endforeach
    @if($order->notes)
    <p class="text-primary font-bold mt-2">NOTE: {{ $order->notes }}</p>
    @endif
  </div>
  <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
    @csrf @method('PATCH')
    <input type="hidden" name="status" value="ready">
    <button type="submit" class="w-full bg-secondary text-on-secondary py-3 font-label-bold text-label-bold border-b-4 border-on-secondary-fixed-variant hover:brightness-110 active:translate-y-1 active:border-b-0 transition-all uppercase tracking-widest">
      MARK AS READY <span class="material-symbols-outlined text-sm align-middle">check_circle</span>
    </button>
  </form>
</div>
@empty
<p class="font-mono text-xs text-on-surface-variant text-center py-8 uppercase tracking-widest">Nothing cooking</p>
@endforelse
</div>
</div>

{{-- ===== COLUMN 3: READY FOR DISPATCH (ready) ===== --}}
<div class="flex flex-col gap-4">
<div class="flex items-center justify-between border-b-2 border-on-surface pb-2">
<h3 class="font-headline-md text-headline-md text-primary">READY FOR DISPATCH</h3>
<span class="bg-secondary text-on-secondary px-3 py-1 text-label-bold rounded-full">{{ $columns['ready']->count() }}</span>
</div>
<div class="flex flex-col gap-6 industrial-scrollbar max-h-[80vh] overflow-y-auto pr-2">
@forelse($columns['ready'] as $order)
<div class="bg-secondary-fixed border-2 border-on-surface p-4 flex flex-col gap-4 shadow-[4px_4px_0px_0px_rgba(27,28,28,1)]">
  <div class="flex justify-between items-start">
    <div>
      <p class="font-label-bold text-label-bold text-on-secondary-fixed">ORDER #{{ $order->id }}</p>
      <p class="font-headline-md text-headline-md">{{ $order->customer_name }}</p>
    </div>
    <span class="font-label-bold text-label-sm uppercase px-2 py-0.5 border border-on-surface bg-white/60">
      {{ $order->type === 'delivery' ? 'Delivery' : ($order->type === 'eat_in' ? 'Eat-In' : 'Collection') }}
    </span>
  </div>
  <div class="border-t border-on-secondary-fixed-variant/20 pt-2 text-body-md text-on-secondary-fixed space-y-1">
    @foreach($order->items as $item)
    <p>• {{ $item->qty }}x {{ $item->name }}</p>
    @endforeach
    @if($order->type === 'delivery' && $order->delivery_address)
    <p class="text-xs mt-2 font-mono opacity-70">{{ $order->delivery_address }}, {{ $order->delivery_postcode }}</p>
    @endif
  </div>
  <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
    @csrf @method('PATCH')
    <input type="hidden" name="status" value="{{ $order->type === 'delivery' ? 'out_for_delivery' : 'collected' }}">
    <button type="submit" class="w-full bg-on-surface text-surface py-3 font-label-bold text-label-bold border-b-4 border-primary hover:brightness-125 transition-all uppercase tracking-widest">
      @if($order->type === 'delivery')
        DISPATCH <span class="material-symbols-outlined text-sm align-middle">moped</span>
      @else
        MARK COLLECTED <span class="material-symbols-outlined text-sm align-middle">check_circle</span>
      @endif
    </button>
  </form>
</div>
@empty
<p class="font-mono text-xs text-on-surface-variant text-center py-8 uppercase tracking-widest">Nothing ready</p>
@endforelse
</div>
</div>

{{-- ===== COLUMN 4: OUT FOR DELIVERY ===== --}}
<div class="flex flex-col gap-4">
<div class="flex items-center justify-between border-b-2 border-on-surface pb-2">
<h3 class="font-headline-md text-headline-md text-primary">OUT FOR DELIVERY</h3>
<span class="bg-on-surface text-surface px-3 py-1 text-label-bold rounded-full">{{ $columns['out_for_delivery']->count() }}</span>
</div>
<div class="flex flex-col gap-6 industrial-scrollbar max-h-[80vh] overflow-y-auto pr-2">
@forelse($columns['out_for_delivery'] as $order)
<div class="bg-surface border-2 border-on-surface p-4 flex flex-col gap-4 opacity-90 shadow-[2px_2px_0px_0px_rgba(27,28,28,1)]">
  <div class="flex justify-between items-start">
    <div>
      <p class="font-label-bold text-label-bold text-on-surface-variant">ORDER #{{ $order->id }}</p>
      <p class="font-headline-md text-headline-md">{{ $order->customer_name }}</p>
    </div>
    <span class="text-xs font-mono text-on-surface-variant">{{ $order->updated_at->diffForHumans(null, true) }}</span>
  </div>
  @if($order->delivery_address)
  <div class="border-t border-outline-variant pt-2 text-label-sm space-y-1">
    <p class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">location_on</span> {{ $order->delivery_address }}, {{ $order->delivery_postcode }}</p>
    @if($order->customer_phone)
    <p class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">call</span> {{ $order->customer_phone }}</p>
    @endif
  </div>
  @endif
  <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
    @csrf @method('PATCH')
    <input type="hidden" name="status" value="delivered">
    <button type="submit" class="w-full border-2 border-on-surface py-2 font-label-bold text-label-bold hover:bg-surface-container-highest transition-all uppercase">
      MARK DELIVERED <span class="material-symbols-outlined text-sm align-middle">check</span>
    </button>
  </form>
</div>
@empty
<p class="font-mono text-xs text-on-surface-variant text-center py-8 uppercase tracking-widest">No active deliveries</p>
@endforelse
</div>
</div>

</div>{{-- end kanban --}}

{{-- Pusher flash on status update --}}
<div x-data="{
       init() {
         if (window.Echo) {
           window.Echo.private('admin.orders')
             .listen('.OrderStatusUpdated', () => { window.location.reload(); })
         }
       }
     }"
     x-init="init()">
</div>

@endsection
