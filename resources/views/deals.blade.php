@extends('layouts.app')
@push('head')
<style>
.deals-masthead { border-bottom: 4px double #690008; padding-bottom: 2rem; margin-bottom: 3rem; }
.deals-masthead-rule { width:100%; height:1px; background:linear-gradient(90deg,transparent,#690008 20%,#690008 80%,transparent); margin:0.75rem 0; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush
@section('content')

{{-- Masthead --}}
<div class="max-w-container-max mx-auto px-6 lg:px-16 pt-20">
    <div class="deals-masthead text-center">
        <p class="font-mono text-[0.65rem] tracking-[0.3em] uppercase text-on-surface-variant mb-4">
            Save More &middot; Order More &middot; Enjoy More
        </p>
        <div class="deals-masthead-rule"></div>
        <h1 class="font-serif font-black uppercase tracking-[0.18em] text-4xl lg:text-5xl text-on-surface my-4">
            Deals &amp; Offers
        </h1>
        <div class="deals-masthead-rule"></div>
        <p class="font-mono text-[0.65rem] tracking-[0.3em] uppercase text-on-surface-variant mt-4">
            Build your perfect meal &nbsp;&middot;&nbsp; Save every time
        </p>
    </div>
</div>

@if($deals->isNotEmpty())

{{-- Deal type filter + cards --}}
<div x-data="{ active: 'all' }">

    {{-- Sticky filter bar --}}
    <div class="sticky top-16 z-40 bg-surface/98 backdrop-blur-md border-b-2 border-[#690008]/30 shadow-sm">
        <div class="max-w-container-max mx-auto px-6 md:px-16">
            <div class="flex overflow-x-auto scrollbar-hide -mb-px">
                <button @click="active = 'all'"
                        :class="active === 'all' ? 'border-b-2 border-[#690008] text-[#690008] font-bold' : 'border-b-2 border-transparent text-on-surface-variant hover:text-on-surface'"
                        class="px-4 py-3.5 font-mono text-[11px] uppercase tracking-widest whitespace-nowrap transition-colors flex-shrink-0">
                    All Deals
                </button>
                @foreach($deals->pluck('deal_type')->unique() as $type)
                <button @click="active = '{{ $type }}'"
                        :class="active === '{{ $type }}' ? 'border-b-2 border-[#690008] text-[#690008] font-bold' : 'border-b-2 border-transparent text-on-surface-variant hover:text-on-surface'"
                        class="px-4 py-3.5 font-mono text-[11px] uppercase tracking-widest whitespace-nowrap transition-colors flex-shrink-0">
                    @php echo match($type) { 'bundle' => 'Bundles', 'bogo' => 'Buy 1 Get 1', 'percentage_off' => '% Off', 'fixed_off' => '£ Off', default => $type } @endphp
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Deal cards grid --}}
    <div class="max-w-container-max mx-auto px-6 md:px-16 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($deals as $deal)
            @php $payload = $deal->toFrontendPayload(); @endphp
            <div x-show="active === 'all' || active === '{{ $deal->deal_type }}'"
                 class="group bg-surface border border-surface-variant hover:border-[#690008]/40 transition-all duration-300 overflow-hidden shadow-sm flex flex-col">

                {{-- Image / placeholder --}}
                @if($deal->image_path)
                <div class="h-52 overflow-hidden flex-shrink-0">
                    <img src="{{ asset('storage/' . $deal->image_path) }}" alt="{{ $deal->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                @else
                <div class="h-52 bg-gradient-to-br from-[#690008]/10 to-[#690008]/5 flex flex-col items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[56px] text-[#690008]/30 mb-2">local_offer</span>
                    <span class="font-mono text-[10px] uppercase tracking-widest text-[#690008]/40">{{ $deal->type_label }}</span>
                </div>
                @endif

                <div class="p-6 flex flex-col flex-1">
                    {{-- Badge + title --}}
                    <div class="flex justify-between items-start gap-3 mb-3">
                        <h3 class="font-serif text-xl font-bold text-on-surface leading-tight">{{ $deal->name }}</h3>
                        <span class="font-mono text-[10px] font-bold uppercase tracking-wide px-2 py-1 flex-shrink-0
                            @if($deal->deal_type === 'bundle') bg-[#690008] text-white
                            @elseif($deal->deal_type === 'bogo') bg-green-700 text-white
                            @elseif($deal->deal_type === 'percentage_off') bg-secondary-container text-on-secondary-container
                            @else bg-surface-container-high text-on-surface
                            @endif">
                            {{ $deal->type_label }}
                        </span>
                    </div>

                    @if($deal->description)
                    <p class="font-sans text-sm text-on-surface-variant mb-4 flex-1">{{ $deal->description }}</p>
                    @else
                    <div class="flex-1"></div>
                    @endif

                    {{-- Slot pills (bundle/bogo) --}}
                    @if($deal->hasSlots() && $deal->slots->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5 mb-5">
                        @foreach($deal->slots as $slot)
                        <span class="font-mono text-[10px] uppercase tracking-wide px-2 py-1 bg-surface-container border border-surface-variant text-on-surface-variant flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">check_box_outline_blank</span>
                            {{ $slot->min_qty === $slot->max_qty ? $slot->max_qty.'×' : $slot->min_qty.'–'.$slot->max_qty.'×' }}
                            {{ $slot->label }}
                            @if($slot->is_free)<span class="text-green-700 font-bold">FREE</span>@endif
                        </span>
                        @endforeach
                    </div>
                    @endif

                    {{-- Schedule badge --}}
                    @if($deal->ends_at)
                    <p class="font-mono text-[10px] text-on-surface-variant mb-4">
                        <span class="material-symbols-outlined text-[13px] align-middle">schedule</span>
                        Ends {{ $deal->ends_at->format('d M Y') }}
                    </p>
                    @endif

                    {{-- CTA --}}
                    <button @click="$store.dealCart.openDeal({{ json_encode($payload) }})"
                            class="btn-primary w-full flex items-center justify-center gap-2 py-3 font-mono text-xs uppercase font-bold mt-auto">
                        <span class="material-symbols-outlined text-[18px]">
                            {{ in_array($deal->deal_type, ['bundle', 'bogo']) ? 'shopping_bag' : 'add_shopping_cart' }}
                        </span>
                        {{ in_array($deal->deal_type, ['bundle', 'bogo']) ? 'Build This Deal' : 'Add to Order' }}
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@else

{{-- Empty state --}}
<div class="max-w-container-max mx-auto px-6 lg:px-16 py-24 text-center">
    <span class="material-symbols-outlined text-[72px] text-on-surface-variant/30 block mb-6">local_offer</span>
    <h2 class="font-serif text-2xl font-bold text-on-surface mb-3">No Active Deals Right Now</h2>
    <p class="font-sans text-sm text-on-surface-variant mb-8 max-w-md mx-auto">
        We're crafting something special. Check back soon for exclusive bundles and offers.
    </p>
    <a href="{{ route('menu') }}" class="btn-primary inline-flex items-center gap-2 px-8 py-3 font-mono text-xs uppercase font-bold">
        Browse Menu
        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
    </a>
</div>

@endif

@endsection
