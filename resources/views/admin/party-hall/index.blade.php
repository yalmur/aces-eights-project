@extends('layouts.admin')
@section('content')

@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">{{ session('success') }}</div>
@endif

<div class="mb-8">
  <h1 class="font-serif text-4xl font-black text-on-surface uppercase">Party Hall Inquiries</h1>
  <p class="font-sans text-sm text-on-surface-variant mt-1">Review and manage venue hire requests.</p>
</div>
<div class="double-divider mb-8"></div>

<div class="industrial-border overflow-hidden bg-white">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[700px]">
      <thead>
        <tr class="bg-surface-container-high border-b border-[#2B2B2B]">
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Name</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Contact</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Event Date</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Guests</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Type</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Status</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Received</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#2B2B2B]/10">
        @forelse($inquiries as $inquiry)
        <tr class="hover:bg-surface-container-low transition-colors">
          <td class="px-6 py-4 font-sans text-sm font-semibold">{{ $inquiry->name }}</td>
          <td class="px-6 py-4 font-mono text-xs">
            <div>{{ $inquiry->email }}</div>
            <div class="text-on-surface-variant">{{ $inquiry->phone }}</div>
          </td>
          <td class="px-6 py-4 font-mono text-sm">{{ $inquiry->event_date->format('d M Y') }}</td>
          <td class="px-6 py-4 font-mono text-sm">{{ $inquiry->guests }}</td>
          <td class="px-6 py-4 font-sans text-sm">{{ $inquiry->event_type }}</td>
          <td class="px-6 py-4">
            <form method="POST" action="{{ route('admin.party-hall.update', $inquiry->id) }}">
              @csrf @method('PATCH')
              <select name="status" onchange="this.form.submit()"
                class="font-mono text-[10px] font-bold px-2 py-1 border border-[#2B2B2B]/20 rounded bg-white">
                @foreach(['new','contacted','confirmed','cancelled'] as $s)
                  <option value="{{ $s }}" {{ $inquiry->status === $s ? 'selected' : '' }}>{{ strtoupper($s) }}</option>
                @endforeach
              </select>
            </form>
          </td>
          <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">{{ $inquiry->created_at->format('d M Y') }}</td>
        </tr>
        @if($inquiry->message)
        <tr class="bg-surface-container-low">
          <td colspan="7" class="px-6 py-3 font-sans text-xs text-on-surface-variant italic">{{ $inquiry->message }}</td>
        </tr>
        @endif
        @empty
        <tr><td colspan="7" class="px-6 py-12 text-center font-sans text-sm text-on-surface-variant">No inquiries yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-6">{{ $inquiries->links() }}</div>
@endsection
