@extends('layouts.admin')
@section('content')
{{-- Delivery Zones Manager --}}
@if(session('success'))
  <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 font-mono text-xs col-span-1 lg:col-span-12">{{ session('success') }}</div>
@endif
<!-- Page Header -->
<div class="col-span-1 lg:col-span-12 mb-8 border-b-4 border-double border-outline pb-4">
<h1 class="font-display text-display text-on-background uppercase tracking-tight">Delivery Logistics</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Manage dispatch zones, active runners, and dynamic routing fees.</p>
</div>
<!-- Left Column: Map & Active Zones -->
<div class="col-span-1 lg:col-span-8 flex flex-col gap-gutter">
<!-- Map Container (Bento Box) -->
<div class="bg-surface border-2 border-outline rounded-sm overflow-hidden flex flex-col h-[500px] relative shadow-none">
<div class="bg-surface-container px-4 py-3 border-b-2 border-outline flex justify-between items-center z-10 relative">
<h2 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
<span class="material-symbols-outlined filled-icon">map</span> Dispatch Map
                    </h2>
<div class="flex gap-2">
<button class="bg-primary-container text-on-primary-container px-3 py-1 font-label-bold text-label-bold rounded-sm border border-outline hover:bg-primary hover:text-on-primary transition-colors">Edit Zones</button>
</div>
</div>
<!-- Map Area -->
<div class="flex-1 relative bg-surface-dim overflow-hidden group">
<img alt="A high-contrast, desaturated vintage city map showing street grids and topography. The map has an industrial, blueprint-like quality with sharp black lines on a light beige background. Subdued red and gold translucent polygons overlay specific neighborhoods, indicating active delivery zones. The aesthetic is practical and utilitarian, like an old dispatch ledger." class="w-full h-full object-cover opacity-80" src="https://placehold.co/800x500/e4e2e1/1b1c1c?text=Dispatch+Map"/>
<!-- Overlay UI Elements -->
<div class="absolute top-4 left-4 flex flex-col gap-2">
<div class="bg-surface border-2 border-outline px-3 py-2 flex items-center gap-2 shadow-sm">
<span class="w-3 h-3 rounded-full bg-primary-container inline-block border border-on-surface"></span>
<span class="font-label-bold text-label-bold text-on-surface">Zone A: High Volume</span>
</div>
<div class="bg-surface border-2 border-outline px-3 py-2 flex items-center gap-2 shadow-sm">
<span class="w-3 h-3 rounded-full bg-secondary-fixed inline-block border border-on-surface"></span>
<span class="font-label-bold text-label-bold text-on-surface">Zone B: Standard</span>
</div>
</div>
<!-- Fake Map Controls -->
<div class="absolute bottom-4 right-4 flex flex-col gap-1">
<button class="bg-surface border-2 border-outline p-2 text-on-surface hover:bg-surface-variant"><span class="material-symbols-outlined">add</span></button>
<button class="bg-surface border-2 border-outline p-2 text-on-surface hover:bg-surface-variant"><span class="material-symbols-outlined">remove</span></button>
</div>
</div>
</div>
<!-- Active Runners List -->
<div class="bg-surface border-2 border-outline rounded-sm flex flex-col">
<div class="bg-surface-container px-4 py-3 border-b-2 border-outline flex justify-between items-center">
<h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
<span class="material-symbols-outlined filled-icon">moped</span> Active Runners
                    </h3>
