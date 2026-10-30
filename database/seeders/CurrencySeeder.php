<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'USD',
                'symbol' => '$',
                'exchange_rate' => 1.000000, // Divisa de referencia del sistema
            ],
            [
                'name' => 'EUR',
                'symbol' => '€',
                'exchange_rate' => 0.920000, // 1 USD = 0.92 EUR
            ],
            [
                'name' => 'MXN',
                'symbol' => '$',
                'exchange_rate' => 17.500000, // 1 USD = 17.50 MXN
            ],
            [
                'name' => 'GBP',
                'symbol' => '£',
                'exchange_rate' => 0.790000, // 1 USD = 0.79 GBP
            ],
            [
                'name' => 'JPY',
                'symbol' => '¥',
                'exchange_rate' => 155.000000, // 1 USD = 155 JPY
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['name' => $currency['name']],
                $currency
            );
        }
    }
}
