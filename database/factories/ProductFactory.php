<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $price = (float) rand(34, 55) . '.00';
        return [
            'name' => 'Product-' . rand(1, 2000),
            'description' => fake()->words(12),
            'quantity' => rand(3, 99),
            'unit_price' => $price,
        ];
    }
}
