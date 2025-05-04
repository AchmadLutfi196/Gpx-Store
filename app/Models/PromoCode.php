<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PromoCode extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'minimum_order',
        'maximum_discount',
        'start_date',
        'end_date',
        'is_active',
        'usage_limit',
        'used_count',
        'show_on_homepage',
    ];
    
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'show_on_homepage' => 'boolean',
    ];
    
    public function isValid($orderTotal = null)
    {
        // Check if code is active
        if (!$this->is_active) {
            return [
                'valid' => false,
                'message' => 'Kode promo tidak aktif.'
            ];
        }
        
        // Check date range
        $now = Carbon::now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return [
                'valid' => false,
                'message' => 'Kode promo belum dapat digunakan.'
            ];
        }
        
        if ($this->end_date && $now->gt($this->end_date)) {
            return [
                'valid' => false,
                'message' => 'Kode promo sudah kadaluarsa.'
            ];
        }
        
        // Check usage limit
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return [
                'valid' => false,
                'message' => 'Kode promo sudah mencapai batas penggunaan.'
            ];
        }
        
        // Check minimum order
        if ($orderTotal !== null && $this->minimum_order > 0 && $orderTotal < $this->minimum_order) {
            return [
                'valid' => false,
                'message' => 'Minimum pembelian Rp ' . number_format($this->minimum_order, 0, ',', '.') . ' untuk menggunakan kode promo ini.'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Kode promo valid.'
        ];
    }
    
    public function calculateDiscount($orderTotal)
    {
        if ($this->discount_type === 'percentage') {
            $discount = ($orderTotal * $this->discount_value) / 100;
            
            // Apply maximum discount cap if defined
            if ($this->maximum_discount > 0 && $discount > $this->maximum_discount) {
                $discount = $this->maximum_discount;
            }
            
            return $discount;
        } else {
            // Fixed amount discount
            return $this->discount_value;
        }
    }
    
    public function incrementUsage()
    {
        $this->used_count += 1;
        $this->save();
    }
    public function getFormattedDiscountAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        } else {
            return 'Rp ' . number_format($this->discount_value, 0, ',', '.');
        }
    }
}