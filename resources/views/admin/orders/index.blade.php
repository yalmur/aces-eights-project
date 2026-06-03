@extends('layouts.admin')
@section('content')

{{-- Header --}}
<section class="mb-10">
  <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-4">
    <div>
      <h1 class="font-serif text-4xl font-black text-on-surface uppercase">Online Orders</h1>
      <p class="font-mono text-xs text-on-surface-variant mt-1 uppercase tracking-widest">{{ now()->format('l, d M Y') }}</p>
    </div>
    <div x-data="{ status: 'all' }" class="flex gap-1 p-1 industrial-border bg-surface-container">
      @foreach(['all' => 'All', 'pending' => 'Pending', 'cooking' => 'Cooking', 'ready' => 'Ready', 'delivered' => 'Delivered'] as $val => $label)
        <button x-on:click="status = '{{ $val }}'"
                :class="status === '{{ $val }}' ? 'bg-primary text-white' : 'hover:bg-surface-variant text-on-surface'"
                class="px-3 py-1 font-mono text-[10px] font-bold uppercase transition-colors">{{ $label }}</button>
      @endforeach
    </div>
  </div>
  <div class="industrial-divider"></div>
</section>

{{-- Stats bar --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
  <div class="industrial-border p-4 bg-surface-container-low text-center">
    <div class="font-serif text-3xl font-black">142</div>
    <div class="font-mono text-[10px] uppercase text-[#2B2B2B]">Today's Orders</div>
  </div>
  <div class="industrial-border p-4 bg-surface-container-low text-center">
    <div class="font-serif text-3xl font-black text-primary">8</div>
    <div class="font-mono text-[10px] uppercase text-[#2B2B2B]">In Progress</div>
  </div>
  <div class="industrial-border p-4 bg-surface-container-low text-center">
    <div class="font-serif text-3xl font-black text-green-700">3</div>
    <div class="font-mono text-[10px] uppercase text-[#2B2B2B]">Ready</div>
  </div>
  <div class="industrial-border p-4 bg-surface-container-low text-center">
    <div class="font-serif text-3xl font-black">£3,240</div>
    <div class="font-mono text-[10px] uppercase text-[#2B2B2B]">Revenue</div>
  </div>
</div>

{{-- Orders table --}}
<div class="industrial-border overflow-hidden bg-white">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[700px]">
      <thead>
        <tr class="bg-surface-container-high border-b border-[#2B2B2B]">
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Order</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Customer</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Items</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Total</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Status</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Time</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#2B2B2B]/10">
        @foreach([
          ['id'=>'ORD-7721','customer'=>'James Henderson','items'=>'2x Meat Lover, 1x Garlic Bread','total'=>'£39.50','status'=>'Accepted','time'=>'4m ago','color'=>'bg-primary'],
          ['id'=>'ORD-7720','customer'=>'Sarah Mitchell','items'=>'1x Tartufo Bianco, 2x Moretti','total'=>'£29.50','status'=>'Cooking','time'=>'11m ago','color'=>'bg-yellow-500'],
          ['id'=>'ORD-7718','customer'=>'Table 04','items'=>'1x Classic Margherita, 2x Moretti','total'=>'£25.50','status'=>'Cooking','time'=>'12m ago','color'=>'bg-yellow-500'],
          ['id'=>'ORD-7715','customer'=>'Marco Vitale','items'=>'3x Spicy Diavola (GF)','total'=>'£43.50','status'=>'Ready','time'=>'18m ago','color'=>'bg-green-600'],
          ['id'=>'ORD-7710','customer'=>'Emma Collins','items'=>'1x Vegan Garden, 1x Cacio e Pepe','total'=>'£24.50','status'=>'Delivered','time'=>'34m ago','color'=>'bg-[#2B2B2B]'],
        ] as $order)
        <tr class="hover:bg-surface-container-low transition-colors">
          <td class="px-6 py-4 font-mono text-sm font-bold">{{ $order['id'] }}</td>
          <td class="px-6 py-4 font-sans text-sm">{{ $order['customer'] }}</td>
          <td class="px-6 py-4 font-sans text-sm text-on-surface-variant">{{ $order['items'] }}</td>
          <td class="px-6 py-4 font-mono text-sm font-bold">{{ $order['total'] }}</td>
          <td class="px-6 py-4">
            <span class="{{ $order['color'] }} text-white text-[10px] px-2 py-0.5 font-bold uppercase rounded-full">{{ $order['status'] }}</span>
          </td>
          <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">{{ $order['time'] }}</td>
          <td class="px-6 py-4 text-right">
            <a href="{{ route('admin.orders.detail', 'preview') }}"
               class="p-2 hover:bg-surface-container rounded transition-colors inline-block">
              <span class="material-symbols-outlined text-on-surface-variant text-lg">visibility</span>
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
