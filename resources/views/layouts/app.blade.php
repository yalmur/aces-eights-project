<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Aces & Eights Pizza' }}</title>
  <meta name="description" content="{{ $description ?? 'Authentic Italian pizza in Tufnell Park, London. Order online for delivery, collection, or eat-in.' }}">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('head')
  @livewireStyles
  <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen flex flex-col bg-surface">

  <x-header />

  <main class="flex-1" x-data>
    @yield('content')
  </main>

  <x-footer />

  <x-cart-drawer />

  @livewireScripts
</body>
</html>
