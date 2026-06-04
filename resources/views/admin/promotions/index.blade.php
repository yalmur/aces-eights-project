@extends('layouts.admin')
@section('content')

@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">{{ session('success') }}</div>
@endif

<div class="flex justify-between items-end mb-8">
  <div>
    <h1 class="font-serif text-4xl font-black text-on-surface uppercase">Promotions</h1>
    <p class="font-sans text-sm text-on-surface-variant mt-1">Manage discount codes and special offers.</p>
  </div>
  <a href="{{ route('admin.promotions.create') }}" class="gold-button px-6 py-3 font-mono text-xs font-bold uppercase flex items-center gap-2">
    <span class="material-symbols-outlined">add</span> ADD PROMOTION
  </a>
</div>
<div class="double-divider mb-8"></div>

<div class="industrial-border overflow-hidden bg-white">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[600px]">
      <thead>
        <tr class="bg-surface-container-high border-b border-[#2B2B2B]">
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Code</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Name</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Discount</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Uses</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase text-center">Active</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Expires</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#2B2B2B]/10">
        @forelse($promotions as $promo)
        <tr class="{{ !$promo->is_active ? 'opacity-60' : '' }} hover:bg-surface-container-low transition-colors">
          <td class="px-6 py-4 font-mono text-sm font-bold">{{ $promo->code }}</td>
          <td class="px-6 py-4 font-sans text-sm">{{ $promo->name }}</td>
          <td class="px-6 py-4 font-mono text-sm font-bold text-primary">{{ $promo->type_label }}</td>
          <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">{{ $promo->current_uses }}{{ $promo->max_uses ? ' / '.$promo->max_uses : '' }}</td>
          <td class="px-6 py-4 text-center">
            <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-full {{ $promo->is_active ? 'bg-green-100 text-green-800' : 'bg-surface-container-high text-on-surface-variant' }}">
              {{ $promo->is_active ? 'ACTIVE' : 'OFF' }}
            </span>
          </td>
          <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">{{ $promo->expires_at?->format('d M Y') ?? '—' }}</td>
          <td class="px-6 py-4 text-right">
            <div class="flex justify-end gap-2">
              <a href="{{ route('admin.promotions.edit', $promo->id) }}" class="p-2 hover:bg-surface-container rounded" title="Edit">
                <span class="material-symbols-outlined text-on-surface-variant">edit</span>
              </a>
              <form method="POST" action="{{ route('admin.promotions.destroy', $promo->id) }}" onsubmit="return confirm('Delete {{ addslashes($promo->code) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 hover:bg-brand-error/10 rounded" title="Delete">
                  <span class="material-symbols-outlined text-brand-error">delete</span>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-6 py-12 text-center font-sans text-sm text-on-surface-variant">No promotions yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-6">{{ $promotions->links() }}</div>
@endsection
