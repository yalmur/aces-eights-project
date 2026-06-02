# Cart & Menu Customisation — Design Spec
**Date:** 2026-06-02
**Scope:** Plan 2 (frontend only, static/mock data, no backend)

---

## Overview

Two features built together because they share the same Alpine.js cart store:

1. **Customisation drawer** — right-side slide-in panel triggered by every `+` button on the menu page. Pizza items show full options (size, crust, toppings). Non-pizza items show simplified options (qty + instructions only).
2. **Cart system** — Alpine.js `$store` persisted to `localStorage`, header mini-cart dropdown, and full `/cart` page.

---

## 1. Cart Store (`Alpine.store('cart')`)

Lives in `resources/js/app.js`. Accessible from every page.

### Item shape
```js
{
  cartId: String,       // crypto.randomUUID() — allows duplicate items with different customisations
  id: String,           // slug e.g. 'classic-margherita'
  name: String,
  category: String,     // 'pizza' | 'starter' | 'salad' | 'pasta' | 'dessert' | 'drink'
  basePrice: Number,
  size: String,         // '12" Standard' | '15" Large' — pizza only, null otherwise
  sizeExtra: Number,    // 0 | 4.00
  crust: String,        // 'Sourdough' | 'Gluten-Free' | 'Cauliflower' — pizza only, null otherwise
  crustExtra: Number,   // 0 | 2.00 | 2.50
  toppings: Array,      // [{ name, price }] — pizza only, [] otherwise
  instructions: String,
  qty: Number,
  lineTotal: Number,    // computed: (basePrice + sizeExtra + crustExtra + toppingsTotal) * qty
}
```

### Store methods
- `openDrawer(itemData)` — sets `drawerItem` and `drawerOpen = true`
- `closeDrawer()` — sets `drawerOpen = false`, resets draft state
- `addToCart(item)` — pushes item to `items[]`, persists to localStorage, closes drawer
- `removeItem(cartId)` — removes by cartId
- `updateQty(cartId, delta)` — adjusts qty, removes if qty reaches 0
- `editItem(cartId)` — opens drawer pre-filled with existing item data; drawer footer shows **UPDATE CART** instead of ADD TO CART; saving replaces the existing item in-place (same cartId, same array index) rather than appending a new one
- `clear()` — empties cart
- Computed getters: `itemCount`, `subtotal`, `deliveryFee(orderType)`, `total(orderType)`

### Persistence
On every mutation: `localStorage.setItem('a8_cart', JSON.stringify(this.items))`.
On `Alpine.start()`: load from localStorage if present.

---

## 2. Customisation Drawer

### Location
Blade partial: `resources/views/components/cart-drawer.blade.php`
Included once in `resources/views/layouts/app.blade.php` (before `@livewireScripts`).

### Behaviour
- **Open**: `$store.cart.openDrawer(item)` — item data passed via `x-on:click` on each menu card `+` button
- **Close**: backdrop click, ✕ button, or after `addToCart()`
- **Transition**: slides in from right (`translate-x-full` → `translate-x-0`, 300ms ease)
- **Backdrop**: fixed inset-0 semi-transparent, `z-[60]`
- **Drawer**: fixed right-0 top-0 h-full w-full max-w-md, `z-[70]`

### Drawer sections

**Header** (always shown)
- Item name (font-serif bold)
- "from £X.XX" base price in oxblood
- ✕ close button

**Pizza-only sections** (shown when `category === 'pizza'`)

*Choose Size* — radio-style selection cards (2 options):
- 12" Standard — INCLUDED
- 15" Large — +£4.00

*Crust* — radio-style selection list (3 options):
- 48hr Sourdough — INCLUDED
- Gluten-Free — +£2.00
- Cauliflower — +£2.50

*Add Toppings* — checkbox list:
- Extra Buffalo Mozzarella +£2.00
- Spicy Salamino +£1.50
- Wild Mushrooms +£1.50
- Roasted Peppers +£1.00
- Red Onion +£1.00
- Anchovies +£1.50

**All items**

*Kitchen Notes* — two-part field:

**Quick-pick chips** (toggle behaviour — click selects/oxblood fill, click again deselects):

