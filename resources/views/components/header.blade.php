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
        <a href="{{ route('home') }}"     class="px-4 py-2 font-serif text-sm font-bold uppercase tracking-[0.12em] text-on-surface-variant hover:text-primary transition-colors">Home</a>
        <a href="{{ route('our-menu') }}" class="px-4 py-2 font-serif text-sm font-bold uppercase tracking-[0.12em] text-on-surface-variant hover:text-primary transition-colors">Menu</a>
        <a href="{{ route('menu') }}"     class="px-4 py-2 font-serif text-sm font-bold uppercase tracking-[0.12em] text-on-surface-variant hover:text-primary transition-colors">Order Now</a>
        <a href="https://www.acesandeightssaloonbar.com/booking/" target="_blank" rel="noopener" class="px-4 py-2 font-serif text-sm font-bold uppercase tracking-[0.12em] text-on-surface-variant hover:text-primary transition-colors">Book a Table</a>
        <a href="{{ route('party-hall') }}" class="px-4 py-2 font-serif text-sm font-bold uppercase tracking-[0.12em] text-on-surface-variant hover:text-primary transition-colors">Party Hall</a>
        <a href="{{ route('about') }}"   class="px-4 py-2 font-serif text-sm font-bold uppercase tracking-[0.12em] text-on-surface-variant hover:text-primary transition-colors">About</a>
        <a href="{{ route('contact') }}" class="px-4 py-2 font-serif text-sm font-bold uppercase tracking-[0.12em] text-on-surface-variant hover:text-primary transition-colors">Contact</a>
      </nav>
    </div>

    {{-- RIGHT: mini-cart + auth --}}
    <div class="flex-1 flex items-center justify-end gap-2">

      {{-- Mini Cart --}}
      <div x-data="{ open: false }" class="relative">

        {{-- Cart icon button --}}
        <button @click="open = !open"
                class="relative p-2 text-on-surface-variant hover:text-primary transition-colors"
                aria-label="Cart">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="square" stroke-linejoin="miter" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span x-show="$store.cart.itemCount > 0"
                x-text="$store.cart.itemCount"
                x-cloak
                class="absolute -top-1 -right-1 bg-primary text-white text-[10px] font-mono font-bold w-4 h-4 rounded-full flex items-center justify-center"></span>
        </button>

        {{-- Dropdown panel --}}
        <div x-show="open"
             x-cloak
             @click.away="open = false"
             @keydown.escape.window="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="absolute right-0 top-full mt-2 w-80 bg-surface border-2 border-outline-variant shadow-xl z-[55]">

          <div class="px-4 py-3 border-b border-outline-variant">
            <h3 class="font-serif text-sm font-bold text-on-surface"
                x-text="'YOUR ORDER (' + $store.cart.itemCount + ')'"></h3>
          </div>

          <div x-show="$store.cart.items.length === 0" class="px-4 py-6 text-center">
            <p class="font-sans text-sm text-on-surface-variant">Your cart is empty.</p>
          </div>

          <div x-show="$store.cart.items.length > 0" class="max-h-64 overflow-y-auto">
            <template x-for="item in $store.cart.items" :key="item.cartId">
              <div class="flex items-start justify-between px-4 py-3 border-b border-outline-variant last:border-0">
                <div class="flex-1 min-w-0 pr-3">
                  <p class="font-sans text-sm font-semibold text-on-surface truncate"
                     x-text="item.name + ' ×' + item.qty"></p>
                  <p class="font-mono text-[10px] text-on-surface-variant truncate mt-0.5"
                     x-text="$store.cart.itemSummary(item)"></p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                  <span class="font-mono text-sm font-bold text-primary"
                        x-text="'£' + item.lineTotal.toFixed(2)"></span>
                  <button @click="$store.cart.removeItem(item.cartId)"
                          class="text-on-surface-variant hover:text-primary transition-colors" aria-label="Remove">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="square" d="M6 6l12 12M6 18L18 6"/>
                    </svg>
                  </button>
                </div>
              </div>
            </template>
          </div>

          <div x-show="$store.cart.items.length > 0" class="px-4 py-4 border-t border-outline-variant bg-surface-container-low">
            <div class="flex justify-between items-center mb-1">
              <span class="font-mono text-xs text-on-surface-variant">Subtotal</span>
              <span class="font-mono text-sm text-on-surface"
                    x-text="'£' + $store.cart.subtotal.toFixed(2)"></span>
            </div>
            <div class="flex justify-between items-center mb-3">
              <span class="font-mono text-xs text-on-surface-variant">Delivery</span>
              <span class="font-mono text-xs text-on-surface-variant">calculated at checkout</span>
            </div>
            <div class="flex justify-between items-center mb-4">
              <span class="font-mono text-xs font-bold uppercase tracking-widest text-on-surface">Total</span>
              <span class="font-mono text-base font-bold text-primary"
                    x-text="'£' + $store.cart.subtotal.toFixed(2)"></span>
            </div>
            <a href="{{ route('checkout') }}"
               class="btn-primary w-full text-center block mb-2">CHECKOUT</a>
            <a href="{{ route('cart') }}"
               @click="open = false"
               class="block text-center font-mono text-[10px] text-on-surface-variant hover:text-primary transition-colors">
              View full cart →
            </a>
          </div>

        </div>{{-- end dropdown --}}
      </div>{{-- end mini cart x-data --}}

      {{-- Auth buttons — unchanged --}}
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
    </div>{{-- end RIGHT section --}}
  </div>

  {{-- Mobile Nav --}}
  <div x-data="{ open: false }"
       @toggle-mobile-menu.window="open = !open"
       x-show="open"
       x-transition
       class="lg:hidden border-t border-outline-variant bg-surface px-4 py-3 flex flex-col gap-1">
    <a href="{{ route('home') }}"     class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">Home</a>
    <a href="{{ route('our-menu') }}" class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">Menu</a>
    <a href="{{ route('menu') }}"     class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">Order Now</a>
    <a href="https://www.acesandeightssaloonbar.com/booking/" target="_blank" rel="noopener" class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">Book a Table</a>
    <a href="{{ route('party-hall') }}" class="py-2 font-mono text-xs font-semibold uppercase tracking-widest text-on-surface-variant hover:text-primary">Party Hall</a>
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
