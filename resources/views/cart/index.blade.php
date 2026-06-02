@extends('layouts.app')
@section('content')

<div x-data="{ orderType: 'delivery' }" class="max-w-container mx-auto px-4 lg:px-16 py-12">

  <h1 class="font-serif text-headline-lg text-on-surface mb-8">Your Order</h1>

  {{-- Empty state --}}
  <div x-show="$store.cart.items.length === 0" x-cloak class="py-24 text-center">
    <svg class="w-16 h-16 text-outline mx-auto mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
      <path stroke-linecap="square" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
    <h2 class="font-serif text-headline-md text-on-surface mb-3">Your cart is empty</h2>
    <p class="font-sans text-sm text-on-surface-variant mb-8">Add something delicious from our menu.</p>
    <a href="{{ route('menu') }}" class="btn-primary">BROWSE MENU</a>
  </div>

  {{-- Cart content --}}
  <div x-show="$store.cart.items.length > 0" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- LEFT: item list --}}
    <div class="lg:col-span-2 space-y-4">

      {{-- Order type toggle --}}
      <div class="flex gap-0 border border-outline-variant mb-6">
        <button @click="orderType = 'delivery'"
                :class="orderType === 'delivery' ? 'bg-primary text-white' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container'"
                class="flex-1 py-3 label-caps text-xs transition-colors">DELIVERY</button>
        <button @click="orderType = 'collection'"
                :class="orderType === 'collection' ? 'bg-primary text-white' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container'"
                class="flex-1 py-3 label-caps text-xs transition-colors border-l border-outline-variant">COLLECTION</button>
      </div>

      {{-- Items --}}
      <template x-for="item in $store.cart.items" :key="item.cartId">
        <div class="bg-surface-container-low border border-outline-variant p-5 flex gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3 mb-1">
              <h3 class="font-serif text-base font-bold text-on-surface" x-text="item.name"></h3>
              <span class="font-mono text-sm font-bold text-primary flex-shrink-0"
                    x-text="'£' + item.lineTotal.toFixed(2)"></span>
            </div>
            <p class="font-mono text-[11px] text-on-surface-variant mb-3"
               x-text="$store.cart.itemSummary(item)"></p>

            <div class="flex items-center gap-4">
              <div class="flex items-center gap-3">
                <button @click="$store.cart.updateQty(item.cartId, -1)"
                        class="w-7 h-7 border border-outline flex items-center justify-center hover:bg-surface-container transition-colors font-bold text-base leading-none">−</button>
                <span class="font-mono text-sm font-bold text-on-surface w-4 text-center" x-text="item.qty"></span>
                <button @click="$store.cart.updateQty(item.cartId, 1)"
                        class="w-7 h-7 border border-outline flex items-center justify-center hover:bg-surface-container transition-colors font-bold text-base leading-none">+</button>
              </div>
              <button @click="$store.cart.editItem(item.cartId)"
                      class="font-mono text-[11px] text-on-surface-variant hover:text-primary transition-colors underline">Edit</button>
              <button @click="$store.cart.removeItem(item.cartId)"
                      class="font-mono text-[11px] text-on-surface-variant hover:text-primary transition-colors underline ml-auto">Remove</button>
            </div>
          </div>
        </div>
      </template>

    </div>

    {{-- RIGHT: order summary --}}
    <div class="lg:col-span-1">
      <div class="bg-surface-container-low border border-outline-variant p-6 lg:sticky lg:top-24">
        <h2 class="font-serif text-headline-sm text-on-surface mb-6">Order Summary</h2>
        <div class="space-y-3 mb-6">
          <div class="flex justify-between">
            <span class="font-sans text-sm text-on-surface-variant">Subtotal</span>
            <span class="font-mono text-sm text-on-surface"
                  x-text="'£' + $store.cart.subtotal.toFixed(2)"></span>
          </div>
          <div class="flex justify-between">
            <span class="font-sans text-sm text-on-surface-variant">Delivery fee</span>
            <span class="font-mono text-sm"
                  :class="orderType === 'delivery' ? 'text-on-surface' : 'text-primary'"
                  x-text="orderType === 'delivery' ? '£3.50' : 'FREE'"></span>
          </div>
          <div class="section-divider"></div>
          <div class="flex justify-between items-center pt-1">
            <span class="font-serif text-base font-bold text-on-surface">Total Due</span>
            <span class="font-mono text-lg font-bold text-primary"
                  x-text="'£' + $store.cart.total(orderType).toFixed(2)"></span>
          </div>
        </div>
        <a href="{{ route('checkout') }}" class="btn-primary w-full text-center block mb-3">
          PROCEED TO CHECKOUT
        </a>
        <a href="{{ route('menu') }}" class="btn-ghost w-full text-center block">
          ← Continue Ordering
        </a>
      </div>
    </div>

  </div>

</div>

@endsection
