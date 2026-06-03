<aside x-data
       @keydown.escape.window="$store.adminNav.close()"
       class="fixed top-0 left-0 h-full w-80 bg-surface border-r border-[#2B2B2B] z-[70]
              lg:fixed lg:top-16 lg:h-[calc(100vh-64px)] lg:z-40"
       :class="$store.adminNav.open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       style="transition: transform 0.3s cubic-bezier(0.4,0,0.2,1)">

  <div class="flex flex-col h-full py-6 px-4">

    {{-- Mobile close button --}}
    <div class="flex justify-between items-center mb-6 px-2 lg:hidden">
      <h2 class="font-serif text-base font-bold text-primary">Aces & Eights Admin</h2>
      <button @click="$store.adminNav.close()" class="p-2 hover:bg-surface-container rounded-full">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    {{-- Nav --}}
    <nav class="space-y-1 flex-1">
      <a href="{{ route('admin.dashboard') }}"
         class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">dashboard</span>
        Dashboard
      </a>
      <p class="font-mono text-[9px] text-[#2B2B2B] px-4 pt-4 pb-1 uppercase tracking-widest">Orders</p>
      <a href="{{ route('admin.orders.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">list_alt</span>
        Online Orders
      </a>
      <a href="{{ route('admin.orders.in-store') }}"
         class="admin-nav-item {{ request()->routeIs('admin.orders.in-store') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">storefront</span>
        In-Store Orders
      </a>
      <a href="{{ route('admin.kitchen.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.kitchen.index') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">outdoor_grill</span>
        Kitchen Command
      </a>
      <p class="font-mono text-[9px] text-[#2B2B2B] px-4 pt-4 pb-1 uppercase tracking-widest">Management</p>
      <a href="{{ route('admin.menu.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">restaurant_menu</span>
        Menu
      </a>
      <a href="{{ route('admin.delivery.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.delivery.*') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">local_shipping</span>
        Delivery
      </a>
      <a href="{{ route('admin.allergy.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.allergy.*') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">warning</span>
        Allergy
      </a>
      <a href="{{ route('admin.promotions.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">campaign</span>
        Promotions
      </a>
      <p class="font-mono text-[9px] text-[#2B2B2B] px-4 pt-4 pb-1 uppercase tracking-widest">System</p>
      <a href="{{ route('admin.settings.index') }}"
         class="admin-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">settings</span>
        Settings
      </a>
    </nav>

    {{-- User footer --}}
    <div class="border-t border-[#2B2B2B]/20 px-2 pt-4 mt-4">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center">
          <span class="font-mono text-xs font-bold text-white">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
          </span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-sans text-xs font-semibold text-on-surface truncate">
            {{ auth()->user()->name ?? 'Admin' }}
          </p>
          <p class="font-mono text-[9px] uppercase text-on-surface-variant tracking-widest">Store Manager</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" title="Logout" class="text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[18px]">logout</span>
          </button>
        </form>
      </div>
    </div>

  </div>
</aside>
