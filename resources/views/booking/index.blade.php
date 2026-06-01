@extends('layouts.app')
@section('content')
<div class="max-w-container-max mx-auto px-4 lg:px-16 py-12" x-data="{
    step: 1,
    guests: 2,
    date: '',
    time: '',
    name: '',
    email: '',
    phone: '',
    notes: '',
    get summary() {
        if (!this.date || !this.time) return '';
        return this.guests + ' guests · ' + this.date + ' at ' + this.time;
    }
}">
  <h1 class="font-display text-headline-lg text-on-surface uppercase tracking-tighter mb-8 border-b-2 border-on-surface pb-4">
    Secure Your Spot
  </h1>

  <!-- Step indicators -->
  <div class="flex gap-0 mb-10 border-2 border-on-surface">
    <div :class="step >= 1 ? 'bg-primary text-on-primary' : 'bg-surface text-on-surface-variant'"
         class="flex-1 py-3 text-center font-label-bold text-label-bold uppercase border-r border-on-surface">
      01. Party
    </div>
    <div :class="step >= 2 ? 'bg-primary text-on-primary' : 'bg-surface text-on-surface-variant'"
         class="flex-1 py-3 text-center font-label-bold text-label-bold uppercase border-r border-on-surface">
      02. When
    </div>
    <div :class="step >= 3 ? 'bg-primary text-on-primary' : 'bg-surface text-on-surface-variant'"
         class="flex-1 py-3 text-center font-label-bold text-label-bold uppercase">
      03. Details
    </div>
  </div>

  <!-- Step 1: Party Size -->
  <div x-show="step === 1" x-transition>
    <p class="font-label-bold text-label-bold text-on-surface-variant uppercase mb-6">How many guests?</p>
    <div class="grid grid-cols-5 gap-3 mb-8 max-w-sm">
      <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="n">
        <button @click="guests = n"
                :class="guests === n ? 'bg-primary text-on-primary border-primary' : 'bg-surface border-on-surface text-on-surface hover:bg-surface-container-low'"
                class="h-12 w-12 border-2 font-label-bold text-label-bold transition-colors"
                x-text="n"></button>
      </template>
      <button @click="guests = 10"
              :class="guests === 10 ? 'bg-primary text-on-primary border-primary' : 'bg-surface border-on-surface text-on-surface hover:bg-surface-container-low'"
              class="col-span-2 px-3 h-12 border-2 font-label-bold text-label-sm uppercase transition-colors">
        10+ Party Hall
      </button>
    </div>
    <p class="font-label-sm text-on-surface-variant mb-6">Party of 10+? Our Party Hall seats up to 50 guests.</p>
    <button @click="step = 2"
            class="btn-primary w-full sm:w-auto px-12 py-3">
      Continue
    </button>
  </div>

  <!-- Step 2: Date & Time -->
  <div x-show="step === 2" x-transition>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
      <div>
        <p class="font-label-bold text-label-bold text-on-surface-variant uppercase mb-4">Select Date</p>
        <input type="date" x-model="date"
               class="input-field w-full max-w-xs"
               :min="new Date().toISOString().split('T')[0]" />
      </div>
      <div>
        <p class="font-label-bold text-label-bold text-on-surface-variant uppercase mb-4">Select Time</p>
        <div class="grid grid-cols-3 gap-2">
          <template x-for="t in ['16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30','21:00','21:30']" :key="t">
            <button @click="time = t"
                    :class="time === t ? 'bg-primary text-on-primary border-primary' : 'bg-surface border-on-surface text-on-surface hover:bg-surface-container-low'"
                    class="py-2 border-2 font-label-sm text-label-sm transition-colors"
                    x-text="t"></button>
          </template>
        </div>
      </div>
    </div>
    <!-- Booking summary preview -->
    <div x-show="date && time" class="bg-surface-container-low border-2 border-on-surface px-6 py-4 mb-6 flex items-center gap-4">
      <span class="material-symbols-outlined text-primary">event_seat</span>
      <p class="font-label-bold text-label-bold uppercase" x-text="summary"></p>
    </div>
    <div class="flex gap-4">
      <button @click="step = 1" class="btn-secondary px-8 py-3">Back</button>
      <button @click="step = 3" :disabled="!date || !time"
              :class="(!date || !time) ? 'opacity-50 cursor-not-allowed' : ''"
              class="btn-primary px-8 py-3">
        Continue
      </button>
    </div>
  </div>

  <!-- Step 3: Personal Details -->
  <div x-show="step === 3" x-transition>
    <form action="{{ route('booking') }}" method="POST" class="space-y-6 max-w-lg">
      @csrf
      <input type="hidden" name="guests" :value="guests">
      <input type="hidden" name="date" :value="date">
      <input type="hidden" name="time" :value="time">
      <div>
        <label class="font-label-bold text-label-bold uppercase text-on-surface-variant mb-2 block" for="name">Full Name</label>
        <input id="name" name="name" type="text" x-model="name"
               placeholder="Jane Smith" required class="input-field">
      </div>
      <div>
        <label class="font-label-bold text-label-bold uppercase text-on-surface-variant mb-2 block" for="phone">Phone</label>
        <input id="phone" name="phone" type="tel" x-model="phone"
               placeholder="+44 7700 900000" class="input-field">
      </div>
      <div>
        <label class="font-label-bold text-label-bold uppercase text-on-surface-variant mb-2 block" for="email">Email</label>
        <input id="email" name="email" type="email" x-model="email"
               placeholder="jane@example.com" required class="input-field">
      </div>
      <div>
        <label class="font-label-bold text-label-bold uppercase text-on-surface-variant mb-2 block" for="notes">Special Requests</label>
        <textarea id="notes" name="notes" x-model="notes"
                  placeholder="Allergies, accessibility needs, occasion..."
                  class="input-field resize-none" rows="3"></textarea>
      </div>
      <p class="font-label-sm text-on-surface-variant text-sm">By confirming you agree to our cancellation policy. Bookings not cancelled 2 hours before will be charged a £5 per head fee.</p>
      <div class="flex gap-4">
        <button type="button" @click="step = 2" class="btn-secondary px-8 py-3">Back</button>
        <button type="submit" class="btn-primary px-8 py-3">Confirm Booking</button>
      </div>
    </form>
  </div>

  <!-- Location reminder -->
  <div class="mt-12 pt-8 border-t-2 border-on-surface flex items-center gap-4">
    <span class="material-symbols-outlined text-primary">location_on</span>
    <div>
      <p class="font-label-bold text-label-bold uppercase">156 &amp; 158 Fortess Road, Tufnell Park, London, NW5 2HP</p>
      <p class="font-label-sm text-on-surface-variant mt-1">Sun–Thu: 16:00–22:45 | Fri–Sat: 16:00–23:15</p>
    </div>
  </div>
</div>
@endsection
