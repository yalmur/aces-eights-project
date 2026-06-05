@extends('layouts.admin')
@section('content')

{{-- Live order updates toast --}}
<div x-data="{
       show: false,
       message: '',
       notify(msg) { this.message = msg; this.show = true; setTimeout(() => this.show = false, 4000) },
       init() {
         if (window.Echo) {
           window.Echo.private('admin.orders')
             .listen('.OrderStatusUpdated', (data) => {
               this.notify('Order #' + data.order_id + ' → ' + data.status_label)
             })
         }
       }
     }"
     x-show="show"
     x-cloak
     x-transition
     class="fixed bottom-6 right-6 z-50 bg-on-surface text-surface px-6 py-3 font-mono text-xs font-bold uppercase shadow-xl">
  <span x-text="message"></span>
</div>

@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">
    {{ session('success') }}
  </div>
@endif

<section class="mb-10">
  <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-4">
    <div>
      <div class="flex items-center gap-3">
        <h1 class="font-serif text-4xl font-black text-on-surface uppercase">Online Orders</h1>
        <span x-data x-show="window.Echo !== undefined" x-cloak
              class="font-mono text-[10px] bg-green-100 text-green-800 px-2 py-1 border border-green-300 uppercase tracking-widest">
          ● LIVE
        </span>
      </div>
      <p class="font-mono text-xs text-on-surface-variant mt-1 uppercase tracking-widest">{{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="flex gap-1 p-1 industrial-border bg-surface-container">
      @foreach(['all' => 'All', 'pending' => 'Pending', 'cooking' => 'Cooking', 'ready' => 'Ready', 'delivered' => 'Delivered'] as $val => $label)
        <a href="{{ route('admin.orders.index', $val === 'all' ? [] : ['status' => $val]) }}"
           class="px-3 py-1 font-mono text-[10px] font-bold uppercase transition-colors
                  {{ $activeTab === $val ? 'bg-primary text-white' : 'hover:bg-surface-variant text-on-surface' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>
  </div>
  <div class="industrial-divider"></div>
</section>

{{-- Stats bar --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
  <div class="industrial-border p-4 bg-surface-container-low text-center">
    <div class="font-serif text-3xl font-black">{{ \App\Models\Order::whereDate('created_at', today())->whereNotIn('status', ['pending_payment', 'cancelled'])->count() }}</div>
    <div class="font-mono text-[10px] uppercase text-[#2B2B2B]">Today's Orders</div>
  </div>
  <div class="industrial-border p-4 bg-surface-container-low text-center">
    <div class="font-serif text-3xl font-black text-primary">{{ \App\Models\Order::whereIn('status', ['accepted', 'cooking'])->count() }}</div>
    <div class="font-mono text-[10px] uppercase text-[#2B2B2B]">In Progress</div>
  </div>
  <div class="industrial-border p-4 bg-surface-container-low text-center">
    <div class="font-serif text-3xl font-black text-green-700">{{ \App\Models\Order::where('status', 'ready')->count() }}</div>
    <div class="font-mono text-[10px] uppercase text-[#2B2B2B]">Ready</div>
  </div>
  <div class="industrial-border p-4 bg-surface-container-low text-center">
    <div class="font-serif text-3xl font-black">£{{ number_format(\App\Models\Order::whereDate('created_at', today())->whereNotIn('status', ['pending_payment', 'cancelled'])->sum('total'), 0) }}</div>
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
        @foreach($orders as $order)
<tr class="hover:bg-surface-container-low transition-colors">
  <td class="px-6 py-4 font-mono text-sm font-bold">ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
  <td class="px-6 py-4 font-sans text-sm">{{ $order->customer_name }}</td>
  <td class="px-6 py-4 font-sans text-sm text-on-surface-variant">
    {{ $order->items->map(fn($i) => $i->qty . 'x ' . $i->name)->join(', ') }}
  </td>
  <td class="px-6 py-4 font-mono text-sm font-bold">£{{ number_format($order->total, 2) }}</td>
  <td class="px-6 py-4">
    <span class="text-white text-[10px] px-2 py-0.5 font-bold uppercase rounded-full
      {{ match($order->status) {
        'accepted'         => 'bg-primary',
        'cooking'          => 'bg-yellow-500',
        'ready'            => 'bg-green-600',
        'out_for_delivery' => 'bg-blue-600',
        default            => 'bg-[#2B2B2B]',
      } }}">
      {{ $order->status_label }}
    </span>
  </td>
  <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">{{ $order->created_at->diffForHumans() }}</td>
  <td class="px-6 py-4 text-right">
    <a href="{{ route('admin.orders.detail', $order->id) }}"
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


<div class="mt-4 font-mono text-xs text-on-surface-variant flex flex-col md:flex-row items-center justify-between gap-2">
  <p>Showing {{ $orders->count() }} of {{ $orders->total() }} orders</p>
  {{ $orders->links() }}
</div>

@endsection
