@extends('layouts.admin')
@section('content')

@php
$menuItemsData = $menuItems->map(fn ($m) => [
    'id'       => $m->id,
    'name'     => $m->name,
    'price'    => (float) $m->base_price,
    'category' => $m->category ? $m->category->name : '',
]);
@endphp

<script>
function inStoreModal() {
    return {
        open: false,
        orderItems: [],
        search: '',
        menuItems: @json($menuItemsData),
        get filteredMenuItems() {
            if (!this.search) return [];
            const q = this.search.toLowerCase();
            return this.menuItems.filter(i => i.name.toLowerCase().includes(q));
        },
        addItem(item) {
            const existing = this.orderItems.find(i => i.id === item.id);
            if (existing) {
                existing.qty++;
                existing.line = existing.qty * existing.price;
            } else {
                this.orderItems.push({ id: item.id, name: item.name, price: item.price, qty: 1, line: item.price });
            }
            this.search = '';
        },
        removeItem(id) {
            this.orderItems = this.orderItems.filter(i => i.id !== id);
        },
        get subtotal() {
            return this.orderItems.reduce((s, i) => s + i.line, 0);
        }
    };
}
</script>

{{-- Header --}}
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8" x-data="inStoreModal()">
  <div class="flex-1">
    <h1 class="font-display text-display text-on-surface leading-tight">IN-STORE ORDERS</h1>
    <div class="double-divider max-w-2xl"></div>
    <p class="text-on-surface-variant font-body-lg italic">Today's walk-in and collection orders.</p>
  </div>
  <button @click="open = true"
          class="bg-primary text-on-primary font-label-bold py-3 px-6 industrial-border flex items-center justify-center gap-2 hover:bg-on-primary-fixed-variant transition-colors active:scale-95 duration-150">
    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add</span>
    NEW ORDER
  </button>

  {{-- New Order Modal --}}
  <div x-show="open"
       x-cloak
       @keydown.escape.window="open = false"
       class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 p-4">

    <div @click.self="open = false" class="absolute inset-0"></div>
    <div class="relative bg-surface border-2 border-outline w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="bg-primary text-on-primary px-6 py-4 flex items-center justify-between">
        <h2 class="font-serif text-lg font-bold uppercase tracking-widest">New In-Store Order</h2>
        <button @click="open = false" class="text-on-primary/70 hover:text-on-primary">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <form method="POST" action="{{ route('admin.orders.in-store.store') }}" class="p-6 space-y-5">
        @csrf

        @if($errors->any())
          <div class="p-3 bg-red-50 border border-red-200 text-red-700 font-mono text-xs space-y-1">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
          </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex flex-col">
            <label class="font-label-bold text-label-bold uppercase text-primary mb-1 text-xs">Customer Name *</label>
            <input name="customer_name" type="text" required value="{{ old('customer_name') }}"
                   class="border-b-2 border-outline bg-transparent py-2 font-body-md focus:ring-0 focus:border-primary text-on-surface"
                   placeholder="e.g. John Smith / Table 5">
          </div>
          <div class="flex flex-col">
            <label class="font-label-bold text-label-bold uppercase text-primary mb-1 text-xs">Phone</label>
            <input name="customer_phone" type="tel" value="{{ old('customer_phone') }}"
                   class="border-b-2 border-outline bg-transparent py-2 font-body-md focus:ring-0 focus:border-primary text-on-surface"
                   placeholder="07700 000 000">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex flex-col">
            <label class="font-label-bold text-label-bold uppercase text-primary mb-1 text-xs">Email (optional)</label>
            <input name="customer_email" type="email" value="{{ old('customer_email') }}"
                   class="border-b-2 border-outline bg-transparent py-2 font-body-md focus:ring-0 focus:border-primary text-on-surface"
                   placeholder="for receipt">
          </div>
          <div class="flex flex-col">
            <label class="font-label-bold text-label-bold uppercase text-primary mb-1 text-xs">Order Type</label>
            <select name="order_type"
                    class="border-b-2 border-outline bg-transparent py-2 font-body-md focus:ring-0 focus:border-primary text-on-surface">
              <option value="eat_in">Eat In</option>
              <option value="collection">Collection</option>
            </select>
          </div>
        </div>

        <div class="flex flex-col">
          <label class="font-label-bold text-label-bold uppercase text-primary mb-1 text-xs">Notes / Table</label>
          <input name="notes" type="text" value="{{ old('notes') }}"
                 class="border-b-2 border-outline bg-transparent py-2 font-body-md focus:ring-0 focus:border-primary text-on-surface"
                 placeholder="Table 5, extra napkins...">
        </div>

        {{-- Item picker --}}
        <div>
          <label class="font-label-bold text-label-bold uppercase text-primary mb-2 text-xs block">Add Items *</label>
          <div class="relative mb-3">
            <span class="material-symbols-outlined absolute left-1 top-2 text-on-surface-variant text-[18px]">search</span>
            <input x-model="search" type="text"
                   class="border-b-2 border-outline bg-transparent py-2 pl-8 font-body-md focus:ring-0 focus:border-primary text-on-surface w-full"
                   placeholder="Search menu...">
            <div x-show="filteredMenuItems.length > 0"
                 x-cloak
                 class="absolute top-full left-0 right-0 bg-surface border border-outline-variant shadow-lg z-10 max-h-48 overflow-y-auto">
              <template x-for="item in filteredMenuItems" :key="item.id">
                <button type="button"
                        @click="addItem(item)"
                        class="w-full text-left px-4 py-2 hover:bg-surface-container flex justify-between items-center border-b border-outline-variant last:border-0">
                  <span class="font-sans text-sm text-on-surface" x-text="item.name"></span>
                  <span class="font-mono text-xs text-primary ml-4" x-text="'£' + item.price.toFixed(2)"></span>
                </button>
              </template>
            </div>
          </div>

          <div class="space-y-2" x-show="orderItems.length > 0">
            <template x-for="(item, idx) in orderItems" :key="item.id">
              <div class="flex items-center gap-3 bg-surface-container px-3 py-2 border border-outline-variant">
                <input type="hidden" :name="'items[' + idx + '][menu_item_id]'" :value="item.id">
                <span class="flex-1 font-sans text-sm text-on-surface" x-text="item.name"></span>
                <div class="flex items-center gap-2">
                  <button type="button" @click="item.qty = Math.max(1, item.qty - 1); item.line = item.qty * item.price"
                          class="w-6 h-6 border border-outline flex items-center justify-center hover:bg-surface-variant text-sm">−</button>
                  <input type="number" :name="'items[' + idx + '][qty]'" x-model.number="item.qty"
                         @change="item.line = item.qty * item.price"
                         min="1" class="w-10 text-center font-mono text-sm border-b border-outline bg-transparent focus:ring-0">
                  <button type="button" @click="item.qty++; item.line = item.qty * item.price"
                          class="w-6 h-6 border border-outline flex items-center justify-center hover:bg-surface-variant text-sm">+</button>
                </div>
                <span class="font-mono text-sm text-primary w-16 text-right" x-text="'£' + item.line.toFixed(2)"></span>
                <button type="button" @click="removeItem(item.id)" class="text-on-surface-variant hover:text-primary">
                  <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
              </div>
            </template>
            <div class="flex justify-end pt-2">
              <span class="font-mono text-sm font-bold text-primary">
                Total: £<span x-text="subtotal.toFixed(2)"></span>
              </span>
            </div>
          </div>
          <p x-show="orderItems.length === 0" class="font-mono text-xs text-on-surface-variant italic">No items added yet</p>
        </div>

        <div class="flex gap-3 pt-2">
          <button type="submit"
                  class="flex-1 bg-primary text-on-primary font-label-bold py-3 px-6 industrial-border hover:bg-on-primary-fixed-variant transition-colors uppercase">
            Create Order
          </button>
          <button type="button" @click="open = false"
                  class="px-6 py-3 border-2 border-outline font-label-bold text-on-surface hover:bg-surface-container uppercase">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Flash --}}
