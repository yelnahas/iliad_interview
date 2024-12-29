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
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,  // Product name
            'price' => $this->faker->randomFloat(2, 10, 1000),  // Price between 10 and 1000 with 2 decimal places
            'stock' => $this->faker->numberBetween(20, 100),  // Random stock between 20 and 100
        ];
    }
}
