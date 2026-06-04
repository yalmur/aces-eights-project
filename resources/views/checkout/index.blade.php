@extends('layouts.app')
@section('content')
{{-- Checkout page adapted from stitch --}}
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-12">
    <div class="mb-12 border-b-4 border-double border-on-surface pb-4">
        <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg uppercase tracking-tight">Checkout</h2>
        <p class="font-body-md text-on-surface-variant mt-2 italic">Secure transaction — 156 &amp; 158 Fortess Road, Tufnell Park, London.</p>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST"
          x-data="{ orderType: 'delivery' }"
          @submit.prevent="document.getElementById('cart-json').value = JSON.stringify($store.cart.items); document.getElementById('order-type-input').value = orderType; $el.submit()">
        @csrf
        <input type="hidden" name="cart_items" id="cart-json">
        <input type="hidden" name="order_type" id="order-type-input" value="delivery">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
            <!-- Left Column: Forms -->
            <div class="lg:col-span-7 space-y-12">

                <!-- Toggle: Delivery/Collection -->
                <div>
                    <section>
                        <h3 class="font-label-bold text-label-bold uppercase mb-4 text-primary">Order Type</h3>
                        <div class="flex border-2 border-on-surface p-1 gap-1">
                            <button type="button"
                                    @click="orderType = 'delivery'"
                                    :class="orderType === 'delivery' ? 'bg-primary text-on-primary border-primary' : 'bg-surface text-on-surface border-on-surface'"
                                    class="flex-1 py-3 border-2 font-label-bold text-label-bold uppercase tracking-wider transition-colors">
                                Delivery
                            </button>
                            <button type="button"
                                    @click="orderType = 'collection'"
                                    :class="orderType === 'collection' ? 'bg-primary text-on-primary border-primary' : 'bg-surface text-on-surface border-on-surface'"
                                    class="flex-1 py-3 border-2 font-label-bold text-label-bold uppercase tracking-wider transition-colors">
                                Collection
                            </button>
                        </div>
                    </section>

                    <!-- Section: Delivery Address -->
                    <section x-show="orderType === 'delivery'">
                        <div class="flex items-center justify-between mb-6 mt-8">
                            <h3 class="font-label-bold text-label-bold uppercase text-primary">Delivery Address</h3>
                            <span class="material-symbols-outlined text-outline">location_on</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
                            <div class="md:col-span-2">
                                <label class="block font-label-sm text-label-sm uppercase mb-1">Street Address</label>
                                <input class="w-full bg-transparent border-b-2 border-on-surface py-2 focus:ring-0 focus:border-primary placeholder:text-outline-variant font-body-md"
                                       name="street_address" placeholder="123 Foundry Lane" type="text"/>
                            </div>
                            <div>
                                <label class="block font-label-sm text-label-sm uppercase mb-1">City</label>
                                <input class="w-full bg-transparent border-b-2 border-on-surface py-2 focus:ring-0 focus:border-primary placeholder:text-outline-variant font-body-md"
                                       name="city" placeholder="Chicago" type="text"/>
                            </div>
                            <div>
                                <label class="block font-label-sm text-label-sm uppercase mb-1">Postal Code</label>
                                <input class="w-full bg-transparent border-b-2 border-on-surface py-2 focus:ring-0 focus:border-primary placeholder:text-outline-variant font-body-md"
                                       name="postal_code" placeholder="60601" type="text"/>
                            </div>
                        </div>
                    </section>

                    <!-- Section: Collection Info -->
                    <section x-show="orderType === 'collection'" style="display: none;">
                        <div class="flex items-center justify-between mb-6 mt-8">
                            <h3 class="font-label-bold text-label-bold uppercase text-primary">Collection Info</h3>
                            <span class="material-symbols-outlined text-outline">storefront</span>
                        </div>
                        <div class="bg-surface-container-low border-2 border-on-surface p-6">
                            <p class="font-label-bold text-label-bold uppercase mb-1">Aces &amp; Eights Pizza Co.</p>
                            <p class="font-body-md text-on-surface-variant">123 Foundry Lane, Chicago, IL 60601</p>
                            <p class="font-body-md text-on-surface-variant mt-2">Ready in approximately <span class="text-primary font-label-bold">20–25 minutes</span></p>
                        </div>
                    </section>
                </div>

                {{-- Promo Code --}}
                <section class="mt-8" x-data="{ applied: false, msg: '' }">
                  <h3 class="font-label-bold text-label-bold uppercase mb-4 text-primary">Promo Code</h3>
                  <div class="flex gap-3">
                    <input name="promo_code" id="promo-code"
                           class="flex-1 bg-transparent border-b-2 border-on-surface py-2 focus:ring-0 focus:border-primary font-mono text-sm uppercase placeholder:text-outline-variant placeholder:normal-case"
                           placeholder="Enter code (e.g. SAVE10)" type="text" maxlength="50"/>
                    <button type="button"
                            @click="const v=document.getElementById('promo-code').value.trim(); if(v){applied=true;msg=''}else{msg='Enter a code first'}"
                            class="px-4 py-2 industrial-border font-mono text-xs font-bold uppercase hover:bg-surface-container transition-colors">
                      APPLY
                    </button>
                  </div>
                  <p x-show="applied" x-cloak class="font-mono text-[10px] text-green-700 mt-2">Code applied — discount calculated at checkout.</p>
                  <p x-show="msg" x-cloak class="font-mono text-[10px] text-brand-error mt-2" x-text="msg"></p>
                </section>

                <!-- Section: Payment Method -->
                <div x-data="{ payment: 'card' }">
                    <section>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-label-bold text-label-bold uppercase text-primary">Payment Method</h3>
                            <span class="material-symbols-outlined text-outline">payments</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <button type="button"
                                    @click="payment = 'card'"
                                    :class="payment === 'card' ? 'border-primary bg-surface-container-low' : 'border-outline-variant'"
                                    class="border-2 p-4 flex flex-col items-center gap-2 hover:bg-surface-container-high transition-colors active:scale-[0.98]">
                                <span class="material-symbols-outlined text-3xl">credit_card</span>
                                <span class="font-label-bold text-label-bold uppercase">Card</span>
                            </button>
                            <button type="button"
                                    @click="payment = 'apple'"
                                    :class="payment === 'apple' ? 'border-primary bg-surface-container-low' : 'border-outline-variant'"
                                    class="border-2 p-4 flex flex-col items-center gap-2 hover:border-on-surface transition-colors active:scale-[0.98]">
                                <span class="material-symbols-outlined text-3xl">apps</span>
                                <span class="font-label-bold text-label-bold uppercase">Apple Pay</span>
                            </button>
                            <button type="button"
                                    @click="payment = 'google'"
                                    :class="payment === 'google' ? 'border-primary bg-surface-container-low' : 'border-outline-variant'"
                                    class="border-2 p-4 flex flex-col items-center gap-2 hover:border-on-surface transition-colors active:scale-[0.98]">
                                <span class="material-symbols-outlined text-3xl">google</span>
                                <span class="font-label-bold text-label-bold uppercase">G Pay</span>
                            </button>
                        </div>

                        <!-- Card details (shown when card selected) -->
                        <div x-show="payment === 'card'" class="mt-8 space-y-6">
                            <div>
                                <label class="block font-label-sm text-label-sm uppercase mb-1">Cardholder Name</label>
                                <input class="w-full bg-transparent border-b-2 border-on-surface py-2 focus:ring-0 focus:border-primary placeholder:text-outline-variant font-body-md uppercase"
                                       name="cardholder_name" placeholder="FRANK COSTELLO" type="text"/>
                            </div>
                            <div class="grid grid-cols-2 gap-gutter">
                                <div class="col-span-2">
                                    <label class="block font-label-sm text-label-sm uppercase mb-1">Card Number</label>
                                    <input class="w-full bg-transparent border-b-2 border-on-surface py-2 focus:ring-0 focus:border-primary placeholder:text-outline-variant font-body-md"
                                           name="card_number" placeholder="**** **** **** 1922" type="text"/>
                                </div>
                                <div>
                                    <label class="block font-label-sm text-label-sm uppercase mb-1">Expiry</label>
                                    <input class="w-full bg-transparent border-b-2 border-on-surface py-2 focus:ring-0 focus:border-primary placeholder:text-outline-variant font-body-md"
                                           name="card_expiry" placeholder="MM/YY" type="text"/>
                                </div>
                                <div>
                                    <label class="block font-label-sm text-label-sm uppercase mb-1">CVV</label>
                                    <input class="w-full bg-transparent border-b-2 border-on-surface py-2 focus:ring-0 focus:border-primary placeholder:text-outline-variant font-body-md"
                                           name="card_cvv" placeholder="***" type="password"/>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Back to Menu -->
                <div>
                    <a href="{{ route('menu') }}"
                       class="inline-flex items-center gap-2 font-label-bold text-label-bold uppercase text-on-surface-variant hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        Back to Menu
                    </a>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="lg:col-span-5 sticky top-24">
                <div class="bg-surface-container border-2 border-on-surface p-8" style="box-shadow: 4px 4px 0px 0px rgba(43, 43, 43, 1);">
                    <div class="border-b-2 border-on-surface pb-4 mb-6">
                        <h3 class="font-headline-md text-headline-md uppercase tracking-tighter">Order Summary</h3>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex justify-between items-start">
                            <div>
                                <p class="font-label-bold text-label-bold uppercase">The Deadman's Hand Pizza</p>
                                <p class="text-label-sm text-on-surface-variant italic">Large &bull; Thin Crust &bull; Extra N'duja</p>
                            </div>
                            <span class="font-label-bold text-label-bold">$24.00</span>
                        </li>
                        <li class="flex justify-between items-start">
                            <div>
                                <p class="font-label-bold text-label-bold uppercase">Foundry Garlic Knots</p>
                                <p class="text-label-sm text-on-surface-variant italic">6pcs &bull; Marinara Dip</p>
                            </div>
                            <span class="font-label-bold text-label-bold">$8.50</span>
                        </li>
                        <li class="flex justify-between items-start">
                            <div>
                                <p class="font-label-bold text-label-bold uppercase">Artisan Root Beer</p>
                                <p class="text-label-sm text-on-surface-variant italic">Glass Bottle &bull; 12oz</p>
                            </div>
                            <span class="font-label-bold text-label-bold">$4.00</span>
                        </li>
                    </ul>
                    <div class="space-y-2 border-t border-outline-variant pt-6 mb-6">
                        <div class="flex justify-between text-body-md">
                            <span>Subtotal</span>
                            <span>$36.50</span>
                        </div>
                        <div class="flex justify-between text-body-md">
                            <span>Delivery Fee</span>
                            <span>$3.50</span>
                        </div>
                        <div class="flex justify-between text-body-md">
                            <span>Tax (8%)</span>
                            <span>$3.20</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center border-t-4 border-double border-on-surface pt-4 mb-8">
                        <span class="font-headline-md text-headline-md uppercase">Total Due</span>
                        <span class="font-headline-md text-headline-md text-primary">$43.20</span>
                    </div>
                    <button type="submit"
                            class="w-full bg-primary text-on-primary py-5 px-8 font-label-bold text-[18px] uppercase tracking-widest border-b-4 border-secondary transition-all hover:bg-primary-container active:translate-y-1 active:border-b-0">
                        Place Order
                    </button>
                    <p class="text-center mt-6 text-[11px] font-label-bold text-on-surface-variant uppercase tracking-widest">
                        By placing order you agree to our heritage terms
                    </p>
                </div>

                <!-- Atmospheric Promo -->
                <div class="mt-8 relative h-48 border-2 border-on-surface overflow-hidden group">
                    <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700"
                         src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Order+Item"
                         alt="Aces and Eights Pizza — Established 1922"/>
                    <div class="absolute inset-0 bg-primary/40 flex items-center justify-center p-6 text-center">
                        <div class="border-2 border-white p-4">
                            <p class="text-white font-display text-2xl uppercase tracking-tighter">Established 1922</p>
                            <p class="text-white font-label-bold text-sm uppercase">Quality Over Everything</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
