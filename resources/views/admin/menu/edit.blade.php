@extends('layouts.admin')
@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-on-surface-variant mb-4 font-mono text-xs">
  <a href="{{ route('admin.menu.index') }}" class="hover:text-primary transition-colors">Menu Management</a>
  <span class="material-symbols-outlined text-sm">chevron_right</span>
  <span>{{ $itemId ?? 'Add New Item' }}</span>
</div>

<form class="grid grid-cols-1 lg:grid-cols-12 gap-gutter"
      method="POST"
      action="{{ $item ? route('admin.menu.update', $item->id) : route('admin.menu.store') }}"
      enctype="multipart/form-data">
  @csrf
  @if($item) @method('PUT') @endif
@if($errors->any())
<div class="lg:col-span-12 p-4 bg-brand-error/10 border border-brand-error font-mono text-xs text-brand-error">
  @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
</div>
@endif
<!-- Left Column: Primary Details -->
<div class="lg:col-span-7 space-y-12">
<!-- Section 1: Basic Info -->
<section class="industrial-border p-8 bg-surface-container-lowest">
<h3 class="font-headline-md text-headline-md mb-6 flex items-center gap-3">
<span class="material-symbols-outlined">edit_note</span> Core Details
</h3>
<div class="space-y-8">
<div class="relative">
<label class="font-label-caps text-label-caps text-on-surface-variant block mb-1">Item Name</label>
<input class="w-full bg-transparent border-t-0 border-x-0 border-b-2 border-industrial-gray py-2 font-headline-md text-headline-md focus:ring-0 px-0" name="name" value="{{ old('name', $item?->name) }}" placeholder="Enter item name..." type="text"/>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
<div class="relative">
<label class="font-label-caps text-label-caps text-on-surface-variant block mb-1">Category</label>
<select name="category_id" class="w-full bg-transparent border-t-0 border-x-0 border-b-2 border-industrial-gray py-2 font-body-lg text-body-lg focus:ring-0 px-0 appearance-none">
  @foreach($categories as $cat)
    <option value="{{ $cat->id }}" {{ $item && $item->category_id == $cat->id ? 'selected' : '' }}>
      {{ $cat->name }}
    </option>
  @endforeach
</select>
</div>
<div class="relative">
<label class="font-label-caps text-label-caps text-on-surface-variant block mb-1">Base Price ($)</label>
<input class="w-full bg-transparent border-t-0 border-x-0 border-b-2 border-industrial-gray py-2 font-body-lg text-body-lg focus:ring-0 px-0" name="base_price" value="{{ old('base_price', $item?->base_price) }}" placeholder="0.00" step="0.01" type="number"/>
</div>
</div>
<div class="relative">
<label class="font-label-caps text-label-caps text-on-surface-variant block mb-1">Description</label>
<textarea name="description" class="w-full bg-transparent border-t-0 border-x-0 border-b-2 border-industrial-gray py-2 font-body-md text-body-md focus:ring-0 px-0 resize-none" placeholder="Describe the flavors and ingredients..." rows="4">{{ old('description', $item?->description) }}</textarea>
</div>
</div>
</section>
<!-- Section 2: Allergy Information -->
<section class="industrial-border p-8 bg-surface-container-lowest">
<h3 class="font-headline-md text-headline-md mb-6 flex items-center gap-3">
<span class="material-symbols-outlined">warning</span> Allergy Information
</h3>
<div class="grid grid-cols-2 md:grid-cols-3 gap-4">
@foreach($allergens as $allergen)
<label class="flex items-center gap-3 cursor-pointer group">
  <input type="checkbox" name="allergens[]" value="{{ $allergen->id }}"
         {{ $item && $item->allergens->contains($allergen->id) ? 'checked' : '' }}
         class="w-5 h-5 border-2 border-industrial-gray text-oxblood-red focus:ring-oxblood-red rounded-sm"/>
  <span class="font-body-md text-body-md group-hover:text-oxblood-red transition-colors">{{ $allergen->name }}</span>
