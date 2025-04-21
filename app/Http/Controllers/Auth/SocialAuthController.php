<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected $socialAuthService;
    
    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }

    /**
     * Redirect user ke halaman authentication provider.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
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
            $socialUser = Socialite::driver($provider)->user();
            
            $user = $this->socialAuthService->findOrCreateUser($socialUser, $provider);
            
            Auth::login($user);
            
            return redirect()->intended('/')->with('status', 'Berhasil login menggunakan ' . ucfirst($provider));
            
        } catch (Exception $e) {
            return redirect('/login')
                ->withErrors(['error' => 'Terjadi kesalahan saat login dengan ' . ucfirst($provider) . '. Silakan coba lagi.']);
        }
    }
}