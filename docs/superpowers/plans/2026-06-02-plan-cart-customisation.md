# Cart & Menu Customisation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a right-side customisation drawer to the menu page (with removable base ingredients + full toppings list), a header mini-cart dropdown, and a full cart page — all powered by an Alpine.js store persisted to localStorage.

**Architecture:** Alpine.js `$store('cart')` is the single source of truth for cart state. It is registered before `Alpine.start()` in `resources/js/app.js` and is accessible from every Blade component via `$store.cart`. The drawer and mini-cart are Blade components included in the customer layout. No backend is touched — this is pure Plan 2 frontend work. Base ingredients and toppings are hardcoded; in Plan 4 they will be replaced by API calls.

**Tech Stack:** Alpine.js 3, Tailwind CSS 3, Laravel Blade, localStorage

---

## File Map

| Action | File | Responsibility |
|--------|------|----------------|
| Modify | `resources/js/app.js` | Register `Alpine.store('cart')` before `Alpine.start()` |
| Create | `resources/views/components/cart-drawer.blade.php` | Right-side customisation drawer (all Alpine) |
| Modify | `resources/views/layouts/app.blade.php` | Include drawer, add `[x-cloak]` style |
| Modify | `resources/views/components/header.blade.php` | Replace static cart link with mini-cart panel |
| Modify | `resources/views/menu/index.blade.php` | Wire every `+` button to `$store.cart.openDrawer()` |
| Modify | `resources/views/cart/index.blade.php` | Replace stub with full cart page |

---

## Task 1: Alpine.js Cart Store

**Files:**
- Modify: `resources/js/app.js`

- [ ] **Step 1: Replace the full contents of `resources/js/app.js`**

