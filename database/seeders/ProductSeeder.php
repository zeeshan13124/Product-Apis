<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
class ProductSeeder extends Seeder
{
    public function run()
    {
        // Sample products
        $products = [
            ['name' => 'Product 1', 'price' => 19.99, 'category' => 'Electronics'],
            ['name' => 'Product 2', 'price' => 29.99, 'category' => 'Clothing'],
            ['name' => 'Product 3', 'price' => 9.99, 'category' => 'Books'],
            // Add more products as needed
        ];

        // Seed the products
        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