</label>
@endforeach
</div>
</section>
<!-- Section 3: Base Ingredients -->
<section class="industrial-border p-8 bg-surface-container-lowest">
<div class="flex justify-between items-center mb-6">
<h3 class="font-headline-md text-headline-md flex items-center gap-3">
<span class="material-symbols-outlined">lunch_dining</span> Base Ingredients
</h3>
</div>
<div x-data="{
  ingredients: {{ json_encode(old('ingredients', $item?->baseIngredients?->pluck('name')->toArray() ?? [])) }},
  newIngredient: '',
  add() {
    let val = this.newIngredient.trim();
    if (val && !this.ingredients.includes(val)) {
      this.ingredients.push(val);
      this.newIngredient = '';
    }
  },
  remove(i) { this.ingredients.splice(i, 1); }
}">
  <div class="flex gap-3 mb-4">
    <input x-model="newIngredient"
           @keydown.enter.prevent="add()"
           class="flex-1 bg-transparent border-t-0 border-x-0 border-b-2 border-industrial-gray py-2 font-body-md text-body-md focus:ring-0 px-0"
           placeholder="e.g. Mozzarella, San Marzano Tomatoes..." type="text"/>
    <button @click="add()" type="button"
            class="px-4 py-2 industrial-border font-mono text-[10px] font-bold uppercase hover:bg-primary hover:text-on-primary transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-sm">add</span> Add
    </button>
  </div>
  <div class="space-y-2">
    <template x-for="(ing, i) in ingredients" :key="i">
      <div class="flex items-center justify-between p-3 border border-surface-variant bg-surface group">
        <input type="hidden" :name="'ingredients['+i+']'" :value="ing" />
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-surface-variant flex items-center justify-center rounded">
            <span class="material-symbols-outlined text-on-surface-variant text-sm">drag_indicator</span>
          </div>
          <span class="font-body-md text-body-md" x-text="ing"></span>
        </div>
        <button @click="remove(i)" type="button"
                class="text-on-surface-variant hover:text-brand-error transition-colors opacity-0 group-hover:opacity-100">
          <span class="material-symbols-outlined text-sm">close</span>
        </button>
      </div>
    </template>
    <p x-show="ingredients.length === 0" class="text-xs text-on-surface-variant py-4 text-center font-mono">
      No ingredients added. Customers will see this as "no listed ingredients".
    </p>
  </div>
</div>
</section>

<!-- Section 3.4: Toppings -->
<section class="industrial-border p-8 bg-surface-container-lowest mt-12">
<h3 class="font-headline-md text-headline-md mb-2 flex items-center gap-3">
<span class="material-symbols-outlined">local_pizza</span> Toppings
</h3>
<p class="font-mono text-[10px] text-on-surface-variant uppercase mb-6">Only shown to customers for pizza-category items. Manage the price list under Admin &rarr; Toppings.</p>
@if($toppings->isEmpty())
<p class="text-xs text-on-surface-variant font-mono">No toppings configured yet. Add some under Admin &rarr; Toppings first.</p>
@else
<div class="grid grid-cols-2 md:grid-cols-3 gap-4">
@foreach($toppings as $topping)
<label class="flex items-center gap-3 cursor-pointer group">
  <input type="checkbox" name="toppings[]" value="{{ $topping->id }}"
         {{ $item && $item->toppings->contains($topping->id) ? 'checked' : '' }}
         class="w-5 h-5 border-2 border-industrial-gray text-oxblood-red focus:ring-oxblood-red rounded-sm"/>
  <span class="font-body-md text-body-md group-hover:text-oxblood-red transition-colors">{{ $topping->name }} <span class="text-on-surface-variant text-xs">(+£{{ number_format($topping->price, 2) }})</span></span>
</label>
@endforeach
</div>
@endif
</section>

