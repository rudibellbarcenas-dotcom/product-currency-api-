<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * Atributos asignables de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'currency_id',
        'tax_cost',
        'manufacturing_cost',
    ];

    /**
     * Conversión nativa de atributos para precisión financiera.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'tax_cost' => 'decimal:2',
        'manufacturing_cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Divisa base del producto.
     */
    public function baseCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * Precios explícitos del producto fijados en otras divisas.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'product_id');
    }

    /**
     * Costo total acumulado (fabricación + impuestos).
     */
    public function getTotalCostAttribute(): float
    {
        return (float) ($this->tax_cost + $this->manufacturing_cost);
    }

    /**
     * Margen bruto en divisa base (precio - costo total).
     */
    public function getGrossMarginAttribute(): float
    {
        return (float) ($this->price - $this->total_cost);
    }

    /**
     * Calcula dinámicamente el precio convertido a una divisa destino
     * según las tasas de cambio de ambas divisas respecto a la base del sistema.
     *
     * Fórmula:
     * Precio_Destino = Precio_Base * (Tasa_Destino / Tasa_Origen)
     */
    public function convertPriceTo(Currency $targetCurrency): float
    {
        $sourceRate = (float) $this->baseCurrency->exchange_rate;
        $targetRate = (float) $targetCurrency->exchange_rate;

        if ($sourceRate <= 0) {
            return 0.0;
        }

        return round(((float) $this->price * ($targetRate / $sourceRate)), 2);
    }
}
