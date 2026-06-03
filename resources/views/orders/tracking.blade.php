@extends('layouts.app')
@section('content')

<div class="max-w-container mx-auto px-4 lg:px-16 py-12">

  <div class="mb-8">
    <h1 class="font-serif text-3xl font-black text-on-surface uppercase">Tracking Order #{{ $order->id }}</h1>
    <p class="font-sans text-sm text-on-surface-variant mt-1">{{ $order->isDelivery() ? 'Delivery' : 'Collection' }} · {{ $order->created_at->format('d M Y, H:i') }}</p>
  </div>

  {{-- Status stepper --}}
  @php
    $statuses = $order->isDelivery()
      ? ['accepted' => 'Accepted', 'cooking' => 'Cooking', 'out_for_delivery' => 'Out for Delivery', 'delivered' => 'Delivered']
      : ['accepted' => 'Accepted', 'cooking' => 'Cooking', 'ready' => 'Ready', 'collected' => 'Collected'];
    $keys = array_keys($statuses);
    $currentIndex = array_search($order->status, $keys);
    $pct = ($currentIndex !== false && count($statuses) > 1)
        ? (($currentIndex / (count($statuses) - 1)) * 100)
        : 0;
  @endphp

  <div class="relative flex items-center justify-between w-full px-4 mb-12">
    <div class="absolute top-6 left-0 w-full h-1 bg-outline-variant z-0"></div>
    <div class="absolute top-6 left-0 h-1 bg-primary z-0" style="width: {{ $pct }}%"></div>
    @foreach($statuses as $key => $label)
    @php $done = $currentIndex !== false && array_search($key, $keys) <= $currentIndex; @endphp
    <div class="flex flex-col items-center gap-2 z-10">
      <div class="w-12 h-12 rounded-full border-2 flex items-center justify-center shadow-lg {{ $done ? 'bg-primary text-white border-primary' : 'bg-surface text-on-surface-variant border-outline' }}">
        <span class="material-symbols-outlined text-[20px]">{{ $done ? 'check' : 'radio_button_unchecked' }}</span>
      </div>
      <span class="font-mono text-[10px] uppercase {{ $order->status === $key ? 'text-primary font-bold' : 'text-on-surface-variant' }}">{{ $label }}</span>
    </div>
    @endforeach
  </div>

  {{-- Current status card --}}
  <div class="bg-surface-container-low border border-outline-variant p-6 mb-8 text-center">
    <p class="font-mono text-[10px] uppercase text-on-surface-variant mb-2">Current Status</p>
    <p class="font-serif text-2xl font-black {{ $order->status_color }}">{{ $order->status_label }}</p>
    @if($order->isDelivery() && $order->delivery_address)
      <p class="font-sans text-sm text-on-surface-variant mt-2">{{ $order->delivery_address }}, {{ $order->delivery_city }}</p>
    @endif
  </div>

  {{-- Items summary --}}
  <div class="border border-outline-variant p-6 mb-8">
    <h3 class="font-serif text-lg font-bold mb-4">Order Summary</h3>
    @foreach($order->items as $item)
    <div class="flex justify-between py-2 border-b border-outline-variant last:border-0">
      <span class="font-sans text-sm">{{ $item->qty }}× {{ $item->name }}</span>
      <span class="font-mono text-sm font-bold">£{{ number_format($item->line_total, 2) }}</span>
    </div>
    @endforeach
    <div class="flex justify-between pt-3 font-bold">
      <span class="font-serif">Total</span>
      <span class="font-mono text-primary">£{{ number_format($order->total, 2) }}</span>
    </div>
  </div>

  <a href="{{ route('home') }}" class="btn-secondary">← Back to Home</a>

</div>

@endsection
