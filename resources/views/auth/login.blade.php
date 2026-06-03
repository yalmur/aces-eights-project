@extends('layouts.guest')

@section('content')
<h2 class="font-serif text-headline-md text-primary mb-6 uppercase tracking-tighter">Sign In</h2>

{{-- Social sign-in --}}
<div class="grid grid-cols-2 gap-4 mb-8">
    <button type="button" class="flex items-center justify-center gap-2 py-3 border-2 border-outline font-label-bold text-label-bold uppercase hover:bg-surface-container-high transition-all active:opacity-80">
        <img alt="Google Logo" class="w-5 h-5" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA0bLWcz02uEyHL5YH4rMSx9OoZ5m70iG3VAX2jWNF68l0Mn6XSX8ARCgNX7kgw99cCJctKV-EjLMg7M_P7EhJnQojIMT4N-pzDewWQWs1IPEWqwXODUmTQ4oawaHldqWPrX5hVHkK-W3rS55QAmWjgNUrw6mLLn_5JaW9VvfrXCTPzvAd7ZI1wIbcyo0ijCn8abaNsqsU23ynhmQMuHbHRKzfc5zm3Kr5uOtj_UjLE7vi4WXQzf8v9Ut8tWcUXAgpd8Y00UMVN9pU"/>
        Google
    </button>
    <button type="button" class="flex items-center justify-center gap-2 py-3 border-2 border-outline font-label-bold text-label-bold uppercase hover:bg-surface-container-high transition-all active:opacity-80">
        <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">social_leaderboard</span>
        Facebook
    </button>
</div>

<div class="flex items-center gap-4 mb-8">
    <div class="h-px bg-outline-variant flex-grow"></div>
    <span class="font-label-sm text-label-sm text-outline uppercase tracking-widest">Or login with email</span>
    <div class="h-px bg-outline-variant flex-grow"></div>
</div>

{{-- Login form --}}
<form method="POST" action="{{ route('login.post') }}" class="space-y-6">
    @csrf

    <div class="flex flex-col">
        <label class="font-label-bold text-label-bold uppercase text-primary mb-1" for="email">Email Address</label>
        <input
            class="industrial-input bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-outline py-2 font-body-md focus:ring-0 placeholder:text-on-surface-variant/40 text-on-surface"
            id="email"
            name="email"
            type="email"
            placeholder="artisan@acesandeights.com"
            value="{{ old('email') }}"
            required
            autofocus
        />
        @error('email')
            <span class="font-label-sm text-label-sm text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex flex-col">
        <div class="flex justify-between items-end mb-1">
            <label class="font-label-bold text-label-bold uppercase text-primary" for="password">Password</label>
            <a class="font-label-sm text-label-sm text-outline hover:text-primary transition-colors uppercase underline" href="#">Forgot?</a>
        </div>
        <input
            class="industrial-input bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-outline py-2 font-body-md focus:ring-0 placeholder:text-on-surface-variant/40 text-on-surface"
            id="password"
            name="password"
            type="password"
            placeholder="••••••••"
            required
        />
        @error('password')
            <span class="font-label-sm text-label-sm text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex items-center gap-2 pt-2">
        <input class="w-4 h-4 border-2 border-outline text-primary focus:ring-primary rounded-none bg-surface" id="remember" name="remember" type="checkbox"/>
        <label class="font-label-sm text-label-sm uppercase cursor-pointer text-on-surface" for="remember">Remember this workstation</label>
    </div>

    <button class="w-full bg-primary-container text-on-primary font-headline-md py-4 mt-4 border-b-[3px] border-secondary-container gold-metallic-glow hover:brightness-110 active:opacity-80 transition-all uppercase tracking-tighter" type="submit">
        Sign In
    </button>
</form>

<p class="mt-6 text-center font-label-sm text-on-surface-variant">
    New here? <a href="{{ route('register') }}" class="text-primary font-label-bold hover:underline uppercase">Create Account</a>
</p>
@endsection
