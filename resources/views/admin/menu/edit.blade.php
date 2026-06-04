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
<!-- Section 3: Upselling & Customization -->
<section class="industrial-border p-8 bg-surface-container-lowest">
<div class="flex justify-between items-center mb-6">
<h3 class="font-headline-md text-headline-md flex items-center gap-3">
<span class="material-symbols-outlined">add_shopping_cart</span> Upselling &amp; Sides
</h3>
<button class="text-oxblood-red font-label-caps text-label-caps flex items-center gap-1 hover:underline" type="button">
<span class="material-symbols-outlined text-sm">add</span> Add Related
</button>
</div>
<div class="space-y-4">
<div class="flex items-center justify-between p-4 border border-surface-variant bg-surface">
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-surface-variant flex items-center justify-center">
<span class="material-symbols-outlined text-on-surface-variant">lunch_dining</span>
</div>
<div>
<p class="font-label-bold text-label-bold">Truffle Garlic Knots</p>
<p class="text-xs text-on-surface-variant">Recommended Side</p>
</div>
</div>
<div class="flex items-center gap-4">
<span class="font-label-bold">+$6.00</span>
<span class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:text-error">delete</span>
</div>
</div>
<div class="flex items-center justify-between p-4 border border-surface-variant bg-surface">
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-surface-variant flex items-center justify-center">
<span class="material-symbols-outlined text-on-surface-variant">local_drink</span>
</div>
<div>
<p class="font-label-bold text-label-bold">Aces Reserve Peroni</p>
<p class="text-xs text-on-surface-variant">Drink Pairing</p>
</div>
</div>
<div class="flex items-center gap-4">
<span class="font-label-bold">+$8.50</span>
<span class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:text-error">delete</span>
</div>
</div>
</div>
</section>
</div>
<!-- Right Column: Media & Actions -->
<div class="lg:col-span-5 space-y-12">
<!-- Section 4: Image Upload -->
<section class="industrial-border p-8 bg-surface-container-lowest">
<h3 class="font-label-caps text-label-caps text-on-surface-variant mb-6 uppercase tracking-widest">Item Photography</h3>
{{-- Existing image preview --}}
@if($item?->image_path)
<div class="mb-4 flex items-center gap-4">
  <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
       class="w-20 h-20 object-cover industrial-border">
  <div>
    <p class="font-mono text-[10px] uppercase text-on-surface-variant">Current Image</p>
    <p class="font-mono text-[10px] text-on-surface truncate max-w-[200px]">{{ basename($item->image_path) }}</p>
  </div>
</div>
@endif
<div class="relative group cursor-pointer border-2 border-dashed border-industrial-gray h-80 flex flex-col items-center justify-center bg-surface overflow-hidden">
<img class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500" src="https://placehold.co/400x400/e4e2e1/1b1c1c?text=Upload+Photo" alt="Upload Photo"/>
<div class="relative z-10 flex flex-col items-center text-on-surface text-center px-6">
<span class="material-symbols-outlined text-4xl mb-4">cloud_upload</span>
<p class="font-label-bold text-label-bold mb-1">Drag and drop or click</p>
<p class="text-xs text-on-surface-variant">High-resolution JPEG or PNG. Max 2MB.</p>
</div>
<input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
</div>
<div class="mt-4 flex gap-4">
<button class="flex-1 py-2 industrial-border text-label-caps font-label-caps hover:bg-industrial-gray hover:text-white transition-colors" type="button">Edit Crop</button>
<button class="flex-1 py-2 industrial-border text-label-caps font-label-caps hover:bg-error hover:text-white transition-colors" type="button">Remove</button>
</div>
</section>
<!-- Section 5: Status & Controls -->
<section class="industrial-border p-8 bg-surface-container-lowest">
<h3 class="font-label-caps text-label-caps text-on-surface-variant mb-6 uppercase tracking-widest">Publishing Status</h3>
<div class="space-y-6">
<div class="flex items-center justify-between">
<span class="font-body-md">Visible on Menu</span>
<label class="relative inline-flex items-center cursor-pointer">
<input name="is_available" type="checkbox" {{ $item?->is_available ? 'checked' : 'checked' }} class="sr-only peer"/>
<div class="w-11 h-6 bg-industrial-gray peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-oxblood-red"></div>
</label>
</div>
<div class="flex items-center justify-between">
<span class="font-body-md">Featured Item</span>
<label class="relative inline-flex items-center cursor-pointer">
<input name="is_featured" type="checkbox" {{ $item?->is_featured ? 'checked' : '' }} class="sr-only peer"/>
<div class="w-11 h-6 bg-industrial-gray peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-heritage-gold"></div>
</label>
</div>
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
