<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Dapatkan brand IDs yang sudah ada
        $brands = Brand::all();
        $brandIds = $brands->pluck('id')->toArray();
        
        // Dapatkan category IDs yang sudah ada
        $categories = Category::all();
        $categoryIds = $categories->pluck('id')->toArray();
        
        if (empty($brandIds) || empty($categoryIds)) {
            $this->command->error('Tidak ada brand atau kategori yang tersedia. Jalankan BrandSeeder dan CategorySeeder terlebih dahulu.');
            return;
        }
        
        $products = [
            [
                'name' => 'Nike Air Zoom Pegasus 38',
                // Kolom lain disesuaikan dengan struktur tabel products
                'price' => 1799000,
                'stock' => 50,
                // Sesuaikan kolom relasi jika ada
            ],
            [
                'name' => 'Adidas Ultraboost 21',
                'price' => 2999000,
                'stock' => 35,
                // Sesuaikan kolom lainnya
            ],
            [
                'name' => 'Nike LeBron 18',
                'price' => 2899000,
                'stock' => 25,
                // Sesuaikan kolom lainnya
            ],
            [
                'name' => 'Adidas Stan Smith',
                'price' => 1499000,
                'stock' => 100,
                // Sesuaikan kolom lainnya
            ],
            [
                'name' => 'Puma RS-X3',
                'price' => 1799000,
                'stock' => 40,
                // Sesuaikan kolom lainnya
            ],
        ];

        // Insert semua produk
        foreach ($products as $product) {
            // Tambahkan brand_id dan category_id random jika diperlukan
            if (isset($product['brand_id']) === false && !empty($brandIds)) {
                $product['brand_id'] = $brandIds[array_rand($brandIds)];
            }
            
            if (isset($product['category_id']) === false && !empty($categoryIds)) {
                $product['category_id'] = $categoryIds[array_rand($categoryIds)];
            }
            
            Product::create($product);
        }
    }
}