<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::truncate();

        $products = [
            [
                'name' => 'Produto Local 1',
                'price' => 29.90,
                'stock' => 15,
                'category' => 'local',
                'description' => 'Produto criado localmente para demonstração'
            ],
            [
                'name' => 'Produto Local 2', 
                'price' => 45.50,
                'stock' => 8,
                'category' => 'local',
                'description' => 'Outro produto local de exemplo'
            ],
            [
                'name' => 'Produto Local 3',
                'price' => 78.00,
                'stock' => 3,
                'category' => 'local',
                'description' => 'Produto com estoque baixo para teste'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

    }
}