<!-- Section 3.5: Sizes -->
<section class="industrial-border p-8 bg-surface-container-lowest mt-12"
  x-data="{
    sizes: {{ json_encode(
      old('sizes', $sizes->map(fn($s) => [
        'name'             => $s->name,
        'price_adjustment' => (float) $s->price_adjustment,
        'is_default'       => $s->is_default,
        'is_available'     => $s->is_available,
      ])->toArray()) ?? []
    ) }},
    newName: '',
    newPrice: '0.00',
    add() {
      let n = this.newName.trim();
      if (!n) return;
      this.sizes.push({ name: n, price_adjustment: parseFloat(this.newPrice) || 0, is_default: this.sizes.length === 0, is_available: true });
      this.newName = ''; this.newPrice = '0.00';
    },
    remove(i) { this.sizes.splice(i, 1); },
    setDefault(i) { this.sizes.forEach((s, j) => s.is_default = (j === i)); }
  }">
  <h3 class="font-headline-md text-headline-md mb-2 flex items-center gap-3">
    <span class="material-symbols-outlined">straighten</span> Sizes
  </h3>
  <p class="font-mono text-[10px] text-on-surface-variant uppercase mb-6">Per-item size options. First added is base price (£0 adj). Mark one as default.</p>
  <div class="flex gap-3 mb-4 flex-wrap">
    <input x-model="newName"
           @keydown.enter.prevent="add()"
           class="flex-1 min-w-[140px] bg-transparent border-t-0 border-x-0 border-b-2 border-industrial-gray py-2 font-body-md text-body-md focus:ring-0 px-0"
           placeholder='e.g. 12" Standard' type="text"/>
    <div class="flex items-center gap-2">
      <span class="font-mono text-xs text-on-surface-variant">+£</span>
      <input x-model="newPrice" step="0.01" min="0"
             class="w-24 bg-transparent border-t-0 border-x-0 border-b-2 border-industrial-gray py-2 font-body-md text-body-md focus:ring-0 px-0"
             placeholder="0.00" type="number"/>
    </div>
    <button @click="add()" type="button"
            class="px-4 py-2 industrial-border font-mono text-[10px] font-bold uppercase hover:bg-primary hover:text-on-primary transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-sm">add</span> Add Size
    </button>
  </div>
  <div class="space-y-2">
    <template x-for="(size, i) in sizes" :key="i">
      <div class="flex items-center justify-between p-3 border border-surface-variant bg-surface group gap-4">
        <input type="hidden" :name="'sizes['+i+'][name]'" :value="size.name" />
        <input type="hidden" :name="'sizes['+i+'][price_adjustment]'" :value="size.price_adjustment" />
        <input type="hidden" :name="'sizes['+i+'][is_default]'" :value="size.is_default ? 1 : 0" />
        <input type="hidden" :name="'sizes['+i+'][is_available]'" :value="size.is_available ? 1 : 0" />
        <div class="flex items-center gap-3 flex-1 min-w-0">
          <span class="material-symbols-outlined text-on-surface-variant text-sm flex-shrink-0">straighten</span>
          <span class="font-body-md text-body-md truncate" x-text="size.name"></span>
          <span class="font-mono text-xs text-on-surface-variant flex-shrink-0" x-text="size.price_adjustment > 0 ? '+£' + size.price_adjustment.toFixed(2) : 'base'"></span>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
          <button @click="setDefault(i)" type="button"
                  :class="size.is_default ? 'text-heritage-gold' : 'text-on-surface-variant hover:text-heritage-gold'"
                  class="transition-colors text-xs font-mono uppercase flex items-center gap-1">
            <span class="material-symbols-outlined text-sm" x-text="size.is_default ? 'star' : 'star_outline'"></span>
            <span x-text="size.is_default ? 'Default' : 'Set default'"></span>
          </button>
          <button @click="size.is_available = !size.is_available" type="button"
                  :class="size.is_available ? 'text-green-600' : 'text-industrial-gray'"
                  class="transition-colors font-mono text-xs uppercase flex items-center gap-1">
            <span class="material-symbols-outlined text-sm" x-text="size.is_available ? 'visibility' : 'visibility_off'"></span>
          </button>
          <button @click="remove(i)" type="button"
                  class="text-on-surface-variant hover:text-brand-error transition-colors opacity-0 group-hover:opacity-100">
            <span class="material-symbols-outlined text-sm">close</span>
          </button>
        </div>
      </div>
    </template>
    <p x-show="sizes.length === 0" class="text-xs text-on-surface-variant py-4 text-center font-mono">
      No sizes added. Customers will not see size options for this item.
    </p>
  </div>
