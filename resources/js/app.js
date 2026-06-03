import Alpine from 'alpinejs'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Alpine = Alpine

// Initialise Pusher Echo when credentials are available
if (import.meta.env.VITE_PUSHER_APP_KEY && import.meta.env.VITE_PUSHER_APP_KEY !== 'your_pusher_app_key') {
    window.Pusher = Pusher
    window.Echo = new Echo({
        broadcaster:  'pusher',
        key:          import.meta.env.VITE_PUSHER_APP_KEY,
        cluster:      import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'eu',
        forceTLS:     true,
        authEndpoint: '/broadcasting/auth',
    })
}

Alpine.store('cart', {
  items: (() => { try { return JSON.parse(localStorage.getItem('a8_cart') || '[]') } catch { return [] } })(),
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

  baseIngredients: {
    'classic-margherita': ['Tomato Sauce', 'Fior di Latte', 'Basil', 'Olive Oil'],
    'spicy-diavola':      ['Tomato Sauce', 'Mozzarella', 'Nduja', 'Calabrese Salami'],
    'tartufo-bianco':     ['White Base', 'Wild Mushrooms', 'Truffle Oil', 'Pecorino'],
    'vegan-garden':       ['Tomato Sauce', 'Vegan Mozzarella', 'Roasted Peppers', 'Zucchini', 'Red Onion'],
    'the-meat-lover':     ['Tomato Sauce', 'Mozzarella', 'Salami', 'Smoked Pancetta', 'Fennel Sausage'],
  },

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
    const toppingsExtra = this.draft.toppings.reduce((s, t) => s + (t.price || 0), 0)
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
      toppings: (item.toppings || []).map(t => ({ ...t })),
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
    const toppingsExtra = (item.toppings || []).reduce((s, t) => s + (t.price || 0), 0)
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

Alpine.store('adminNav', {
  open: false,
  toggle() { this.open = !this.open },
  close() { this.open = false },
})

Alpine.start()
