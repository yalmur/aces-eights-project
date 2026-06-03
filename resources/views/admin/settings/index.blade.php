@extends('layouts.admin')
@section('content')
<header class="mb-12">
<h2 class="font-display text-headline-lg uppercase border-b-4 border-primary inline-block pb-2 mb-4">General Settings</h2>
<p class="font-body-lg text-on-surface-variant max-w-2xl">Refine the operational blueprint of the workshop. Manage physical presence, logistical hours, and the digital ledger.</p>
</header>
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
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-1">Workshop Address</label>
<input class="w-full bg-transparent industrial-border-b border-on-surface-variant py-2 font-body-md focus:border-primary transition-colors" type="text" value="156 &amp; 158 Fortess Road, Tufnell Park, London, NW5 2HP"/>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-1">Command Phone</label>
<input class="w-full bg-transparent industrial-border-b border-on-surface-variant py-2 font-body-md focus:border-primary transition-colors" type="text" value="+44 020 7485 4033"/>
</div>
<div>
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-1">Dispatch Email</label>
<input class="w-full bg-transparent industrial-border-b border-on-surface-variant py-2 font-body-md focus:border-primary transition-colors" type="email" value="nw5pizza@gmail.com"/>
</div>
</div>
</div>
<div class="industrial-divider"></div>
<div class="flex justify-end">
<button class="bg-primary text-on-primary px-8 py-3 font-label-bold uppercase tracking-widest industrial-border hover:bg-on-primary-fixed-variant transition-colors flex items-center gap-2">
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
<span class="font-label-bold text-label-bold uppercase w-24">Mon - Thu</span>
<div class="flex items-center gap-2">
<input class="w-16 bg-transparent text-center industrial-border-b border-on-surface-variant/40 py-1" type="text" value="16:00"/>
<span>-</span>
<input class="w-16 bg-transparent text-center industrial-border-b border-on-surface-variant/40 py-1" type="text" value="22:45"/>
</div>
<div class="flex items-center gap-2">
<div class="w-10 h-5 bg-primary rounded-full relative cursor-pointer">
<div class="absolute right-1 top-1 w-3 h-3 bg-white rounded-full"></div>
</div>
</div>
</div>
<!-- Day Row -->
<div class="flex items-center justify-between py-2 border-b border-on-surface-variant/20">
<span class="font-label-bold text-label-bold uppercase w-24">Friday</span>
<div class="flex items-center gap-2">
<input class="w-16 bg-transparent text-center industrial-border-b border-on-surface-variant/40 py-1" type="text" value="16:00"/>
<span>-</span>
<input class="w-16 bg-transparent text-center industrial-border-b border-on-surface-variant/40 py-1" type="text" value="23:15"/>
</div>
<div class="flex items-center gap-2">
<div class="w-10 h-5 bg-primary rounded-full relative cursor-pointer">
<div class="absolute right-1 top-1 w-3 h-3 bg-white rounded-full"></div>
</div>
</div>
</div>
<!-- Day Row -->
<div class="flex items-center justify-between py-2 border-b border-on-surface-variant/20">
<span class="font-label-bold text-label-bold uppercase w-24">Saturday</span>
<div class="flex items-center gap-2">
<input class="w-16 bg-transparent text-center industrial-border-b border-on-surface-variant/40 py-1" type="text" value="16:00"/>
<span>-</span>
<input class="w-16 bg-transparent text-center industrial-border-b border-on-surface-variant/40 py-1" type="text" value="23:15"/>
</div>
<div class="flex items-center gap-2">
<div class="w-10 h-5 bg-primary rounded-full relative cursor-pointer">
<div class="absolute right-1 top-1 w-3 h-3 bg-white rounded-full"></div>
</div>
</div>
</div>
<!-- Day Row -->
<div class="flex items-center justify-between py-2 border-b border-on-surface-variant/20">
<span class="font-label-bold text-label-bold uppercase w-24">Sunday</span>
<div class="flex items-center gap-2">
<input class="w-16 bg-transparent text-center industrial-border-b border-on-surface-variant/40 py-1" type="text" value="16:00"/>
<span>-</span>
<input class="w-16 bg-transparent text-center industrial-border-b border-on-surface-variant/40 py-1" type="text" value="22:45"/>
</div>
<div class="flex items-center gap-2">
<div class="w-10 h-5 bg-primary rounded-full relative cursor-pointer">
<div class="absolute right-1 top-1 w-3 h-3 bg-white rounded-full"></div>
</div>
</div>
</div>
</div>
<button class="w-full mt-8 industrial-border py-2 font-label-bold uppercase hover:bg-surface-container-highest transition-colors">Lock Logistical Hours</button>
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
<textarea class="w-full bg-surface-container-low industrial-border p-4 font-body-md focus:border-primary transition-colors resize-none" rows="3">FORGED IN THE FIRE OF TRADITION. SERVED WITH INDUSTRIAL PRECISION. ACES &amp; EIGHTS PIZZA — ESTABLISHED 2012.</textarea>
</div>
<div>
<label class="font-label-bold text-label-bold uppercase text-on-surface-variant block mb-2">Our Story / Heritage Text</label>
<textarea class="w-full bg-surface-container-low industrial-border p-4 font-body-md focus:border-primary transition-colors resize-none" rows="4">Born from the hum of machinery and the heat of the forge, Aces &amp; Eights was founded on the premise that the best food is made by hand, with tools that have stood the test of time.</textarea>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="industrial-border p-4 bg-surface-bright">
<p class="font-label-bold text-label-bold uppercase mb-2">Primary Asset</p>
<div class="h-32 industrial-border bg-surface-container-highest flex items-center justify-center overflow-hidden">
<img alt="Active Brand Asset" class="h-16 w-auto opacity-50 grayscale hover:grayscale-0 transition-all" src="{{ asset('images/logo.jpg') }}"/>
</div>
<button class="mt-4 text-primary font-label-bold uppercase text-sm flex items-center gap-1">
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
<button class="text-primary font-label-bold uppercase text-sm border-b border-primary">Config Gateway</button>
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
<button class="w-full bg-on-surface text-surface py-3 font-label-bold uppercase tracking-widest industrial-border hover:bg-tertiary transition-colors">
                        Reboot Terminal
                    </button>
</div>
</section>
</div>
@endsection
