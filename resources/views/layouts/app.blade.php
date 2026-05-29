<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Aces & Eights Pizza' }}</title>
  <meta name="description" content="{{ $description ?? 'Authentic Italian pizza in Tufnell Park, London. Order online for delivery, collection, or eat-in.' }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>
<body class="min-h-screen flex flex-col bg-surface">

  <x-header />

  <main class="flex-1">
    @yield('content')
  </main>

  <x-footer />

  @livewireScripts
</body>
</html>
