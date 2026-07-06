import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

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

const cartStore = {
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
    return this.drawerItem?.ingredients ?? []
  },

  get drawerIsCustomizable() {
    return this.drawerItem?.isCustomizable !== false
  },

  get drawerToppings() {
    return this.drawerItem?.availableToppings ?? []
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
    const defSize  = (itemData.sizes  || []).find(s => s.is_default) || (itemData.sizes  || [])[0] || null
    const defCrust = (itemData.crusts || []).find(c => c.is_default) || (itemData.crusts || [])[0] || null
    this.draft = {
      size:       defSize  ? defSize.name       : null,
      sizeExtra:  defSize  ? defSize.price_adj  : 0,
      crust:      defCrust ? defCrust.name      : null,
      crustExtra: defCrust ? defCrust.price_adj : 0,
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
      image: item.image,
      ingredients: item.ingredients,
      relatedItems: item.relatedItems,
      isCustomizable: item.isCustomizable,
      availableToppings: item.availableToppings,
      sizes: item.sizes || [],
      crusts: item.crusts || [],
    }
    this.editingCartId = cartId
    this.draft = {
      size: item.size || null,
      sizeExtra: item.sizeExtra || 0,
      crust: item.crust || null,
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
    const hasSizes  = (this.drawerItem.sizes  || []).length > 0
    const hasCrusts = (this.drawerItem.crusts || []).length > 0
    const isPizza   = this.drawerItem.category === 'pizza'
    const lineTotal = (
      this.drawerItem.basePrice +
      (hasSizes  ? this.draft.sizeExtra  : 0) +
      (hasCrusts ? this.draft.crustExtra : 0) +
      (isPizza   ? toppingsExtra         : 0)
    ) * this.draft.qty

    const cartItem = {
      cartId: this.editingCartId || crypto.randomUUID(),
      id: this.drawerItem.id,
      name: this.drawerItem.name,
      category: this.drawerItem.category,
      basePrice: this.drawerItem.basePrice,
      image: this.drawerItem.image,
      ingredients: this.drawerItem.ingredients,
      relatedItems: this.drawerItem.relatedItems,
      isCustomizable: this.drawerItem.isCustomizable,
      availableToppings: this.drawerItem.availableToppings,
      sizes:  this.drawerItem.sizes  || [],
      crusts: this.drawerItem.crusts || [],
      size:               hasSizes  ? this.draft.size        : null,
      sizeExtra:          hasSizes  ? this.draft.sizeExtra   : 0,
      crust:              hasCrusts ? this.draft.crust       : null,
      crustExtra:         hasCrusts ? this.draft.crustExtra  : 0,
      toppings:           isPizza ? [...this.draft.toppings] : [],
      removedIngredients: [...this.draft.removedIngredients],
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

  addRelatedToCart(related) {
    this.items.push({
      cartId: crypto.randomUUID(),
      id: related.id,
      name: related.name,
      category: null,
      basePrice: related.price,
      image: related.image,
      ingredients: [],
      relatedItems: [],
      isCustomizable: false,
      availableToppings: [],
      size: null,
      sizeExtra: 0,
      crust: null,
      crustExtra: 0,
      toppings: [],
      removedIngredients: [],
      chips: [],
      instructions: '',
      qty: 1,
      lineTotal: related.price,
    })
    this._persist()
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

  reorder(items) {
    this.items = items.map(item => ({ ...item, cartId: crypto.randomUUID() }))
    this._persist()
    window.location.href = '/cart'
  },

  _persist() {
    localStorage.setItem('a8_cart', JSON.stringify(this.items))
  },
}

const adminNavStore = {
  open: false,
  toggle() { this.open = !this.open },
  close() { this.open = false },
}

Alpine.store('cart', cartStore)
Alpine.store('adminNav', adminNavStore)

document.addEventListener('alpine:init', () => {
  Alpine.data('imageCropper', (existingUrl = null, existingName = null) => ({
    previewUrl: existingUrl,
    fileName: existingName,
    cropping: false,
    cropper: null,

    onFileChange(e) {
      const file = e.target.files[0]
      if (!file) return
      this.fileName = file.name
      this.previewUrl = URL.createObjectURL(file)
      this.$refs.removeFlag.value = '0'
    },

    async openCropper() {
      if (!this.previewUrl) return
      const [{ default: Cropper }] = await Promise.all([
        import('cropperjs'),
        import('cropperjs/dist/cropper.css'),
      ])
      this.cropping = true
      await this.$nextTick()
      this.cropper = new Cropper(this.$refs.cropImg, { viewMode: 1, aspectRatio: 1, background: false })
    },

    applyCrop() {
      this.cropper.getCroppedCanvas({ width: 800, height: 800 }).toBlob((blob) => {
        this.previewUrl = URL.createObjectURL(blob)
        const dt = new DataTransfer()
        dt.items.add(new File([blob], this.fileName || 'cropped.jpg', { type: 'image/jpeg' }))
        this.$refs.fileInput.files = dt.files
        this.closeCropper()
      }, 'image/jpeg', 0.9)
    },

    closeCropper() {
      if (this.cropper) { this.cropper.destroy(); this.cropper = null }
      this.cropping = false
    },

    removeImage() {
      this.previewUrl = null
      this.fileName = null
      this.$refs.fileInput.value = ''
      this.$refs.removeFlag.value = '1'
    },
  }))
})
