@extends('layouts.app')
@section('content')

<div class="max-w-container mx-auto px-4 lg:px-16 py-12">

  <h1 class="font-serif text-3xl font-black text-on-surface uppercase mb-8">My Account</h1>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Profile card --}}
    <div class="lg:col-span-1">
      <div class="bg-surface-container-low border border-outline-variant p-6">
        <div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center mb-4">
          <span class="font-serif text-2xl font-black text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
        </div>
        <h2 class="font-serif text-xl font-bold text-on-surface">{{ $user->name }}</h2>
        <p class="font-mono text-xs text-on-surface-variant mt-1">{{ $user->email }}</p>
        <div class="section-divider my-4"></div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn-ghost text-left">Sign Out →</button>
        </form>
      </div>
    </div>

    {{-- Order history --}}
    <div class="lg:col-span-2">
      <h2 class="font-serif text-xl font-bold text-on-surface mb-6 uppercase">Order History</h2>

      @if($orders->isEmpty())
        <div class="bg-surface-container-low border border-outline-variant p-12 text-center">
          <p class="font-sans text-sm text-on-surface-variant mb-4">No orders yet.</p>
          <a href="{{ route('menu') }}" class="btn-primary">ORDER NOW</a>
        </div>
      @else
        <div class="space-y-4">
          @foreach($orders as $order)
          <div class="bg-surface-container-low border border-outline-variant p-5">
            <div class="flex items-start justify-between mb-3">
              <div>
                <p class="font-mono text-xs font-bold text-on-surface">ORDER #{{ $order->id }}</p>
                <p class="font-sans text-xs text-on-surface-variant mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
              </div>
              <span class="font-mono text-[10px] font-bold uppercase {{ $order->status_color }}">{{ $order->status_label }}</span>
            </div>
            <p class="font-sans text-xs text-on-surface-variant mb-3">
              {{ $order->items->map(fn($i) => $i->qty . '× ' . $i->name)->join(', ') }}
            </p>
            <div class="flex items-center justify-between">
              <span class="font-mono text-sm font-bold text-primary">£{{ number_format($order->total, 2) }}</span>
              <a href="{{ route('orders.tracking', $order->id) }}"
                 class="font-mono text-[10px] text-on-surface-variant hover:text-primary transition-colors underline uppercase">
                Track →
              </a>
            </div>
          </div>
          @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
      @endif
    </div>

  </div>

</div>

@endsection
