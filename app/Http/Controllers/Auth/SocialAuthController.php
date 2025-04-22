<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected $socialAuthService;
    protected $supportedProviders = ['github','google']; // Hanya menggunakan GitHub
    
    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }
    
    /**
     * Get the middleware assigned to the controller.
     *
     * @return array
     */
    protected function middleware()
    {
        return ['guest'];
    }

    /**
     * Redirect user ke halaman authentication provider.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider)
{
    // Validasi provider yang didukung
    if (! in_array($provider, $this->supportedProviders)) {
        return redirect()->route('login')
            ->withErrors(['error' => 'Provider login tidak didukung.']);
    }

        /** @var AbstractProvider $driver */
    $driver = Socialite::driver($provider);

    if ($provider === 'github') {
    $driver->scopes(['read:user','user:email']);
    }

    return $driver->redirect();
}

    /**
     * Mendapatkan informasi user dari provider.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
        try {
            // Validasi provider yang didukung
            if (!in_array($provider, $this->supportedProviders)) {
                return redirect()->route('login')
                    ->withErrors(['error' => 'Provider login tidak didukung.']);
            }

            $socialUser = Socialite::driver($provider)->user();
            
            $user = $this->socialAuthService->findOrCreateUser($socialUser, $provider);
            
            Auth::login($user);
            
            return redirect()->intended('/profile')
                ->with('success', 'Berhasil login menggunakan ' . ucfirst($provider));
            
        } catch (Exception $e) {
            Log::error('Social login error: ' . $e->getMessage(), [
                'provider' => $provider,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('login')
                ->withErrors(['error' => 'Terjadi kesalahan saat login dengan ' . ucfirst($provider) . '. Silakan coba lagi.']);
        }
    }
}