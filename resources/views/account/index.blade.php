@extends('layouts.app')
@section('content')

@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs max-w-container mx-auto px-4 lg:px-16">
    {{ session('success') }}
  </div>
@endif

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

    {{-- Saved Addresses --}}
    <div class="lg:col-span-1">
      <div class="bg-surface-container-low border border-outline-variant p-6">
        <h2 class="font-serif text-base font-bold text-on-surface uppercase mb-4">Saved Addresses</h2>

        @if($addresses->isEmpty())
          <p class="font-sans text-xs text-on-surface-variant mb-4">No saved addresses.</p>
        @else
          <div class="space-y-3 mb-4">
            @foreach($addresses as $addr)
            <div class="border border-outline-variant p-3 {{ $addr->is_default ? 'border-primary' : '' }}">
              <div class="flex justify-between items-start">
                <div class="flex-1 min-w-0">
                  <p class="font-mono text-[10px] font-bold uppercase {{ $addr->is_default ? 'text-primary' : 'text-on-surface-variant' }}">
                    {{ $addr->label }}{{ $addr->is_default ? ' · DEFAULT' : '' }}
                  </p>
                  <p class="font-sans text-xs text-on-surface mt-1">{{ $addr->full_address }}</p>
                </div>
                <div class="flex flex-col gap-1 ml-2">
                  @if(!$addr->is_default)
                  <form method="POST" action="{{ route('account.addresses.default', $addr->id) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="font-mono text-[9px] text-primary hover:underline uppercase whitespace-nowrap">Set Default</button>
                  </form>
                  @endif
                  <form method="POST" action="{{ route('account.addresses.destroy', $addr->id) }}" onsubmit="return confirm('Remove this address?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="font-mono text-[9px] text-brand-error hover:underline uppercase">Remove</button>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('account.addresses.store') }}" class="space-y-3" x-data="{ open: false }">
          @csrf
          <button type="button" @click="open = !open" class="font-mono text-[10px] text-primary hover:underline uppercase flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">add</span> Add Address
          </button>
          <div x-show="open" x-cloak class="space-y-3 pt-2">
            <input name="label" placeholder="Label (e.g. Home)" value="{{ old('label', 'Home') }}"
                   class="w-full border-b border-outline-variant font-sans text-xs py-1.5 focus:outline-none focus:border-primary bg-transparent">
            <input name="street_address" placeholder="Street address" value="{{ old('street_address') }}"
                   class="w-full border-b border-outline-variant font-sans text-xs py-1.5 focus:outline-none focus:border-primary bg-transparent">
            <div class="grid grid-cols-2 gap-2">
              <input name="city" placeholder="City" value="{{ old('city', 'London') }}"
                     class="border-b border-outline-variant font-sans text-xs py-1.5 focus:outline-none focus:border-primary bg-transparent">
              <input name="postcode" placeholder="Postcode" value="{{ old('postcode') }}"
                     class="border-b border-outline-variant font-mono text-xs py-1.5 focus:outline-none focus:border-primary bg-transparent uppercase">
            </div>
            <button type="submit" class="btn-primary w-full text-center text-xs py-2">SAVE ADDRESS</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Password Change --}}
    <div class="lg:col-span-2">
      <div class="bg-surface-container-low border border-outline-variant p-6">
        <h2 class="font-serif text-base font-bold text-on-surface uppercase mb-6">Change Password</h2>
        <form method="POST" action="{{ route('account.password') }}" class="max-w-sm space-y-4">
          @csrf
          <div>
            <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Current Password</label>
            <input name="current_password" type="password" required
                   class="w-full border-b-2 border-outline bg-transparent py-2 font-sans text-sm focus:outline-none focus:border-primary">
            @error('current_password')
              <p class="font-mono text-[10px] text-brand-error mt-1">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">New Password</label>
            <input name="password" type="password" required minlength="8"
                   class="w-full border-b-2 border-outline bg-transparent py-2 font-sans text-sm focus:outline-none focus:border-primary">
            @error('password')
              <p class="font-mono text-[10px] text-brand-error mt-1">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Confirm New Password</label>
            <input name="password_confirmation" type="password" required minlength="8"
                   class="w-full border-b-2 border-outline bg-transparent py-2 font-sans text-sm focus:outline-none focus:border-primary">
          </div>
          <button type="submit" class="btn-primary">UPDATE PASSWORD</button>
        </form>
      </div>
    </div>

  </div>

</div>

@endsection
