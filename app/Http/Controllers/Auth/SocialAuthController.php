<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Exception;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        try {
            return Socialite::driver($provider)->redirect();
        } catch (Exception $e) {
            Log::error('Social login redirect error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Tidak dapat terhubung dengan ' . ucfirst($provider) . '. Silakan coba lagi.');
        }
    }

    /**
     * Obtain the user information from provider.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Log the social user info for debugging
            Log::info('Social user info:', [
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId()
            ]);
            
            // Check if the social account exists
            $socialAccount = SocialAccount::where('provider_name', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();
                
            if ($socialAccount) {
                // Login the existing user
                Auth::login($socialAccount->user);
                return redirect()->route('profile.index')
                    ->with('success', 'Berhasil login dengan ' . ucfirst($provider));
            }
            
            // If email exists, associate the social account with the existing user
            if ($socialUser->getEmail()) {
                $user = User::where('email', $socialUser->getEmail())->first();
                
                if (!$user) {
                    // Create a new user
                    $user = User::create([
                        'name' => $socialUser->getName(),
                        'email' => $socialUser->getEmail(),
                        // Social logins are automatically verified because the OAuth provider
                        // has already verified the user's email address
                        'email_verified_at' => now(),
                        'password' => Hash::make(Str::random(16)), // Random password
                    ]);
                }
                
                // Create social account
                $user->socialAccounts()->create([
                    'provider_id' => $socialUser->getId(),
                    'provider_name' => $provider,
                    'provider_email' => $socialUser->getEmail(),
                ]);
                
                // Login the user
                Auth::login($user);
                
                // If the user's email is not verified, redirect to verification notice
                if (!$user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice')
                        ->with('info', 'Silahkan verifikasi email Anda untuk mengakses semua fitur.');
                }
                
                return redirect()->route('profile.index')
                    ->with('success', 'Berhasil login dengan ' . ucfirst($provider));
            } else {
                // Provider did not return an email
                return redirect()->route('login')
                    ->with('error', 'Tidak dapat mengakses email dari akun ' . ucfirst($provider) . ' Anda. Email diperlukan untuk login.');
            }
                
        } catch (Exception $e) {
            Log::error('Social login callback error: ' . $e->getMessage(), [
                'provider' => $provider,                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Terjadi kesalahan saat login dengan ' . ucfirst($provider) . '. Silakan coba lagi.');
        }
    }
}