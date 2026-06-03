@extends('layouts.app')
@section('content')

<div class="max-w-container mx-auto px-4 lg:px-16 py-12">

  {{-- Success banner --}}
  <div class="bg-green-50 border-2 border-green-600 p-8 mb-10 text-center">
    <span class="material-symbols-outlined text-green-600 text-5xl mb-4 block" style="font-variation-settings:'FILL' 1">check_circle</span>
    <h1 class="font-serif text-3xl font-black text-on-surface uppercase mb-2">Order Confirmed!</h1>
    <p class="font-sans text-sm text-on-surface-variant">Order <strong class="text-on-surface">#{{ $order->id }}</strong> — {{ $order->isDelivery() ? 'Delivery' : 'Collection' }}</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">

    {{-- Order items --}}
    <div class="bg-surface-container-low border border-outline-variant p-6">
      <h2 class="font-serif text-xl font-bold mb-6 uppercase">Your Order</h2>
      @foreach($order->items as $item)
      <div class="flex justify-between items-start border-b border-outline-variant py-4 last:border-0">
        <div class="flex-1">
          <p class="font-sans text-sm font-bold">{{ $item->qty }}× {{ $item->name }}</p>
          @if($item->customisation_summary !== 'No extras')
            <p class="font-mono text-[10px] text-on-surface-variant mt-1">{{ $item->customisation_summary }}</p>
          @endif
        </div>
        <span class="font-mono text-sm font-bold text-primary ml-4">£{{ number_format($item->line_total, 2) }}</span>
      </div>
      @endforeach
      <div class="pt-4 space-y-2">
        <div class="flex justify-between font-sans text-sm"><span class="text-on-surface-variant">Subtotal</span><span>£{{ number_format($order->subtotal, 2) }}</span></div>
        <div class="flex justify-between font-sans text-sm"><span class="text-on-surface-variant">Delivery</span><span>{{ $order->delivery_fee > 0 ? '£' . number_format($order->delivery_fee, 2) : 'FREE' }}</span></div>
        <div class="flex justify-between font-serif text-base font-bold border-t border-outline-variant pt-2">
          <span>Total</span><span class="text-primary">£{{ number_format($order->total, 2) }}</span>
        </div>
      </div>
    </div>

    {{-- Delivery details + status --}}
    <div class="bg-surface-container-low border border-outline-variant p-6">
      <h2 class="font-serif text-xl font-bold mb-6 uppercase">{{ $order->isDelivery() ? 'Delivery Details' : 'Collection Details' }}</h2>
      @if($order->isDelivery())
        <p class="font-sans text-sm text-on-surface-variant mb-1">Delivering to:</p>
        <p class="font-sans text-sm font-bold">{{ $order->delivery_address }}, {{ $order->delivery_city }}, {{ $order->delivery_postcode }}</p>
        <p class="font-sans text-sm text-on-surface-variant mt-4">Estimated delivery: <strong>30–45 minutes</strong></p>
      @else
        <p class="font-sans text-sm text-on-surface-variant mb-1">Pick up from:</p>
        <p class="font-sans text-sm font-bold">156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP</p>
        <p class="font-sans text-sm text-on-surface-variant mt-4">Estimated ready: <strong>20–30 minutes</strong></p>
      @endif
      <div class="mt-6 p-4 bg-surface border border-outline-variant">
        <p class="font-mono text-[10px] uppercase text-on-surface-variant mb-1">Status</p>
        <p class="font-serif text-lg font-bold {{ $order->status_color }}">{{ $order->status_label }}</p>
      </div>
    </div>
  </div>

  <div class="flex gap-4 justify-center flex-wrap">
    <a href="{{ route('orders.tracking', $order->id) }}" class="btn-primary">TRACK MY ORDER</a>
    <a href="{{ route('menu') }}" class="btn-secondary" x-data @click="$store.cart.clear()">ORDER MORE</a>
  </div>

</div>

@endsection