```js
import Alpine from 'alpinejs'

window.Alpine = Alpine

Alpine.store('cart', {
  items: JSON.parse(localStorage.getItem('a8_cart') || '[]'),
  drawerOpen: false,
  drawerItem: null,
  editingCartId: null,

  draft: {
    size: '12" Standard',
    sizeExtra: 0,
    crust: '48hr Sourdough',
    crustExtra: 0,
    toppings: [],
    removedIngredients: [],
    chips: [],
    instructions: '',
    qty: 1,
  },

  // Base ingredients per pizza item — hardcoded Plan 2, replaced by API in Plan 4
  baseIngredients: {
    'classic-margherita': ['Tomato Sauce', 'Fior di Latte', 'Basil', 'Olive Oil'],
    'spicy-diavola':      ['Tomato Sauce', 'Mozzarella', 'Nduja', 'Calabrese Salami'],
    'tartufo-bianco':     ['White Base', 'Wild Mushrooms', 'Truffle Oil', 'Pecorino'],
    'vegan-garden':       ['Tomato Sauce', 'Vegan Mozzarella', 'Roasted Peppers', 'Zucchini', 'Red Onion'],
    'the-meat-lover':     ['Tomato Sauce', 'Mozzarella', 'Salami', 'Smoked Pancetta', 'Fennel Sausage'],
  },

  // Global toppings list available on all pizzas
  allToppings: [
    { name: 'Aubergines',         price: 2.00 },
    { name: 'Mixed Peppers',      price: 1.50 },
    { name: 'Mushrooms',          price: 1.50 },
    { name: 'Regular Pepperoni',  price: 2.00 },
    { name: 'Nduja',              price: 2.00 },
    { name: 'Spicy Ground Beef',  price: 2.00 },
    { name: 'Broccoli',           price: 2.00 },
    { name: 'Parmesan',           price: 2.00 },
    { name: 'Pine Nuts',          price: 1.50 },
    { name: 'Garlic Oil',         price: 1.50 },
    { name: 'Mozzarella',         price: 2.00 },
    { name: 'Olive Oil',          price: 1.50 },
    { name: 'Smoky Pancetta',     price: 2.00 },
    { name: 'Tomato Sauce',       price: 1.00 },
    { name: 'Basil',              price: 0.50 },
    { name: 'Red Onion',          price: 1.50 },
    { name: 'Anchovies',          price: 2.00 },
    { name: 'Chilli Flakes',      price: 1.00 },
    { name: 'Whole Black Olives', price: 1.50 },
    { name: 'Oregano',            price: 0.50 },
    { name: 'Vegan Mozzarella',   price: 2.50 },
    { name: 'Sicilian Sausage',   price: 2.00 },
    { name: 'Hot Honey',          price: 2.00 },
    { name: 'Speck Ham',          price: 2.00 },
    { name: 'Provolone Picante',  price: 1.50 },
  ],

  pizzaChips: [
    'WELL DONE CRUST', 'EXTRA SPICY', 'LESS SAUCE',
    'NO ONION', 'NO CHILLI', 'EXTRA CRISPY', 'CUT IN SQUARES',
  ],

  otherChips: [
    'EXTRA SPICY', 'NO ONION', 'NO GARLIC',
    'DRESSING ON SIDE', 'WELL DONE', 'NO NUTS',
  ],

  get itemCount() {
    return this.items.reduce((sum, item) => sum + item.qty, 0)
  },

  get subtotal() {
    return this.items.reduce((sum, item) => sum + item.lineTotal, 0)
  },

  deliveryFee(orderType) {
    return orderType === 'delivery' ? 3.50 : 0
  },

  total(orderType) {
    return this.subtotal + this.deliveryFee(orderType)
  },

  get draftLineTotal() {
    if (!this.drawerItem) return 0
    const toppingsExtra = this.draft.toppings.reduce((s, t) => s + t.price, 0)
    return (this.drawerItem.basePrice + this.draft.sizeExtra + this.draft.crustExtra + toppingsExtra) * this.draft.qty
  },

  get activeChips() {
    return this.drawerItem && this.drawerItem.category === 'pizza'
      ? this.pizzaChips
      : this.otherChips
  },

  get drawerBaseIngredients() {
    if (!this.drawerItem || this.drawerItem.category !== 'pizza') return []
    return this.baseIngredients[this.drawerItem.id] || []
  },

  itemSummary(item) {
    const parts = []
    if (item.size && item.size !== '12" Standard') parts.push(item.size)
    if (item.crust && item.crust !== '48hr Sourdough') parts.push(item.crust)
    if (item.removedIngredients && item.removedIngredients.length) {
      parts.push('no ' + item.removedIngredients.join(', no '))
    }
    if (item.toppings && item.toppings.length) {
      parts.push(item.toppings.map(t => '+' + t.name).join(', '))
    }
    if (item.instructions) parts.push(item.instructions)
    return parts.length ? parts.join(' · ') : 'No extras'
  },

  openDrawer(itemData) {
    this.drawerItem = itemData
    this.editingCartId = null
    this.draft = {
      size: '12" Standard',
      sizeExtra: 0,
      crust: '48hr Sourdough',
      crustExtra: 0,
      toppings: [],
      removedIngredients: [],
      chips: [],
      instructions: '',
      qty: 1,
    }
    this.drawerOpen = true
    document.body.style.overflow = 'hidden'
  },

  editItem(cartId) {
    const item = this.items.find(i => i.cartId === cartId)
    if (!item) return
    this.drawerItem = {
      id: item.id,
      name: item.name,
      category: item.category,
      basePrice: item.basePrice,
    }
    this.editingCartId = cartId
    this.draft = {
      size: item.size || '12" Standard',
      sizeExtra: item.sizeExtra || 0,
      crust: item.crust || '48hr Sourdough',
      crustExtra: item.crustExtra || 0,
      toppings: [...item.toppings],
      removedIngredients: [...(item.removedIngredients || [])],
      chips: [...(item.chips || [])],
      instructions: item.instructions || '',
      qty: item.qty,
    }
    this.drawerOpen = true
    document.body.style.overflow = 'hidden'
  },

  closeDrawer() {
    this.drawerOpen = false
    this.drawerItem = null
    this.editingCartId = null
    document.body.style.overflow = ''
  },

  setSize(size, extra) {
    this.draft.size = size
    this.draft.sizeExtra = extra
  },

  setCrust(crust, extra) {
    this.draft.crust = crust
    this.draft.crustExtra = extra
  },

  toggleTopping(topping) {
    const idx = this.draft.toppings.findIndex(t => t.name === topping.name)
    if (idx >= 0) {
      this.draft.toppings.splice(idx, 1)
    } else {
      this.draft.toppings.push({ name: topping.name, price: topping.price })
    }
  },

  isToppingSelected(name) {
    return this.draft.toppings.some(t => t.name === name)
  },

  toggleIngredient(name) {
    const idx = this.draft.removedIngredients.indexOf(name)
    if (idx >= 0) {
      this.draft.removedIngredients.splice(idx, 1)
    } else {
      this.draft.removedIngredients.push(name)
    }
  },

  isIngredientRemoved(name) {
    return this.draft.removedIngredients.includes(name)
  },

  toggleChip(chip) {
    const idx = this.draft.chips.indexOf(chip)
    if (idx >= 0) {
      this.draft.chips.splice(idx, 1)
    } else {
      this.draft.chips.push(chip)
    }
    this.draft.instructions = this.draft.chips.join(', ')
  },

  isChipSelected(chip) {
    return this.draft.chips.includes(chip)
  },

  addToCart() {
    const toppingsExtra = this.draft.toppings.reduce((s, t) => s + t.price, 0)
    const isPizza = this.drawerItem.category === 'pizza'
    const lineTotal = (
      this.drawerItem.basePrice +
      (isPizza ? this.draft.sizeExtra : 0) +
      (isPizza ? this.draft.crustExtra : 0) +
      (isPizza ? toppingsExtra : 0)
    ) * this.draft.qty

    const cartItem = {
      cartId: this.editingCartId || crypto.randomUUID(),
      id: this.drawerItem.id,
      name: this.drawerItem.name,
      category: this.drawerItem.category,
      basePrice: this.drawerItem.basePrice,
      size:               isPizza ? this.draft.size        : null,
      sizeExtra:          isPizza ? this.draft.sizeExtra   : 0,
      crust:              isPizza ? this.draft.crust       : null,
      crustExtra:         isPizza ? this.draft.crustExtra  : 0,
      toppings:           isPizza ? [...this.draft.toppings] : [],
      removedIngredients: isPizza ? [...this.draft.removedIngredients] : [],
      chips:     [...this.draft.chips],
      instructions: this.draft.instructions,
      qty: this.draft.qty,
      lineTotal,
    }

    if (this.editingCartId) {
      const idx = this.items.findIndex(i => i.cartId === this.editingCartId)
      if (idx >= 0) this.items.splice(idx, 1, cartItem)
    } else {
      this.items.push(cartItem)
    }

    this._persist()
    this.closeDrawer()
  },

  removeItem(cartId) {
    this.items = this.items.filter(i => i.cartId !== cartId)
    this._persist()
  },

  updateQty(cartId, delta) {
    const item = this.items.find(i => i.cartId === cartId)
    if (!item) return
    const newQty = item.qty + delta
    if (newQty <= 0) {
      this.removeItem(cartId)
      return
    }
    item.qty = newQty
    const toppingsExtra = item.toppings.reduce((s, t) => s + t.price, 0)
    item.lineTotal = (item.basePrice + item.sizeExtra + item.crustExtra + toppingsExtra) * item.qty
    this._persist()
  },

  clear() {
    this.items = []
    this._persist()
  },

  _persist() {
    localStorage.setItem('a8_cart', JSON.stringify(this.items))
  },
})

Alpine.start()
```

