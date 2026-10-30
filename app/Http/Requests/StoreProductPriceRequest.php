<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreProductPriceRequest extends BaseApiRequest
{
    /**
     * Reglas de validación aplicables al registro de precio en una divisa.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('id') ?? $this->route('product');

        return [
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
                Rule::unique('product_prices', 'currency_id')->where(function ($query) use ($productId) {
                    return $query->where('product_id', $productId);
                }),
            ],
            'price' => [
                'required',
                'numeric',
                'gte:0',
            ],
        ];
    }

    /**
     * Mensajes descriptivos en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'currency_id.required' => 'La divisa del precio es obligatoria.',
            'currency_id.exists' => 'La divisa especificada no existe en el catálogo.',
            'currency_id.unique' => 'Este producto ya cuenta con un precio registrado para la divisa indicada.',
            'price.required' => 'El importe del precio es obligatorio.',
            'price.gte' => 'El importe del precio no puede ser negativo.',
        ];
    }
}
