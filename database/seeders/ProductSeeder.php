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
                'name' => 'Camiseta TechFit',
                'price' => 59.90,
                'stock' => 25,
                'category' => 'Vestuário',
                'description' => 'Camiseta esportiva leve e respirável, ideal para treinos e uso diário'
            ],
            [
                'name' => 'Fone Bluetooth Pro',
                'price' => 199.90,
                'stock' => 12,
                'category' => 'Eletrônicos',
                'description' => 'Fone sem fio com cancelamento de ruído e bateria de até 30 horas'
            ],
            [
                'name' => 'Cafeteira Automática',
                'price' => 349.00,
                'stock' => 5,
                'category' => 'Eletrodomésticos',
                'description' => 'Cafeteira inteligente com timer programável e função de manter aquecido'
            ],
            [
                'name' => 'Tênis Running X',
                'price' => 299.99,
                'stock' => 18,
                'category' => 'Calçados',
                'description' => 'Tênis de corrida com amortecimento avançado e design leve'
            ],
            [
                'name' => 'Mochila Casual Urban',
                'price' => 149.90,
                'stock' => 30,
                'category' => 'Acessórios',
                'description' => 'Mochila resistente à água com compartimento para notebook até 15"'
            ],
            [
                'name' => 'Luminária LED Smart',
                'price' => 89.90,
                'stock' => 10,
                'category' => 'Casa',
                'description' => 'Luminária inteligente com ajuste de cor e controle por aplicativo'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

    }
}
