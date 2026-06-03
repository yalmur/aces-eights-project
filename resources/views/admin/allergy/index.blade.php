@extends('layouts.admin')
@section('content')

{{-- Header --}}
<section class="mb-8">
  <h2 class="font-serif text-4xl font-black text-on-surface uppercase tracking-tight">ALLERGY MANAGEMENT</h2>
  <div class="double-divider text-on-surface-variant mt-2"></div>
</section>

{{-- Section 1: Global Allergy Alerts --}}
<section class="mb-10 bg-surface-container-low border-2 border-on-surface p-6" x-data="{ alerts: true }">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1">campaign</span>
      <h3 class="font-mono text-xs font-bold uppercase">Global Safety Alerts</h3>
    </div>
    <label class="relative inline-flex items-center cursor-pointer">
      <input x-model="alerts" class="sr-only peer" type="checkbox" checked/>
      <div class="w-11 h-6 bg-surface-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
    </label>
  </div>
  <p class="font-sans text-sm text-on-surface-variant leading-relaxed">
    When enabled, a high-visibility warning banner will appear across the top of the consumer site regarding ingredient cross-contamination and the current allergy protocol.
  </p>
</section>

{{-- Section 2: Allergen Library --}}
<section class="mb-10">
  <div class="flex items-center justify-between mb-4">
    <h3 class="font-mono text-xs font-bold uppercase">Allergen Library</h3>
    <button class="text-primary font-mono text-[10px] font-bold flex items-center gap-1 border-b border-primary uppercase">
      <span class="material-symbols-outlined text-[18px]">add</span> ADD NEW
    </button>
  </div>
  <div class="grid grid-cols-1 gap-3">
    @foreach([
      ['icon'=>'egg','label'=>'Dairy & Eggs','active'=>true],
      ['icon'=>'bakery_dining','label'=>'Gluten / Wheat','active'=>true],
      ['icon'=>'set_meal','label'=>'Shellfish','active'=>false],
      ['icon'=>'nutrition','label'=>'Tree Nuts','active'=>true],
      ['icon'=>'grass','label'=>'Soy','active'=>true],
    ] as $allergen)
    <div class="bg-surface border border-on-surface flex items-center p-3 justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-primary-container flex items-center justify-center text-on-primary">
          <span class="material-symbols-outlined">{{ $allergen['icon'] }}</span>
        </div>
        <span class="font-mono text-xs font-bold uppercase">{{ $allergen['label'] }}</span>
      </div>
      <div class="flex items-center gap-4">
        <span class="material-symbols-outlined {{ $allergen['active'] ? 'text-on-surface-variant' : 'text-primary' }}" style="font-variation-settings:'FILL' 1">
          {{ $allergen['active'] ? 'visibility' : 'visibility_off' }}
        </span>
        <span class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:text-primary transition-colors">edit</span>
      </div>
    </div>
    @endforeach
  </div>
</section>

{{-- Section 3: Menu Item Mapping --}}
<section class="mb-10">
  <h3 class="font-mono text-xs font-bold uppercase mb-4">Menu Item Allergen Mapping</h3>
  <div class="relative mb-6">
    <input class="w-full bg-surface border-0 border-b-2 border-on-surface py-3 pl-10 focus:ring-0 focus:border-primary placeholder:text-on-surface-variant/50 font-sans text-sm"
           placeholder="Search menu items (e.g. Margherita, Garlic Bread)" type="text"/>
    <span class="material-symbols-outlined absolute left-2 top-3 text-on-surface-variant">search</span>
  </div>
  <div class="border-2 border-on-surface overflow-hidden">
    <div class="bg-on-surface text-surface p-4 flex justify-between items-center">
      <h4 class="font-mono text-xs font-bold uppercase">Classic Margherita</h4>
      <span class="font-mono text-[10px]">SKU: AE-001</span>
    </div>
    <div class="p-4 bg-surface-container">
      <p class="font-mono text-[10px] uppercase mb-3 text-on-surface-variant">Active Allergen Tags:</p>
      <div class="flex flex-wrap gap-2 mb-6" x-data="{ tags: ['Gluten','Dairy'] }">
        <template x-for="tag in tags" :key="tag">
          <span class="bg-primary text-white text-[10px] font-bold px-3 py-1 rounded-full flex items-center gap-1 uppercase">
            <span x-text="tag"></span>
            <span @click="tags.splice(tags.indexOf(tag),1)" class="material-symbols-outlined text-[12px] cursor-pointer">close</span>
          </span>
        </template>
        <button class="bg-on-surface text-surface text-[10px] font-bold px-3 py-1 rounded-full flex items-center gap-1 uppercase">
          <span class="material-symbols-outlined text-[12px]">add</span> ADD TAG
        </button>
      </div>
      <button class="w-full border-2 border-on-surface py-3 text-on-surface font-mono text-xs font-bold uppercase hover:bg-surface-container-highest transition-all active:scale-[0.98]">
        SAVE ITEM MAPPING
      </button>
    </div>
  </div>
</section>

{{-- Section 4: Checkout Disclaimer CMS --}}
<section class="mb-8">
  <h3 class="font-mono text-xs font-bold uppercase mb-4">Checkout Disclaimer CMS</h3>
  <div class="border-2 border-on-surface bg-white">
    <div class="border-b border-on-surface flex gap-2 p-2 bg-surface-container">
      <button class="p-1 hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined">format_bold</span></button>
      <button class="p-1 hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined">format_italic</span></button>
      <button class="p-1 hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined">link</span></button>
      <div class="w-[1px] h-6 bg-on-surface-variant/30 self-center"></div>
      <button class="p-1 hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined">history</span></button>
    </div>
    <textarea class="w-full border-0 p-4 font-sans text-sm leading-relaxed focus:ring-0 resize-none" rows="6">ACES & EIGHTS PIZZA CO. TAKES FOOD SAFETY SERIOUSLY. Please be advised that our kitchen handles wheat, dairy, and eggs. While we take meticulous steps to prevent cross-contact, we cannot guarantee a 100% allergen-free environment for those with severe sensitivities. By proceeding with your order, you acknowledge these risks. Contact our floor manager for specific ingredient concerns.</textarea>
  </div>
  <p class="font-mono text-[10px] text-on-surface-variant mt-2 italic">* This text is legally required at the point of purchase in all digital storefronts.</p>
</section>

<button class="w-full bg-primary text-on-primary py-5 font-mono text-xs font-bold uppercase tracking-widest shadow-lg active:scale-95 transition-transform">
  PUBLISH CHANGES
</button>

@endsection
