<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\ProductReview;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
        'email_verified_at',
        'phone',
        'avatar',
        'role',
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
    
    // All other methods remain the same...
    
    /**
     * Get the addresses for the user.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the orders for the user.
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

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Mendapatkan alamat default pengguna.
     *
     * @return \App\Models\Address|null
     */
    public function getDefaultAddressAttribute()
    {
        return $this->addresses()->where('is_default', true)->first() 
               ?? $this->addresses()->first();
    }

    /**
     * Get the social accounts for the user.
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * User's contact messages
     */
    public function contactMessages()
    {
        return $this->hasMany(ContactMessage::class);
    }
}