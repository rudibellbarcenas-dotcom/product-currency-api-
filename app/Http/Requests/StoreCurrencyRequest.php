<?php

namespace App\Http\Requests;

class StoreCurrencyRequest extends BaseApiRequest
{
    /**
     * Reglas de validación aplicables a la creación de una divisa.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                'unique:currencies,name',
            ],
            'symbol' => [
                'required',
                'string',
                'max:10',
            ],
            'exchange_rate' => [
                'required',
                'numeric',
                'gt:0',
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
            'name.required' => 'El código o nombre de la divisa es obligatorio.',
            'name.unique' => 'Ya existe una divisa registrada con este nombre o código.',
            'symbol.required' => 'El símbolo monetario es obligatorio.',
            'exchange_rate.required' => 'La tasa de cambio es obligatoria.',
            'exchange_rate.gt' => 'La tasa de cambio debe ser estrictamente mayor que cero.',
        ];
    }
}