- [ ] **Step 2: Build assets to verify no JS errors**

```bash
cd C:\AcesAndEightsPizza\webapp
npm run build
```

Expected: `✓ built in Xms` — no errors.

- [ ] **Step 3: Verify store in browser console**

Start server: `& "C:\xampp\php\php.exe" artisan serve --port=8000`
Open `http://localhost:8000`, open DevTools console:

```js
Alpine.store('cart').allToppings.length
// Expected: 25

Alpine.store('cart').drawerBaseIngredients
// Expected: [] (no drawer open yet)

Alpine.store('cart').openDrawer({ id: 'classic-margherita', name: 'Classic Margherita', category: 'pizza', basePrice: 12.50 })
Alpine.store('cart').drawerBaseIngredients
// Expected: ['Tomato Sauce', 'Fior di Latte', 'Basil', 'Olive Oil']

Alpine.store('cart').closeDrawer()
```

- [ ] **Step 4: Commit**

```bash
git add resources/js/app.js
git commit -m "feat: add Alpine.js cart store with ingredients, full toppings, localStorage"
```

---

## Task 2: Customisation Drawer Component

**Files:**
- Create: `resources/views/components/cart-drawer.blade.php`

- [ ] **Step 1: Create `resources/views/components/cart-drawer.blade.php`**

