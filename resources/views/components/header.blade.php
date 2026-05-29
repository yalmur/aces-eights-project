<header class="sticky top-0 z-50 bg-surface border-b-2 border-outline-variant shadow-sm">
  <div class="max-w-container mx-auto px-4 lg:px-16 flex items-center justify-between h-16">

    {{-- Logo --}}
    <a href="{{ route('home') }}" class="flex items-center gap-3">
      <img src="{{ asset('images/logo.jpg') }}" alt="Aces & Eights Pizza" class="h-10 w-auto rounded">
      <span class="font-serif text-lg font-bold text-primary hidden sm:block leading-tight">
        Aces &amp; Eights<br><span class="text-xs font-mono font-semibold uppercase tracking-widest text-on-surface-variant">Pizza</span>
      </span>
    </a>

    {{-- Desktop Nav --}}
    <nav class="hidden lg:flex items-center gap-1">
      <a href="{{ route('home') }}"    class="px-3 py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary transition-colors">Home</a>
      <a href="{{ route('menu') }}"    class="px-3 py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary transition-colors">Menu</a>
      <a href="{{ route('booking') }}" class="px-3 py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary transition-colors">Book a Table</a>
      <a href="{{ route('about') }}"   class="px-3 py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary transition-colors">About</a>
      <a href="{{ route('contact') }}" class="px-3 py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary transition-colors">Contact</a>
    </nav>

    {{-- Right actions --}}
    <div class="flex items-center gap-3">
      {{-- Cart --}}
      <a href="{{ route('cart') }}" class="relative p-2 text-on-surface-variant hover:text-primary transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="square" stroke-linejoin="miter" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <span id="cart-count" class="absolute -top-1 -right-1 bg-primary text-white text-[10px] font-mono font-bold w-4 h-4 rounded-full flex items-center justify-center hidden">0</span>
      </a>

      {{-- Auth --}}
      @auth
        <a href="{{ route('account') }}" class="btn-secondary py-2 px-4">Account</a>
      @else
        <a href="{{ route('login') }}"    class="font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary transition-colors">Login</a>
        <a href="{{ route('register') }}" class="btn-primary py-2 px-4">Sign Up</a>
      @endauth

      {{-- Mobile menu button --}}
      <button x-data @click="$dispatch('toggle-mobile-menu')" class="lg:hidden p-2 text-on-surface-variant hover:text-primary" aria-label="Toggle menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="square" stroke-linejoin="miter" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
  </div>

  {{-- Mobile Nav --}}
  <div x-data="{ open: false }"
       @toggle-mobile-menu.window="open = !open"
       x-show="open"
       x-transition
       class="lg:hidden border-t border-outline-variant bg-surface px-4 py-3 flex flex-col gap-1">
    <a href="{{ route('home') }}"    class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">Home</a>
    <a href="{{ route('menu') }}"    class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">Menu</a>
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
