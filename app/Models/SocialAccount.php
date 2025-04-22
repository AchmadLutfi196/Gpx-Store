<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider_id',
        'provider_name',
        'provider_email'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getAvatar()
    {
        // Implementasi untuk mendapatkan avatar dari provider
        // Misalnya, jika provider adalah GitHub, kita bisa menggunakan URL avatar dari API GitHub
        if ($this->provider_name === 'github') {
            return 'https://avatars.githubusercontent.com/u/' . $this->provider_id . '?v=4';
        }
        // Tambahkan logika untuk provider lain jika diperlukan
        return null;
    }
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }
}