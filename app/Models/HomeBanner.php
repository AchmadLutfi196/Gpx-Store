<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'banner_title',
        'banner_subtitle',
        'banner_image',
        'is_active',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    // Get the active banner
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }
}
