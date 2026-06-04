@extends('layouts.app')

@section('content')
<div class="max-w-container mx-auto px-4 lg:px-16 py-24 text-center">
  <div class="mb-8">
    <h1 class="font-serif text-8xl font-black text-outline mb-4">403</h1>
    <h2 class="font-serif text-3xl font-black text-on-surface uppercase mb-4">Access Denied</h2>
    <p class="font-sans text-sm text-on-surface-variant max-w-md mx-auto mb-8">
      You don't have permission to view this page.
    </p>
  </div>
  <a href="{{ route('home') }}" class="btn-primary">← BACK TO HOME</a>
</div>
@endsection
