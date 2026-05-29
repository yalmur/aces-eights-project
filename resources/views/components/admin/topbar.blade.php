@props(['title' => 'Dashboard'])
<header class="h-16 border-b border-outline-variant bg-white flex items-center justify-between px-6 flex-shrink-0">
  <h1 class="font-serif text-xl font-bold text-on-surface">{{ $title }}</h1>
  <div class="flex items-center gap-4">
    <span class="label-caps text-xs text-on-surface-variant">{{ now()->format('D, d M Y') }}</span>
    <a href="{{ route('home') }}" target="_blank"
       class="label-caps text-xs text-primary hover:underline">
      View Site ↗
    </a>
  </div>
</header>
