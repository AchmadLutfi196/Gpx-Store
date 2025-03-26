<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'slug', 'image'];
    
    /**
     * Get products that belong to this category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}