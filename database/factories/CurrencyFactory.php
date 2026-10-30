<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        return [
            'name' => strtoupper($this->faker->unique()->currencyCode()),
            'symbol' => '$',
            'exchange_rate' => $this->faker->randomFloat(6, 0.1, 50.0),
        ];
    }
}