</section>

<!-- Section 3.6: Crusts -->
<section class="industrial-border p-8 bg-surface-container-lowest mt-12"
  x-data="{
    crusts: {{ json_encode(
      old('crusts', $crusts->map(fn($c) => [
        'name'             => $c->name,
        'price_adjustment' => (float) $c->price_adjustment,
        'is_default'       => $c->is_default,
        'is_available'     => $c->is_available,
      ])->toArray()) ?? []
    ) }},
    newName: '',
    newPrice: '0.00',
    add() {
      let n = this.newName.trim();
      if (!n) return;
      this.crusts.push({ name: n, price_adjustment: parseFloat(this.newPrice) || 0, is_default: this.crusts.length === 0, is_available: true });
      this.newName = ''; this.newPrice = '0.00';
    },
    remove(i) { this.crusts.splice(i, 1); },
    setDefault(i) { this.crusts.forEach((c, j) => c.is_default = (j === i)); }
  }">
  <h3 class="font-headline-md text-headline-md mb-2 flex items-center gap-3">
    <span class="material-symbols-outlined">circle</span> Crusts
  </h3>
  <p class="font-mono text-[10px] text-on-surface-variant uppercase mb-6">Per-item crust options. Set price surcharge and mark one as default.</p>
  <div class="flex gap-3 mb-4 flex-wrap">
    <input x-model="newName"
           @keydown.enter.prevent="add()"
           class="flex-1 min-w-[140px] bg-transparent border-t-0 border-x-0 border-b-2 border-industrial-gray py-2 font-body-md text-body-md focus:ring-0 px-0"
           placeholder="e.g. 48hr Sourdough" type="text"/>
    <div class="flex items-center gap-2">
      <span class="font-mono text-xs text-on-surface-variant">+£</span>
      <input x-model="newPrice" step="0.01" min="0"
             class="w-24 bg-transparent border-t-0 border-x-0 border-b-2 border-industrial-gray py-2 font-body-md text-body-md focus:ring-0 px-0"
             placeholder="0.00" type="number"/>
    </div>
    <button @click="add()" type="button"
            class="px-4 py-2 industrial-border font-mono text-[10px] font-bold uppercase hover:bg-primary hover:text-on-primary transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-sm">add</span> Add Crust
    </button>
  </div>
  <div class="space-y-2">
    <template x-for="(crust, i) in crusts" :key="i">
      <div class="flex items-center justify-between p-3 border border-surface-variant bg-surface group gap-4">
        <input type="hidden" :name="'crusts['+i+'][name]'" :value="crust.name" />
        <input type="hidden" :name="'crusts['+i+'][price_adjustment]'" :value="crust.price_adjustment" />
        <input type="hidden" :name="'crusts['+i+'][is_default]'" :value="crust.is_default ? 1 : 0" />
        <input type="hidden" :name="'crusts['+i+'][is_available]'" :value="crust.is_available ? 1 : 0" />
        <div class="flex items-center gap-3 flex-1 min-w-0">
          <span class="material-symbols-outlined text-on-surface-variant text-sm flex-shrink-0">circle</span>
          <span class="font-body-md text-body-md truncate" x-text="crust.name"></span>
          <span class="font-mono text-xs text-on-surface-variant flex-shrink-0" x-text="crust.price_adjustment > 0 ? '+£' + crust.price_adjustment.toFixed(2) : 'base'"></span>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
          <button @click="setDefault(i)" type="button"
                  :class="crust.is_default ? 'text-heritage-gold' : 'text-on-surface-variant hover:text-heritage-gold'"
                  class="transition-colors text-xs font-mono uppercase flex items-center gap-1">
            <span class="material-symbols-outlined text-sm" x-text="crust.is_default ? 'star' : 'star_outline'"></span>
            <span x-text="crust.is_default ? 'Default' : 'Set default'"></span>
          </button>
          <button @click="crust.is_available = !crust.is_available" type="button"
                  :class="crust.is_available ? 'text-green-600' : 'text-industrial-gray'"
                  class="transition-colors font-mono text-xs uppercase flex items-center gap-1">
            <span class="material-symbols-outlined text-sm" x-text="crust.is_available ? 'visibility' : 'visibility_off'"></span>
          </button>
          <button @click="remove(i)" type="button"
                  class="text-on-surface-variant hover:text-brand-error transition-colors opacity-0 group-hover:opacity-100">
            <span class="material-symbols-outlined text-sm">close</span>
          </button>
        </div>
      </div>
    </template>
    <p x-show="crusts.length === 0" class="text-xs text-on-surface-variant py-4 text-center font-mono">
      No crusts added. Customers will not see crust options for this item.
    </p>
  </div>