```blade
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
    <div class="px-6 py-5 border-b border-outline-variant flex justify-between items-start flex-shrink-0">
      <div>
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

      {{-- SIZE — pizza only --}}
      <div x-show="$store.cart.drawerItem?.category === 'pizza'">
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

      {{-- CRUST — pizza only --}}
      <div x-show="$store.cart.drawerItem?.category === 'pizza'">
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

      {{-- INGREDIENTS — pizza only, pre-checked, uncheck to remove --}}
      <div x-show="$store.cart.drawerItem?.category === 'pizza' && $store.cart.drawerBaseIngredients.length > 0">
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

      {{-- TOPPINGS — pizza only, full 25-item list --}}
      <div x-show="$store.cart.drawerItem?.category === 'pizza'">
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
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/components/cart-drawer.blade.php
git commit -m "feat: add customisation drawer with ingredients, toppings, kitchen notes"
```

---

## Task 3: Wire Drawer into App Layout

**Files:**
- Modify: `resources/views/layouts/app.blade.php`

- [ ] **Step 1: Replace the full contents of `resources/views/layouts/app.blade.php`**

```blade
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Aces & Eights Pizza' }}</title>
  <meta name="description" content="{{ $description ?? 'Authentic Italian pizza in Tufnell Park, London. Order online for delivery, collection, or eat-in.' }}">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('head')
  @livewireStyles
  <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen flex flex-col bg-surface">

  <x-header />

  <main class="flex-1">
    @yield('content')
  </main>

  <x-footer />

  <x-cart-drawer />

  @livewireScripts
</body>
</html>
```

- [ ] **Step 2: Build and smoke test**

```bash
npm run build
```

Visit `http://localhost:8000` — DevTools console must show 0 errors.
Run: `Alpine.store('cart').drawerOpen` → `false`.

- [ ] **Step 3: Commit**

```bash
git add resources/views/layouts/app.blade.php
git commit -m "feat: include cart drawer in app layout"
```

---

## Task 4: Update Header with Mini Cart

**Files:**
- Modify: `resources/views/components/header.blade.php`

- [ ] **Step 1: Find and replace the RIGHT section of the header**

Find this exact block (starts at line ~37 of `header.blade.php`):

```blade
    {{-- RIGHT: cart + auth (both breakpoints) --}}
    <div class="flex-1 flex items-center justify-end gap-2">
      <a href="{{ route('cart') }}" class="relative p-2 text-on-surface-variant hover:text-primary transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="square" stroke-linejoin="miter" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <span id="cart-count" class="absolute -top-1 -right-1 bg-primary text-white text-[10px] font-mono font-bold w-4 h-4 rounded-full flex items-center justify-center hidden">0</span>
      </a>
      @auth
```

Replace that entire RIGHT section (from `{{-- RIGHT: cart + auth --}}` through the closing `</div>` that ends the RIGHT section, just before the `{{-- Mobile Nav --}}` comment) with:

```blade
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
```

The `{{-- Mobile Nav --}}` block that follows remains **unchanged**.

- [ ] **Step 2: Build and verify**

```bash
npm run build
```

Open `http://localhost:8000`. Cart icon visible. Run in console:

```js
Alpine.store('cart').openDrawer({ id: 'classic-margherita', name: 'Classic Margherita', category: 'pizza', basePrice: 12.50 })
Alpine.store('cart').addToCart()
// Badge shows "1"
```

Click cart icon → dropdown shows "Classic Margherita ×1". Click ✕ → item removed, badge gone.

- [ ] **Step 3: Commit**

