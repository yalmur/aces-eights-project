{{-- Cart Customisation Drawer --}}
{{-- Included once in layouts/app.blade.php. Controlled by $store.cart --}}

<div x-data
     x-show="$store.cart.drawerOpen"
     x-cloak
     class="fixed inset-0 z-[60]"
     @keydown.escape.window="$store.cart.closeDrawer()">

  {{-- Backdrop --}}
  <div class="absolute inset-0 bg-on-surface/50 backdrop-blur-sm"
       @click="$store.cart.closeDrawer()"></div>

  {{-- Drawer panel --}}
  <div class="absolute right-0 top-0 h-full w-full max-w-md bg-surface border-l-2 border-outline-variant flex flex-col shadow-2xl"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="translate-x-full"
       @click.stop>

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-outline-variant flex justify-between items-start gap-4 flex-shrink-0">
      <img x-show="$store.cart.drawerItem?.image" x-cloak
           :src="$store.cart.drawerItem?.image"
           :alt="$store.cart.drawerItem?.name"
           class="w-16 h-16 object-cover border border-outline-variant flex-shrink-0">
      <div class="flex-1 min-w-0">
        <h2 class="font-serif text-xl font-bold text-on-surface"
            x-text="$store.cart.drawerItem?.name ?? ''"></h2>
        <p class="font-mono text-xs text-primary mt-1"
           x-text="'from £' + ($store.cart.drawerItem?.basePrice?.toFixed(2) ?? '0.00')"></p>
      </div>
      <button class="text-on-surface-variant hover:text-on-surface transition-colors p-1 mt-1"
              @click="$store.cart.closeDrawer()" aria-label="Close">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="square" d="M6 6l12 12M6 18L18 6"/>
        </svg>
      </button>
    </div>

    {{-- Scrollable body --}}
    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-8">

      {{-- SIZE — pizza only, customizable items only --}}
      <div x-show="$store.cart.drawerIsCustomizable && $store.cart.drawerItem?.category === 'pizza'">
        <h4 class="label-caps text-on-surface-variant mb-4">Choose Size</h4>
        <div class="grid grid-cols-2 gap-3">
          <button @click="$store.cart.setSize('12&quot; Standard', 0)"
                  :class="$store.cart.draft.size === '12&quot; Standard'
                    ? 'border-2 border-primary bg-surface-container'
                    : 'border border-outline-variant bg-surface-container-low hover:bg-surface-container'"
                  class="p-4 text-center transition-colors">
            <span class="block font-serif text-sm font-bold text-on-surface">12" Standard</span>
            <span class="label-caps text-on-surface-variant text-[10px]">INCLUDED</span>
          </button>
          <button @click="$store.cart.setSize('15&quot; Large', 4)"
                  :class="$store.cart.draft.size === '15&quot; Large'
                    ? 'border-2 border-primary bg-surface-container'
                    : 'border border-outline-variant bg-surface-container-low hover:bg-surface-container'"
                  class="p-4 text-center transition-colors">
            <span class="block font-serif text-sm font-bold text-on-surface">15" Large</span>
            <span class="label-caps text-primary text-[10px]">+£4.00</span>
          </button>
        </div>
      </div>

      {{-- CRUST — pizza only, customizable items only --}}
      <div x-show="$store.cart.drawerIsCustomizable && $store.cart.drawerItem?.category === 'pizza'">
        <h4 class="label-caps text-on-surface-variant mb-4">Crust</h4>
        <div class="flex flex-col gap-2">
          <template x-for="[crust, extra, label] in [
            ['48hr Sourdough', 0, 'INCLUDED'],
            ['Gluten-Free', 2, '+£2.00'],
            ['Cauliflower', 2.5, '+£2.50']
          ]" :key="crust">
            <button @click="$store.cart.setCrust(crust, extra)"
                    :class="$store.cart.draft.crust === crust
                      ? 'border-2 border-primary bg-surface-container'
                      : 'border border-outline-variant bg-surface-container-low hover:bg-surface-container'"
                    class="flex items-center justify-between px-4 py-3 transition-colors w-full text-left">
              <span class="font-sans text-sm text-on-surface" x-text="crust"></span>
              <span :class="extra > 0 ? 'text-primary' : 'text-on-surface-variant'"
                    class="label-caps text-[10px]" x-text="label"></span>
            </button>
          </template>
        </div>
      </div>

      {{-- INGREDIENTS — any item with recorded base ingredients, pre-checked, uncheck to remove --}}
      <div x-show="$store.cart.drawerIsCustomizable && $store.cart.drawerBaseIngredients.length > 0">
        <h4 class="label-caps text-on-surface-variant mb-1">Ingredients</h4>
        <p class="font-mono text-[10px] text-on-surface-variant mb-4">Uncheck boxes to remove base ingredients</p>
        <div class="flex flex-col gap-2">
          <template x-for="ingredient in $store.cart.drawerBaseIngredients" :key="ingredient">
            <button @click="$store.cart.toggleIngredient(ingredient)"
                    :class="$store.cart.isIngredientRemoved(ingredient)
                      ? 'border border-outline-variant bg-surface-container-low opacity-60'
                      : 'border-2 border-primary bg-surface-container'"
                    class="flex items-center gap-3 px-4 py-3 transition-all w-full text-left">
              <div :class="$store.cart.isIngredientRemoved(ingredient)
                     ? 'border-outline bg-transparent'
                     : 'bg-primary border-primary'"
                   class="w-4 h-4 border flex items-center justify-center flex-shrink-0 transition-colors">
                <svg x-show="!$store.cart.isIngredientRemoved(ingredient)"
                     class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="square" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
              <span class="font-sans text-sm text-on-surface" x-text="ingredient"></span>
              <span x-show="$store.cart.isIngredientRemoved(ingredient)"
                    class="ml-auto label-caps text-[10px] text-brand-error">REMOVED</span>
            </button>
          </template>
        </div>
      </div>

      {{-- TOPPINGS — pizza only, customizable items only, full 25-item list --}}
      <div x-show="$store.cart.drawerIsCustomizable && $store.cart.drawerItem?.category === 'pizza'">
        <h4 class="label-caps text-on-surface-variant mb-4">Toppings</h4>
        <div class="grid grid-cols-1 gap-2">
          <template x-for="topping in $store.cart.allToppings" :key="topping.name">
            <button @click="$store.cart.toggleTopping(topping)"
                    :class="$store.cart.isToppingSelected(topping.name)
                      ? 'border-2 border-primary bg-surface-container'
                      : 'border border-outline-variant bg-surface-container-low hover:bg-surface-container'"
                    class="flex items-center justify-between px-4 py-3 transition-colors w-full text-left">
              <div class="flex items-center gap-3">
                <div :class="$store.cart.isToppingSelected(topping.name) ? 'bg-primary border-primary' : 'border-outline'"
                     class="w-4 h-4 border flex items-center justify-center flex-shrink-0 transition-colors">
                  <svg x-show="$store.cart.isToppingSelected(topping.name)"
                       class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="square" d="M5 13l4 4L19 7"/>
                  </svg>
                </div>
                <span class="font-sans text-sm text-on-surface" x-text="topping.name"></span>
              </div>
              <span class="label-caps text-primary text-[10px]"
                    x-text="'+£' + topping.price.toFixed(2)"></span>
            </button>
          </template>
        </div>
      </div>

      {{-- UPSELLING & SIDES — customizable items only, admin-curated related items --}}
      <div x-show="$store.cart.drawerIsCustomizable && ($store.cart.drawerItem?.relatedItems ?? []).length > 0" x-cloak>
        <h4 class="label-caps text-on-surface-variant mb-4">Add a Side?</h4>
        <div class="flex flex-col gap-2">
          <template x-for="related in ($store.cart.drawerItem?.relatedItems ?? [])" :key="related.id">
            <div class="flex items-center gap-3 px-3 py-2 border border-outline-variant bg-surface-container-low">
              <img :src="related.image" :alt="related.name" class="w-10 h-10 object-cover flex-shrink-0 border border-outline-variant">
              <div class="flex-1 min-w-0">
                <p class="font-sans text-sm text-on-surface truncate" x-text="related.name"></p>
                <p class="font-mono text-[10px] text-primary" x-text="'+£' + related.price.toFixed(2)"></p>
              </div>
              <button @click="$store.cart.addRelatedToCart(related)" type="button"
                      class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-surface-container border border-outline hover:bg-primary hover:text-on-primary hover:border-primary transition-colors">
                <span class="material-symbols-outlined text-[18px]">add</span>
              </button>
            </div>
          </template>
        </div>
      </div>

      {{-- ALLERGEN INFO — shown when item has allergens --}}
      <div x-show="$store.cart.drawerItem?.allergens?.length > 0" x-cloak>
        <div class="bg-amber-50 border border-amber-200 px-4 py-3">
          <p class="font-mono text-[10px] font-bold uppercase text-amber-800 mb-2 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">warning</span>
            Allergen Information
          </p>
          <p class="font-mono text-[10px] text-amber-700 mb-2">This item contains:</p>
          <div class="flex flex-wrap gap-1.5">
            <template x-for="allergen in ($store.cart.drawerItem?.allergens ?? [])" :key="allergen">
              <span class="font-mono text-[10px] font-bold uppercase bg-amber-100 border border-amber-300 text-amber-900 px-2 py-0.5"
                    x-text="allergen"></span>
            </template>
          </div>
          <p class="font-mono text-[9px] text-amber-700 mt-2">If you have a food allergy, please contact us before ordering.</p>
        </div>
      </div>

      {{-- KITCHEN NOTES — all items --}}
      <div>
        <h4 class="label-caps text-on-surface-variant mb-4">Kitchen Notes <span class="normal-case font-sans font-normal tracking-normal text-[11px]">(optional)</span></h4>

        {{-- Quick-pick chips --}}
        <div class="flex flex-wrap gap-2 mb-4">
          <template x-for="chip in $store.cart.activeChips" :key="chip">
            <button @click="$store.cart.toggleChip(chip)"
                    :class="$store.cart.isChipSelected(chip)
                      ? 'bg-primary text-white border-primary'
                      : 'bg-surface-container-low text-on-surface-variant border-outline-variant hover:border-outline'"
                    class="px-3 py-1.5 border label-caps text-[10px] transition-colors"
                    x-text="chip">
            </button>
          </template>
        </div>

        {{-- Free-text --}}
        <div class="relative">
          <textarea
            x-model="$store.cart.draft.instructions"
            @input="$store.cart.draft.chips = []"
            maxlength="120"
            rows="2"
            placeholder="Or write your own note to the kitchen…"
            class="w-full bg-transparent border-0 border-b-2 border-outline py-2 font-sans text-sm text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none transition-colors resize-none pr-10"></textarea>
          <span class="absolute right-0 bottom-2 label-caps text-[10px] text-on-surface-variant"
                x-text="(120 - ($store.cart.draft.instructions?.length ?? 0)) + '/120'"></span>
        </div>
        <p class="mt-2 font-mono text-[10px] text-on-surface-variant">Tip: tap chips to add quickly — or type anything you need.</p>
      </div>

    </div>{{-- end scrollable body --}}

    {{-- Footer --}}
    <div class="px-6 py-5 border-t border-outline-variant bg-surface flex-shrink-0">

      {{-- Qty stepper --}}
      <div class="flex items-center justify-between mb-4">
        <span class="label-caps text-on-surface-variant">Quantity</span>
        <div class="flex items-center gap-4">
          <button @click="$store.cart.draft.qty = Math.max(1, $store.cart.draft.qty - 1)"
                  class="w-8 h-8 border border-outline flex items-center justify-center hover:bg-surface-container transition-colors font-bold text-lg leading-none">−</button>
          <span class="font-mono font-bold text-on-surface w-4 text-center"
                x-text="$store.cart.draft.qty"></span>
          <button @click="$store.cart.draft.qty = Math.min(9, $store.cart.draft.qty + 1)"
                  class="w-8 h-8 border border-outline flex items-center justify-center hover:bg-surface-container transition-colors font-bold text-lg leading-none">+</button>
        </div>
      </div>

      {{-- Add / Update button --}}
      <button @click="$store.cart.addToCart()"
              class="btn-primary w-full flex justify-between items-center px-6">
        <span x-text="$store.cart.editingCartId ? 'UPDATE CART' : 'ADD TO CART'"></span>
        <span x-text="'£' + $store.cart.draftLineTotal.toFixed(2)"></span>
      </button>

    </div>

  </div>{{-- end drawer panel --}}
</div>
