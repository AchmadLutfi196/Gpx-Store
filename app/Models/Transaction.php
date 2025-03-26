<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionItem;
use App\Models\Product;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'user_id', 'total_amount', 'status'];

public function items()
{
    return $this->hasMany(TransactionItem::class, 'transaction_id');
}
public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}
}