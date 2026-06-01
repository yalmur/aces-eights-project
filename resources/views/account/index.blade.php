@extends('layouts.app')
@section('content')
{{-- Customer account dashboard --}}
<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-12 pt-32 flex-1 w-full">
    <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">

        <!-- Sidebar Navigation -->
        <aside class="lg:w-1/4 hidden lg:block border-r-2 border-outline pr-8">
            <h3 class="font-headline-md text-headline-md uppercase tracking-tight mb-6">Account</h3>
            <nav class="flex flex-col gap-3">
                <a class="p-4 bg-primary text-on-primary font-label-bold text-label-bold uppercase industrial-border heritage-shadow block transition-all" href="#">Dashboard</a>
                <a class="p-4 bg-surface text-on-surface hover:bg-surface-container-high font-label-bold text-label-bold uppercase industrial-border block transition-all" href="#">Order History</a>
                <a class="p-4 bg-surface text-on-surface hover:bg-surface-container-high font-label-bold text-label-bold uppercase industrial-border block transition-all" href="#">Payment Methods</a>
                <a class="p-4 bg-surface text-on-surface hover:bg-surface-container-high font-label-bold text-label-bold uppercase industrial-border block transition-all" href="#">Addresses</a>
                <a class="p-4 bg-surface text-on-surface hover:bg-surface-container-high font-label-bold text-label-bold uppercase industrial-border block transition-all" href="#">Settings</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left p-4 bg-surface text-on-surface-variant hover:bg-surface-container-high font-label-bold text-label-bold uppercase industrial-border block transition-all">Log Out</button>
                </form>
            </nav>
        </aside>

        <!-- Mobile Tab Navigation -->
        <div class="lg:hidden flex overflow-x-auto gap-4 pb-4 mb-4 border-b-2 border-outline snap-x">
            <a class="snap-start shrink-0 px-4 py-2 bg-primary text-on-primary font-label-bold uppercase industrial-border" href="#">Dashboard</a>
            <a class="snap-start shrink-0 px-4 py-2 bg-surface text-on-surface font-label-bold uppercase industrial-border" href="#">Orders</a>
            <a class="snap-start shrink-0 px-4 py-2 bg-surface text-on-surface font-label-bold uppercase industrial-border" href="#">Payment</a>
            <a class="snap-start shrink-0 px-4 py-2 bg-surface text-on-surface font-label-bold uppercase industrial-border" href="#">Addresses</a>
            <a class="snap-start shrink-0 px-4 py-2 bg-surface text-on-surface font-label-bold uppercase industrial-border" href="#">Settings</a>
        </div>

        <div class="lg:w-3/4 flex flex-col">

            <!-- Dashboard Hero Section -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter mb-12">
                <div class="md:col-span-7 flex flex-col justify-center">
                    <h2 class="font-headline-lg text-headline-lg mb-2">Welcome back, Marcello.</h2>
                    <p class="font-body-lg text-body-lg text-on-surface-variant">Your workshop for the finest artisanal pies in the city.</p>
                </div>
                <div class="md:col-span-5 bg-primary-container p-6 industrial-border heritage-shadow text-on-primary-container flex flex-col justify-between mt-6 md:mt-0 min-h-[160px]">
                    <div>
                        <span class="font-label-bold text-label-bold uppercase tracking-widest text-on-primary-container opacity-80">Loyalty Status</span>
                        <div class="font-display text-headline-lg md:text-display mt-2 whitespace-nowrap">420 <span class="text-headline-md font-body-md">PTS</span></div>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-primary h-2 industrial-border">
                            <div class="bg-secondary-fixed h-full" style="width: 84%;"></div>
                        </div>
                        <p class="font-label-sm text-label-sm mt-2">80 points until your next free Artisan Pie.</p>
                    </div>
                </div>
            </div>

            <div class="double-divider"></div>

            <!-- Bento Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">

                <!-- Active Order Tracking -->
                <section class="md:col-span-2 lg:col-span-1 bg-surface-container-low p-8 industrial-border">
                    <div class="flex justify-between items-center mb-8 border-b-2 border-outline pb-4">
                        <h3 class="font-headline-md text-headline-md uppercase tracking-tight">Active Tracking</h3>
                        <span class="bg-secondary-container text-on-secondary-container font-label-bold text-label-bold px-3 py-1 industrial-border">Order #8821</span>
                    </div>

                    <div x-data="{ step: 2 }">
                        <div class="relative py-4">
                            <div class="absolute top-1/2 left-0 w-full h-1 bg-outline-variant -translate-y-1/2 z-0"></div>
                            <div class="absolute top-1/2 left-0 h-1 bg-primary -translate-y-1/2 z-0" style="width: 33%;"></div>
                            <div class="relative z-10 flex justify-between">
                                <!-- Step 1: Accepted -->
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-10 h-10 rounded-full industrial-border flex items-center justify-center"
                                         :class="step >= 1 ? 'bg-primary text-on-primary' : 'bg-surface-container-highest text-on-surface'">
                                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check</span>
                                    </div>
                                    <span class="font-label-bold text-[10px] uppercase"
                                          :class="step >= 1 ? 'text-primary' : 'text-on-surface-variant'">Accepted</span>
                                </div>
                                <!-- Step 2: Cooking -->
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-10 h-10 rounded-full industrial-border flex items-center justify-center"
                                         :class="step >= 2 ? 'bg-primary text-on-primary' : 'bg-surface-container-highest text-on-surface'">
                                        <span class="material-symbols-outlined">restaurant</span>
                                    </div>
                                    <span class="font-label-bold text-[10px] uppercase"
                                          :class="step >= 2 ? 'text-primary' : 'text-on-surface-variant'">Cooking</span>
                                </div>
                                <!-- Step 3: On Way -->
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-10 h-10 rounded-full industrial-border flex items-center justify-center"
                                         :class="step >= 3 ? 'bg-primary text-on-primary' : 'bg-surface-container-highest text-on-surface opacity-30'">
                                        <span class="material-symbols-outlined">moped</span>
                                    </div>
                                    <span class="font-label-bold text-[10px] uppercase"
                                          :class="step >= 3 ? 'text-primary' : 'text-on-surface-variant opacity-30'">On Way</span>
                                </div>
                                <!-- Step 4: Arrived -->
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-10 h-10 rounded-full industrial-border flex items-center justify-center"
                                         :class="step >= 4 ? 'bg-primary text-on-primary' : 'bg-surface-container-highest text-on-surface opacity-30'">
                                        <span class="material-symbols-outlined">home_pin</span>
                                    </div>
                                    <span class="font-label-bold text-[10px] uppercase"
                                          :class="step >= 4 ? 'text-primary' : 'text-on-surface-variant opacity-30'">Arrived</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 bg-surface p-4 border border-outline flex items-center gap-4">
                        <div class="w-16 h-16 bg-surface-container-high industrial-border overflow-hidden shrink-0">
                            <img alt="Pizza" class="w-full h-full object-cover" src="https://placehold.co/400x300/e4e2e1/1b1c1c?text=Photo">
                        </div>
                        <div>
                            <p class="font-label-bold text-label-bold">Order Details</p>
                            <p class="text-body-md line-clamp-2">Dead Man's Hand + 2x Soda</p>
                            <p class="text-on-surface-variant text-label-sm">Estimated arrival: 12:45 PM</p>
                        </div>
                    </div>
                </section>

                <!-- Favourites & Order Again -->
                <section class="md:col-span-2 lg:col-span-1 flex flex-col gap-gutter">
                    <div class="bg-surface-container-high p-8 industrial-border flex-1">
                        <h3 class="font-headline-md text-headline-md uppercase tracking-tight mb-6">The Usual Suspects</h3>
                        <div class="space-y-4">
                            <button class="w-full text-left p-4 bg-surface industrial-border hover:bg-primary-container hover:text-on-primary-container transition-all group">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-label-bold text-label-bold uppercase">"The Foundry" Special</p>
                                        <p class="text-label-sm opacity-80">Spicy Capicola, Honey, Burrata</p>
                                    </div>
                                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">chevron_right</span>
                                </div>
                            </button>
                            <button class="w-full text-left p-4 bg-surface industrial-border hover:bg-primary-container hover:text-on-primary-container transition-all group">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-label-bold text-label-bold uppercase">White Truffle Market</p>
                                        <p class="text-label-sm opacity-80">Wild Mushroom, Fontina, Thyme</p>
                                    </div>
                                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">chevron_right</span>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div class="bg-surface p-6 border-2 border-primary flex items-center justify-between">
                        <div>
                            <h4 class="font-label-bold text-label-bold text-primary uppercase">Planning Ahead?</h4>
                            <p class="text-body-md">Book a table for tonight.</p>
                        </div>
                        <a href="{{ route('booking') }}" class="bg-primary text-on-primary px-6 py-2 industrial-border font-label-bold text-label-bold uppercase hover:bg-primary-container transition-colors">
                            Reserve
                        </a>
                    </div>
                </section>

                <!-- Payment -->
                <section class="md:col-span-1 bg-surface-container-low p-8 industrial-border">
                    <h3 class="font-headline-md text-headline-md uppercase tracking-tight mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined">credit_card</span>
                        Payment
                    </h3>
                    <div class="space-y-4">
                        <div class="p-4 border border-outline flex justify-between items-center bg-surface">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
                                <div>
                                    <p class="font-label-bold text-label-sm uppercase">Visa ending in 4492</p>
                                    <p class="text-label-sm opacity-60">Expires 09/26</p>
                                </div>
                            </div>
                            <button class="text-primary hover:underline font-label-bold text-[10px]">EDIT</button>
                        </div>
                        <button class="w-full py-3 border border-dashed border-outline text-on-surface-variant font-label-bold text-label-sm uppercase hover:bg-surface-container transition-colors">
                            + Add Payment Method
                        </button>
                    </div>
                </section>

                <!-- Saved Addresses -->
                <section class="md:col-span-1 bg-surface-container-low p-8 industrial-border">
                    <h3 class="font-headline-md text-headline-md uppercase tracking-tight mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined">location_on</span>
                        Addresses
                    </h3>
                    <div class="space-y-4">
                        <div class="p-4 border border-outline bg-surface">
                            <p class="font-label-bold text-label-sm uppercase mb-1">Home (Workshop)</p>
                            <p class="text-body-md leading-tight">412 Industry St. Loft 4B<br>Brooklyn, NY 11201</p>
                            <div class="mt-3 flex gap-4">
                                <button class="text-primary hover:underline font-label-bold text-[10px] uppercase">Edit</button>
                                <button class="text-on-surface-variant hover:underline font-label-bold text-[10px] uppercase">Delete</button>
                            </div>
                        </div>
                        <button class="w-full py-3 border border-dashed border-outline text-on-surface-variant font-label-bold text-label-sm uppercase hover:bg-surface-container transition-colors">
                            + Add New Address
                        </button>
                    </div>
                </section>

            </div>

            <!-- Recent History Section -->
            <div class="mt-12 w-full">
                <h3 class="font-headline-md text-headline-md uppercase tracking-tight mb-6">The Archive (Recent Orders)</h3>
                <div class="w-full">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b-2 border-on-surface">
                                <th class="text-left py-4 px-2 font-label-bold text-label-bold uppercase">Date</th>
                                <th class="text-left py-4 px-2 font-label-bold text-label-bold uppercase">Order #</th>
                                <th class="text-left py-4 px-2 font-label-bold text-label-bold uppercase hidden sm:table-cell">Items</th>
                                <th class="text-left py-4 px-2 font-label-bold text-label-bold uppercase">Total</th>
                                <th class="text-right py-4 px-2 font-label-bold text-label-bold uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-body-md">
                            <tr>
                                <td class="font-label-sm text-label-sm py-3 pr-6 text-on-surface-variant border-b border-outline-variant">14 May 2026</td>
                                <td class="font-label-sm text-label-sm py-3 pr-6 text-on-surface border-b border-outline-variant">#8821</td>
                                <td class="font-label-sm text-label-sm py-3 pr-6 text-on-surface border-b border-outline-variant">Dead Man's Hand + 2x Soda</td>
                                <td class="font-label-sm text-label-sm py-3 pr-6 text-on-surface border-b border-outline-variant">£34.50</td>
                                <td class="py-3 border-b border-outline-variant"><a href="{{ route('orders.tracking', '8821') }}" class="font-label-sm text-primary hover:underline uppercase">View</a></td>
                            </tr>
                            <tr>
                                <td class="font-label-sm text-label-sm py-3 pr-6 text-on-surface-variant border-b border-outline-variant">8 May 2026</td>
                                <td class="font-label-sm text-label-sm py-3 pr-6 text-on-surface border-b border-outline-variant">#8804</td>
                                <td class="font-label-sm text-label-sm py-3 pr-6 text-on-surface border-b border-outline-variant">The Blueprint × 2</td>
                                <td class="font-label-sm text-label-sm py-3 pr-6 text-on-surface border-b border-outline-variant">£30.00</td>
                                <td class="py-3 border-b border-outline-variant"><a href="{{ route('orders.tracking', '8804') }}" class="font-label-sm text-primary hover:underline uppercase">View</a></td>
                            </tr>
                            <tr>
                                <td class="font-label-sm text-label-sm py-3 pr-6 text-on-surface-variant">29 Apr 2026</td>
                                <td class="font-label-sm text-label-sm py-3 pr-6 text-on-surface">#8791</td>
                                <td class="font-label-sm text-label-sm py-3 pr-6 text-on-surface">The Ironworker + Garlic Knots</td>
                                <td class="font-label-sm text-label-sm py-3 pr-6 text-on-surface">£29.50</td>
                                <td class="py-3"><a href="{{ route('orders.tracking', '8791') }}" class="font-label-sm text-primary hover:underline uppercase">View</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
