<?php

namespace App\Services;

use App\Models\Currency;
use InvalidArgumentException;

class CurrencyConversionService
{
    /**
     * Convierte un monto de una divisa origen a una divisa destino
     * utilizando sus tasas de cambio relativas a la divisa base del sistema.
     *
     * Fórmula:
     * Monto_Destino = Monto_Origen * (Tasa_Destino / Tasa_Origen)
     *
     * @param  float  $amount
     * @param  Currency  $fromCurrency
     * @param  Currency  $toCurrency
     * @param  int  $precision
     * @return float
     */
    public function convert(float $amount, Currency $fromCurrency, Currency $toCurrency, int $precision = 2): float
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('El monto a convertir no puede ser negativo.');
        }

        $fromRate = (float) $fromCurrency->exchange_rate;
        $toRate = (float) $toCurrency->exchange_rate;

        if ($fromRate <= 0 || $toRate <= 0) {
            throw new InvalidArgumentException('Las tasas de cambio deben ser mayores a cero.');
        }

        // Si ambas divisas son la misma, evitamos redondeos innecesarios
        if ($fromCurrency->id === $toCurrency->id) {
            return round($amount, $precision);
        }

        $converted = $amount * ($toRate / $fromRate);

        return round($converted, $precision);
    }
}
