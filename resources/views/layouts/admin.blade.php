<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Admin' }} — Aces & Eights Pizza</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>
<body class="bg-surface min-h-screen flex">

  <x-admin.sidebar />

  {{-- Main content area: offset by sidebar width --}}
  <div class="flex-1 flex flex-col ml-64 min-h-screen">
    <x-admin.topbar :title="$title ?? 'Dashboard'" />

    <main class="flex-1 p-6 overflow-auto">
      @yield('content')
    </main>
  </div>

  @livewireScripts
</body>
</html>
