<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currency = Currency::factory()->create([
            'name' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1.0,
        ]);
    }

    public function test_can_list_products_with_pagination(): void
    {
        Product::factory()->count(15)->create([
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson('/api/products?per_page=10');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'items' => [
                             '*' => [
                                 'id', 'name', 'description', 'price',
                                 'currency_id', 'base_currency', 'tax_cost',
                                 'manufacturing_cost', 'total_cost', 'gross_margin',
                                 'prices', 'created_at', 'updated_at'
                             ]
                         ],
                         'pagination' => [
                             'total', 'per_page', 'current_page', 'last_page', 'has_more_pages'
                         ]
                     ]
                 ])
                 ->assertJsonPath('data.pagination.total', 15)
                 ->assertJsonPath('data.pagination.per_page', 10);
    }

    public function test_can_create_product_successfully(): void
    {
        $payload = [
            'name' => 'Monitor 4K Ultra',
            'description' => 'Monitor profesional de 27 pulgadas IPS.',
            'price' => 399.99,
            'currency_id' => $this->currency->id,
            'tax_cost' => 40.00,
            'manufacturing_cost' => 200.00,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Producto creado exitosamente.',
                     'data' => [
                         'name' => 'Monitor 4K Ultra',
                         'price' => 399.99,
                         'currency_id' => $this->currency->id,
                         'tax_cost' => 40.00,
                         'manufacturing_cost' => 200.00,
                         'total_cost' => 240.00,
                         'gross_margin' => 159.99,
                     ]
                 ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Monitor 4K Ultra',
            'currency_id' => $this->currency->id,
        ]);
    }

    public function test_cannot_create_product_with_negative_price_or_costs(): void
    {
        $payload = [
            'name' => 'Producto Inválido',
            'price' => -50.00,
            'currency_id' => $this->currency->id,
            'tax_cost' => -10.00,
            'manufacturing_cost' => -5.00,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['price', 'tax_cost', 'manufacturing_cost']);
    }

    public function test_cannot_create_product_with_non_existent_currency(): void
    {
        $payload = [
            'name' => 'Teclado Mecánico',
            'price' => 89.99,
            'currency_id' => 99999,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['currency_id']);
    }

    public function test_can_show_product(): void
    {
        $product = Product::factory()->create([
            'name' => 'Mouse Pad XL',
            'currency_id' => $this->currency->id,
            'price' => 25.00,
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $product->id,
                         'name' => 'Mouse Pad XL',
                         'price' => 25.00,
                     ]
                 ]);
    }

    public function test_returns_404_when_product_not_found(): void
    {
        $response = $this->getJson('/api/products/99999');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Producto no encontrado.',
                 ]);
    }

    public function test_can_update_product(): void
    {
        $product = Product::factory()->create([
            'name' => 'Silla Gamer V1',
            'currency_id' => $this->currency->id,
            'price' => 199.99,
        ]);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Silla Gamer V2 Pro',
            'price' => 249.99,
        ]);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Silla Gamer V2 Pro',
                         'price' => 249.99,
                     ]
                 ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Silla Gamer V2 Pro',
            'price' => 249.99,
        ]);
    }

    public function test_can_delete_product_and_cascade_removes_prices(): void
    {
        $product = Product::factory()->create([
            'currency_id' => $this->currency->id,
        ]);

        $eur = Currency::factory()->create(['name' => 'EUR', 'symbol' => '€']);

        $price = ProductPrice::create([
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => 120.00,
        ]);

        $this->assertDatabaseHas('product_prices', ['id' => $price->id]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Producto y sus precios asociados eliminados correctamente.',
                 ]);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_prices', ['id' => $price->id]);
    }
}
