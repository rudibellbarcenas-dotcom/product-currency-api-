<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateCurrencyRequest extends BaseApiRequest
{
    /**
     * Reglas de validación aplicables a la actualización de una divisa.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currencyId = $this->route('id') ?? $this->route('currency');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('currencies', 'name')->ignore($currencyId),
            ],
            'symbol' => [
                'sometimes',
                'required',
                'string',
                'max:10',
            ],
            'exchange_rate' => [
                'sometimes',
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
            'name.required' => 'El nombre o código de la divisa no puede ser nulo.',
            'name.unique' => 'Ya existe otra divisa registrada con este nombre o código.',
            'symbol.required' => 'El símbolo monetario no puede ser nulo.',
            'exchange_rate.required' => 'La tasa de cambio no puede ser nula.',
            'exchange_rate.gt' => 'La tasa de cambio debe ser estrictamente mayor que cero.',
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
