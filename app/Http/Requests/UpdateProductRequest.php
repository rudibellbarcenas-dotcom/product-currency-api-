<?php

namespace App\Http\Requests;

class UpdateProductRequest extends BaseApiRequest
{
    /**
     * Reglas de validación aplicables a la actualización de un producto.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'price' => [
                'sometimes',
                'required',
                'numeric',
                'gte:0',
            ],
            'currency_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:currencies,id',
            ],
            'tax_cost' => [
                'sometimes',
                'numeric',
                'gte:0',
            ],
            'manufacturing_cost' => [
                'sometimes',
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
            'name.required' => 'El nombre del producto no puede ser nulo.',
            'price.required' => 'El precio del producto no puede ser nulo.',
            'price.gte' => 'El precio del producto no puede ser negativo.',
            'currency_id.exists' => 'La divisa base seleccionada no existe en el sistema.',
            'tax_cost.gte' => 'El costo de impuestos no puede ser negativo.',
            'manufacturing_cost.gte' => 'El costo de fabricación no puede ser negativo.',
        ];
    }

    /**
     * Valida que la solicitud de actualización contenga al menos un campo a modificar.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (empty($this->all())) {
                $validator->errors()->add('payload', 'Debe enviar al menos un campo válido para actualizar (el cuerpo JSON llegó vacío o con comillas malformadas en la terminal).');
            }
        });
    }
}