@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">
    {{ session('success') }}
  </div>
@endif

{{-- Orders --}}
@if($orders->isEmpty())
  <div class="text-center py-24 border-2 border-dashed border-outline-variant">
    <span class="material-symbols-outlined text-5xl text-on-surface-variant block mb-3">receipt_long</span>
    <p class="font-mono text-sm text-on-surface-variant uppercase tracking-widest">No in-store orders today</p>
    <p class="font-sans text-xs text-on-surface-variant mt-1">Use the NEW ORDER button to add one</p>
  </div>
@else
  <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-gutter">
    @foreach($orders as $order)
    <div class="bg-surface industrial-border p-6 flex flex-col gap-4">
      <div class="flex justify-between items-start">
        <div>
          <span class="font-label-bold text-secondary text-label-sm uppercase tracking-widest">Order #{{ $order->id }}</span>
          <h3 class="font-headline-md text-on-surface uppercase">{{ $order->customer_name }}</h3>
          <span class="font-mono text-[10px] text-on-surface-variant uppercase">
            {{ $order->type === 'eat_in' ? 'Eat In' : 'Collection' }} · {{ $order->created_at->format('H:i') }}
            @if($order->notes) · {{ $order->notes }} @endif
          </span>
        </div>
        <span class="font-label-bold text-[10px] px-2 py-1 industrial-border uppercase {{ $order->status_color }} bg-surface-container">
          {{ $order->status_label }}
        </span>
      </div>

      <div class="border-y border-outline-variant py-3 space-y-1">
        @foreach($order->items as $item)
          <div class="flex justify-between text-sm">
            <span class="text-on-surface">{{ $item->qty }}× {{ $item->name }}</span>
            <span class="font-mono font-bold">£{{ number_format($item->line_total, 2) }}</span>
          </div>
        @endforeach
      </div>

      <div class="flex justify-between items-center mt-auto">
        <span class="font-display text-headline-md text-primary">£{{ number_format($order->total, 2) }}</span>
        <div class="flex items-center gap-2">
          @php
            $nextStatus = match($order->status) {
                'accepted' => 'cooking',
                'cooking'  => 'ready',
                'ready'    => 'collected',
                default    => null,
            };
          @endphp
          @if($nextStatus)
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
              @csrf @method('PATCH')
              <input type="hidden" name="status" value="{{ $nextStatus }}">
              <button type="submit"
                      class="bg-on-surface text-surface px-4 py-2 font-label-bold text-[10px] uppercase hover:bg-primary transition-colors">
                → {{ ucwords(str_replace('_', ' ', $nextStatus)) }}
              </button>
            </form>
          @endif
          <form method="POST" action="{{ route('admin.orders.status', $order->id) }}"
                onsubmit="return confirm('Cancel order #{{ $order->id }}?')">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="cancelled">
            <button type="submit" class="p-2 border border-outline hover:bg-surface-variant" title="Cancel">
              <span class="material-symbols-outlined text-on-surface-variant text-[18px]">cancel</span>
            </button>
          </form>
        </div>
      </div>
    </div>
    @endforeach

    <div class="lg:col-span-2 xl:col-span-3 grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
      <div class="industrial-border p-4 bg-surface text-center">
        <span class="font-label-bold text-[10px] text-on-surface-variant block uppercase">Active Today</span>
        <span class="font-display text-4xl text-primary">{{ $orders->count() }}</span>
      </div>
      <div class="industrial-border p-4 bg-surface text-center">
        <span class="font-label-bold text-[10px] text-on-surface-variant block uppercase">Revenue</span>
        <span class="font-display text-3xl text-primary">£{{ number_format($orders->sum('total'), 0) }}</span>
      </div>
      <div class="industrial-border p-4 bg-surface text-center">
        <span class="font-label-bold text-[10px] text-on-surface-variant block uppercase">Eat In</span>
        <span class="font-display text-4xl text-secondary">{{ $orders->where('type', 'eat_in')->count() }}</span>
      </div>
      <div class="industrial-border p-4 bg-surface text-center">
        <span class="font-label-bold text-[10px] text-on-surface-variant block uppercase">Collection</span>
        <span class="font-display text-4xl text-secondary">{{ $orders->where('type', 'collection')->count() }}</span>
      </div>
    </div>
  </div>
@endif

@endsection