</section>

<!-- Section 3.5: Upselling & Sides -->
<section class="industrial-border p-8 bg-surface-container-lowest mt-12">
<div class="flex justify-between items-center mb-6">
<h3 class="font-headline-md text-headline-md flex items-center gap-3">
<span class="material-symbols-outlined">add_shopping_cart</span> Upselling &amp; Sides
</h3>
</div>
<div x-data="{
  related: {{ json_encode(old('related_items', $item?->relatedItems?->pluck('id')->toArray() ?? [])) }},
  allItems: {{ json_encode($allItems->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'price' => $i->base_price])->toArray()) }},
  selectedId: '',
  add() {
    if (this.selectedId && !this.related.includes(parseInt(this.selectedId))) {
      this.related.push(parseInt(this.selectedId));
      this.selectedId = '';
    }
  },
  remove(i) { this.related.splice(i, 1); },
  getItem(id) { return this.allItems.find(i => i.id === id) || {}; }
}">
  <div class="flex gap-3 mb-4">
    <select x-model="selectedId" class="flex-1 bg-transparent border-t-0 border-x-0 border-b-2 border-industrial-gray py-2 font-body-md text-body-md focus:ring-0 px-0">
      <option value="">-- Select an item to recommend --</option>
      <template x-for="opt in allItems" :key="opt.id">
        <option :value="opt.id" x-text="opt.name + ' (+£' + opt.price + ')'" :disabled="related.includes(opt.id)"></option>
      </template>
    </select>
    <button @click="add()" type="button" class="px-4 py-2 industrial-border font-mono text-[10px] font-bold uppercase hover:bg-primary hover:text-on-primary transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-sm">add</span> Add
    </button>
  </div>
  <div class="space-y-2">
    <template x-for="(relId, i) in related" :key="relId">
      <div class="flex items-center justify-between p-3 border border-surface-variant bg-surface group">
        <input type="hidden" :name="'related_items['+i+']'" :value="relId" />
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-surface-variant flex items-center justify-center rounded">
            <span class="material-symbols-outlined text-on-surface-variant text-sm">fastfood</span>
          </div>
          <div>
            <p class="font-label-bold text-label-bold" x-text="getItem(relId).name"></p>
            <p class="text-xs text-on-surface-variant" x-text="'+£' + getItem(relId).price"></p>
          </div>
        </div>
        <button @click="remove(i)" type="button" class="text-on-surface-variant hover:text-brand-error transition-colors opacity-0 group-hover:opacity-100 p-2">
          <span class="material-symbols-outlined text-sm">delete</span>
        </button>
      </div>
    </template>
    <p x-show="related.length === 0" class="text-xs text-on-surface-variant py-4 text-center font-mono">
      No upselling items added.
    </p>
  </div>
