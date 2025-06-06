<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'shipping_address',
        'shipping_postal_code',
        'shipping_phone',
        'notes',
        'payment_method',
        'payment_status',
        'transaction_id',
        'snap_token',
        'shipping_cost',
        'tax_amount',
        'discount_amount',
        'shipping_amount',
        'shipping_method',
        'payment_token',
        'payment_details',
        'promo_code_id',
        'cancelled_at',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
    ];

    protected function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value;
        
        if ($value === 'cancelled' && empty($this->attributes['cancelled_at'])) {
            $this->attributes['cancelled_at'] = now();
        }
    }

    protected function setPaymentStatusAttribute($value)
    {
        $this->attributes['payment_status'] = $value;
        
        if ($value === 'cancelled' && empty($this->attributes['cancelled_at'])) {
            $this->attributes['cancelled_at'] = now();
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>',
            'processing' => '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Processing</span>',
            'completed' => '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Completed</span>',
            'cancelled' => '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Cancelled</span>',
        ];

        return $statuses[$this->status] ?? $statuses['pending'];
    }

    public function getPaymentStatusLabelAttribute()
    {
        $statuses = [
            'pending' => '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>',
            'paid' => '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Paid</span>',
            'failed' => '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Failed</span>',
            'cancelled' => '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Failed</span>',
            'expired' => '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Expired</span>',
        ];

        return $statuses[$this->payment_status] ?? $statuses['pending'];
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the address associated with the order.
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    // Sesuaikan method untuk menghitung item yang sudah direview
    public function getReviewedItemsCountAttribute()
    {
        // Ambil produk ID yang sudah direview di order ini
        $reviewedProductIds = DB::table('product_reviews')
            ->where('order_id', $this->id)
            ->pluck('product_id')
            ->toArray();
        
        // Hitung berapa item order yang produknya sudah direview
        return DB::table('order_items')
            ->where('order_id', $this->id)
            ->whereIn('product_id', $reviewedProductIds)
            ->count();
    }

    // Method untuk menghitung item yang belum direview
    public function getUnreviewedItemsCountAttribute()
    {
        $totalItems = $this->items()->count();
        $reviewedItems = $this->getReviewedItemsCountAttribute();
        
        return $totalItems - $reviewedItems;
    }
}