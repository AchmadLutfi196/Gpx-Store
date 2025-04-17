<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'original_price',
        'discount',
        'image',
        'stock',
        'sku',
        'is_active',
        'is_featured',
        'is_new',
        'weight',
        'rating',
        'review_count',
        'brand_id',
        'category_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'rating' => 'float',
        'review_count' => 'integer',
    ];

    /**
     * Get the category that owns the product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get users who have this product in their wishlist.
     */
    public function wishlists()
    {
        return $this->belongsToMany(User::class, 'wishlist_items');
    }

    /**
     * Get the formatted price attribute.
     *
     * @return string
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get the formatted original price attribute.
     *
     * @return string
     */
    public function getFormattedOriginalPriceAttribute()
    {
        return 'Rp ' . number_format($this->original_price, 0, ',', '.');
    }

    /**
     * Check if product is on sale.
     *
     * @return bool
     */
    public function getIsOnSaleAttribute()
    {
        return $this->original_price > $this->price;
    }

    /**
     * Check if product is in stock.
     *
     * @return bool
     */
    public function getInStockAttribute()
    {
        return $this->stock > 0;
    }

    /**
     * Scope a query to only include active products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include new products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    /**
     * Scope a query to only include products on sale.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSale($query)
    {
        return $query->whereNotNull('original_price')
                    ->whereColumn('original_price', '>', 'price');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    // Method untuk mendapatkan rating rata-rata
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }

    // Method untuk mendapatkan jumlah review
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }
}