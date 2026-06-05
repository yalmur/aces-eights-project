@extends('layouts.app')

@push('head')
<style>
/* ─── Menu page: The Broadsheet ─────────────────────────────────────────── */
.menu-page {
    background-color: #faf8f4;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='4'%3E%3Crect width='4' height='4' fill='%23faf8f4'/%3E%3Crect width='1' height='1' fill='%23e8e4dc' opacity='0.4'/%3E%3C/svg%3E");
}
.menu-masthead { border-bottom: 4px double #690008; padding-bottom: 2rem; margin-bottom: 3rem; }
.menu-masthead-rule { width:100%; height:1px; background:linear-gradient(90deg,transparent,#690008 20%,#690008 80%,transparent); margin:0.75rem 0; }
.menu-section { padding: 2.5rem 0; border-bottom: 1px solid #e0bfbc; }
.menu-section:last-of-type { border-bottom: none; }
.menu-section-title { font-family:'Source Serif 4',Georgia,serif; font-size:1.5rem; font-weight:900; letter-spacing:0.12em; text-transform:uppercase; color:#690008; display:flex; align-items:baseline; gap:1rem; margin-bottom:0.25rem; }
.menu-section-subtitle { font-family:'JetBrains Mono',monospace; font-size:0.7rem; letter-spacing:0.2em; text-transform:uppercase; color:#8c716e; margin-bottom:1.5rem; }

/* Item row — 3 columns: info | price | button */
.menu-item {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 0 0.75rem;
    align-items: center;
    padding: 0.55rem 0.25rem;
    border-bottom: 1px dotted #d4c5c3;
    transition: background-color 0.15s;
}
.menu-item:last-child { border-bottom: none; }
.menu-item:hover { background-color: rgba(105,0,8,0.04); }
.menu-item-name { font-family:'Source Serif 4',Georgia,serif; font-size:1rem; font-weight:700; color:#1b1c1c; line-height:1.3; }
.menu-item-desc { font-family:'Hanken Grotesk',sans-serif; font-size:0.8rem; color:#58413f; margin-top:0.12rem; font-style:italic; line-height:1.4; }
.menu-item-price { font-family:'JetBrains Mono',monospace; font-size:0.875rem; font-weight:600; color:#690008; white-space:nowrap; letter-spacing:0.04em; }
.menu-item-btn { width:2rem; height:2rem; background:#690008; color:#fff; border:1px solid #4d0006; box-shadow:1px 1px 0 #1b1c1c; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:background-color 0.15s, transform 0.1s, box-shadow 0.1s; flex-shrink:0; }
.menu-item-btn:hover { background:#8b1a1a; }
.menu-item-btn:active { background:#4d0006; box-shadow:none; transform:translate(1px,1px); }

/* Pizza 2-col */
.pizza-grid { display:grid; grid-template-columns:1fr; gap:0 3rem; }
@media(min-width:768px){ .pizza-grid { grid-template-columns:1fr 1fr; } }

/* Modal overlay */
.modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:9990; display:flex; align-items:center; justify-content:center; padding:1rem; }
.modal-box { background:#faf8f4; border:2px solid #690008; box-shadow:4px 4px 0 #1b1c1c; width:100%; max-width:480px; max-height:90vh; overflow-y:auto; }
.modal-header { background:#690008; color:#fff; padding:1.25rem 1.5rem; display:flex; justify-content:space-between; align-items:center; }
.modal-body { padding:1.5rem; }
.modal-section-label { font-family:'JetBrains Mono',monospace; font-size:0.65rem; letter-spacing:0.2em; text-transform:uppercase; color:#8c716e; margin-bottom:0.5rem; }
.size-btn { padding:0.5rem 1rem; border:2px solid #d4c5c3; font-family:'JetBrains Mono',monospace; font-size:0.75rem; font-weight:600; cursor:pointer; transition:all 0.15s; text-align:center; }
.size-btn.active { border-color:#690008; background:#690008; color:#fff; }
.size-btn:not(.active):hover { border-color:#8c716e; }
.crust-btn { padding:0.4rem 0.75rem; border:1px solid #d4c5c3; font-family:'JetBrains Mono',monospace; font-size:0.7rem; cursor:pointer; transition:all 0.15s; }
.crust-btn.active { border-color:#690008; background:#ffdad6; color:#690008; }
.qty-btn { width:2.25rem; height:2.25rem; border:1px solid #d4c5c3; background:#fff; font-size:1.1rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.12s; }
.qty-btn:hover { background:#f0eded; }
.modal-add-btn { width:100%; padding:1rem; background:#690008; color:#fff; font-family:'JetBrains Mono',monospace; font-size:0.8rem; font-weight:700; letter-spacing:0.15em; text-transform:uppercase; border:none; cursor:pointer; transition:background 0.15s; box-shadow:2px 2px 0 #1b1c1c; margin-top:1.5rem; }
.modal-add-btn:hover { background:#8b1a1a; }
.toast { position:fixed; bottom:2rem; left:50%; transform:translateX(-50%); background:#1b1c1c; color:#fff; padding:0.75rem 1.5rem; font-family:'JetBrains Mono',monospace; font-size:0.75rem; letter-spacing:0.1em; z-index:9999; box-shadow:2px 2px 0 #690008; white-space:nowrap; }
.order-cta { background:#690008; color:#fff; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:1rem; padding:2.5rem; text-align:center; margin-top:4rem; }
@media(min-width:640px){ .order-cta { flex-direction:row; justify-content:space-between; } }
.allergen-note { font-family:'JetBrains Mono',monospace; font-size:0.65rem; letter-spacing:0.08em; color:#8c716e; text-transform:uppercase; border-top:1px solid #e0bfbc; padding-top:1rem; margin-top:1rem; line-height:1.6; }
</style>
@endpush

@section('content')
<div class="menu-page min-h-screen">

<div class="max-w-container-max mx-auto px-6 lg:px-16 py-16">

    {{-- ── Masthead ──────────────────────────────────────────────────────── --}}
    <div class="menu-masthead text-center">
        <p class="font-mono text-[0.65rem] tracking-[0.3em] uppercase text-on-surface-variant mb-4">
            156 &amp; 158 Fortess Road · Tufnell Park · London, NW5 2HP
        </p>
        <div class="menu-masthead-rule"></div>
        <h1 class="font-serif font-black uppercase tracking-[0.18em] text-4xl lg:text-5xl text-on-surface my-4">
            Aces &amp; Eights Pizza
        </h1>
        <div class="menu-masthead-rule"></div>
        <p class="font-mono text-[0.65rem] tracking-[0.3em] uppercase text-on-surface-variant mt-4">
            Est. 2010 &nbsp;·&nbsp; Authentic Italian Takeaway
        </p>
    </div>

    {{-- ── Sections ──────────────────────────────────────────────────────── --}}
    @foreach ($sections as $section)
    <div class="menu-section">
        <div class="menu-section-title">
            {{ $section['heading'] }}
            <span class="flex-1 h-px bg-outline-variant self-center opacity-50"></span>
        </div>
        <p class="menu-section-subtitle">— {{ $section['italian'] }}</p>

        @php $isPizza = $section['slug'] === 'pizza'; @endphp

        <div class="{{ $isPizza ? 'pizza-grid' : '' }}">
            @foreach ($section['items'] as $item)
            <div class="menu-item">
                {{-- Name + description --}}
                <div>
                    <div class="menu-item-name">{{ $item['name'] }}</div>
                    <div class="menu-item-desc">{{ $item['desc'] }}</div>
                </div>
                {{-- Price --}}
                <div class="menu-item-price">£{{ $item['price'] }}</div>
                {{-- Add button --}}
                <button class="menu-item-btn"
                        @click="$store.cart.openDrawer({ id: {{ json_encode($item['id']) }}, name: {{ json_encode($item['name']) }}, category: {{ json_encode($item['category']) }}, basePrice: {{ $item['basePrice'] }}, allergens: {{ json_encode($item['allergens']) }} })"
                        aria-label="Add {{ $item['name'] }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="square" d="M12 4v16M4 12h16"/>
                    </svg>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div class="allergen-note">
        <strong>Allergens:</strong> If you have a food allergy or intolerance, please inform a member of staff before ordering.
        Full allergen information available on request. Some dishes may contain traces of nuts, gluten, dairy, eggs, and shellfish.
        All prices include VAT. Menu subject to change without notice.
    </div>

    <div class="order-cta">
        <div class="text-left">
            <p class="font-mono text-[0.65rem] tracking-[0.25em] uppercase text-white/60 mb-1">Ready to order?</p>
            <p class="font-serif text-xl font-bold text-white">Order for delivery or collection</p>
        </div>
        <a href="{{ route('menu') }}" class="inline-flex items-center gap-2 px-8 py-3 border-2 border-white/80 text-white font-mono text-xs font-bold uppercase tracking-widest hover:bg-white hover:text-primary transition-colors flex-shrink-0">
            Order Now
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="square" d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>


</div>
@endsection
