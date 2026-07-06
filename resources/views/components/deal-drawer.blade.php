{{-- Deal Builder Drawer --}}
{{-- Controlled by $store.dealCart --}}

<div x-data
     x-show="$store.dealCart.open"
     x-cloak
     class="fixed inset-0 z-[65]"
     @keydown.escape.window="$store.dealCart.close()">

  {{-- Backdrop --}}
  <div class="absolute inset-0 bg-on-surface/50 backdrop-blur-sm"
       @click="$store.dealCart.close()"></div>

  {{-- Panel --}}
  <div class="absolute right-0 top-0 h-full w-full max-w-lg bg-surface border-l-2 border-outline-variant flex flex-col shadow-2xl"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="translate-x-full"
       @click.stop>

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-outline-variant flex justify-between items-start gap-4 flex-shrink-0 bg-surface-container-low">
      <div class="flex-1">
        <p class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-1">Building Deal</p>
        <h2 class="font-serif text-2xl font-bold text-on-surface" x-text="$store.dealCart.deal?.name ?? ''"></h2>
        <p class="font-sans text-sm text-on-surface-variant mt-1" x-text="$store.dealCart.deal?.description ?? ''"></p>
      </div>
      <button @click="$store.dealCart.close()" class="text-on-surface-variant hover:text-on-surface p-1 mt-1">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="square" d="M6 6l12 12M6 18L18 6"/>
        </svg>
      </button>
    </div>

    {{-- Progress bar --}}
    <div class="h-1 bg-surface-container flex-shrink-0">
      <div class="h-full bg-[#690008] transition-all duration-300"
           :style="'width: ' + $store.dealCart.progressPct + '%'"></div>
    </div>

    {{-- Slot tabs --}}
    <div class="flex border-b border-outline-variant overflow-x-auto flex-shrink-0 scrollbar-hide">
      <template x-for="(slot, i) in ($store.dealCart.deal?.slots ?? [])" :key="slot.id">
        <button @click="$store.dealCart.activeSlotIdx = i"
                :class="$store.dealCart.activeSlotIdx === i
                    ? 'border-b-2 border-[#690008] text-[#690008] font-bold'
                    : 'border-b-2 border-transparent text-on-surface-variant hover:text-on-surface'"
                class="px-4 py-3 font-mono text-[10px] uppercase tracking-widest whitespace-nowrap transition-colors flex items-center gap-1.5 flex-shrink-0">
          <span x-show="$store.dealCart.isSlotFilled(i)"
                class="material-symbols-outlined text-green-600 text-[14px]" style="font-variation-settings:'FILL' 1">check_circle</span>
          <span x-show="!$store.dealCart.isSlotFilled(i)" class="material-symbols-outlined text-[14px]"
                :class="slot.is_required ? 'text-[#690008]' : 'text-on-surface-variant'">radio_button_unchecked</span>
          <span x-text="slot.label"></span>
          <span x-show="slot.is_free" class="ml-0.5 text-green-700 text-[9px] font-bold">FREE</span>
          <span x-show="slot.min_qty > 1 || slot.max_qty > 1"
                class="text-on-surface-variant text-[9px]"
                x-text="'×' + slot.max_qty"></span>
        </button>
      </template>
    </div>

    {{-- Slot content --}}
    <div class="flex-1 overflow-y-auto">
      <template x-for="(slot, i) in ($store.dealCart.deal?.slots ?? [])" :key="slot.id">
        <div x-show="$store.dealCart.activeSlotIdx === i" class="p-6">

          <div class="flex justify-between items-center mb-4">
            <div>
              <p class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant"
                 x-text="slot.is_free ? 'Choose your FREE item' : (slot.max_qty > 1 ? 'Choose up to ' + slot.max_qty + ' items' : 'Choose 1 item')"></p>
              <p x-show="$store.dealCart.selections[slot.id]?.length > 0" x-cloak
                 class="font-mono text-[10px] text-green-700"
                 x-text="$store.dealCart.selections[slot.id]?.length + ' selected'"></p>
            </div>
            <button x-show="$store.dealCart.selections[slot.id]?.length > 0" x-cloak
                    @click="$store.dealCart.selections[slot.id] = []"
                    class="font-mono text-[10px] text-on-surface-variant hover:text-primary uppercase tracking-widest">
              Clear
            </button>
          </div>

          <div class="grid grid-cols-1 gap-2">
            <template x-for="item in slot.items" :key="item.id">
              <button @click="$store.dealCart.toggleSlotItem(slot, item)"
                      :class="$store.dealCart.isItemSelected(slot.id, item.id)
                          ? 'border-2 border-[#690008] bg-[#690008]/5'
                          : 'border border-outline-variant bg-surface hover:bg-surface-container-low'"
                      class="flex items-center gap-4 px-4 py-3 transition-all text-left w-full">
                <img :src="item.image" :alt="item.name"
                     class="w-14 h-14 object-cover border border-outline-variant flex-shrink-0">
                <div class="flex-1 min-w-0">
                  <p class="font-sans text-sm font-semibold text-on-surface" x-text="item.name"></p>
                  <p class="font-mono text-xs"
                     :class="slot.is_free ? 'text-green-700 font-bold' : 'text-on-surface-variant'"
                     x-text="slot.is_free ? 'FREE' : '£' + item.price.toFixed(2)"></p>
                </div>
                <div :class="$store.dealCart.isItemSelected(slot.id, item.id)
                             ? 'bg-[#690008] border-[#690008]' : 'border-outline bg-transparent'"
                     class="w-5 h-5 border flex items-center justify-center flex-shrink-0 transition-colors rounded-full">
                  <svg x-show="$store.dealCart.isItemSelected(slot.id, item.id)"
                       class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="square" d="M5 13l4 4L19 7"/>
                  </svg>
                </div>
              </button>
            </template>

            <div x-show="!slot.items || slot.items.length === 0"
                 class="py-8 text-center font-mono text-xs text-on-surface-variant uppercase tracking-widest">
              No items configured for this slot.
            </div>
          </div>

          {{-- Navigate to next slot --}}
          <div class="flex gap-3 mt-6">
            <button x-show="i > 0" @click="$store.dealCart.activeSlotIdx = i - 1"
                    class="flex-1 py-3 border border-surface-variant font-mono text-[10px] uppercase tracking-widest text-on-surface-variant hover:bg-surface-container transition-colors">
              ← Back
            </button>
            <button x-show="i < ($store.dealCart.deal?.slots?.length ?? 0) - 1"
                    @click="$store.dealCart.activeSlotIdx = i + 1"
                    class="flex-1 py-3 bg-surface-container border border-surface-variant font-mono text-[10px] uppercase tracking-widest text-on-surface hover:bg-surface-container-high transition-colors">
              Next →
            </button>
          </div>
        </div>
      </template>
    </div>

    {{-- Footer --}}
    <div class="px-6 py-5 border-t border-outline-variant bg-surface flex-shrink-0">
      {{-- Slot fill summary --}}
      <div class="flex flex-wrap gap-1.5 mb-4">
        <template x-for="(slot, i) in ($store.dealCart.deal?.slots ?? [])" :key="slot.id">
          <span :class="$store.dealCart.isSlotFilled(i)
                    ? 'bg-green-100 text-green-800 border-green-300'
                    : (slot.is_required ? 'bg-red-50 text-[#690008] border-[#690008]/30' : 'bg-surface-container text-on-surface-variant border-surface-variant')"
                class="font-mono text-[9px] uppercase tracking-wide px-2 py-0.5 border"
                x-text="slot.label + ($store.dealCart.isSlotFilled(i) ? ' ✓' : (slot.is_required ? ' *' : ''))">
          </span>
        </template>
      </div>

      {{-- Qty + Add --}}
      <div class="flex items-center justify-between mb-4">
        <span class="label-caps text-on-surface-variant">Quantity</span>
        <div class="flex items-center gap-4">
          <button @click="$store.dealCart.qty = Math.max(1, $store.dealCart.qty - 1)"
                  class="w-8 h-8 border border-outline flex items-center justify-center hover:bg-surface-container transition-colors font-bold text-lg leading-none">−</button>
          <span class="font-mono font-bold text-on-surface w-4 text-center" x-text="$store.dealCart.qty"></span>
          <button @click="$store.dealCart.qty = Math.min(9, $store.dealCart.qty + 1)"
                  class="w-8 h-8 border border-outline flex items-center justify-center hover:bg-surface-container transition-colors font-bold text-lg leading-none">+</button>
        </div>
      </div>

      <button @click="$store.dealCart.addToCart()"
              :disabled="!$store.dealCart.canAddToCart"
              :class="$store.dealCart.canAddToCart
                  ? 'btn-primary'
                  : 'bg-surface-container text-on-surface-variant border border-surface-variant cursor-not-allowed'"
              class="w-full flex justify-between items-center px-6 py-3 font-mono text-xs uppercase font-bold transition-colors">
        <span x-text="$store.dealCart.canAddToCart ? 'Add Deal to Cart' : 'Fill required slots first'"></span>
        <span x-text="'£' + $store.dealCart.totalPrice.toFixed(2)"></span>
      </button>
    </div>

  </div>{{-- end panel --}}
</div>
