@extends('layouts.admin')
@section('content')

@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">{{ session('success') }}</div>
@endif

<div class="flex justify-between items-end mb-8">
  <div>
    <h1 class="font-serif text-4xl font-black text-on-surface uppercase">Deals</h1>
    <p class="font-sans text-sm text-on-surface-variant mt-1">Manage bundles, BOGO, meal deals, family deals, and more.</p>
  </div>
  <a href="{{ route('admin.deals.create') }}" class="gold-button px-6 py-3 font-mono text-xs font-bold uppercase flex items-center gap-2">
    <span class="material-symbols-outlined">add</span> ADD DEAL
  </a>
</div>
<div class="double-divider mb-8"></div>

<div class="industrial-border overflow-hidden bg-white">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[700px]">
      <thead>
        <tr class="bg-surface-container-high border-b border-[#2B2B2B]">
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Deal</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Type</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Slots</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase text-center">Active</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Schedule</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#2B2B2B]/10">
        @forelse($deals as $deal)
        <tr class="{{ !$deal->is_active ? 'opacity-60' : '' }} hover:bg-surface-container-low transition-colors">
          <td class="px-6 py-4">
            <p class="font-sans text-sm font-semibold text-on-surface">{{ $deal->name }}</p>
            @if($deal->description)
              <p class="font-mono text-[10px] text-on-surface-variant truncate max-w-xs">{{ $deal->description }}</p>
            @endif
          </td>
          <td class="px-6 py-4 font-mono text-xs font-bold text-primary">{{ $deal->type_label }}</td>
          <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">
            {{ $deal->slots_count }} slot{{ $deal->slots_count !== 1 ? 's' : '' }}
          </td>
          <td class="px-6 py-4 text-center">
            <form method="POST" action="{{ route('admin.deals.toggle', $deal->id) }}">
              @csrf @method('PATCH')
              <button type="submit" class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-full {{ $deal->is_active ? 'bg-green-100 text-green-800' : 'bg-surface-container-high text-on-surface-variant' }}">
                {{ $deal->is_active ? 'ACTIVE' : 'OFF' }}
              </button>
            </form>
          </td>
          <td class="px-6 py-4 font-mono text-[10px] text-on-surface-variant">
            @if($deal->starts_at || $deal->ends_at)
              {{ $deal->starts_at?->format('d M') ?? '—' }} → {{ $deal->ends_at?->format('d M Y') ?? '∞' }}
            @else
              Always
            @endif
          </td>
          <td class="px-6 py-4 text-right">
            <div class="flex justify-end gap-2">
              <a href="{{ route('admin.deals.edit', $deal->id) }}" class="p-2 hover:bg-surface-container rounded" title="Edit">
                <span class="material-symbols-outlined text-on-surface-variant">edit</span>
              </a>
              <form method="POST" action="{{ route('admin.deals.destroy', $deal->id) }}" onsubmit="return confirm('Delete {{ addslashes($deal->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 hover:bg-brand-error/10 rounded" title="Delete">
                  <span class="material-symbols-outlined text-brand-error">delete</span>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="px-6 py-12 text-center font-mono text-xs text-on-surface-variant uppercase tracking-widest">
            No deals yet — <a href="{{ route('admin.deals.create') }}" class="text-primary hover:underline">create one</a>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
