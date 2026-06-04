@extends('layouts.admin')
@section('content')

@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">{{ session('success') }}</div>
@endif

{{-- Header --}}
<section class="mb-8">
  <h2 class="font-serif text-4xl font-black text-on-surface uppercase tracking-tight">ALLERGY MANAGEMENT</h2>
  <div class="double-divider text-on-surface-variant mt-2"></div>
</section>

{{-- Section 1: Global Allergy Alerts + Disclaimer --}}
<form method="POST" action="{{ route('admin.allergy.settings') }}" class="mb-10">
  @csrf
  <section class="bg-surface-container-low border-2 border-on-surface p-6">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1">campaign</span>
        <h3 class="font-mono text-xs font-bold uppercase">Global Safety Alerts</h3>
      </div>
      <label class="relative inline-flex items-center cursor-pointer">
        <input name="allergy_alerts_enabled" type="checkbox" value="1" {{ $alertsEnabled ? 'checked' : '' }} class="sr-only peer"/>
        <div class="w-11 h-6 bg-surface-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
      </label>
    </div>
    <p class="font-sans text-sm text-on-surface-variant leading-relaxed mb-6">
      When enabled, a high-visibility warning banner will appear across the top of the consumer site regarding ingredient cross-contamination and the current allergy protocol.
    </p>
    <div>
      <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-2">Checkout Disclaimer</label>
      <textarea name="checkout_disclaimer" rows="5" class="w-full border-2 border-on-surface p-3 font-sans text-sm focus:ring-0 focus:border-primary resize-none">{{ $disclaimer }}</textarea>
    </div>
    <div class="flex justify-end mt-4">
      <button type="submit" class="bg-primary text-on-primary px-6 py-3 font-mono text-xs font-bold uppercase">PUBLISH CHANGES</button>
    </div>
  </section>
</form>

{{-- Section 2: Allergen Library --}}
<section class="mb-10">
  <div class="flex items-center justify-between mb-4">
    <h3 class="font-mono text-xs font-bold uppercase">Allergen Library</h3>
    <button class="text-primary font-mono text-[10px] font-bold flex items-center gap-1 border-b border-primary uppercase">
      <span class="material-symbols-outlined text-[18px]">add</span> ADD NEW
    </button>
  </div>
  <div class="grid grid-cols-1 gap-3">
    @foreach($allergens as $allergen)
    <div class="bg-surface border border-on-surface flex items-center p-3 justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-primary-container flex items-center justify-center text-on-primary">
          <span class="material-symbols-outlined">{{ $allergen->icon }}</span>
        </div>
        <span class="font-mono text-xs font-bold uppercase">{{ $allergen->name }}</span>
      </div>
      <div class="flex items-center gap-3">
        <form method="POST" action="{{ route('admin.allergens.toggle', $allergen->id) }}">
          @csrf @method('PATCH')
          <button type="submit" class="material-symbols-outlined text-on-surface-variant hover:text-primary cursor-pointer" title="{{ $allergen->is_visible ? 'Visible' : 'Hidden' }}" style="{{ $allergen->is_visible ? 'font-variation-settings:\'FILL\' 1' : '' }}">
            {{ $allergen->is_visible ? 'visibility' : 'visibility_off' }}
          </button>
        </form>
        <form method="POST" action="{{ route('admin.allergens.destroy', $allergen->id) }}" onsubmit="return confirm('Delete {{ addslashes($allergen->name) }}?')">
          @csrf @method('DELETE')
          <button type="submit" class="material-symbols-outlined text-brand-error hover:opacity-70 cursor-pointer">delete</button>
        </form>
      </div>
    </div>
    @endforeach
  </div>
</section>

{{-- Section 3: Menu Item Mapping --}}
<section class="mb-10">
  <h3 class="font-mono text-xs font-bold uppercase mb-4">Menu Item Allergen Mapping</h3>
  <form method="POST" action="{{ route('admin.allergy.map') }}">
    @csrf
    <div class="mb-4">
      <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-2">Select Menu Item</label>
      <select name="menu_item_id" required class="w-full bg-surface border-0 border-b-2 border-on-surface py-3 font-sans text-sm focus:ring-0 focus:border-primary">
        <option value="">— Choose item —</option>
        @foreach($menuItems as $item)
          <option value="{{ $item->id }}">{{ $item->category->name }} — {{ $item->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-4">
      <p class="font-mono text-[10px] uppercase mb-3 text-on-surface-variant">Allergen Tags:</p>
      <div class="flex flex-wrap gap-2">
        @foreach($allergens as $allergen)
        <label class="flex items-center gap-2 cursor-pointer px-3 py-1.5 border border-outline-variant hover:border-primary transition-colors">
          <input type="checkbox" name="allergens[]" value="{{ $allergen->id }}" class="w-4 h-4 text-primary border-outline rounded focus:ring-primary">
          <span class="font-mono text-[10px] uppercase">{{ $allergen->name }}</span>
        </label>
        @endforeach
      </div>
    </div>
    <button type="submit" class="w-full border-2 border-on-surface py-3 text-on-surface font-mono text-xs font-bold uppercase hover:bg-surface-container-highest transition-all">
      SAVE ITEM MAPPING
    </button>
  </form>
</section>

@endsection
