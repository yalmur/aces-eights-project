@extends('layouts.admin')
@section('content')
@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">{{ session('success') }}</div>
@endif
<header class="mb-12">
<h2 class="font-display text-headline-lg uppercase border-b-4 border-primary inline-block pb-2 mb-4">General Settings</h2>
<p class="font-body-lg text-on-surface-variant max-w-2xl">Refine the operational blueprint of the workshop. Manage physical presence, logistical hours, and the digital ledger.</p>
</header>
<form method="POST" action="{{ route('admin.settings.update') }}">
  @csrf
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
<!-- Section 1: Store Information -->
<section class="lg:col-span-7 bg-surface industrial-border p-8 flex flex-col gap-6">
<div class="flex items-center justify-between mb-2">
<h3 class="font-headline-md uppercase tracking-tight flex items-center gap-2">
<span class="material-symbols-outlined text-primary" data-icon="store">store</span>
                        Store Information
                    </h3>
</div>
<div class="space-y-6">
<div class="group">
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-1">Shop Name</label>
<input class="w-full bg-transparent industrial-border-b border-on-surface-variant py-2 font-body-md focus:border-primary transition-colors" type="text" name="store_name" value="{{ $settings['store_name'] }}"/>
</div>
<div class="group">
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-1">Workshop Address</label>
<input class="w-full bg-transparent industrial-border-b border-on-surface-variant py-2 font-body-md focus:border-primary transition-colors" type="text" name="store_address" value="{{ $settings['store_address'] }}"/>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-1">Command Phone</label>
<input class="w-full bg-transparent industrial-border-b border-on-surface-variant py-2 font-body-md focus:border-primary transition-colors" type="text" name="store_phone" value="{{ $settings['store_phone'] }}"/>
</div>
<div>
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-1">Dispatch Email</label>
<input class="w-full bg-transparent industrial-border-b border-on-surface-variant py-2 font-body-md focus:border-primary transition-colors" type="email" name="store_email" value="{{ $settings['store_email'] }}"/>
</div>
</div>
</div>
<div class="industrial-divider"></div>
<div class="flex justify-end">
<button type="submit" class="bg-primary text-on-primary px-8 py-3 font-label-bold uppercase tracking-widest industrial-border hover:bg-on-primary-fixed-variant transition-colors flex items-center gap-2">
                        Update Records <span class="material-symbols-outlined text-sm" data-icon="save">save</span>
</button>
</div>
</section>
<!-- Section 2: Opening Times -->
<section class="lg:col-span-5 bg-surface-container-low industrial-border p-8">
<h3 class="font-headline-md uppercase tracking-tight flex items-center gap-2 mb-8">
<span class="material-symbols-outlined text-primary" data-icon="schedule">schedule</span>
                    Opening Times
                </h3>
<div class="space-y-4">
<!-- Day Row -->
<div class="flex items-center justify-between py-2 border-b border-on-surface-variant/20">
<span class="font-label-bold text-label-bold uppercase w-24">Sun - Thu</span>
<div class="flex items-center gap-2">
<input class="w-32 bg-transparent text-center industrial-border-b border-on-surface-variant/40 py-1" type="text" name="opening_sun_thu" value="{{ $settings['opening_sun_thu'] }}"/>
</div>
<div class="flex items-center gap-2">
<div class="w-10 h-5 bg-primary rounded-full relative cursor-pointer">
<div class="absolute right-1 top-1 w-3 h-3 bg-white rounded-full"></div>
</div>
</div>
</div>
<!-- Day Row -->
<div class="flex items-center justify-between py-2 border-b border-on-surface-variant/20">
<span class="font-label-bold text-label-bold uppercase w-24">Fri - Sat</span>
<div class="flex items-center gap-2">
<input class="w-32 bg-transparent text-center industrial-border-b border-on-surface-variant/40 py-1" type="text" name="opening_fri_sat" value="{{ $settings['opening_fri_sat'] }}"/>
</div>
<div class="flex items-center gap-2">
<div class="w-10 h-5 bg-primary rounded-full relative cursor-pointer">
<div class="absolute right-1 top-1 w-3 h-3 bg-white rounded-full"></div>
</div>
</div>
</div>
</div>
<button type="submit" class="w-full mt-8 industrial-border py-2 font-label-bold uppercase hover:bg-surface-container-highest transition-colors">Lock Logistical Hours</button>
</section>
<!-- Section 3: CMS Content Management -->
<section class="lg:col-span-8 bg-surface industrial-border overflow-hidden">
<div class="bg-primary text-on-primary p-4 flex items-center justify-between">
<h3 class="font-headline-md uppercase tracking-tight flex items-center gap-2">
<span class="material-symbols-outlined" data-icon="edit_note">edit_note</span>
                        CMS Control Center
                    </h3>
