<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Nike Brasilia Training Backpack',
                'price' => 599000,
                'stock' => 45,
                'description' => 'Tas ransel Nike Brasilia dengan kompartemen luas dan nyaman untuk kebutuhan olahraga dan aktivitas sehari-hari.',
                'slug' => 'nike-brasilia-training-backpack',
            ],
            [
                'name' => 'Adidas Linear Classic Backpack',
                'price' => 450000,
                'stock' => 60,
                'description' => 'Tas ransel ringan dengan desain minimalis dan logo Adidas yang ikonik, cocok untuk penggunaan sehari-hari.',
                'slug' => 'adidas-linear-classic-backpack',
            ],
            [
                'name' => 'Puma Contender Duffel Bag',
                'price' => 499000,
                'stock' => 30,
                'description' => 'Tas olahraga dengan kapasitas besar untuk membawa perlengkapan latihan atau perjalanan singkat.',
                'slug' => 'puma-contender-duffel-bag',
            ],
            [
                'name' => 'Nike Heritage Crossbody Bag',
                'price' => 350000,
                'stock' => 75,
                'description' => 'Tas selempang kompak dengan ruang cukup untuk barang-barang penting dan gaya retro yang trendi.',
                'slug' => 'nike-heritage-crossbody-bag',
            ],
            [
                'name' => 'Under Armour Storm Undeniable Backpack',
                'price' => 899000,
                'stock' => 25,
                'description' => 'Tas ransel premium dengan teknologi tahan air dan kompartemen laptop, sempurna untuk atlet dan profesional aktif.',
                'slug' => 'under-armour-storm-undeniable-backpack',
            ],
            [
                'name' => 'Herschel Little America Backpack',
                'price' => 1299000,
                'stock' => 20,
                'description' => 'Tas ransel bergaya vintage dengan kompartemen laptop dan detail kulit sintetis premium.',
                'slug' => 'herschel-little-america-backpack',
            ],
            [
                'name' => 'Fjällräven Kånken Classic',
                'price' => 1499000,
                'stock' => 35,
                'description' => 'Tas ransel ikonik dari Swedia dengan desain fungsional dan bahan tahan lama yang cocok untuk gaya kasual.',
                'slug' => 'fjallraven-kanken-classic',
            ],
            [
                'name' => 'The North Face Borealis Backpack',
                'price' => 1250000,
                'stock' => 18,
                'description' => 'Tas ransel serbaguna untuk hiking dan penggunaan sehari-hari dengan banyak kantong dan sistem ventilasi.',
                'slug' => 'the-north-face-borealis-backpack',
            ],
            [
                'name' => 'Louis Vuitton Neverfull Tote',
                'price' => 21500000,
                'stock' => 5,
                'description' => 'Tas jinjing mewah dengan bahan kanvas premium, desain ikonik dan kapasitas yang fleksibel.',
                'slug' => 'louis-vuitton-neverfull-tote',
            ],
            [
                'name' => 'Samsonite Omni PC Hardside Luggage',
                'price' => 2850000,
                'stock' => 12,
                'description' => 'Koper kokoh dengan bahan polikarbonat, roda 360 derajat, dan sistem penguncian TSA terintegrasi.',
                'slug' => 'samsonite-omni-pc-hardside-luggage',
            ],
            [
                'name' => 'Tumi Alpha 3 Slim Briefcase',
                'price' => 9750000,
                'stock' => 8,
                'description' => 'Tas kerja profesional premium dengan kompartemen laptop, organizer, dan bahan ballistic nylon tahan lama.',
                'slug' => 'tumi-alpha-3-slim-briefcase',
            ],
            [
                'name' => 'Coach Tabby Shoulder Bag',
                'price' => 5950000,
                'stock' => 15,
                'description' => 'Tas bahu elegan dengan kulit premium, detail rantai, dan logo Coach yang ikonik.',
                'slug' => 'coach-tabby-shoulder-bag',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}