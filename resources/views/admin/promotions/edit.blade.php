@extends('layouts.admin')
@section('content')

<div class="flex items-center gap-2 text-on-surface-variant mb-4 font-mono text-xs">
  <a href="{{ route('admin.promotions.index') }}" class="hover:text-primary">Promotions</a>
  <span class="material-symbols-outlined text-sm">chevron_right</span>
  <span>{{ $promo ? $promo->code : 'New' }}</span>
</div>
<h1 class="font-serif text-3xl font-black text-on-surface uppercase mb-8">{{ $title }}</h1>

<form method="POST" action="{{ $promo ? route('admin.promotions.update', $promo->id) : route('admin.promotions.store') }}" class="max-w-2xl">
  @csrf
  @if($promo) @method('PUT') @endif

  @if($errors->any())
    <div class="mb-6 p-4 bg-brand-error/10 border border-brand-error font-mono text-xs text-brand-error">
      @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
  @endif

  <div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Promo Code *</label>
        <input name="code" value="{{ old('code', $promo?->code) }}" required class="w-full industrial-border-b font-mono text-sm py-2 uppercase focus:outline-none focus:border-primary" placeholder="e.g. SAVE10">
      </div>
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Name *</label>
        <input name="name" value="{{ old('name', $promo?->name) }}" required class="w-full industrial-border-b font-sans text-sm py-2 focus:outline-none focus:border-primary" placeholder="e.g. 10% off everything">
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Discount Type *</label>
        <select name="type" required class="w-full industrial-border-b font-sans text-sm py-2 focus:outline-none focus:border-primary">
          @foreach(['percentage' => 'Percentage (%)', 'fixed_amount' => 'Fixed Amount (£)', 'free_delivery' => 'Free Delivery', 'buy_one_get_one' => 'Buy 1 Get 1 Free', 'multi_buy' => '3 for 2 (Multi-Buy)'] as $val => $lbl)
            <option value="{{ $val }}" {{ old('type', $promo?->type) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Value (% or £)</label>
        <input name="value" type="number" step="0.01" min="0" value="{{ old('value', $promo?->value) }}" class="w-full industrial-border-b font-mono text-sm py-2 focus:outline-none focus:border-primary" placeholder="e.g. 10">
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Min. Order Amount (£)</label>
        <input name="min_order_amount" type="number" step="0.01" min="0" value="{{ old('min_order_amount', $promo?->min_order_amount) }}" class="w-full industrial-border-b font-mono text-sm py-2 focus:outline-none focus:border-primary" placeholder="No minimum">
      </div>
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Max Uses</label>
        <input name="max_uses" type="number" min="1" value="{{ old('max_uses', $promo?->max_uses) }}" class="w-full industrial-border-b font-mono text-sm py-2 focus:outline-none focus:border-primary" placeholder="Unlimited">
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Expires At</label>
        <input name="expires_at" type="date" value="{{ old('expires_at', $promo?->expires_at?->format('Y-m-d')) }}" class="w-full industrial-border-b font-mono text-sm py-2 focus:outline-none focus:border-primary">
      </div>
      <div class="flex items-center gap-3 mt-6">
        <label class="relative inline-flex items-center cursor-pointer">
          <input name="is_active" type="checkbox" value="1" {{ old('is_active', $promo?->is_active ?? true) ? 'checked' : '' }} class="sr-only peer"/>
          <div class="w-11 h-6 bg-surface-variant rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
        </label>
        <span class="font-mono text-xs uppercase text-on-surface-variant">Active</span>
      </div>
    </div>
    <div class="flex gap-4 pt-4">
      <button type="submit" class="gold-button px-8 py-3 font-mono text-xs uppercase">{{ $promo ? 'UPDATE PROMOTION' : 'CREATE PROMOTION' }}</button>
      <a href="{{ route('admin.promotions.index') }}" class="btn-ghost">Cancel</a>
    </div>
  </div>
</form>
@endsection
