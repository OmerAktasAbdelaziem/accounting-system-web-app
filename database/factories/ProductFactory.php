<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $purchasePrice = fake()->randomFloat(2, 10, 100);
        
        return [
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'purchase_price' => $purchasePrice,
            'selling_price' => $purchasePrice * 2, // 100% markup
            'current_stock' => fake()->numberBetween(10, 1000),
            'is_active' => true,
        ];
    }
}
