<aside class="fixed inset-y-0 left-0 w-64 bg-primary flex flex-col z-40">
  {{-- Logo --}}
  <div class="flex items-center gap-3 px-6 py-5 border-b border-[#8b1a1a]">
    <img src="{{ asset('images/logo.jpg') }}" alt="Aces & Eights" class="h-9 w-auto rounded">
    <div>
      <p class="font-serif text-sm font-bold text-white leading-tight">Aces &amp; Eights</p>
      <p class="label-caps text-[10px] text-[#e0bfbc]">Admin Panel</p>
    </div>
  </div>

  {{-- Navigation --}}
  <nav class="flex-1 px-3 py-4 flex flex-col gap-1 overflow-y-auto">

    <a href="{{ route('admin.dashboard') }}"
       class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="square" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
      </svg>
      Dashboard
    </a>

    <p class="label-caps text-[10px] text-[#8b1a1a] px-4 pt-4 pb-1">Orders</p>

    <a href="{{ route('admin.orders.index') }}"
       class="sidebar-item {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="square" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
      </svg>
      Online Orders
    </a>

    <a href="{{ route('admin.orders.in-store') }}"
       class="sidebar-item {{ request()->routeIs('admin.orders.in-store') ? 'active' : '' }}">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="square" d="M3 3h18v4H3zM7 7v14M17 7v14M3 21h18"/>
      </svg>
      In-Store Orders
    </a>

    <p class="label-caps text-[10px] text-[#8b1a1a] px-4 pt-4 pb-1">Management</p>

    <a href="{{ route('admin.menu.index') }}"
       class="sidebar-item {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="square" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
      </svg>
      Menu
    </a>

    <a href="{{ route('admin.delivery.index') }}"
       class="sidebar-item {{ request()->routeIs('admin.delivery.*') ? 'active' : '' }}">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="square" d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8z"/>
        <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
      </svg>
      Delivery Zones
    </a>

    <a href="{{ route('admin.promotions.index') }}"
       class="sidebar-item {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="square" d="M7 7h.01M17 17h.01M3 3l18 18M7 3h14v14M3 7v14h14"/>
      </svg>
      Promotions
    </a>

    <p class="label-caps text-[10px] text-[#8b1a1a] px-4 pt-4 pb-1">System</p>

    <a href="{{ route('admin.settings.index') }}"
       class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="square" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <path stroke-linecap="square" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
      Settings
    </a>

    {{-- TableAgent link --}}
    <a href="https://tableagent.com" target="_blank" rel="noopener"
       class="sidebar-item mt-1">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="square" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      Table Bookings ↗
    </a>
  </nav>

  {{-- User at bottom --}}
  <div class="border-t border-[#8b1a1a] px-4 py-4 flex items-center gap-3">
    <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center">
      <span class="font-mono text-xs font-bold text-white">
        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
      </span>
    </div>
    <div class="flex-1 min-w-0">
      <p class="font-sans text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
      <p class="label-caps text-[10px] text-[#e0bfbc]">Administrator</p>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" title="Logout" class="text-[#e0bfbc] hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="square" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
      </button>
    </form>
  </div>
</aside>
