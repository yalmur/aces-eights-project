@extends('layouts.app')

@section('content')
<div class="max-w-container mx-auto px-4 lg:px-16 py-24 text-center">
  <div class="mb-8">
    <h1 class="font-serif text-8xl font-black text-primary mb-4">500</h1>
    <h2 class="font-serif text-3xl font-black text-on-surface uppercase mb-4">Something Went Wrong</h2>
    <p class="font-sans text-sm text-on-surface-variant max-w-md mx-auto mb-8">
      Our kitchen had an unexpected problem. We've been notified and are working on it. Please try again in a moment.
    </p>
  </div>
  <a href="{{ route('home') }}" class="btn-primary">← BACK TO HOME</a>
</div>
@endsection
