<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Nike',
                // Hapus kolom yang tidak ada dalam tabel
                // 'slug' => 'nike', 
                // 'logo' => null,
                // 'description' => 'Nike, Inc. adalah perusahaan multinasional Amerika...',
                // 'is_featured' => true,
                // 'is_active' => true,
            ],
            [
                'name' => 'Adidas',
                // Hapus kolom yang tidak ada
            ],
            [
                'name' => 'Puma',
                // Hapus kolom yang tidak ada
            ],
            [
                'name' => 'Reebok',
                // Hapus kolom yang tidak ada
            ],
            [
                'name' => 'Under Armour',
                // Hapus kolom yang tidak ada
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}