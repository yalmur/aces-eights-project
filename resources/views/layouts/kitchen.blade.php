<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Kitchen Dashboard — Aces & Eights Pizza</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    .kitchen-scroll::-webkit-scrollbar { width: 4px; }
    .kitchen-scroll::-webkit-scrollbar-track { background: #09090b; }
    .kitchen-scroll::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 2px; }
    .kitchen-scroll::-webkit-scrollbar-thumb:hover { background: #52525b; }
    [x-cloak] { display: none !important; }
  </style>
</head>
<body class="h-full bg-zinc-950 text-white overflow-hidden">
  @yield('content')
</body>
</html>
