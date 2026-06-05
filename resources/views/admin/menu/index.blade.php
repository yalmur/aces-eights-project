@extends('layouts.admin')
@section('content')

@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs rounded">
    {{ session('success') }}
  </div>
@endif

{{-- Header --}}
<header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
  <div class="flex-1">
    <h2 class="font-serif text-4xl font-black text-on-surface uppercase tracking-tight leading-none mb-4">Menu Management</h2>
    <p class="font-sans text-sm text-on-surface-variant max-w-2xl">Modify your offerings, adjust pricing, and toggle item availability for the daily service ledger.</p>
  </div>
  <a href="{{ route('admin.menu.create') }}"
     class="gold-button flex items-center justify-center gap-2 px-6 py-3 font-mono text-xs font-bold uppercase industrial-border whitespace-nowrap">
    <span class="material-symbols-outlined">add</span> ADD NEW ITEM
  </a>
</header>
<div class="double-divider mb-8"></div>

{{-- Search & Filters (server-side) --}}
<section class="mb-10 space-y-4">
  <form method="GET" action="{{ route('admin.menu.index') }}" class="space-y-4">
    <div class="flex gap-3">
      <div class="relative flex-1">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant">search</span>
        <input name="search" value="{{ request('search') }}"
               class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-[#2B2B2B] border focus:outline-none focus:ring-1 focus:ring-primary font-sans text-sm"
               placeholder="Search items..." type="text"/>
      </div>
      @if(request('search') || request('category'))
      <a href="{{ route('admin.menu.index') }}"
         class="px-4 py-2 industrial-border font-mono text-xs font-bold uppercase hover:bg-surface-container self-center">Clear</a>
      @endif
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('admin.menu.index', array_merge(request()->except('category', 'page'), [])) }}"
         class="px-5 py-2 rounded-full font-mono text-[10px] font-bold industrial-border transition-colors uppercase {{ !request('category') ? 'bg-primary text-white' : 'hover:bg-surface-container text-on-surface' }}">
        All Items
      </a>
      @foreach($categories as $cat)
      <a href="{{ route('admin.menu.index', array_merge(request()->except('category', 'page'), ['category' => $cat->slug])) }}"
         class="px-5 py-2 rounded-full font-mono text-[10px] font-bold industrial-border transition-colors uppercase {{ request('category') === $cat->slug ? 'bg-primary text-white' : 'hover:bg-surface-container text-on-surface' }}">
        {{ $cat->name }}
      </a>
      @endforeach
    </div>
  </form>

  {{-- Table --}}
  <div class="bg-surface industrial-border overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse min-w-[700px]">
        <thead>
          <tr class="bg-surface-container-high border-b border-[#2B2B2B]">
            <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase tracking-wider">Item</th>
            <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase tracking-wider">Category</th>
            <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase tracking-wider">Price</th>
            <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase tracking-wider text-center">Active</th>
            <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#2B2B2B]/20">
@foreach($items as $item)
          <tr class="{{ !$item->is_available ? 'opacity-60' : '' }} hover:bg-surface-container-low transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-surface-container industrial-border overflow-hidden flex-shrink-0">
                  <img src="{{ $item->image_path ? asset('storage/'.$item->image_path) : 'https://placehold.co/56x56/e4e2e1/1b1c1c?text=+' }}"
                       alt="{{ $item->name }}" class="w-full h-full object-cover">
                </div>
                <div>
                  <p class="font-mono text-xs font-bold text-on-surface">{{ $item->name }}</p>
                  <p class="font-sans text-xs text-on-surface-variant">{{ $item->description ? Str::limit($item->description, 50) : '' }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 font-sans text-sm">{{ $item->category->name }}</td>
            <td class="px-6 py-4 font-mono text-sm font-bold">{{ $item->formatted_price }}</td>
            <td class="px-6 py-4">
              <div class="flex justify-center"
                   x-data="{ on: {{ $item->is_available ? 'true' : 'false' }}, saving: false }">
                <label class="flex items-center cursor-pointer" :class="saving ? 'opacity-50 pointer-events-none' : ''">
                  <div class="relative">
                    <input @change="
                      saving = true;
                      fetch('{{ route('admin.menu.toggle', $item->id) }}', {
                        method: 'PATCH',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                      })
                      .then(r => r.json())
                      .then(d => { on = d.available; saving = false; })
                      .catch(() => saving = false);
                    "
                    :checked="on" class="sr-only" type="checkbox"/>
                    <div :class="on ? 'bg-primary' : 'bg-[#2B2B2B]/30'" class="block w-10 h-6 rounded-full transition-colors">
                      <div :class="on ? 'translate-x-4' : 'translate-x-1'" class="absolute top-1 left-0 bg-white w-4 h-4 rounded-full transition-transform"></div>
                    </div>
                  </div>
                </label>
              </div>
            </td>
            <td class="px-6 py-4 text-right">
              <div class="flex justify-end gap-2">
                <a href="{{ route('admin.menu.edit', $item->id) }}" class="p-2 hover:bg-surface-container rounded transition-colors" title="Edit">
                  <span class="material-symbols-outlined text-on-surface-variant">edit</span>
                </a>
                <form method="POST" action="{{ route('admin.menu.destroy', $item->id) }}"
                      onsubmit="return confirm('Delete {{ addslashes($item->name) }}?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="p-2 hover:bg-brand-error/10 rounded transition-colors" title="Delete">
                    <span class="material-symbols-outlined text-brand-error">delete</span>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Pagination --}}
  <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-on-surface-variant font-mono text-xs font-bold">
    <p>Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }} menu items</p>
    {{ $items->links() }}
  </div>
</section>

@endsection
