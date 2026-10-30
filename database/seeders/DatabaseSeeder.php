<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CurrencySeeder::class);

        $usd = Currency::where('name', 'USD')->first();
        $eur = Currency::where('name', 'EUR')->first();
        $mxn = Currency::where('name', 'MXN')->first();

        if ($usd && $eur && $mxn) {
            $laptop = Product::create([
                'name' => 'Laptop Gamer Pro 16',
                'description' => 'Portátil para alto rendimiento con procesador de última generación y GPU dedicada.',
                'price' => 1500.00,
                'currency_id' => $usd->id,
                'tax_cost' => 150.00,
                'manufacturing_cost' => 950.00,
            ]);

            ProductPrice::create([
                'product_id' => $laptop->id,
                'currency_id' => $eur->id,
                'price' => 1380.00,
            ]);

            ProductPrice::create([
                'product_id' => $laptop->id,
                'currency_id' => $mxn->id,
                'price' => 26250.00,
            ]);

            $mouse = Product::create([
                'name' => 'Mouse Ergonómico Inalámbrico',
                'description' => 'Mouse óptico ergonómico con conexión Bluetooth 5.2 y batería recargable.',
                'price' => 45.00,
                'currency_id' => $usd->id,
                'tax_cost' => 4.50,
                'manufacturing_cost' => 18.00,
            ]);

            ProductPrice::create([
                'product_id' => $mouse->id,
                'currency_id' => $eur->id,
                'price' => 41.40,
            ]);
        }
    }
}
