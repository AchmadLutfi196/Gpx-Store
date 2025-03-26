<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    /**
     * Get the user's addresses.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    
    /**
     * Get the user's orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Get the user's wishlist items.
     */
    public function wishlistItems()
    {
        return $this->belongsToMany(Product::class, 'wishlist_items')
                    ->withTimestamps();
    }
    
    /**
     * Check if a product is in the user's wishlist.
     *
     * @param int $productId
     * @return bool
     */
    public function hasInWishlist($productId)
    {
        return $this->wishlist()->where('product_id', $productId)->exists();
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the user's default shipping address.
     */
    public function defaultAddress()
    {
        return $this->addresses()->where('is_default', true)->first();
    }
    
    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Default avatar based on name initials
        $name = $this->name ?? 'User';
        $initials = mb_strtoupper(mb_substr($name, 0, 1));
        $color = substr(md5($this->id ?? rand()), 0, 6);
        
        return "https://ui-avatars.com/api/?name={$initials}&background={$color}&color=ffffff&size=150";
    }
    
    /**
     * Check if user is a new customer (registered in the last 30 days).
     */
    public function getIsNewCustomerAttribute()
    {
        return $this->created_at->diffInDays(now()) <= 30;
    }

    public function wishlist()
{
    return $this->hasMany(Wishlist::class);
}


}