```bash
git add resources/views/components/header.blade.php
git commit -m "feat: add mini-cart dropdown to header"
```

---

## Task 5: Wire Menu + Buttons

**Files:**
- Modify: `resources/views/menu/index.blade.php`

All pizza and non-pizza `+` buttons currently use `<a href="route('menu.show', 'slug')">`. Replace every one with a `<button @click="$store.cart.openDrawer({...})">`.

- [ ] **Step 1: Replace pizza card + buttons**

```blade
{{-- Classic Margherita --}}
<button @click="$store.cart.openDrawer({ id: 'classic-margherita', name: 'Classic Margherita', category: 'pizza', basePrice: 12.50 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- Spicy Diavola --}}
<button @click="$store.cart.openDrawer({ id: 'spicy-diavola', name: 'Spicy Diavola', category: 'pizza', basePrice: 14.50 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- Tartufo Bianco --}}
<button @click="$store.cart.openDrawer({ id: 'tartufo-bianco', name: 'Tartufo Bianco', category: 'pizza', basePrice: 16.00 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- Vegan Garden --}}
<button @click="$store.cart.openDrawer({ id: 'vegan-garden', name: 'Vegan Garden', category: 'pizza', basePrice: 13.50 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- The Meat Lover --}}
<button @click="$store.cart.openDrawer({ id: 'the-meat-lover', name: 'The Meat Lover', category: 'pizza', basePrice: 17.00 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>
```

- [ ] **Step 2: Replace non-pizza card + buttons**

```blade
{{-- Garlic Bread --}}
<button @click="$store.cart.openDrawer({ id: 'garlic-bread', name: 'Garlic Bread', category: 'starter', basePrice: 5.50 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- Nocellara Olives --}}
<button @click="$store.cart.openDrawer({ id: 'nocellara-olives', name: 'Nocellara Olives', category: 'starter', basePrice: 4.00 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- Burrata --}}
<button @click="$store.cart.openDrawer({ id: 'burrata', name: 'Burrata', category: 'starter', basePrice: 8.50 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- Caesar Salad --}}
<button @click="$store.cart.openDrawer({ id: 'caesar-salad', name: 'Caesar Salad', category: 'salad', basePrice: 9.00 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- Rocket & Parmesan --}}
<button @click="$store.cart.openDrawer({ id: 'rocket-parmesan', name: 'Rocket &amp; Parmesan', category: 'salad', basePrice: 7.50 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- Cacio e Pepe --}}
<button @click="$store.cart.openDrawer({ id: 'cacio-e-pepe', name: 'Cacio e Pepe', category: 'pasta', basePrice: 11.00 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- Amatriciana --}}
<button @click="$store.cart.openDrawer({ id: 'amatriciana', name: 'Amatriciana', category: 'pasta', basePrice: 13.00 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- Tiramisu --}}
<button @click="$store.cart.openDrawer({ id: 'tiramisu', name: 'Tiramisu', category: 'dessert', basePrice: 7.00 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- Panna Cotta --}}
<button @click="$store.cart.openDrawer({ id: 'panna-cotta', name: 'Panna Cotta', category: 'dessert', basePrice: 6.50 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- Moretti Draft --}}
<button @click="$store.cart.openDrawer({ id: 'moretti-draft', name: 'Moretti Draft', category: 'drink', basePrice: 6.50 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- San Pellegrino --}}
<button @click="$store.cart.openDrawer({ id: 'san-pellegrino', name: 'San Pellegrino', category: 'drink', basePrice: 3.50 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>

{{-- House Red Wine --}}
<button @click="$store.cart.openDrawer({ id: 'house-red-wine', name: 'House Red Wine', category: 'drink', basePrice: 28.00 })"
        class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>
```

- [ ] **Step 3: Verify drawer opens correctly for pizza vs non-pizza**

Visit `http://localhost:8000/menu`. Click `+` on Classic Margherita.

