<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function callback()
    {
        $provider = env('SSO_PROVIDER');

        if (!$provider || !config("services.{$provider}.enabled")) {
            abort(404, 'SSO not configured');
        }

        try {
            $socialiteUser = Socialite::driver($provider)->user();

            // Check if user exists
            $user = User::where('email', $socialiteUser->getEmail())->first();

            if (!$user) {
                // Check if registration is allowed
                if (!env('SSO_ALLOW_REGISTRATION', false)) {
                    return redirect('/')->withErrors(
                        ['sso' => 'Your account is not authorized. Please contact an administrator.']
                    );
                }

                // Create new user
                $user = User::create([
                    'email' => $socialiteUser->getEmail(),
                    'name' => $socialiteUser->getName() ?? $socialiteUser->getNickname(),
                ]);
            } else {
                // Update existing user
                $user->update([
                    'name' => $socialiteUser->getName() ?? $socialiteUser->getNickname(),
                ]);
            }

            Auth::login($user, true);

            request()->session()->regenerate();

            return redirect()->intended('/');
        } catch (\Exception $e) {
            \Log::error('SSO Authentication failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/')->withErrors(['sso' => 'Authentication failed: ' . $e->getMessage()]);
        }
    }
}
