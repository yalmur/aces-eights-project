@extends('layouts.guest')
@section('content')
<h2 class="font-serif text-headline-md text-primary mb-2 uppercase tracking-tighter">Reset Password</h2>
<p class="font-body-sm text-on-surface-variant mb-8">Enter your email and we'll send you a reset link.</p>

@if(session('status'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-6">
    @csrf
    <div class="flex flex-col">
        <label class="font-label-bold text-label-bold uppercase text-primary mb-1" for="email">Email Address</label>
        <input
            class="industrial-input bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-outline py-2 font-body-md focus:ring-0 placeholder:text-on-surface-variant/40 text-on-surface"
            id="email" name="email" type="email"
            value="{{ old('email') }}" required autofocus
            placeholder="artisan@acesandeightspizza.com"
        />
        @error('email')
            <span class="font-label-sm text-label-sm text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    <button class="w-full bg-primary-container text-on-primary font-headline-md py-4 border-b-[3px] border-secondary-container gold-metallic-glow hover:brightness-110 active:opacity-80 transition-all uppercase tracking-tighter" type="submit">
        Send Reset Link
    </button>
</form>

<p class="mt-6 text-center font-label-sm text-on-surface-variant">
    Remembered it? <a href="{{ route('login') }}" class="text-primary font-label-bold hover:underline uppercase">Sign In</a>
</p>
@endsection