</div>
</section>
</div>
<!-- Right Column: Media & Actions -->
<div class="lg:col-span-5 space-y-12">
<!-- Section 4: Image Upload -->
<section class="industrial-border p-8 bg-surface-container-lowest"
  x-data="imageCropper(
    {{ $item?->hasStoredImage() ? json_encode(asset('storage/' . $item->image_path)) : 'null' }},
    {{ $item?->hasStoredImage() ? json_encode(basename($item->image_path)) : 'null' }}
  )">
<h3 class="font-label-caps text-label-caps text-on-surface-variant mb-6 uppercase tracking-widest">Item Photography</h3>
{{-- Selected/current image preview --}}
<div class="mb-4 flex items-center gap-4" x-show="previewUrl" x-cloak>
  <img :src="previewUrl" alt="Preview" class="w-20 h-20 object-cover industrial-border">
  <div>
    <p class="font-mono text-[10px] uppercase text-on-surface-variant">Current Image</p>
    <p class="font-mono text-[10px] text-on-surface truncate max-w-[200px]" x-text="fileName"></p>
  </div>
</div>
<div class="relative group cursor-pointer border-2 border-dashed border-industrial-gray h-80 flex flex-col items-center justify-center bg-surface overflow-hidden">
<img x-show="previewUrl" x-cloak :src="previewUrl" class="absolute inset-0 w-full h-full object-cover pointer-events-none" alt="Selected photo"/>
<div class="relative z-10 flex flex-col items-center text-on-surface text-center px-6 pointer-events-none" x-show="!previewUrl">
<span class="material-symbols-outlined text-4xl mb-4">cloud_upload</span>
<p class="font-label-bold text-label-bold mb-1">Drag and drop or click</p>
<p class="text-xs text-on-surface-variant">High-resolution JPEG or PNG. Max 2MB.</p>
</div>
<input type="file" name="image" x-ref="fileInput" @change="onFileChange"
       accept="image/jpeg,image/png,image/jpg,image/webp"
       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
</div>
<input type="hidden" name="remove_image" x-ref="removeFlag" value="0">
<div class="mt-4 flex gap-4">
<button class="flex-1 py-2 industrial-border text-label-caps font-label-caps hover:bg-industrial-gray hover:text-white transition-colors disabled:opacity-40 disabled:cursor-not-allowed" type="button" :disabled="!previewUrl" @click="openCropper()">Edit Crop</button>
<button class="flex-1 py-2 industrial-border text-label-caps font-label-caps hover:bg-error hover:text-white transition-colors disabled:opacity-40 disabled:cursor-not-allowed" type="button" :disabled="!previewUrl" @click="removeImage()">Remove</button>
</div>

{{-- Crop modal --}}
<div x-show="cropping" x-cloak class="fixed inset-0 z-[100] bg-black/70 flex items-center justify-center p-6">
  <div class="bg-surface-container-lowest industrial-border p-6 w-full max-w-lg">
    <div class="h-96 bg-surface">
      <img x-ref="cropImg" :src="previewUrl" class="max-w-full block" alt="Crop preview">
    </div>
    <div class="mt-4 flex gap-4">
      <button type="button" class="flex-1 py-2 industrial-border text-label-caps font-label-caps hover:bg-industrial-gray hover:text-white transition-colors" @click="closeCropper()">Cancel</button>
      <button type="button" class="flex-1 py-2 industrial-border gold-metallic text-label-caps font-label-caps" @click="applyCrop()">Apply Crop</button>
    </div>
  </div>