Expected:
- Drawer slides in from right
- Shows Size, Crust sections
- Shows **INGREDIENTS** section: Tomato Sauce ✓, Fior di Latte ✓, Basil ✓, Olive Oil ✓ (all pre-checked/oxblood)
- Uncheck "Basil" → row dims, shows "REMOVED" label
- Shows **TOPPINGS** section with all 25 items unchecked
- Check "Anchovies" → row highlights oxblood, price updates in footer
- Footer: `ADD TO CART £12.50` → after selecting 15" + Anchovies → `ADD TO CART £18.00` (12.50 + 4 + 2 × 1 qty)
- Kitchen Notes chips show pizza set

Click `+` on Garlic Bread.
Expected:
- No Size / Crust / Ingredients / Toppings sections
- Shows Kitchen Notes with non-pizza chips (EXTRA SPICY, NO ONION, etc.)
- Footer: `ADD TO CART £5.50`

- [ ] **Step 4: Commit**

```bash
git add resources/views/menu/index.blade.php
git commit -m "feat: wire menu + buttons to customisation drawer"
```

---

## Task 6: Build Cart Page

**Files:**
- Modify: `resources/views/cart/index.blade.php`

- [ ] **Step 1: Replace the full contents of `resources/views/cart/index.blade.php`**

```blade
@extends('layouts.app')
@section('content')

<div x-data="{ orderType: 'delivery' }" class="max-w-container mx-auto px-4 lg:px-16 py-12">

  <h1 class="font-serif text-headline-lg text-on-surface mb-8">Your Order</h1>

  {{-- Empty state --}}
  <div x-show="$store.cart.items.length === 0" x-cloak class="py-24 text-center">
    <svg class="w-16 h-16 text-outline mx-auto mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
      <path stroke-linecap="square" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
    <h2 class="font-serif text-headline-md text-on-surface mb-3">Your cart is empty</h2>
    <p class="font-sans text-sm text-on-surface-variant mb-8">Add something delicious from our menu.</p>
    <a href="{{ route('menu') }}" class="btn-primary">BROWSE MENU</a>
  </div>

  {{-- Cart content --}}
  <div x-show="$store.cart.items.length > 0" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- LEFT: item list --}}
    <div class="lg:col-span-2 space-y-4">

      {{-- Order type toggle --}}
      <div class="flex gap-0 border border-outline-variant mb-6">
        <button @click="orderType = 'delivery'"
                :class="orderType === 'delivery' ? 'bg-primary text-white' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container'"
                class="flex-1 py-3 label-caps text-xs transition-colors">DELIVERY</button>
        <button @click="orderType = 'collection'"
                :class="orderType === 'collection' ? 'bg-primary text-white' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container'"
                class="flex-1 py-3 label-caps text-xs transition-colors border-l border-outline-variant">COLLECTION</button>
      </div>

      {{-- Items --}}
      <template x-for="item in $store.cart.items" :key="item.cartId">
        <div class="bg-surface-container-low border border-outline-variant p-5 flex gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3 mb-1">
              <h3 class="font-serif text-base font-bold text-on-surface" x-text="item.name"></h3>
              <span class="font-mono text-sm font-bold text-primary flex-shrink-0"
                    x-text="'£' + item.lineTotal.toFixed(2)"></span>
            </div>
            <p class="font-mono text-[11px] text-on-surface-variant mb-3"
               x-text="$store.cart.itemSummary(item)"></p>

            <div class="flex items-center gap-4">
              <div class="flex items-center gap-3">
                <button @click="$store.cart.updateQty(item.cartId, -1)"
                        class="w-7 h-7 border border-outline flex items-center justify-center hover:bg-surface-container transition-colors font-bold text-base leading-none">−</button>
                <span class="font-mono text-sm font-bold text-on-surface w-4 text-center" x-text="item.qty"></span>
                <button @click="$store.cart.updateQty(item.cartId, 1)"
                        class="w-7 h-7 border border-outline flex items-center justify-center hover:bg-surface-container transition-colors font-bold text-base leading-none">+</button>
              </div>
              <button @click="$store.cart.editItem(item.cartId)"
                      class="font-mono text-[11px] text-on-surface-variant hover:text-primary transition-colors underline">Edit</button>
              <button @click="$store.cart.removeItem(item.cartId)"
                      class="font-mono text-[11px] text-on-surface-variant hover:text-primary transition-colors underline ml-auto">Remove</button>
            </div>
          </div>
        </div>
      </template>

    </div>

    {{-- RIGHT: order summary --}}
    <div class="lg:col-span-1">
      <div class="bg-surface-container-low border border-outline-variant p-6 lg:sticky lg:top-24">
        <h2 class="font-serif text-headline-sm text-on-surface mb-6">Order Summary</h2>
        <div class="space-y-3 mb-6">
          <div class="flex justify-between">
            <span class="font-sans text-sm text-on-surface-variant">Subtotal</span>
            <span class="font-mono text-sm text-on-surface"
                  x-text="'£' + $store.cart.subtotal.toFixed(2)"></span>
          </div>
          <div class="flex justify-between">
            <span class="font-sans text-sm text-on-surface-variant">Delivery fee</span>
            <span class="font-mono text-sm"
                  :class="orderType === 'delivery' ? 'text-on-surface' : 'text-primary'"
                  x-text="orderType === 'delivery' ? '£3.50' : 'FREE'"></span>
          </div>
          <div class="section-divider"></div>
          <div class="flex justify-between items-center pt-1">
            <span class="font-serif text-base font-bold text-on-surface">Total Due</span>
            <span class="font-mono text-lg font-bold text-primary"
                  x-text="'£' + $store.cart.total(orderType).toFixed(2)"></span>
          </div>
        </div>
        <a href="{{ route('checkout') }}" class="btn-primary w-full text-center block mb-3">
          PROCEED TO CHECKOUT
        </a>
        <a href="{{ route('menu') }}" class="btn-ghost w-full text-center block">
          ← Continue Ordering
        </a>
      </div>
    </div>

  </div>

</div>

@endsection
```

