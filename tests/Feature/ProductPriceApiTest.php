<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductPriceApiTest extends TestCase
{
    use RefreshDatabase;

    private Currency $usd;
    private Currency $eur;
    private Currency $mxn;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usd = Currency::factory()->create(['name' => 'USD', 'symbol' => '$', 'exchange_rate' => 1.0]);
        $this->eur = Currency::factory()->create(['name' => 'EUR', 'symbol' => '€', 'exchange_rate' => 0.92]);
        $this->mxn = Currency::factory()->create(['name' => 'MXN', 'symbol' => '$', 'exchange_rate' => 17.5]);

        $this->product = Product::factory()->create([
            'name' => 'Laptop ThinkPad X1',
            'price' => 1200.00,
            'currency_id' => $this->usd->id,
            'tax_cost' => 120.00,
            'manufacturing_cost' => 800.00,
        ]);
    }

    public function test_can_list_prices_for_a_product(): void
    {
        ProductPrice::create([
            'product_id' => $this->product->id,
            'currency_id' => $this->eur->id,
            'price' => 1104.00,
        ]);

        ProductPrice::create([
            'product_id' => $this->product->id,
            'currency_id' => $this->mxn->id,
            'price' => 21000.00,
        ]);

        $response = $this->getJson("/api/products/{$this->product->id}/prices");

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Precios del producto obtenidos exitosamente.',
                 ])
                 ->assertJsonCount(2, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'product_id',
                             'currency_id',
                             'currency' => ['id', 'name', 'symbol', 'exchange_rate'],
                             'price',
                             'created_at',
                             'updated_at',
                         ]
                     ]
                 ]);
    }

    public function test_can_register_price_for_specific_currency(): void
    {
        $payload = [
            'currency_id' => $this->eur->id,
            'price' => 1104.50,
        ];

        $response = $this->postJson("/api/products/{$this->product->id}/prices", $payload);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Precio registrado exitosamente para la divisa.',
                     'data' => [
                         'product_id' => $this->product->id,
                         'currency_id' => $this->eur->id,
                         'price' => 1104.50,
                     ]
                 ]);

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $this->product->id,
            'currency_id' => $this->eur->id,
            'price' => 1104.50,
        ]);
    }

    public function test_cannot_register_duplicate_price_for_same_currency(): void
    {
        // Registrar primer precio en EUR
        ProductPrice::create([
            'product_id' => $this->product->id,
            'currency_id' => $this->eur->id,
            'price' => 1104.00,
        ]);

        // Intentar registrar nuevamente precio en EUR para el mismo producto
        $payload = [
            'currency_id' => $this->eur->id,
            'price' => 1150.00,
        ];

        $response = $this->postJson("/api/products/{$this->product->id}/prices", $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['currency_id']);
    }

    public function test_cannot_register_negative_price(): void
    {
        $payload = [
            'currency_id' => $this->eur->id,
            'price' => -20.00,
        ];

        $response = $this->postJson("/api/products/{$this->product->id}/prices", $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['price']);
    }

    public function test_cannot_register_price_with_non_existent_currency(): void
    {
        $payload = [
            'currency_id' => 88888,
            'price' => 100.00,
        ];

        $response = $this->postJson("/api/products/{$this->product->id}/prices", $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['currency_id']);
    }

    public function test_cannot_register_price_for_non_existent_product(): void
    {
        $payload = [
            'currency_id' => $this->eur->id,
            'price' => 100.00,
        ];

        $response = $this->postJson("/api/products/99999/prices", $payload);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Producto no encontrado.',
                 ]);
    }

    public function test_returns_404_when_listing_prices_for_non_existent_product(): void
    {
        $response = $this->getJson('/api/products/99999/prices');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Producto no encontrado.',
                 ]);
    }
}
