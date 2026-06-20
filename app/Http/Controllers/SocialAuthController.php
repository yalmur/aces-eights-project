<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook']), 404);

        if (!config("services.{$provider}.client_id")) {
            return redirect()->route('login')->withErrors(['email' => 'Social login is not available.']);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook']), 404);

        try {
            $social = Socialite::driver($provider)->user();
        } catch (\Exception) {
            return redirect()->route('login')->withErrors(['email' => 'Social login failed. Please try again.']);
        }

        $idField = $provider . '_id';

        $user = User::where($idField, $social->getId())
            ->orWhere('email', $social->getEmail())
            ->first();

        if ($user) {
            $user->update([
                $idField => $social->getId(),
                'avatar' => $user->avatar ?? $social->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'name'     => $social->getName() ?? $social->getNickname() ?? 'User',
                'email'    => $social->getEmail(),
                $idField   => $social->getId(),
                'avatar'   => $social->getAvatar(),
                'password' => null,
            ]);
            $user->forceFill(['role' => 'customer'])->save();
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('home'));
    }
}
