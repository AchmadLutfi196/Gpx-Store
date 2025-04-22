<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\SocialAccount;

class SocialLoginHelper
{
    /**
     * Get avatar URL for currently logged in user
     *
     * @return string|null
     */
    public static function getUserAvatar()
    {
        if (!Auth::check()) {
            return null;
        }
        
        $user = Auth::user();
        
        // Jika user memiliki avatar yang tersimpan, gunakan itu
        if ($user->avatar) {
            return $user->avatar;
        }
        
        // Check provider accounts untuk avatar
        $providers = ['github', 'facebook', 'google'];
        
        foreach ($providers as $provider) {
            $account = SocialAccount::where('user_id', $user->id)
                ->where('provider_name', $provider)
                ->first();
                
            if ($account) {
                // GitHub dan Google sudah memberikan URL avatar lengkap melalui getAvatar()
                if ($provider === 'facebook') {
                    return 'https://graph.facebook.com/' . $account->provider_id . '/picture?type=normal';
                }
                
                // Untuk GitHub, kita bisa menggunakan URL dari username
                if ($provider === 'github') {
                    // Jika kita memiliki username GitHub
                    if (strpos($account->provider_email, '@github.user') !== false) {
                        $username = str_replace('@github.user', '', $account->provider_email);
                        return 'https://avatars.githubusercontent.com/u/' . $account->provider_id . '?v=4';
                    }
                }
                
                break;
            }
        }
        
        // Default avatar
        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random';
    }
}