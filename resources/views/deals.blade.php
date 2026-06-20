@extends('layouts.app')
@section('content')

<!-- Hero Banner -->
<section class="relative bg-primary border-b-2 border-outline overflow-hidden">
  <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-16 md:py-24 text-center relative z-10">
    <span class="font-label-bold text-label-bold text-on-primary-container uppercase tracking-widest block mb-4">Limited Time</span>
    <h1 class="font-display text-display md:text-[64px] text-on-primary leading-[1.1] uppercase tracking-tighter mb-6">Deals &amp; Offers</h1>
    <p class="font-body-lg text-body-lg text-on-primary-container max-w-xl mx-auto">
      Use these codes at checkout to save on your next order. Forged for value, built to last.
    </p>
  </div>
  <!-- Decorative cross-hatch overlay -->
  <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: repeating-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%), repeating-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%); background-position: 0 0, 10px 10px; background-size: 20px 20px;"></div>
</section>

<div class="w-full h-2 border-t border-b border-outline my-0"></div>

<!-- Active Deals Grid -->
<section class="bg-surface py-16 md:py-20">
  <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">

    @if($deals->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
      @foreach($deals as $deal)
      <div class="border-2 border-outline bg-surface group hover:border-primary transition-colors duration-300 flex flex-col">
        <!-- Deal type badge -->
        <div class="px-6 pt-6 pb-4 border-b-2 border-outline bg-surface-container-low">
          <div class="flex items-center justify-between mb-3">
            <span class="font-mono text-[10px] font-bold uppercase tracking-widest px-3 py-1 border border-outline
              @if($deal->type === 'percentage') bg-secondary-container text-on-secondary-container
              @elseif($deal->type === 'fixed_amount') bg-primary-fixed text-on-primary-fixed
              @elseif($deal->type === 'buy_one_get_one') bg-primary text-on-primary
              @elseif($deal->type === 'multi_buy') bg-tertiary text-on-tertiary
              @else bg-green-100 text-green-800
              @endif">
              @if($deal->type === 'percentage') {{ (float)$deal->value }}% OFF
              @elseif($deal->type === 'fixed_amount') £{{ number_format($deal->value, 2) }} OFF
              @elseif($deal->type === 'buy_one_get_one') BOGOF
              @elseif($deal->type === 'multi_buy') 3 FOR 2
              @else FREE DELIVERY
              @endif
            </span>
            @if($deal->expires_at)
            <span class="font-mono text-[10px] text-on-surface-variant">
              Ends {{ $deal->expires_at->format('d M') }}
            </span>
            @endif
          </div>
          <h3 class="font-headline-md text-headline-md text-on-surface group-hover:text-primary transition-colors">{{ $deal->name }}</h3>
        </div>

        <!-- Deal details -->
        <div class="px-6 py-5 flex-1 flex flex-col">
          <div class="space-y-3 flex-1">
            @if($deal->min_order_amount)
            <div class="flex items-center gap-2 text-on-surface-variant">
              <span class="material-symbols-outlined text-sm">shopping_cart</span>
              <span class="font-body-md text-body-md">Min. order: <strong class="text-on-surface">£{{ number_format($deal->min_order_amount, 2) }}</strong></span>
            </div>
            @endif
            @if($deal->max_uses)
            @php $remaining = max(0, $deal->max_uses - $deal->current_uses); @endphp
            <div class="flex items-center gap-2 text-on-surface-variant">
              <span class="material-symbols-outlined text-sm">confirmation_number</span>
              <span class="font-body-md text-body-md">{{ $remaining }} use{{ $remaining !== 1 ? 's' : '' }} remaining</span>
            </div>
            @endif
          </div>

          <!-- Promo code display -->
          <div class="mt-6 pt-4 border-t-2 border-dashed border-outline-variant">
            <p class="font-mono text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Your Code</p>
            <div class="flex items-center gap-3"
                 x-data="{ copied: false }">
              <code class="flex-1 bg-surface-container-high px-4 py-3 font-mono text-sm font-bold text-primary tracking-widest border border-outline text-center select-all">{{ $deal->code }}</code>
              <button @click="navigator.clipboard.writeText('{{ $deal->code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                      class="px-4 py-3 border-2 border-outline font-mono text-[10px] font-bold uppercase hover:bg-primary hover:text-on-primary hover:border-primary transition-colors whitespace-nowrap"
                      :class="copied ? 'bg-green-700 text-white border-green-700' : ''">
                <span x-show="!copied">COPY</span>
                <span x-show="copied" x-cloak>COPIED!</span>
              </button>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <!-- CTA -->
    <div class="text-center mt-12 pt-8 border-t-2 border-outline">
      <p class="font-body-lg text-body-lg text-on-surface-variant mb-6">Ready to use your deal?</p>
      <a href="{{ route('menu') }}" class="inline-flex items-center gap-2 bg-primary text-on-primary font-label-bold text-label-bold px-10 py-4 uppercase tracking-widest hover:brightness-110 transition-all border-b-4 border-primary-fixed-dim active:translate-y-1 active:border-b-0">
        Order Now <span class="material-symbols-outlined text-base">arrow_forward</span>
      </a>
    </div>

    @else
    <!-- Empty state -->
    <div class="text-center py-20 max-w-lg mx-auto">
      <span class="material-symbols-outlined text-[64px] text-outline-variant mb-6">local_offer</span>
      <h2 class="font-headline-md text-headline-md text-on-surface mb-4">No Active Deals Right Now</h2>
      <p class="font-body-lg text-body-lg text-on-surface-variant mb-8">
        We're cooking up something special. Check back soon for exclusive offers and discounts.
      </p>
      <a href="{{ route('menu') }}" class="inline-flex items-center gap-2 px-8 py-3 border-2 border-on-surface font-label-bold text-label-bold uppercase tracking-widest hover:bg-on-surface hover:text-surface transition-colors">
        Browse Menu <span class="material-symbols-outlined text-base">arrow_forward</span>
      </a>
    </div>
    @endif

  </div>
</section>

@endsection
