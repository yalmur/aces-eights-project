<header class="sticky top-0 z-50 bg-surface border-b-2 border-outline-variant shadow-sm">
  <div class="max-w-container mx-auto px-4 lg:px-16 h-16 lg:h-20 flex items-center">

    {{-- LEFT: hamburger (mobile) | logo (desktop) --}}
    <div class="flex-1 flex items-center justify-start">
      {{-- Hamburger — mobile only --}}
      <button x-data @click="$dispatch('toggle-mobile-menu')"
              class="lg:hidden p-2 -ml-2 text-on-surface-variant hover:text-primary" aria-label="Toggle menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="square" stroke-linejoin="miter" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      {{-- Logo — desktop only --}}
      <a href="{{ route('home') }}" class="hidden lg:block">
        <img src="{{ asset('images/logo.jpg') }}" alt="Aces & Eights Pizza" class="h-14 w-auto rounded">
      </a>
    </div>

    {{-- CENTRE: logo (mobile) | nav (desktop) --}}
    <div class="flex items-center justify-center">
      {{-- Logo centred — mobile only --}}
      <a href="{{ route('home') }}" class="lg:hidden">
        <img src="{{ asset('images/logo.jpg') }}" alt="Aces & Eights Pizza" class="h-10 w-auto rounded">
      </a>
      {{-- Nav — desktop only --}}
      <nav class="hidden lg:flex items-center gap-1">
        <a href="{{ route('home') }}"    class="px-4 py-2 font-serif text-sm font-bold uppercase tracking-[0.12em] text-on-surface-variant hover:text-primary transition-colors">Home</a>
        <a href="{{ route('menu') }}"    class="px-4 py-2 font-serif text-sm font-bold uppercase tracking-[0.12em] text-on-surface-variant hover:text-primary transition-colors">Order</a>
        <a href="{{ route('booking') }}" class="px-4 py-2 font-serif text-sm font-bold uppercase tracking-[0.12em] text-on-surface-variant hover:text-primary transition-colors">Book a Table</a>
        <a href="{{ route('about') }}"   class="px-4 py-2 font-serif text-sm font-bold uppercase tracking-[0.12em] text-on-surface-variant hover:text-primary transition-colors">About</a>
        <a href="{{ route('contact') }}" class="px-4 py-2 font-serif text-sm font-bold uppercase tracking-[0.12em] text-on-surface-variant hover:text-primary transition-colors">Contact</a>
      </nav>
    </div>

    {{-- RIGHT: cart + auth (both breakpoints) --}}
    <div class="flex-1 flex items-center justify-end gap-2">
      <a href="{{ route('cart') }}" class="relative p-2 text-on-surface-variant hover:text-primary transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="square" stroke-linejoin="miter" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <span id="cart-count" class="absolute -top-1 -right-1 bg-primary text-white text-[10px] font-mono font-bold w-4 h-4 rounded-full flex items-center justify-center hidden">0</span>
      </a>
      @auth
        <a href="{{ route('account') }}" class="p-2 text-on-surface-variant hover:text-primary transition-colors" aria-label="My Account">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="square" stroke-linejoin="miter" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/>
          </svg>
        </a>
      @else
        <a href="{{ route('login') }}" class="p-2 text-on-surface-variant hover:text-primary transition-colors" aria-label="Login / Sign Up">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="square" stroke-linejoin="miter" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/>
          </svg>
        </a>
      @endauth
    </div>
  </div>

  {{-- Mobile Nav --}}
  <div x-data="{ open: false }"
       @toggle-mobile-menu.window="open = !open"
       x-show="open"
       x-transition
       class="lg:hidden border-t border-outline-variant bg-surface px-4 py-3 flex flex-col gap-1">
    <a href="{{ route('home') }}"    class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">Home</a>
    <a href="{{ route('menu') }}"    class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">Order</a>
    <a href="{{ route('booking') }}" class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">Book a Table</a>
    <a href="{{ route('about') }}"   class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">About</a>
    <a href="{{ route('contact') }}" class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">Contact</a>
    <div class="section-divider my-2"></div>
    @auth
      <a href="{{ route('account') }}" class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">My Account</a>
    @else
      <a href="{{ route('login') }}"   class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">Login</a>
      <a href="{{ route('register') }}" class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-primary">Sign Up</a>
    @endauth
  </div>
</header>
