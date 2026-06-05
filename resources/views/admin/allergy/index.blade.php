@extends('layouts.admin')
@section('content')

@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">
    {{ session('success') }}
  </div>
@endif

<header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
  <div>
    <h2 class="font-serif text-4xl font-black text-on-surface uppercase tracking-tight leading-none mb-2">Allergy Management</h2>
    <p class="font-mono text-xs text-on-surface-variant uppercase tracking-widest">Allergen labels · menu item mappings · checkout warnings</p>
  </div>
</header>

<div class="industrial-divider mb-8"></div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

  {{-- ── Allergen List ───────────────────────────────── --}}
  <section>
    <h3 class="font-mono text-xs font-bold uppercase border-b border-[#2B2B2B] pb-2 mb-4">Allergen Labels</h3>

    @forelse($allergens as $allergen)
    <div class="industrial-border p-4 mb-3 flex items-center justify-between gap-4 bg-white">
      <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-primary text-lg">{{ $allergen->icon }}</span>
        <div>
          <p class="font-mono text-sm font-bold">{{ $allergen->name }}</p>
          <p class="font-mono text-[10px] text-on-surface-variant uppercase">{{ $allergen->is_visible ? 'Visible' : 'Hidden' }}</p>
        </div>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <form action="{{ route('admin.allergens.toggle', $allergen->id) }}" method="POST">
          @csrf @method('PATCH')
          <button type="submit"
                  class="px-3 py-1 font-mono text-[10px] font-bold uppercase industrial-border hover:bg-surface-container transition-colors {{ $allergen->is_visible ? 'text-green-700' : 'text-on-surface-variant' }}">
            {{ $allergen->is_visible ? 'Visible' : 'Hidden' }}
          </button>
        </form>
        <form action="{{ route('admin.allergens.destroy', $allergen->id) }}" method="POST"
              onsubmit="return confirm('Remove {{ addslashes($allergen->name) }}?')">
          @csrf @method('DELETE')
          <button type="submit" class="p-1 hover:text-error transition-colors">
            <span class="material-symbols-outlined text-sm">delete</span>
          </button>
        </form>
      </div>
    </div>
    @empty
    <p class="font-mono text-xs text-on-surface-variant py-4 uppercase">No allergens configured yet.</p>
    @endforelse

    {{-- Add new allergen --}}
    <div x-data="{ open: false }" class="mt-4">
      <button @click="open = !open"
              class="gold-button px-4 py-2 font-mono text-xs font-bold uppercase flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">add</span> Add Allergen
      </button>
      <form x-show="open" x-cloak x-transition
            action="{{ route('admin.allergens.store') }}" method="POST"
            class="mt-4 industrial-border p-4 bg-surface-container-low space-y-3">
        @csrf
        <div>
          <label class="font-mono text-[10px] font-bold uppercase block mb-1">Name *</label>
          <input type="text" name="name" required placeholder="e.g. Gluten"
                 class="w-full industrial-border p-2 font-mono text-sm bg-white focus:outline-none focus:ring-1 focus:ring-primary">
        </div>
        <div>
          <label class="font-mono text-[10px] font-bold uppercase block mb-1">Material Icon Name</label>
          <input type="text" name="icon" placeholder="warning" value="warning"
                 class="w-full industrial-border p-2 font-mono text-sm bg-white focus:outline-none focus:ring-1 focus:ring-primary">
          <p class="font-mono text-[9px] text-on-surface-variant mt-1">Use any Material Symbols name, e.g. wheat, egg_alt, dairy, no_food</p>
        </div>
        <div>
          <label class="font-mono text-[10px] font-bold uppercase block mb-1">Sort Order</label>
          <input type="number" name="sort_order" value="0" min="0"
                 class="w-24 industrial-border p-2 font-mono text-sm bg-white focus:outline-none focus:ring-1 focus:ring-primary">
        </div>
        <div class="flex gap-2 pt-1">
          <button type="submit" class="gold-button px-4 py-2 font-mono text-xs font-bold uppercase">Save</button>
          <button type="button" @click="open = false"
                  class="px-4 py-2 font-mono text-xs font-bold uppercase industrial-border hover:bg-surface-container transition-colors">Cancel</button>
        </div>
      </form>
    </div>
  </section>

  {{-- ── Alert Settings ──────────────────────────────── --}}
  <section>
    <h3 class="font-mono text-xs font-bold uppercase border-b border-[#2B2B2B] pb-2 mb-4">Checkout Alert Settings</h3>
    <form action="{{ route('admin.allergy.settings') }}" method="POST" class="space-y-5">
      @csrf
      <div class="industrial-border p-4 bg-white flex items-center justify-between gap-4">
        <div>
          <p class="font-mono text-sm font-bold">Show Allergy Alerts at Checkout</p>
          <p class="font-mono text-[10px] text-on-surface-variant mt-1">Displays disclaimer on checkout page</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer shrink-0">
          <input type="hidden" name="allergy_alerts_enabled" value="0">
          <input type="checkbox" name="allergy_alerts_enabled" value="1"
                 class="sr-only peer" {{ $alertsEnabled ? 'checked' : '' }}>
          <div class="w-11 h-6 bg-surface-container border border-[#2B2B2B] rounded-full relative
                      peer-checked:bg-primary
                      after:content-[''] after:absolute after:top-0.5 after:left-[2px]
                      after:bg-white after:border after:border-[#2B2B2B] after:rounded-full
                      after:h-5 after:w-5 after:transition-all
                      peer-checked:after:translate-x-full"></div>
        </label>
      </div>

      <div>
        <label class="font-mono text-xs font-bold uppercase block mb-2">Checkout Disclaimer Text</label>
        <textarea name="checkout_disclaimer" rows="7"
                  class="w-full industrial-border p-3 font-mono text-xs bg-white resize-y focus:outline-none focus:ring-1 focus:ring-primary">{{ old('checkout_disclaimer', $disclaimer) }}</textarea>
        <p class="font-mono text-[10px] text-on-surface-variant mt-1">Displayed to customers at checkout when alerts are enabled.</p>
      </div>

      <button type="submit" class="gold-button px-6 py-3 font-mono text-xs font-bold uppercase">
        Save Settings
      </button>
    </form>
  </section>

</div>

{{-- ── Menu Item → Allergen Mapping ────────────────────────────────── --}}
<section class="mt-12">
  <h3 class="font-mono text-xs font-bold uppercase border-b border-[#2B2B2B] pb-2 mb-6">Menu Item Allergen Mapping</h3>

  @if($allergens->isEmpty())
    <p class="font-mono text-xs text-on-surface-variant">Add allergens above before mapping them to menu items.</p>
  @else
  <div x-data="{ selected: '' }" class="space-y-4">
    <div>
      <label class="font-mono text-xs font-bold uppercase block mb-2">Select Menu Item</label>
      <select x-model="selected"
              class="industrial-border p-3 font-mono text-sm bg-white w-full max-w-md focus:outline-none focus:ring-1 focus:ring-primary">
        <option value="">— choose item —</option>
        @foreach($menuItems as $item)
        <option value="{{ $item->id }}">{{ $item->name }}@if($item->category) ({{ $item->category->name }})@endif</option>
        @endforeach
      </select>
    </div>

    @foreach($menuItems as $item)
    <form x-show="selected == '{{ $item->id }}'" x-cloak
          action="{{ route('admin.allergy.map') }}" method="POST"
          class="industrial-border p-6 bg-white">
      @csrf
      <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
      <p class="font-serif text-lg font-bold mb-1">{{ $item->name }}</p>
      <p class="font-mono text-[10px] text-on-surface-variant uppercase mb-4">{{ $item->category->name ?? '—' }}</p>

      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
        @foreach($allergens as $allergen)
        <label class="flex items-center gap-2 cursor-pointer industrial-border p-2 hover:bg-surface-container-low transition-colors">
          <input type="checkbox" name="allergens[]" value="{{ $allergen->id }}"
                 {{ $item->allergens->contains($allergen->id) ? 'checked' : '' }}
                 class="w-4 h-4 accent-primary">
          <span class="material-symbols-outlined text-sm text-primary">{{ $allergen->icon }}</span>
          <span class="font-mono text-xs">{{ $allergen->name }}</span>
        </label>
        @endforeach
      </div>

      <button type="submit" class="gold-button px-6 py-2 font-mono text-xs font-bold uppercase">
        Save Mapping
      </button>
    </form>
    @endforeach
  </div>
  @endif
</section>

@endsection
