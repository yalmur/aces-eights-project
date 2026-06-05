@extends('layouts.guest')
@section('content')
<h2 class="font-serif text-headline-md text-primary mb-6 uppercase tracking-tighter">New Password</h2>

<form method="POST" action="{{ route('password.update') }}" class="space-y-6">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <div class="flex flex-col">
        <label class="font-label-bold text-label-bold uppercase text-primary mb-1" for="password">New Password</label>
        <input
            class="industrial-input bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-outline py-2 font-body-md focus:ring-0 text-on-surface"
            id="password" name="password" type="password" required autofocus minlength="8"
        />
        @error('password')
            <span class="font-label-sm text-label-sm text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex flex-col">
        <label class="font-label-bold text-label-bold uppercase text-primary mb-1" for="password_confirmation">Confirm Password</label>
        <input
            class="industrial-input bg-transparent border-t-0 border-l-0 border-r-0 border-b-2 border-outline py-2 font-body-md focus:ring-0 text-on-surface"
            id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
        />
    </div>

    @error('email')
        <p class="font-label-sm text-label-sm text-error">{{ $message }}</p>
    @enderror

    <button class="w-full bg-primary-container text-on-primary font-headline-md py-4 border-b-[3px] border-secondary-container gold-metallic-glow hover:brightness-110 active:opacity-80 transition-all uppercase tracking-tighter" type="submit">
        Set New Password
    </button>
</form>
@endsection
