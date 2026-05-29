<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Login' }} — Aces & Eights Pizza</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>
<body class="min-h-screen bg-surface flex flex-col items-center justify-center px-4">

  <a href="{{ route('home') }}" class="mb-8 flex flex-col items-center gap-2">
    <img src="{{ asset('images/logo.jpg') }}" alt="Aces & Eights Pizza" class="h-16 w-auto rounded">
    <span class="font-serif text-xl font-bold text-primary">Aces &amp; Eights Pizza</span>
  </a>

  <div class="w-full max-w-md card p-8 shadow-sm">
    @yield('content')
  </div>

  <p class="mt-6 label-caps text-xs text-on-surface-variant">
    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">← Back to site</a>
  </p>

  @livewireScripts
</body>
</html>
