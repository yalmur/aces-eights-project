@php
  $diff    = $order->created_at->diff(now());
  $elapsed = ($diff->h > 0 ? $diff->h . 'h ' : '') . $diff->i . 'm';
@endphp

<div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 flex flex-col gap-3">

  {{-- Header --}}
  <div class="flex items-start justify-between gap-2">
    <div>
      <p class="text-zinc-500 text-[10px] font-mono uppercase tracking-widest leading-none">Order</p>
      <p class="text-white text-xl font-black mt-0.5">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
    </div>
    <div class="flex flex-col items-end gap-1">
      <span class="bg-zinc-800 text-zinc-400 text-[10px] font-mono px-2 py-0.5 rounded-full whitespace-nowrap">{{ $elapsed }}</span>
      @if($column === 'preparing')
        @if($order->status === 'accepted')
          <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Queued</span>
        @else
          <span class="text-[10px] font-bold uppercase tracking-wider text-red-400 flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse inline-block"></span>Cooking
          </span>
        @endif
      @endif
    </div>
  </div>

  {{-- Customer --}}
  <div class="space-y-1">
    <div class="flex items-center gap-2">
      <span class="material-symbols-outlined text-[14px] text-zinc-500 flex-none">person</span>
      <span class="text-zinc-200 text-xs">{{ $order->customer_name }}</span>
      <span class="ml-auto text-[10px] font-mono uppercase px-1.5 py-0.5 rounded flex-none
        {{ $order->type === 'delivery'   ? 'bg-blue-950 text-blue-400 border border-blue-900'
         : ($order->type === 'eat_in'    ? 'bg-purple-950 text-purple-400 border border-purple-900'
                                         : 'bg-zinc-800 text-zinc-400 border border-zinc-700') }}">
        {{ $order->type === 'delivery' ? 'Delivery' : ($order->type === 'eat_in' ? 'Eat-In' : 'Collection') }}
      </span>
    </div>
    @if($order->customer_phone)
    <div class="flex items-center gap-2">
      <span class="material-symbols-outlined text-[14px] text-zinc-500 flex-none">call</span>
      <span class="text-zinc-400 text-xs">{{ $order->customer_phone }}</span>
    </div>
    @endif
    @if($order->type === 'delivery' && $order->delivery_address)
    <div class="flex items-center gap-2">
      <span class="material-symbols-outlined text-[14px] text-zinc-500 flex-none">location_on</span>
      <span class="text-zinc-400 text-xs">{{ $order->delivery_address }}, {{ $order->delivery_postcode }}</span>
    </div>
    @endif
  </div>

  {{-- Notes --}}
  @if($order->notes)
  <div class="bg-yellow-950/40 border border-yellow-900/40 rounded-lg px-3 py-2">
    <p class="text-yellow-500 text-xs flex gap-1.5">
      <span class="material-symbols-outlined text-[14px] flex-none mt-0.5">warning</span>
      {{ $order->notes }}
    </p>
  </div>
  @endif

  {{-- Items --}}
  <div class="space-y-2.5">
    @foreach($order->items as $item)
    <div>
      <p class="text-sm leading-snug">
        <span class="text-red-500 font-bold">{{ $item->qty }}x</span>
        <span class="text-white font-semibold ml-1">{{ $item->name }}</span>
        @if($item->size && $item->size !== '12" Standard')
          <span class="text-zinc-500 text-[11px] ml-1">· {{ $item->size }}</span>
        @endif
        @if($item->crust && $item->crust !== '48hr Sourdough')
          <span class="text-zinc-500 text-[11px] ml-1">· {{ $item->crust }}</span>
        @endif
      </p>
      @if($item->removed_ingredients)
        @foreach($item->removed_ingredients as $removed)
        <p class="text-red-500 text-[11px] font-bold uppercase ml-4">NO {{ $removed }}</p>
        @endforeach
      @endif
      @if($item->added_toppings)
        @foreach($item->added_toppings as $topping)
        <p class="text-green-500 text-[11px] ml-4">+ {{ $topping['name'] }}</p>
        @endforeach
      @endif
      @if($item->instructions)
        <p class="text-amber-400 text-[11px] italic ml-4">{{ $item->instructions }}</p>
      @endif
    </div>
    @endforeach
  </div>

  {{-- Footer --}}
  <div class="border-t border-zinc-800 pt-3 flex items-center justify-between gap-2">
    <span class="text-white text-base font-black">£{{ number_format($order->total, 2) }}</span>
    <div class="flex gap-1.5">
      {{-- Primary action --}}
      @if($column === 'preparing' && $order->status === 'accepted')
        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
          @csrf @method('PATCH')
          <input type="hidden" name="status" value="cooking">
          <button class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold uppercase rounded transition-colors flex items-center gap-1">
            Fire <span class="material-symbols-outlined text-[12px]" style="font-variation-settings:'FILL' 1">local_fire_department</span>
          </button>
        </form>
      @elseif($column === 'preparing' && $order->status === 'cooking')
        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
          @csrf @method('PATCH')
          <input type="hidden" name="status" value="ready">
          <button class="px-3 py-1.5 bg-green-700 hover:bg-green-600 text-white text-[11px] font-bold uppercase rounded transition-colors flex items-center gap-1">
            Ready <span class="material-symbols-outlined text-[12px]">check_circle</span>
          </button>
        </form>
      @elseif($column === 'ready')
        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
          @csrf @method('PATCH')
          <input type="hidden" name="status" value="{{ $order->type === 'delivery' ? 'out_for_delivery' : 'collected' }}">
          <button class="px-3 py-1.5 bg-zinc-700 hover:bg-zinc-600 text-white text-[11px] font-bold uppercase rounded transition-colors flex items-center gap-1">
            @if($order->type === 'delivery')
              Dispatch <span class="material-symbols-outlined text-[12px]">moped</span>
            @else
              Collected <span class="material-symbols-outlined text-[12px]">check</span>
            @endif
          </button>
        </form>
      @elseif($column === 'dispatched')
        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
          @csrf @method('PATCH')
          <input type="hidden" name="status" value="delivered">
          <button class="px-3 py-1.5 bg-zinc-700 hover:bg-zinc-600 text-white text-[11px] font-bold uppercase rounded transition-colors flex items-center gap-1">
            Delivered <span class="material-symbols-outlined text-[12px]">check</span>
          </button>
        </form>
      @endif
      {{-- Receipt / detail link --}}
      <a href="{{ route('admin.orders.detail', $order->id) }}"
         class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-zinc-200 text-[11px] font-bold uppercase rounded transition-colors flex items-center gap-1">
        <span class="material-symbols-outlined text-[12px]">receipt</span>
      </a>
    </div>
  </div>

</div>
