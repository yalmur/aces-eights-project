@extends('layouts.admin')
@section('content')

@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">
    {{ session('success') }}
  </div>
@endif

<header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
  <div>
    <h2 class="font-serif text-4xl font-black text-on-surface uppercase tracking-tight leading-none mb-2">Topping Management</h2>
    <p class="font-mono text-xs text-on-surface-variant uppercase tracking-widest">Prices &middot; availability &middot; assigned per-item on the Menu edit page</p>
  </div>
</header>

<div class="industrial-divider mb-8"></div>

<div class="max-w-2xl">

  @forelse($toppings as $topping)
  <div x-data="{ editing: false }" class="industrial-border p-4 mb-3 bg-white">
    <div x-show="!editing" class="flex items-center justify-between gap-4">
      <div>
        <p class="font-mono text-sm font-bold">{{ $topping->name }}</p>
        <p class="font-mono text-[10px] text-on-surface-variant uppercase">
          £{{ number_format($topping->price, 2) }} &middot; {{ $topping->is_available ? 'Available' : 'Hidden' }}
        </p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button @click="editing = true" class="p-1 hover:text-primary transition-colors">
          <span class="material-symbols-outlined text-sm">edit</span>
        </button>
        <form action="{{ route('admin.toppings.toggle', $topping->id) }}" method="POST">
          @csrf @method('PATCH')
          <button type="submit"
                  class="px-3 py-1 font-mono text-[10px] font-bold uppercase industrial-border hover:bg-surface-container transition-colors {{ $topping->is_available ? 'text-green-700' : 'text-on-surface-variant' }}">
            {{ $topping->is_available ? 'Available' : 'Hidden' }}
          </button>
        </form>
        <form action="{{ route('admin.toppings.destroy', $topping->id) }}" method="POST"
              onsubmit="return confirm('Remove {{ addslashes($topping->name) }}?')">
          @csrf @method('DELETE')
          <button type="submit" class="p-1 hover:text-error transition-colors">
            <span class="material-symbols-outlined text-sm">delete</span>
          </button>
        </form>
      </div>
    </div>

    <form x-show="editing" x-cloak
          action="{{ route('admin.toppings.update', $topping->id) }}" method="POST"
          class="flex flex-wrap items-end gap-3">
      @csrf @method('PUT')
      <div>
        <label class="font-mono text-[10px] font-bold uppercase block mb-1">Name</label>
        <input type="text" name="name" value="{{ $topping->name }}" required
               class="industrial-border p-2 font-mono text-sm bg-white focus:outline-none focus:ring-1 focus:ring-primary">
      </div>
      <div>
        <label class="font-mono text-[10px] font-bold uppercase block mb-1">Price (&pound;)</label>
        <input type="number" name="price" value="{{ $topping->price }}" step="0.01" min="0" required
               class="w-28 industrial-border p-2 font-mono text-sm bg-white focus:outline-none focus:ring-1 focus:ring-primary">
      </div>
      <div>
        <label class="font-mono text-[10px] font-bold uppercase block mb-1">Sort Order</label>
        <input type="number" name="sort_order" value="{{ $topping->sort_order }}" min="0"
               class="w-24 industrial-border p-2 font-mono text-sm bg-white focus:outline-none focus:ring-1 focus:ring-primary">
      </div>
      <button type="submit" class="gold-button px-4 py-2 font-mono text-xs font-bold uppercase">Save</button>
      <button type="button" @click="editing = false"
              class="px-4 py-2 font-mono text-xs font-bold uppercase industrial-border hover:bg-surface-container transition-colors">Cancel</button>
    </form>
  </div>
  @empty
  <p class="font-mono text-xs text-on-surface-variant py-4 uppercase">No toppings configured yet.</p>
  @endforelse

  {{-- Add new topping --}}
  <div x-data="{ open: false }" class="mt-4">
    <button @click="open = !open"
            class="gold-button px-4 py-2 font-mono text-xs font-bold uppercase flex items-center gap-2">
      <span class="material-symbols-outlined text-sm">add</span> Add Topping
    </button>
    <form x-show="open" x-cloak x-transition
          action="{{ route('admin.toppings.store') }}" method="POST"
          class="mt-4 industrial-border p-4 bg-surface-container-low space-y-3">
      @csrf
      <div>
        <label class="font-mono text-[10px] font-bold uppercase block mb-1">Name *</label>
        <input type="text" name="name" required placeholder="e.g. Mushrooms"
               class="w-full industrial-border p-2 font-mono text-sm bg-white focus:outline-none focus:ring-1 focus:ring-primary">
      </div>
      <div>
        <label class="font-mono text-[10px] font-bold uppercase block mb-1">Price (&pound;) *</label>
        <input type="number" name="price" step="0.01" min="0" required placeholder="1.50"
               class="w-32 industrial-border p-2 font-mono text-sm bg-white focus:outline-none focus:ring-1 focus:ring-primary">
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

</div>

@endsection
