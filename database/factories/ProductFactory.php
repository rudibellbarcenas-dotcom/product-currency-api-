<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $manufacturing = $this->faker->randomFloat(2, 10, 500);
        $tax = round($manufacturing * 0.16, 2);
        $price = round($manufacturing + $tax + $this->faker->randomFloat(2, 20, 200), 2);

        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price' => $price,
            'currency_id' => Currency::factory(),
            'tax_cost' => $tax,
            'manufacturing_cost' => $manufacturing,
        ];
    }
}
