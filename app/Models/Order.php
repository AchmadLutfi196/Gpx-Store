<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zipcode',
        'shipping_phone',
        'notes',
        'payment_method',
        'payment_status',
        'transaction_id',
        'snap_token',
        'shipping_cost',
        'tax_amount',
        'discount_amount',
    ];


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
}