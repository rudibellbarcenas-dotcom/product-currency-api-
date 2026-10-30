<?php

namespace App\Http\Requests;

class StoreProductRequest extends BaseApiRequest
{
    /**
     * Reglas de validación aplicables a la creación de un producto.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'price' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
            ],
            'tax_cost' => [
                'nullable',
                'numeric',
                'gte:0',
            ],
            'manufacturing_cost' => [
                'nullable',
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
            'name.required' => 'El nombre del producto es obligatorio.',
            'price.required' => 'El precio del producto es obligatorio.',
            'price.gte' => 'El precio del producto no puede ser negativo.',
            'currency_id.required' => 'La divisa base es obligatoria.',
            'currency_id.exists' => 'La divisa base seleccionada no existe en el sistema.',
            'tax_cost.gte' => 'El costo de impuestos no puede ser negativo.',
            'manufacturing_cost.gte' => 'El costo de fabricación no puede ser negativo.',
        ];
    }
}
