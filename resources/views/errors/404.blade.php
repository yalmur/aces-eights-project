@extends('layouts.app')

@section('content')
<div class="max-w-container mx-auto px-4 lg:px-16 py-24 text-center">
  <div class="mb-8">
    <h1 class="font-serif text-8xl font-black text-outline mb-4">404</h1>
    <h2 class="font-serif text-3xl font-black text-on-surface uppercase mb-4">Page Not Found</h2>
    <p class="font-sans text-sm text-on-surface-variant max-w-md mx-auto mb-8">
      Looks like this page went the way of the dough — it doesn't exist. Try heading back to our menu.
    </p>
  </div>
  <div class="flex gap-4 justify-center flex-wrap">
    <a href="{{ route('home') }}" class="btn-primary">← BACK TO HOME</a>
    <a href="{{ route('menu') }}" class="btn-secondary">VIEW MENU</a>
  </div>
</div>
@endsection
