@extends('layouts.kitchen')
@section('content')

<div class="flex flex-col h-screen w-full overflow-hidden"
     x-data="kitchenDashboard()">

  {{-- ── TOP BAR ─────────────────────────────────────────────────────────── --}}
  <header class="flex-none h-14 bg-zinc-900 border-b border-zinc-800 flex items-center px-4 gap-3 z-10">

    {{-- Brand --}}
    <div class="flex items-center gap-2.5 mr-3">
      <span class="material-symbols-outlined text-red-500 text-2xl flex-none"
            style="font-variation-settings:'FILL' 1">outdoor_grill</span>
      <div class="leading-none">
        <p class="text-white text-sm font-bold leading-none">Kitchen Dashboard</p>
        <p class="text-zinc-500 text-[10px] leading-none mt-0.5">Aces &amp; Eights Pizza</p>
      </div>
    </div>

    {{-- Mode toggle --}}
    <div class="flex-1 flex justify-center">
      <div class="bg-zinc-800 rounded-full p-0.5 flex">
        <button @click="mode = 'kitchen'"
                :class="mode === 'kitchen' ? 'bg-zinc-600 text-white shadow' : 'text-zinc-500 hover:text-zinc-300'"
                class="px-5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all">
          Kitchen
        </button>
        <button @click="mode = 'dispatch'"
                :class="mode === 'dispatch' ? 'bg-zinc-600 text-white shadow' : 'text-zinc-500 hover:text-zinc-300'"
                class="px-5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all">
          Dispatch
        </button>
      </div>
    </div>

    {{-- Controls + clock --}}
    <div class="flex items-center gap-2 ml-3">
      <span class="text-zinc-500 text-xs hidden xl:block">{{ auth()->user()->email }}</span>
      <button @click="muteToggle"
              :class="muted ? 'text-zinc-600 line-through' : 'text-zinc-300 hover:text-white'"
              class="px-2.5 py-1 bg-zinc-800 hover:bg-zinc-700 text-[11px] font-mono uppercase rounded transition-colors hidden md:block">
        <span x-text="muted ? 'Unmute Alerts' : 'Mute Alerts'"></span>
      </button>
      <a href="{{ route('admin.dashboard') }}"
         class="px-2.5 py-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white text-[11px] font-mono uppercase rounded transition-colors hidden sm:block">
        Admin
      </a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="px-2.5 py-1 bg-red-700 hover:bg-red-600 text-white text-[11px] font-bold uppercase rounded transition-colors">
          Sign Out
        </button>
      </form>
      <div class="pl-3 border-l border-zinc-800 text-right">
        <p class="text-white text-sm font-mono font-bold leading-none tracking-widest" x-text="clock"></p>
        <p class="text-zinc-600 text-[10px] font-mono leading-none mt-0.5" x-text="dateStr"></p>
      </div>
    </div>

  </header>

  {{-- ── KANBAN ──────────────────────────────────────────────────────────── --}}
  <main class="flex-1 grid grid-cols-3 divide-x divide-zinc-800/60 overflow-hidden">

    {{-- COLUMN 1: PREPARING (accepted + cooking) --}}
    <div class="flex flex-col overflow-hidden transition-opacity duration-300"
         :class="mode === 'dispatch' ? 'opacity-30' : 'opacity-100'">
      <div class="flex-none px-4 py-3 border-t-2 border-red-500 bg-zinc-900/60 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="text-xs font-bold uppercase tracking-widest text-red-400">Preparing</span>
          <span class="text-[10px] text-zinc-600 font-mono hidden sm:block">
            {{ $preparing->where('status','accepted')->count() }} queued · {{ $preparing->where('status','cooking')->count() }} cooking
          </span>
        </div>
        <span class="min-w-[24px] h-6 px-1.5 rounded-full bg-red-600 text-white text-[11px] font-bold flex items-center justify-center">
          {{ $preparing->count() }}
        </span>
      </div>
      <div class="flex-1 overflow-y-auto kitchen-scroll px-3 py-3 space-y-3">
        @forelse($preparing as $order)
          @include('admin.kitchen._card', ['order' => $order, 'column' => 'preparing'])
        @empty
          <div class="flex flex-col items-center justify-center py-16 gap-2 opacity-40">
            <span class="material-symbols-outlined text-3xl text-zinc-600">check_circle</span>
            <p class="text-zinc-600 text-xs uppercase tracking-widest">Queue clear</p>
          </div>
        @endforelse
      </div>
    </div>

    {{-- COLUMN 2: READY --}}
    <div class="flex flex-col overflow-hidden">
      <div class="flex-none px-4 py-3 border-t-2 border-green-500 bg-zinc-900/60 flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-widest text-green-400">Ready</span>
        <span class="min-w-[24px] h-6 px-1.5 rounded-full bg-green-600 text-white text-[11px] font-bold flex items-center justify-center">
          {{ $ready->count() }}
        </span>
      </div>
      <div class="flex-1 overflow-y-auto kitchen-scroll px-3 py-3 space-y-3">
        @forelse($ready as $order)
          @include('admin.kitchen._card', ['order' => $order, 'column' => 'ready'])
        @empty
          <div class="flex flex-col items-center justify-center py-16 gap-2 opacity-40">
            <span class="material-symbols-outlined text-3xl text-zinc-600">hourglass_empty</span>
            <p class="text-zinc-600 text-xs uppercase tracking-widest">Nothing ready</p>
          </div>
        @endforelse
      </div>
    </div>

    {{-- COLUMN 3: DISPATCHED --}}
    <div class="flex flex-col overflow-hidden transition-opacity duration-300"
         :class="mode === 'kitchen' ? 'opacity-30' : 'opacity-100'">
      <div class="flex-none px-4 py-3 border-t-2 border-zinc-600 bg-zinc-900/60 flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-widest text-zinc-400">Dispatched</span>
        <span class="min-w-[24px] h-6 px-1.5 rounded-full bg-zinc-700 text-zinc-300 text-[11px] font-bold flex items-center justify-center">
          {{ $dispatched->count() }}
        </span>
      </div>
      <div class="flex-1 overflow-y-auto kitchen-scroll px-3 py-3 space-y-3">
        @forelse($dispatched as $order)
          @include('admin.kitchen._card', ['order' => $order, 'column' => 'dispatched'])
        @empty
          <div class="flex flex-col items-center justify-center py-16 gap-2 opacity-40">
            <span class="material-symbols-outlined text-3xl text-zinc-600">moped</span>
            <p class="text-zinc-600 text-xs uppercase tracking-widest">No active dispatches</p>
          </div>
        @endforelse
      </div>
    </div>

  </main>

