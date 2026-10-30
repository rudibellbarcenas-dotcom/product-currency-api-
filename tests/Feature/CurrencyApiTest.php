<?php

namespace Tests\Feature;

use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CurrencyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_currencies(): void
    {
        Currency::factory()->count(3)->create();

        $response = $this->getJson('/api/currencies');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         '*' => ['id', 'name', 'symbol', 'exchange_rate', 'created_at', 'updated_at']
                     ]
                 ])
                 ->assertJson(['success' => true]);
    }

    public function test_can_create_currency_successfully(): void
    {
        $payload = [
            'name' => 'EUR',
            'symbol' => '€',
            'exchange_rate' => 0.92,
        ];

        $response = $this->postJson('/api/currencies', $payload);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Divisa creada exitosamente.',
                     'data' => [
                         'name' => 'EUR',
                         'symbol' => '€',
                         'exchange_rate' => 0.92,
                     ]
                 ]);

        $this->assertDatabaseHas('currencies', [
            'name' => 'EUR',
            'symbol' => '€',
        ]);
    }

    public function test_cannot_create_currency_with_duplicate_name(): void
    {
        Currency::factory()->create(['name' => 'USD']);

        $payload = [
            'name' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1.0,
        ];

        $response = $this->postJson('/api/currencies', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonValidationErrors(['name']);
    }

    public function test_cannot_create_currency_with_zero_or_negative_rate(): void
    {
        $payload = [
            'name' => 'BAD',
            'symbol' => 'B',
            'exchange_rate' => 0.0,
        ];

        $response = $this->postJson('/api/currencies', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['exchange_rate']);
    }

    public function test_can_show_currency(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'GBP',
            'symbol' => '£',
            'exchange_rate' => 0.79,
        ]);

        $response = $this->getJson("/api/currencies/{$currency->id}");

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $currency->id,
                         'name' => 'GBP',
                         'symbol' => '£',
                     ]
                 ]);
    }

    public function test_returns_404_when_currency_does_not_exist(): void
    {
        $response = $this->getJson('/api/currencies/9999');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Divisa no encontrada.',
                 ]);
    }

    public function test_can_update_currency_exchange_rate(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'MXN',
            'exchange_rate' => 17.0,
        ]);

        $response = $this->putJson("/api/currencies/{$currency->id}", [
            'exchange_rate' => 18.25,
        ]);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'exchange_rate' => 18.25,
                     ]
                 ]);

        $this->assertDatabaseHas('currencies', [
            'id' => $currency->id,
            'exchange_rate' => 18.25,
        ]);
    }
}
