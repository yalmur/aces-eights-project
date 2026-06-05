<footer class="bg-primary text-white mt-16">
  {{-- Top section --}}
  <div class="max-w-container mx-auto px-4 lg:px-16 py-12 grid grid-cols-1 md:grid-cols-5 gap-8">

    {{-- Brand --}}
    <div class="md:col-span-2 flex flex-col gap-4">
      <img src="{{ asset('images/logo.jpg') }}" alt="Aces & Eights Pizza" class="h-28 w-auto self-start rounded">
      <p class="font-sans text-sm text-[#e0bfbc] leading-relaxed">
        Authentic Italian pizza in the heart of Tufnell Park, North London.
      </p>
      <div class="flex gap-3">
        <a href="#" aria-label="Facebook" class="w-9 h-9 rounded border border-[#8b1a1a] flex items-center justify-center hover:bg-primary-container transition-colors">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
        </a>
        <a href="#" aria-label="Instagram" class="w-9 h-9 rounded border border-[#8b1a1a] flex items-center justify-center hover:bg-primary-container transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/></svg>
        </a>
      </div>
    </div>

    {{-- Opening Hours --}}
    <div class="md:col-span-2 flex flex-col gap-3">
      <h3 class="font-serif text-lg font-bold">Opening Hours</h3>
      <div class="section-divider border-[#8b1a1a]"></div>
      <dl class="grid grid-cols-2 gap-x-4 gap-y-2 font-sans text-sm text-[#e0bfbc]">
        <dt class="font-semibold text-white">Sun – Thu</dt>
        <dd>{{ \App\Models\Setting::get('opening_sun_thu', '16:00 – 22:45') }}</dd>
        <dt class="font-semibold text-white">Fri – Sat</dt>
        <dd>{{ \App\Models\Setting::get('opening_fri_sat', '16:00 – 23:15') }}</dd>
      </dl>
    </div>

    {{-- Contact --}}
    <div class="md:col-span-1 flex flex-col gap-3">
      <h3 class="font-serif text-lg font-bold">Find Us</h3>
      <div class="section-divider border-[#8b1a1a]"></div>
      <address class="font-sans text-sm text-[#e0bfbc] not-italic leading-relaxed">
        {{ \App\Models\Setting::get('store_address', '156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP') }}
      </address>
      <a href="tel:+442074854033" class="font-mono text-xs tracking-widest text-[#e0bfbc] hover:text-white transition-colors">{{ \App\Models\Setting::get('store_phone', '+44 020 7485 4033') }}</a>
      <a href="mailto:nw5pizza@gmail.com" class="font-mono text-xs tracking-widest text-[#e0bfbc] hover:text-white transition-colors">{{ \App\Models\Setting::get('store_email', 'nw5pizza@gmail.com') }}</a>
    </div>
  </div>

  {{-- Bottom bar --}}
  <div class="border-t border-[#8b1a1a]">
    <div class="max-w-container mx-auto px-4 lg:px-16 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
      <p class="label-caps text-[#e0bfbc] text-[10px]">&copy; {{ date('Y') }} Aces &amp; Eights Pizza. All rights reserved.</p>
      <nav class="flex gap-4">
        <a href="{{ route('menu') }}"    class="label-caps text-[10px] text-[#e0bfbc] hover:text-white transition-colors">Order</a>
        <a href="https://www.acesandeightssaloonbar.com/booking/" target="_blank" rel="noopener" class="label-caps text-[10px] text-[#e0bfbc] hover:text-white transition-colors">Book a Table</a>
        <a href="{{ route('contact') }}" class="label-caps text-[10px] text-[#e0bfbc] hover:text-white transition-colors">Contact</a>
      </nav>
    </div>
  </div>
</footer>
