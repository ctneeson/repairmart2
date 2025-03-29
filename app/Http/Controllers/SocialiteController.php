<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the OAuth provider's authentication page.
     *
     * @param  string  $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the provider and log them in.
     *
     * @param  string  $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
        try {
            // $user = \Socialite::driver($provider)->user();
            $field = null;

            if ($provider === 'google') {
                $field = 'google_id';
            } elseif ($provider === 'facebook') {
                $field = 'facebook_id';
            }

            $user = Socialite::driver($provider)->stateless()->user();
            $dbUser = User::where('email', $user->email)->first();

            if ($dbUser) {
                // User exists, update their provider ID
                $dbUser->{$field} = $user->id;
                $dbUser->save();
            } else {
                // User does not exist, create a new one
                $dbUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    $field => $user->id,
                    'email_verified_at' => now(),
                ]);
            }
            // Log the user in
            Auth::login($dbUser);
            // Redirect to intended page
            return redirect()->intended(route('home'));


        } catch (\Exception $e) {
            return redirect(route('login'))
                ->with('error', $e->getMessage() ?: 'Login failed');
        }
    }
}
