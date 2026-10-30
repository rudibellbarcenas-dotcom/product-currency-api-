<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transforma el recurso de producto en un array estructurado.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'currency_id' => $this->currency_id,
            'base_currency' => new CurrencyResource($this->whenLoaded('baseCurrency')),
            'tax_cost' => (float) $this->tax_cost,
            'manufacturing_cost' => (float) $this->manufacturing_cost,
            'total_cost' => (float) $this->total_cost,
            'gross_margin' => (float) $this->gross_margin,
            'prices' => ProductPriceResource::collection($this->whenLoaded('prices')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
