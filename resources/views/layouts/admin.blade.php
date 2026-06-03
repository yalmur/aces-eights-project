<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Admin' }} — Aces & Eights Pizza</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
  <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-surface text-on-surface font-sans overflow-x-hidden">

  {{-- Backdrop --}}
  <div x-data
       x-show="$store.adminNav.open"
       x-cloak
       @click="$store.adminNav.close()"
       class="fixed inset-0 bg-black/50 z-[60]"></div>

  {{-- TopAppBar --}}
  <header class="fixed top-0 w-full z-50 border-b bg-surface-container-lowest border-surface-variant">
    <div class="flex items-center justify-between px-4 md:px-16 h-16 w-full max-w-[1280px] mx-auto relative">
      <button @click="$store.adminNav.toggle()"
              class="active:scale-95 transition-transform flex items-center justify-center p-2 -ml-2">
        <span class="material-symbols-outlined text-primary">menu</span>
      </button>
      <div class="absolute left-1/2 -translate-x-1/2 flex items-center h-full py-2">
        <img src="{{ asset('images/logo.jpg') }}" alt="Aces & Eights Pizza" class="h-10 w-auto rounded">
      </div>
      <div class="flex items-center gap-3">
        <span class="font-mono text-[10px] text-on-surface-variant hidden sm:block uppercase tracking-widest">
          {{ now()->format('D d M') }}
        </span>
        <a href="{{ route('home') }}" target="_blank"
           class="font-mono text-[10px] text-primary hover:underline uppercase tracking-widest">
          View Site ↗
        </a>
      </div>
    </div>
  </header>

  {{-- Navigation Drawer --}}
  <x-admin.sidebar />

  {{-- Main content area --}}
  <main class="pt-20 pb-12 px-4 md:px-16 max-w-[1280px] mx-auto lg:ml-80">
    @yield('content')
  </main>

  @livewireScripts
</body>
</html>