<span class="bg-primary text-on-primary font-label-bold text-label-bold px-3 py-1 rounded-full">4 on Route</span>
</div>
<div class="flex flex-col divide-y-2 divide-outline">
<!-- Runner Item -->
<div class="flex items-center justify-between p-4 hover:bg-surface-container-low transition-colors">
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-secondary-container rounded-full border-2 border-outline flex items-center justify-center font-headline-md text-headline-md text-on-secondary-container">JR</div>
<div>
<p class="font-label-bold text-label-bold text-on-surface">Jimmy Rossi</p>
<p class="font-body-md text-body-md text-on-surface-variant flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">location_on</span> Zone A (En Route)</p>
</div>
</div>
<div class="text-right">
<p class="font-headline-md text-headline-md text-primary">12 mins</p>
<p class="font-label-sm text-label-sm text-on-surface-variant">Est. Return</p>
</div>
</div>
<!-- Runner Item -->
<div class="flex items-center justify-between p-4 hover:bg-surface-container-low transition-colors">
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-surface-variant rounded-full border-2 border-outline flex items-center justify-center font-headline-md text-headline-md text-on-surface-variant">SA</div>
<div>
<p class="font-label-bold text-label-bold text-on-surface">Sal Agnello</p>
<p class="font-body-md text-body-md text-on-surface-variant flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">storefront</span> Foundry (Waiting)</p>
</div>
</div>
<div class="text-right">
<p class="font-headline-md text-headline-md text-on-surface">--</p>
<p class="font-label-sm text-label-sm text-on-surface-variant">Ready</p>
</div>
</div>
<!-- Runner Item -->
<div class="flex items-center justify-between p-4 hover:bg-surface-container-low transition-colors">
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-secondary-container rounded-full border-2 border-outline flex items-center justify-center font-headline-md text-headline-md text-on-secondary-container">MV</div>
<div>
<p class="font-label-bold text-label-bold text-on-surface">Marco Vitale</p>
<p class="font-body-md text-body-md text-on-surface-variant flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">location_on</span> Zone B (En Route)</p>
</div>
</div>
<div class="text-right">
<p class="font-headline-md text-headline-md text-primary">22 mins</p>
<p class="font-label-sm text-label-sm text-on-surface-variant">Est. Return</p>
</div>
</div>
<!-- Runner Item -->
<div class="flex items-center justify-between p-4 hover:bg-surface-container-low transition-colors">
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-secondary-container rounded-full border-2 border-outline flex items-center justify-center font-headline-md text-headline-md text-on-secondary-container">TM</div>
<div>
<p class="font-label-bold text-label-bold text-on-surface">Tony Moretti</p>
<p class="font-body-md text-body-md text-on-surface-variant flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">location_on</span> Zone A (En Route)</p>
</div>
</div>
<div class="text-right">
<p class="font-headline-md text-headline-md text-primary">35 mins</p>
<p class="font-label-sm text-label-sm text-on-surface-variant">Est. Return</p>
</div>
</div>
</div>
</div>
</div>
<!-- Right Column: Settings & Fees -->
<div class="col-span-1 lg:col-span-4 flex flex-col gap-gutter">
{{-- Delivery Zones Manager --}}
<div class="mb-8 bg-surface border-2 border-outline p-4">
  <h2 class="font-serif text-xl font-bold text-on-surface mb-4 uppercase">Delivery Zone Fees</h2>
  <div class="flex flex-col gap-3">
    @foreach($zones as $zone)
    <div x-data="{ editing: false }" class="border-b border-outline pb-3 mb-3">
      <div class="flex items-center justify-between gap-2">
        <div class="flex-1 min-w-0">
          <span class="font-mono text-[10px] font-bold text-on-surface uppercase">{{ $zone->name }}</span>
          <span class="font-mono text-[9px] text-on-surface-variant block">
            Fee: £{{ $zone->fee }} ·
            {{ $zone->is_active ? 'Active' : 'Inactive' }}
            @if($zone->postcodes)
              · {{ $zone->postcodes }}
            @else
              · <span class="text-error">No postcodes set</span>
            @endif
          </span>
        </div>
        <button @click="editing = !editing" class="font-mono text-[10px] text-primary hover:underline uppercase shrink-0">EDIT</button>
        <form method="POST" action="{{ route('admin.delivery.destroy', $zone->id) }}" onsubmit="return confirm('Delete {{ addslashes($zone->name) }}?')">
          @csrf @method('DELETE')
          <button type="submit" class="font-mono text-[10px] text-brand-error hover:underline uppercase">DEL</button>
        </form>
      </div>
      <form x-show="editing" x-cloak x-transition
            method="POST" action="{{ route('admin.delivery.update', $zone->id) }}"
            class="mt-3 bg-surface-container-low border border-outline p-3 flex flex-col gap-2">
        @csrf @method('PUT')
        <input type="text" name="name" value="{{ $zone->name }}" required
               placeholder="Zone name" class="w-full border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0 bg-transparent">
        <div class="grid grid-cols-3 gap-2">
          <div>
            <label class="font-mono text-[9px] uppercase text-on-surface-variant">From km</label>
            <input type="number" name="min_km" step="0.1" min="0" value="{{ $zone->min_km }}" required
                   class="w-full border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0 bg-transparent">
          </div>
          <div>
            <label class="font-mono text-[9px] uppercase text-on-surface-variant">To km</label>
            <input type="number" name="max_km" step="0.1" min="0" value="{{ $zone->max_km }}" required
                   class="w-full border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0 bg-transparent">
          </div>
          <div>
            <label class="font-mono text-[9px] uppercase text-on-surface-variant">Fee £</label>
            <input type="number" name="fee" step="0.01" min="0" value="{{ $zone->fee }}" required
                   class="w-full border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0 bg-transparent">
          </div>
        </div>
        <div>
          <label class="font-mono text-[9px] uppercase text-on-surface-variant">Postcode Districts (comma-separated)</label>
          <input type="text" name="postcodes" value="{{ $zone->postcodes }}"
                 placeholder="e.g. NW5, N7, N19"
                 class="w-full border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0 bg-transparent">
          <span class="font-mono text-[9px] text-on-surface-variant">Customers outside these postcodes cannot order delivery.</span>
        </div>
        <div class="flex items-center gap-2">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" value="1" id="active_{{ $zone->id }}" {{ $zone->is_active ? 'checked' : '' }}>
          <label for="active_{{ $zone->id }}" class="font-mono text-[10px] uppercase">Active</label>
        </div>
        <div class="flex gap-2">
          <button type="submit" class="font-mono text-[10px] text-on-primary bg-primary px-3 py-1 uppercase hover:bg-primary-container hover:text-on-primary-container transition-colors">SAVE</button>
          <button type="button" @click="editing = false" class="font-mono text-[10px] uppercase border border-outline px-3 py-1">CANCEL</button>
        </div>
      </form>
    </div>
    @endforeach

    <form method="POST" action="{{ route('admin.delivery.store') }}" class="pt-3 flex flex-col gap-2">
      @csrf
      <p class="font-mono text-[10px] uppercase text-on-surface-variant font-bold">Add New Zone</p>
      <input type="text" name="name" placeholder="Zone name" required class="w-full border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0">
      <div class="grid grid-cols-3 gap-2">
        <input type="number" name="min_km" placeholder="From km" step="0.1" min="0" required class="border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0">
        <input type="number" name="max_km" placeholder="To km" step="0.1" min="0" required class="border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0">
        <input type="number" name="fee" placeholder="Fee £" step="0.01" min="0" required class="border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0">
      </div>
      <div>
        <input type="text" name="postcodes" placeholder="Postcode districts e.g. NW5, N7" class="w-full border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0">
      </div>
      <input type="hidden" name="is_active" value="1">
      <button type="submit" class="w-full bg-primary text-on-primary py-2 font-mono text-[10px] font-bold uppercase mt-1">ADD ZONE</button>
    </form>
  </div>
</div>
<!-- Quick Actions -->
<div class="bg-surface-container-high border-2 border-outline p-4 flex flex-col gap-3 relative overflow-hidden">
<div class="absolute -right-4 -top-4 opacity-5 pointer-events-none">
<span class="material-symbols-outlined text-[120px]">local_shipping</span>
</div>
<h4 class="font-headline-md text-headline-md text-on-surface border-b-2 border-outline pb-2 mb-2">Dispatch Log</h4>
<button class="w-full text-left font-body-lg text-body-lg text-on-surface py-2 border-b border-outline hover:text-primary transition-colors flex justify-between items-center">
                    Review End of Day Report <span class="material-symbols-outlined">arrow_forward</span>
</button>
<button class="w-full text-left font-body-lg text-body-lg text-on-surface py-2 border-b border-outline hover:text-primary transition-colors flex justify-between items-center">
                    Manage Fleet Vehicles <span class="material-symbols-outlined">arrow_forward</span>
</button>
<button class="w-full text-left font-body-lg text-body-lg text-error py-2 hover:text-primary transition-colors flex justify-between items-center">
                    Halt All Deliveries <span class="material-symbols-outlined">warning</span>
</button>
</div>
</div>
@endsection