<span class="text-label-sm uppercase font-label-bold opacity-80">Last Edit: 12 Hours Ago</span>
</div>
<div class="p-8 space-y-8">
<div>
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-2">Landing Page Hero Text</label>
<textarea class="w-full bg-surface-container-low industrial-border p-4 font-body-md focus:border-primary transition-colors resize-none" name="hero_text" rows="3">{{ $settings['hero_text'] }}</textarea>
</div>
<div>
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-2">Our Story / Heritage Text</label>
<textarea class="w-full bg-surface-container-low industrial-border p-4 font-body-md focus:border-primary transition-colors resize-none" name="story_text" rows="4">{{ $settings['story_text'] }}</textarea>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="industrial-border p-4 bg-surface-bright">
<p class="font-label-bold text-label-bold uppercase mb-2">Primary Asset</p>
<div class="h-32 industrial-border bg-surface-container-highest flex items-center justify-center overflow-hidden">
<img alt="Active Brand Asset" class="h-16 w-auto opacity-50 grayscale hover:grayscale-0 transition-all" src="{{ asset('images/logo.jpg') }}"/>
</div>
<button type="button" class="mt-4 text-primary font-label-bold uppercase text-sm flex items-center gap-1">
<span class="material-symbols-outlined text-sm" data-icon="upload_file">upload_file</span> Change Asset
                            </button>
</div>
<div class="industrial-border p-4 bg-surface-bright">
<p class="font-label-bold text-label-bold uppercase mb-2">Menu Spotlight</p>
<div class="space-y-2">
<div class="flex items-center gap-2 p-2 bg-secondary-container/20 border border-secondary">
<span class="material-symbols-outlined text-secondary" data-icon="star" data-weight="fill">star</span>
<span class="text-label-bold">The Foundry Classic</span>
</div>
<div class="flex items-center gap-2 p-2 border border-on-surface-variant/20">
<span class="material-symbols-outlined text-on-surface-variant" data-icon="restaurant">restaurant</span>
<span class="text-label-bold">Double-Down Pepperoni</span>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Section: Pricing Upcharges -->
<section class="lg:col-span-4 bg-surface-container-low industrial-border p-8 h-fit">
<h3 class="font-headline-md uppercase tracking-tight flex items-center gap-2 mb-6">
<span class="material-symbols-outlined text-primary" data-icon="price_change">price_change</span>
                    Pricing Upcharges
                </h3>
<div class="space-y-4">
<div>
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-1">Large Size Extra (£)</label>
<input class="w-full bg-transparent industrial-border-b border-on-surface-variant py-2 font-body-md focus:border-primary transition-colors" type="number" step="0.01" min="0" max="50" name="size_large_extra" value="{{ $settings['size_large_extra'] }}"/>
</div>
<div>
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-1">Gluten-Free Crust Extra (£)</label>
<input class="w-full bg-transparent industrial-border-b border-on-surface-variant py-2 font-body-md focus:border-primary transition-colors" type="number" step="0.01" min="0" max="50" name="crust_gluten_free_extra" value="{{ $settings['crust_gluten_free_extra'] }}"/>
</div>
<div>
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-1">Cauliflower Crust Extra (£)</label>
<input class="w-full bg-transparent industrial-border-b border-on-surface-variant py-2 font-body-md focus:border-primary transition-colors" type="number" step="0.01" min="0" max="50" name="crust_cauliflower_extra" value="{{ $settings['crust_cauliflower_extra'] }}"/>
</div>
</div>
</section>
<!-- Section 4: System Settings (Payment) -->
<section class="lg:col-span-4 bg-surface-container industrial-border p-8 h-fit">
<h3 class="font-headline-md uppercase tracking-tight flex items-center gap-2 mb-6">
<span class="material-symbols-outlined text-primary" data-icon="payments">payments</span>
                    System Ledger
                </h3>
<div class="space-y-6">
<div class="p-4 bg-surface industrial-border">
<div class="flex items-center justify-between mb-4">
<span class="font-label-bold text-label-bold uppercase">Stripe Gateway</span>
<span class="bg-secondary text-on-secondary px-2 py-0.5 text-[10px] font-bold uppercase rounded-sm">Active</span>
</div>
<p class="text-label-sm text-on-surface-variant mb-4">Processing all digital transactions via encrypted industrial protocols.</p>
<button type="button" class="text-primary font-label-bold uppercase text-sm border-b border-primary">Config Gateway</button>
</div>
<div class="flex items-center justify-between group">
<span class="font-label-bold text-label-bold uppercase">Maintenance Mode</span>
<div class="w-12 h-6 industrial-border bg-surface-container-highest relative cursor-pointer transition-colors" onclick="this.classList.toggle('bg-primary')">
<div class="absolute left-1 top-1 w-3 h-3 bg-on-surface-variant transition-transform group-active:scale-90"></div>
</div>
</div>
<div class="flex items-center justify-between group">
<span class="font-label-bold text-label-bold uppercase">Tax Automated</span>
<div class="w-12 h-6 industrial-border bg-primary relative cursor-pointer">
<div class="absolute right-1 top-1 w-3 h-3 bg-white"></div>
</div>
</div>
<div class="industrial-divider"></div>
<button type="button" class="w-full bg-on-surface text-surface py-3 font-label-bold uppercase tracking-widest industrial-border hover:bg-tertiary transition-colors">
                        Reboot Terminal
                    </button>
</div>
</section>
</div>
</form>
@endsection