</div>

{{-- Toast --}}
<div x-data="{ show: false, msg: '' }"
     x-on:kitchen-toast.window="msg = $event.detail; show = true; setTimeout(() => show = false, 4500)"
     x-show="show" x-cloak x-transition
     class="fixed bottom-5 right-5 z-50 bg-zinc-800 border border-zinc-700 text-white text-xs font-mono px-4 py-3 rounded-xl shadow-2xl flex items-center gap-2">
  <span class="material-symbols-outlined text-[14px] text-green-400">notifications</span>
  <span x-text="msg"></span>
</div>

<script>
function kitchenDashboard() {
  return {
    mode: 'kitchen',
    muted: false,
    clock: '',
    dateStr: '',
    _audioCtx: null,

    init() {
      this.tick();
      setInterval(() => this.tick(), 1000);

      // Initialise AudioContext on first user gesture so beep() works immediately after
      document.addEventListener('click', () => {
        if (!this._audioCtx) {
          try { this._audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch {}
        }
      }, { once: true });

      if (window.Echo) {
        window.Echo.private('admin.orders')
          .listen('.OrderStatusUpdated', (data) => {
            this.beep();
            if (!this.muted) {
              window.dispatchEvent(new CustomEvent('kitchen-toast', {
                detail: 'Order #' + data.order_id + ' → ' + data.status_label
              }));
            }
            setTimeout(() => window.location.reload(), 1800);
          });
      }
    },

    beep() {
      if (this.muted) return;
      try {
        const ctx = this._audioCtx || new (window.AudioContext || window.webkitAudioContext)();
        this._audioCtx = ctx;
        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.value = 880;
        gain.gain.setValueAtTime(0.35, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.55);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.55);
      } catch {}
    },

    muteToggle() {
      // Initialise AudioContext here too — this is always a user gesture
      if (!this._audioCtx) {
        try { this._audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch {}
      }
      this.muted = !this.muted;
    },

    tick() {
      const now  = new Date();
      const pad  = n => String(n).padStart(2, '0');
      this.clock   = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
      this.dateStr = now.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
    }
  }
}
</script>

@endsection
