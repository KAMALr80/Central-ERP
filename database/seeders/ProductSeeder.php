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
                'product_code' => 'PRD001',
                'name' => 'Smartphone X',
                'description' => 'Latest smartphone with 5G',
                'price' => 24999,
                'quantity' => 50,
                'category' => 'Electronics',
            ],
            [
                'product_code' => 'PRD002',
                'name' => 'Laptop Pro',
                'description' => 'High performance laptop',
                'price' => 55999,
                'quantity' => 30,
                'category' => 'Electronics',
            ],
            [
                'product_code' => 'PRD003',
                'name' => 'Wireless Headphones',
                'description' => 'Noise cancelling headphones',
                'price' => 3999,
                'quantity' => 100,
                'category' => 'Electronics',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['product_code' => $product['product_code']],
                $product
            );
        }

        $this->command->info('✅ Products seeded successfully!');
    }
}
