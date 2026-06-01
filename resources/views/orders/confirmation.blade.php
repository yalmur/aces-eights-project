@extends('layouts.app')
@section('content')
{{-- Order confirmation: success banner + live tracking at step 1 (Accepted) --}}
{{-- Adapted from aces_eights_live_order_tracking_final_polish stitch --}}

{{-- Success Banner --}}
<div class="max-w-container-max mx-auto px-4 lg:px-16 pt-8">
  <div class="bg-secondary-container border-2 border-on-surface px-6 py-4 mb-8 flex items-center gap-4">
    <span class="material-symbols-outlined text-on-surface text-3xl">check_circle</span>
    <div>
      <p class="font-headline-sm text-on-surface uppercase tracking-wider">Order Confirmed — #{{ $orderId }}</p>
      <p class="font-body-md text-on-surface-variant mt-1">We're firing up the oven. You'll receive a confirmation email shortly.</p>
    </div>
  </div>
</div>

{{-- Tracking Panel (step: 1 = Accepted) --}}
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pb-24"
     x-data="{ step: 1 }">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
        <!-- Left Column: Order Status & Details -->
        <div class="lg:col-span-5 flex flex-col gap-8">
            <!-- Status Card -->
            <section class="industrial-border bg-white p-6 relative overflow-hidden">
                <div class="absolute top-4 right-4">
                    <span class="font-label-bold text-label-bold bg-secondary-container px-3 py-1 industrial-border uppercase">Live Status</span>
                </div>
                <h2 class="font-headline-md text-headline-md mb-2 uppercase tracking-tight text-primary">Order #{{ $orderId }}</h2>
                <p class="text-on-surface-variant font-label-bold mb-8 italic">Est. Delivery: 12:45 PM</p>
                <!-- Progress Tracker -->
                <div class="space-y-6 relative">
                    <!-- Connecting Line -->
                    <div class="absolute left-[15px] top-4 bottom-4 w-0.5 bg-outline-variant"></div>
                    <!-- Step 1: Order Accepted -->
                    <div class="flex items-center gap-4 relative"
                         :class="step >= 1 ? '' : 'opacity-40'">
                        <div class="z-10 w-8 h-8 rounded-full flex items-center justify-center industrial-border"
                             :class="step >= 1 ? 'bg-primary animate-pulse' : 'bg-surface-container'">
                            <span class="material-symbols-outlined text-sm"
                                  :class="step >= 1 ? 'text-on-primary' : 'text-on-surface-variant'"
                                  style="font-variation-settings: 'FILL' 1;">check</span>
                        </div>
                        <span class="font-label-bold text-label-bold"
                              :class="step >= 1 ? 'text-primary' : ''">Order Accepted</span>
                    </div>
                    <!-- Step 2: In the Kitchen -->
                    <div class="flex items-center gap-4 relative"
                         :class="step >= 2 ? '' : 'opacity-40'">
                        <div class="z-10 w-8 h-8 rounded-full flex items-center justify-center industrial-border"
                             :class="step >= 2 ? 'bg-primary' : 'bg-surface-container'">
                            <span class="material-symbols-outlined text-sm"
                                  :class="step >= 2 ? 'text-on-primary' : 'text-on-surface-variant'">restaurant_menu</span>
                        </div>
                        <span class="font-label-bold text-label-bold"
                              :class="step >= 2 ? 'text-primary' : ''">In the Kitchen</span>
                    </div>
                    <!-- Step 3: Out for Delivery -->
                    <div class="flex items-center gap-4 relative"
                         :class="step >= 3 ? '' : 'opacity-40'">
                        <div class="z-10 w-8 h-8 rounded-full flex items-center justify-center industrial-border"
                             :class="step >= 3 ? 'bg-primary' : 'bg-surface-container'">
                            <span class="material-symbols-outlined text-sm"
                                  :class="step >= 3 ? 'text-on-primary' : 'text-on-surface-variant'">delivery_dining</span>
                        </div>
                        <span class="font-label-bold text-label-bold"
                              :class="step >= 3 ? 'text-primary' : ''">Out for Delivery</span>
                    </div>
                    <!-- Step 4: Delivered -->
                    <div class="flex items-center gap-4 relative"
                         :class="step >= 4 ? '' : 'opacity-40'">
                        <div class="z-10 w-8 h-8 rounded-full flex items-center justify-center industrial-border"
                             :class="step >= 4 ? 'bg-primary' : 'bg-surface-container'">
                            <span class="material-symbols-outlined text-sm"
                                  :class="step >= 4 ? 'text-on-primary' : 'text-on-surface-variant'">home</span>
                        </div>
                        <span class="font-label-bold text-label-bold"
                              :class="step >= 4 ? 'text-primary' : ''">Delivered</span>
                    </div>
                </div>
            </section>
            <!-- Order Summary Card -->
            <section class="industrial-border bg-white p-6">
                <div class="flex justify-between items-end mb-2">
                    <h3 class="font-headline-md text-headline-md uppercase tracking-tight">YOUR ORDER</h3>
                    <span class="font-label-bold text-primary underline cursor-pointer">Modify</span>
                </div>
                <div class="border-t-2 border-on-surface mb-4"></div>
                <ul class="space-y-4">
                    <li class="flex justify-between items-start">
                        <div>
                            <span class="font-label-bold block uppercase">1x Dead Man's Hand Pizza</span>
                            <span class="text-label-sm text-on-surface-variant uppercase">Large, Extra Char, No Olives</span>
                        </div>
                        <span class="font-label-bold">$24.00</span>
                    </li>
                    <li class="flex justify-between items-start">
                        <div>
                            <span class="font-label-bold block uppercase">2x Iron Foundry Sodas</span>
                            <span class="text-label-sm text-on-surface-variant uppercase">Classic Root Beer</span>
                        </div>
                        <span class="font-label-bold">$8.00</span>
                    </li>
                </ul>
                <div class="border-t border-outline-variant mt-6 pt-4">
                    <div class="flex justify-between text-on-surface-variant mb-1">
                        <span class="text-label-bold uppercase">Delivery Fee</span>
                        <span class="font-label-bold">$2.50</span>
                    </div>
                    <div class="flex justify-between font-headline-md text-headline-md pt-2">
                        <span>TOTAL</span>
                        <span class="text-primary font-black">$34.50</span>
                    </div>
                </div>
            </section>
            <!-- Call the Shop -->
            <button class="w-full py-4 bg-oxblood-red text-on-primary px-6 font-label-bold text-label-bold flex items-center justify-center gap-2 industrial-border hover:brightness-110 transition-all active:scale-95 border-b-[3px] border-b-secondary-container uppercase tracking-widest">
                <span class="material-symbols-outlined">phone_in_talk</span>
                CALL THE SHOP
            </button>
        </div>
        <!-- Right Column: Live Map -->
        <div class="lg:col-span-7 h-[500px] lg:h-auto min-h-[550px]">
            <div class="industrial-border h-full relative overflow-hidden bg-surface-dim">
                <!-- Map Placeholder -->
                <img alt="Live map"
                     class="w-full h-full object-cover opacity-90"
                     style="filter: grayscale(1) contrast(1.1) brightness(0.9);"
                     src="https://placehold.co/400x200/e4e2e1/1b1c1c?text=Live+Map">
                <!-- Driver Badge -->
                <div class="absolute top-6 left-6 pointer-events-none">
                    <div class="bg-white industrial-border px-4 py-2 flex items-center gap-3 shadow-[2px_2px_0px_#1b1c1c]">
                        <div class="w-2.5 h-2.5 rounded-full bg-error animate-ping"></div>
                        <span class="font-label-bold text-on-background uppercase tracking-widest text-xs">Driver: Marco V.</span>
                    </div>
                </div>
                <!-- Destination Banner -->
                <div class="absolute bottom-6 left-6 right-6">
                    <div class="bg-white industrial-border p-4 flex items-center gap-4 shadow-[4px_4px_0px_#1b1c1c]">
                        <div class="bg-oxblood-red p-2.5 industrial-border">
                            <span class="material-symbols-outlined text-on-primary">motorcycle</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-on-surface-variant leading-none uppercase mb-1 tracking-widest">On the way to</p>
                            <p class="font-label-bold text-on-surface truncate uppercase">1422 Foundry Ave, Unit 4B</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
