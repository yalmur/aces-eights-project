@extends('layouts.admin')
@section('content')

@php $isEdit = $deal !== null; @endphp

<div class="flex items-center gap-4 mb-8">
  <a href="{{ route('admin.deals.index') }}" class="text-on-surface-variant hover:text-primary transition-colors">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <div>
    <h1 class="font-serif text-4xl font-black text-on-surface uppercase">{{ $title }}</h1>
    <p class="font-sans text-sm text-on-surface-variant mt-1">Configure deal settings and slots.</p>
  </div>
</div>
<div class="double-divider mb-8"></div>

@if($errors->any())
  <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 font-mono text-xs">
    <ul class="list-disc pl-4 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

<form method="POST" enctype="multipart/form-data"
      action="{{ $isEdit ? route('admin.deals.update', $deal->id) : route('admin.deals.store') }}"
      x-data="dealBuilder({{ json_encode($deal?->slotsAdminPayload() ?? []) }}, {{ json_encode($deal?->deal_type ?? 'bundle') }})">
  @csrf
  @if($isEdit) @method('PUT') @endif

  {{-- ── SECTION 1: BASICS ── --}}
  <div class="industrial-border bg-white p-8 mb-8">
    <h2 class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-6">1. Deal Info</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div>
        <label class="admin-label" for="name">Name *</label>
        <input id="name" name="name" type="text" required maxlength="120"
               value="{{ old('name', $deal?->name) }}"
               class="admin-input w-full" placeholder="e.g. Family Feast Deal">
      </div>
      <div>
        <label class="admin-label" for="deal_type">Deal Type *</label>
        <select id="deal_type" name="deal_type" x-model="dealType" class="admin-input w-full">
          <option value="bundle">Bundle (fixed price)</option>
          <option value="bogo">Buy 1 Get 1 Free</option>
          <option value="percentage_off">Percentage Off</option>
          <option value="fixed_off">Fixed Amount Off</option>
        </select>
      </div>
    </div>

    <div class="mb-6">
      <label class="admin-label" for="custom_label">Custom Label <span class="normal-case font-sans font-normal tracking-normal">(optional — overrides the auto-generated badge text, e.g. "Family Deal" on a Bundle)</span></label>
      <input id="custom_label" name="custom_label" type="text" maxlength="120"
             value="{{ old('custom_label', $deal?->custom_label) }}"
             class="admin-input w-full" placeholder="Leave blank to use the default label">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      {{-- Price: bundle only --}}
      <div x-show="dealType === 'bundle'">
        <label class="admin-label" for="price">Bundle Price (£) *</label>
        <input id="price" name="price" type="number" step="0.01" min="0"
               value="{{ old('price', $deal?->price) }}"
               class="admin-input w-full" placeholder="12.99">
      </div>
      {{-- Discount value: percentage_off / fixed_off --}}
      <div x-show="dealType === 'percentage_off' || dealType === 'fixed_off'">
        <label class="admin-label" for="discount_value" x-text="dealType === 'percentage_off' ? 'Discount %' : 'Discount £'"></label>
        <input id="discount_value" name="discount_value" type="number" step="0.01" min="0"
               value="{{ old('discount_value', $deal?->discount_value) }}"
               class="admin-input w-full" placeholder="10">
      </div>
      <div>
        <label class="admin-label" for="sort_order">Sort Order</label>
        <input id="sort_order" name="sort_order" type="number" min="0"
               value="{{ old('sort_order', $deal?->sort_order ?? 0) }}"
               class="admin-input w-full">
      </div>
    </div>

    <div class="mb-6">
      <label class="admin-label" for="description">Description</label>
      <textarea id="description" name="description" rows="2" maxlength="500"
                class="admin-input w-full"
                placeholder="Short description shown to customers…">{{ old('description', $deal?->description) }}</textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div>
        <label class="admin-label" for="starts_at">Available From</label>
        <input id="starts_at" name="starts_at" type="datetime-local"
               value="{{ old('starts_at', $deal?->starts_at?->format('Y-m-d\TH:i')) }}"
               class="admin-input w-full">
      </div>
      <div>
        <label class="admin-label" for="ends_at">Available Until</label>
        <input id="ends_at" name="ends_at" type="datetime-local"
               value="{{ old('ends_at', $deal?->ends_at?->format('Y-m-d\TH:i')) }}"
               class="admin-input w-full">
      </div>
    </div>

    <div class="mb-6">
      <label class="admin-label" for="image">Deal Image</label>
      @if($isEdit && $deal->image_path)
        <div class="mb-2 flex items-center gap-3">
          <img src="{{ asset('storage/' . $deal->image_path) }}" class="h-20 w-32 object-cover border border-surface-variant">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remove_image" value="1" class="w-4 h-4 accent-primary">
            <span class="font-mono text-[10px] uppercase tracking-wide text-brand-error">Remove image</span>
          </label>
        </div>
      @endif
      <input id="image" name="image" type="file" accept="image/png,image/jpeg,image/webp" class="admin-input w-full">
    </div>

    <div class="flex items-center gap-3">
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
               {{ old('is_active', $deal?->is_active ?? true) ? 'checked' : '' }}>
        <div class="w-11 h-6 bg-surface-container peer-checked:bg-primary rounded-full transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:w-5 after:h-5 after:rounded-full after:transition-transform peer-checked:after:translate-x-5"></div>
      </label>
      <span class="font-mono text-xs uppercase tracking-widest">Active</span>
    </div>
  </div>

  {{-- ── SECTION 2: SLOTS (bundle + bogo only) ── --}}
  <div x-show="dealType === 'bundle' || dealType === 'bogo'" x-cloak class="industrial-border bg-white p-8 mb-8">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant">2. Slots</h2>
        <p class="font-sans text-xs text-on-surface-variant mt-1">
          Define what the customer picks. Manually choose which items are allowed in each slot.
        </p>
      </div>
      <button type="button" @click="addSlot()"
              class="gold-button px-4 py-2 font-mono text-[10px] uppercase font-bold flex items-center gap-1">
        <span class="material-symbols-outlined text-[16px]">add</span> Add Slot
      </button>
    </div>

    <div class="space-y-6">
      <template x-for="(slot, i) in slots" :key="slot._key">
        <div class="border border-surface-variant bg-surface-container-low p-6">
          <div class="flex justify-between items-start mb-4">
            <span class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant" x-text="'Slot ' + (i+1)"></span>
            <button type="button" @click="removeSlot(slot._key)"
                    class="text-brand-error hover:opacity-70 transition-opacity">
              <span class="material-symbols-outlined text-[18px]">delete</span>
            </button>
          </div>

          {{-- Hidden inputs --}}
          <input type="hidden" :name="'slots['+i+'][sort_order]'" :value="i">

          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <div class="md:col-span-2">
              <label class="admin-label">Slot Label *</label>
              <input type="text" :name="'slots['+i+'][label]'" x-model="slot.label"
                     class="admin-input w-full" placeholder="e.g. Main / Side / Drink">
            </div>
            <div>
              <label class="admin-label">Min Qty</label>
              <input type="number" :name="'slots['+i+'][min_qty]'" x-model.number="slot.min_qty"
                     min="1" max="10" class="admin-input w-full">
            </div>
            <div>
              <label class="admin-label">Max Qty</label>
              <input type="number" :name="'slots['+i+'][max_qty]'" x-model.number="slot.max_qty"
                     min="1" max="10" class="admin-input w-full">
            </div>
          </div>

          <div class="flex items-center gap-6 mb-4">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="hidden" :name="'slots['+i+'][is_required]'" value="0">
              <input type="checkbox" :name="'slots['+i+'][is_required]'" value="1"
                     x-model="slot.is_required"
                     class="w-4 h-4 accent-primary">
              <span class="font-mono text-[10px] uppercase tracking-wide">Required</span>
            </label>
            <label x-show="dealType === 'bogo'" class="flex items-center gap-2 cursor-pointer">
              <input type="hidden" :name="'slots['+i+'][is_free]'" value="0">
              <input type="checkbox" :name="'slots['+i+'][is_free]'" value="1"
                     x-model="slot.is_free"
                     class="w-4 h-4 accent-primary">
              <span class="font-mono text-[10px] uppercase tracking-wide text-green-700">Free (BOGO get slot)</span>
            </label>
          </div>

          {{-- Items in this slot (manual only — no category auto-include) --}}
          <div class="mb-3">
            <p class="admin-label mb-2">
              Items in This Slot *
              <span class="normal-case font-sans font-normal tracking-normal" :class="slot.item_ids.length === 0 ? 'text-brand-error' : 'text-on-surface-variant'"
                    x-text="slot.item_ids.length === 0 ? '(pick at least one item)' : '(' + slot.item_ids.length + ' selected)'"></span>
            </p>
            <div class="flex gap-2 mb-2">
              <input type="text" x-model="slot.itemSearch" placeholder="Find item…"
                     class="admin-input flex-1 text-xs">
              <select x-model="slot.itemCategory" class="admin-input text-xs">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="max-h-48 overflow-y-auto border border-surface-variant bg-white p-3">
              <div class="grid grid-cols-2 md:grid-cols-3 gap-1">
                @foreach($menuItems as $item)
                <label class="flex items-center gap-1.5 cursor-pointer p-1.5 hover:bg-surface-container-low rounded transition-colors"
                       x-show="(!slot.itemCategory || slot.itemCategory == {{ $item->category_id }}) && {{ json_encode(strtolower($item->name)) }}.includes(slot.itemSearch.toLowerCase())">
                  <input type="checkbox" :name="'slots['+i+'][item_ids][]'" value="{{ $item->id }}"
                         @change="toggleItem(slot, {{ $item->id }})"
                         :checked="slot.item_ids.includes({{ $item->id }})"
                         class="w-3.5 h-3.5 accent-primary flex-shrink-0">
                  <span class="font-sans text-[11px] text-on-surface truncate">{{ $item->name }}</span>
                </label>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </template>

      <div x-show="slots.length === 0" class="py-8 text-center font-mono text-xs text-on-surface-variant uppercase tracking-widest">
        No slots yet — click "Add Slot" to define what customers can choose.
      </div>
    </div>
  </div>

  {{-- Submit --}}
  <div class="flex gap-4">
    <button type="submit" class="btn-primary px-8 py-3 font-mono text-xs uppercase font-bold">
      {{ $isEdit ? 'Save Changes' : 'Create Deal' }}
    </button>
    <a href="{{ route('admin.deals.index') }}" class="px-8 py-3 border border-surface-variant font-mono text-xs uppercase text-on-surface-variant hover:bg-surface-container transition-colors">
      Cancel
    </a>
  </div>
</form>

<script>
function dealBuilder(existingSlots, initialType) {
  return {
    dealType: initialType,
    slots: existingSlots.map(s => ({ ...s, _key: Math.random(), itemSearch: '', itemCategory: '' })),

    addSlot() {
      this.slots.push({
        _key: Math.random(),
        label: '',
        min_qty: 1,
        max_qty: 1,
        is_required: true,
        is_free: false,
        item_ids: [],
        itemSearch: '',
        itemCategory: '',
      });
    },

    removeSlot(key) {
      this.slots = this.slots.filter(s => s._key !== key);
    },

    toggleItem(slot, id) {
      const idx = slot.item_ids.indexOf(id);
      if (idx >= 0) slot.item_ids.splice(idx, 1);
      else slot.item_ids.push(id);
    },
  };
}
</script>

@endsection