</div>
</section>
<!-- Section 5: Status & Controls -->
<section class="industrial-border p-8 bg-surface-container-lowest">
<h3 class="font-label-caps text-label-caps text-on-surface-variant mb-6 uppercase tracking-widest">Publishing Status</h3>
<div class="space-y-6">
<div class="flex items-center justify-between">
<span class="font-body-md">Visible on Menu</span>
<label class="relative inline-flex items-center cursor-pointer">
<input type="hidden" name="is_available" value="0">
<input name="is_available" type="checkbox" value="1" {{ old('is_available', $item?->is_available ?? true) ? 'checked' : '' }} class="sr-only peer"/>
<div class="w-11 h-6 bg-industrial-gray peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-oxblood-red"></div>
</label>
</div>
<div class="flex items-center justify-between">
<span class="font-body-md">Featured Item</span>
<label class="relative inline-flex items-center cursor-pointer">
<input type="hidden" name="is_featured" value="0">
<input name="is_featured" type="checkbox" value="1" {{ old('is_featured', $item?->is_featured) ? 'checked' : '' }} class="sr-only peer"/>
<div class="w-11 h-6 bg-industrial-gray peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-heritage-gold"></div>
</label>
</div>
<div class="flex items-center justify-between">
<span class="font-body-md">Vegetarian</span>
<label class="relative inline-flex items-center cursor-pointer">
<input type="hidden" name="is_vegetarian" value="0">
<input name="is_vegetarian" type="checkbox" value="1" {{ old('is_vegetarian', $item?->is_vegetarian) ? 'checked' : '' }} class="sr-only peer"/>
<div class="w-11 h-6 bg-industrial-gray peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
</label>
</div>
<div class="flex items-center justify-between">
<span class="font-body-md">Vegan</span>
<label class="relative inline-flex items-center cursor-pointer">
<input type="hidden" name="is_vegan" value="0">
<input name="is_vegan" type="checkbox" value="1" {{ old('is_vegan', $item?->is_vegan) ? 'checked' : '' }} class="sr-only peer"/>
<div class="w-11 h-6 bg-industrial-gray peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-700"></div>
</label>
</div>
<div class="flex items-center justify-between">
<span class="font-body-md">Customizable</span>
<label class="relative inline-flex items-center cursor-pointer">
<input type="hidden" name="is_customizable" value="0">
<input name="is_customizable" type="checkbox" value="1" {{ old('is_customizable', $item?->is_customizable ?? true) ? 'checked' : '' }} class="sr-only peer"/>
<div class="w-11 h-6 bg-industrial-gray peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-oxblood-red"></div>
</label>
</div>
<p class="text-[10px] font-mono text-on-surface-variant -mt-2">Turn off for items that can't be customised (e.g. drinks, desserts).</p>
<div class="double-divider"></div>
<div class="space-y-4">
<button class="gold-metallic w-full py-4 text-on-primary font-headline-md text-headline-md industrial-border-thick active:scale-95 transition-transform" type="submit">
                        SAVE CHANGES
                    </button>
<button class="w-full py-3 industrial-border text-oxblood-red font-label-caps text-label-caps hover:bg-industrial-gray hover:text-white transition-colors" type="button">
                        DISCARD CHANGES
                    </button>
<button class="w-full text-error font-label-caps text-[10px] uppercase tracking-[0.2em] pt-4 hover:underline" type="button">
                        PERMANENTLY DELETE ITEM
                    </button>
</div>
</div>
</section>
<!-- Help Box -->
<div class="p-6 bg-tertiary-container text-on-tertiary-container industrial-border">
<p class="font-label-bold text-label-bold mb-2 flex items-center gap-2">
<span class="material-symbols-outlined text-sm">info</span> Admin Tip
</p>
<p class="text-xs leading-relaxed opacity-80">
    Items with allergen tags are automatically highlighted in the customer interface. Ensure descriptions follow the 'Industrial Metaphor' style guide (e.g., use words like 'Forced', 'Fired', 'Workshop-fresh').
</p>
</div>
</div>
</form>

@endsection