- Pizza chips: WELL DONE CRUST · EXTRA SPICY · LESS SAUCE · NO ONION · NO CHILLI · EXTRA CRISPY · CUT IN SQUARES
- Non-pizza chips: EXTRA SPICY · NO ONION · NO GARLIC · DRESSING ON SIDE · WELL DONE · NO NUTS

Selected chips are stored as an array. The textarea below displays them joined by ", " and is kept in sync — if customer edits the textarea directly, the raw string is used as-is (chip sync is one-way: chip → text; text edits break chip state cleanly).

**Free-text textarea** — max 120 chars, char counter shown bottom-right, placeholder "Or write your own note to the kitchen…". Combined value saved to `instructions` on the cart item.

*Quantity* — −/+ stepper (min 1, max 9)

**Footer** (always shown)
- Live total: recalculates reactively as options change
- Gold glossy **ADD TO CART** button with live price on right
- Clicking `addToCart()` → closes drawer, shows header cart badge update

### Menu card changes
Each `+` button changes from:
```blade
<a href="{{ route('menu.show', 'slug') }}" class="btn-add ...">
```
to:
```blade
<button
  class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation"
  @click="$store.cart.openDrawer({
    id: 'classic-margherita',
    name: 'Classic Margherita',
    category: 'pizza',
    basePrice: 12.50
  })">
  <span class="material-symbols-outlined text-white text-[20px]">add</span>
</button>
```

---

## 3. Header Mini Cart

### Location
`resources/views/components/header.blade.php` — cart icon section, wrapped in Alpine `x-data`.

### Behaviour
- Cart icon shows badge with `$store.cart.itemCount` (hidden when 0)
- Clicking icon toggles `miniCartOpen`
- Panel closes on click-outside (`@click.away`) and on Escape key
- Panel sits below header, right-aligned, `z-[50]`

### Panel content
- Section heading "YOUR ORDER (N)"
- Item rows: name, customisation summary (e.g. "15" · GF · +Salamino"), qty, line total, ✕ remove
- If cart empty: "Your cart is empty" message
- Divider line
- Subtotal row
- Delivery row (£3.50 — or "FREE for collection")
- **Total** row (bold, oxblood)
- Gold **CHECKOUT** button → `href="{{ route('checkout') }}"`
- Ghost "View full cart →" link → `href="{{ route('cart') }}"`

---

## 4. Cart Page (`/cart`)

**File:** `resources/views/cart/index.blade.php`
**Route:** `GET /cart` → `CartController@index` (already exists, currently stub)

### Layout
Two-column on desktop (items left, summary right). Single column on mobile.

### Order type toggle
Delivery / Collection toggle (Alpine, default Delivery).
Affects delivery fee in summary: Delivery = £3.50, Collection = free.

### Item list
Each row:
- Item name (font-serif bold) + customisation summary below (font-mono small, muted)
- Qty stepper: −/+ (calls `$store.cart.updateQty`)
- Line total (oxblood)
- **Edit** link → reopens drawer pre-filled
- ✕ remove

### Order summary (sticky on desktop)
- Subtotal
- Delivery fee (reactive to order type toggle)
- **TOTAL DUE** (large, oxblood)
- Gold **PROCEED TO CHECKOUT** CTA → `/checkout`

### Empty state
Centred: "Your cart is empty" heading, subtitle, gold "BROWSE MENU" CTA → `/menu`

---

## 5. Files Changed / Created

| Action | File |
|--------|------|
| Modify | `resources/js/app.js` — add Alpine.store('cart') |
| Create | `resources/views/components/cart-drawer.blade.php` |
| Modify | `resources/views/layouts/app.blade.php` — include drawer |
| Modify | `resources/views/components/header.blade.php` — mini cart panel |
| Modify | `resources/views/menu/index.blade.php` — wire all + buttons |
| Modify | `resources/views/cart/index.blade.php` — replace stub with full page |

---

## 6. Out of Scope (Plan 2)

- No server-side cart validation
- No session/database cart — localStorage only
- No promo codes
- No stock checking
- Checkout page wiring (already built separately)
