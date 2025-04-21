<?php

namespace App\Services;

use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialAuthService
{
    public function findOrCreateUser(SocialiteUser $socialiteUser, string $provider): User
    {
        // Cek apakah ada akun sosial yang terkait
        $socialAccount = SocialAccount::where('provider_name', $provider)
            ->where('provider_id', $socialiteUser->getId())
            ->first();

        // Jika ada, return user yang terkait
        if ($socialAccount) {
            return $socialAccount->user;
        }

        // Cek apakah ada user dengan email yang sama
        $user = User::where('email', $socialiteUser->getEmail())->first();

        // Jika tidak ada, buat user baru
        if (!$user) {
            return DB::transaction(function () use ($socialiteUser, $provider) {
                $user = User::create([
                    'name' => $socialiteUser->getName() ?? $socialiteUser->getNickname(),
                    'email' => $socialiteUser->getEmail(),
                    'password' => Hash::make(rand(1000000, 9999999)),
                    'email_verified_at' => now(),
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
        $this->createSocialAccount($user, $socialiteUser, $provider);

        return $user;
    }

    protected function createSocialAccount(User $user, SocialiteUser $socialiteUser, string $provider): SocialAccount
    {
        return $user->socialAccounts()->create([
            'provider_id' => $socialiteUser->getId(),
            'provider_name' => $provider,
            'provider_email' => $socialiteUser->getEmail(),
        ]);
    }
}