- [ ] **Step 2: Verify cart page end-to-end**

1. Go to `/menu`, add Classic Margherita: uncheck Basil, add Anchovies, select 15" Large, Gluten-Free crust → ADD TO CART
2. Add Garlic Bread ×2 → ADD TO CART
3. Go to `/cart`
4. Verify:
   - Margherita summary shows: `15" Large · Gluten-Free · no Basil · +Anchovies` (£20.50 × 1 = £20.50)
   - Garlic Bread ×2 shows: `No extras` (£5.50 × 2 = £11.00)
   - Subtotal: £31.50
   - Delivery toggle: switching to Collection removes £3.50
   - Edit Margherita → drawer opens pre-filled with 15"/GF/Basil unchecked/Anchovies checked, footer shows UPDATE CART
   - Qty − on Garlic Bread → qty becomes 1, line total updates
   - Remove → item disappears
   - Remove all → empty state with BROWSE MENU

- [ ] **Step 3: Commit**

```bash
git add resources/views/cart/index.blade.php
git commit -m "feat: build full cart page"
```

---

## Task 7: Run Tests + Final Verification

- [ ] **Step 1: Run smoke tests**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/RouteSmokeTest.php
```

Expected: `Tests: 16 passed`

- [ ] **Step 2: Build production assets**

```bash
npm run build
```

Expected: `✓ built in Xms`

- [ ] **Step 3: Final manual checklist**

- [ ] Pizza drawer: Size, Crust, Ingredients (pre-checked), Toppings (25 items), Kitchen Notes
- [ ] Uncheck ingredient → dims + REMOVED label
- [ ] Re-check ingredient → returns to oxblood
- [ ] Topping selected → price in footer updates
- [ ] Removing ingredient does NOT change price; adding topping DOES
- [ ] Non-pizza drawer: no Size/Crust/Ingredients/Toppings, only Kitchen Notes
- [ ] Kitchen note chips toggle, sync to textarea; typing in textarea clears chips
- [ ] ADD TO CART → badge increments, drawer closes
- [ ] Mini-cart dropdown: shows items with customisation summary, remove works
- [ ] Cart page: correct line totals, delivery toggle reactive, edit works, remove works
- [ ] Refresh page → cart persists (localStorage)
- [ ] `/admin` → redirects to `/login`

- [ ] **Step 4: Final commit**

```bash
git add .
git commit -m "feat: complete cart + menu customisation with ingredients and toppings (Plan 2)"
```
