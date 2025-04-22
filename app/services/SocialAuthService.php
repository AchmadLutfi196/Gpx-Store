<?php

namespace App\Services;

use App\Models\User;
use App\Models\SocialAccount;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialAuthService
{
    public function findOrCreateUser(SocialiteUser $socialiteUser, string $provider): User
    {
        // Log informasi untuk debugging
        Log::debug('Social login attempt', [
            'provider' => $provider,
            'id' => $socialiteUser->getId(),
            'email' => $socialiteUser->getEmail(),
            'name' => $this->getNameFromSocialite($socialiteUser, $provider),
        ]);

        // Cek apakah ada akun sosial yang terkait
        $socialAccount = SocialAccount::where('provider_name', $provider)
            ->where('provider_id', $socialiteUser->getId())
            ->first();

        // Jika ada, return user yang terkait
        if ($socialAccount) {
            Log::debug('Found existing social account', ['user_id' => $socialAccount->user_id]);
            return $socialAccount->user;
        }

        // Cek apakah ada user dengan email yang sama
        $user = null;
        if ($socialiteUser->getEmail()) {
            $user = User::where('email', $socialiteUser->getEmail())->first();
        }

        // Jika tidak ada, buat user baru
        if (!$user) {
            Log::debug('Creating new user for social login', ['email' => $socialiteUser->getEmail()]);
            
            // Handle khusus untuk GitHub yang mungkin tidak memberikan email
            if (!$socialiteUser->getEmail() && $provider == 'github') {
                // Buat email dummy berdasarkan username GitHub dan domain aplikasi
                $dummyEmail = $socialiteUser->getNickname() . '@github.user';
                Log::warning('No email from GitHub, using dummy: ' . $dummyEmail);
            } elseif (!$socialiteUser->getEmail()) {
                throw new Exception('Email tidak tersedia dari ' . $provider);
            }
            
            return DB::transaction(function () use ($socialiteUser, $provider) {
                $name = $this->getNameFromSocialite($socialiteUser, $provider);
                $email = $socialiteUser->getEmail() ?? 
                         ($provider == 'github' ? $socialiteUser->getNickname() . '@github.user' : null);
                
                if (!$email) {
                    throw new Exception('Email tidak tersedia dari ' . $provider);
                }
                
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(rand(1000000, 9999999)),
                    'email_verified_at' => now(),
                    'avatar' => $socialiteUser->getAvatar(),
                ]);

                // Tambahkan role 'user' jika menggunakan system role
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('user');
                }

                $this->createSocialAccount($user, $socialiteUser, $provider);
                
                return $user;
            });
        }

        // Jika user sudah ada tapi belum terhubung ke social account
        Log::debug('Connecting existing user to social account', ['user_id' => $user->id]);
        
        // Update avatar jika user belum memiliki avatar
        if ($socialiteUser->getAvatar() && empty($user->avatar)) {
            $user->avatar = $socialiteUser->getAvatar();
            $user->save();
        }
        
        $this->createSocialAccount($user, $socialiteUser, $provider);

        return $user;
    }

    protected function createSocialAccount(User $user, SocialiteUser $socialiteUser, string $provider): SocialAccount
    {
        // Simpan email dari provider jika tersedia
        $providerEmail = $socialiteUser->getEmail() ?? null;
        
        return $user->socialAccounts()->create([
            'provider_id' => $socialiteUser->getId(),
            'provider_name' => $provider,
            'provider_email' => $providerEmail,
        ]);
    }
    
    /**
     * Get name from socialite user based on provider
     */
    private function getNameFromSocialite(SocialiteUser $socialiteUser, string $provider): string
    {
        // GitHub terkadang memberikan name = null
        if ($provider === 'github') {
            return $socialiteUser->getName() 
                ?? $socialiteUser->getNickname() 
                ?? 'GitHub User';
        }
        
        return $socialiteUser->getName() 
            ?? $socialiteUser->getNickname() 
            ?? 'User';
    }
}