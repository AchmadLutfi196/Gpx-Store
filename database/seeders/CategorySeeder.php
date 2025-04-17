<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Sepatu Lari',
                // Sesuaikan dengan kolom yang ada di tabel categories
                // Hapus kolom yang tidak ada
            ],
            [
                'name' => 'Sepatu Basket',
                // Sesuaikan dengan kolom yang ada
            ],
            [
                'name' => 'Sepatu Casual',
                // Sesuaikan dengan kolom yang ada
            ],
            [
                'name' => 'Pakaian Pria',
                // Sesuaikan dengan kolom yang ada
            ],
            [
                'name' => 'Pakaian Wanita',
                // Sesuaikan dengan kolom yang ada
            ],
            [
                'name' => 'Aksesoris',
                // Sesuaikan dengan kolom yang ada
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}