<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    /**
     * Atributos asignables de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'symbol',
        'exchange_rate',
    ];

    /**
     * Conversión nativa de atributos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Productos que tienen esta divisa como divisa base.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'currency_id');
    }

    /**
     * Precios específicos fijados en esta divisa.
     */
    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'currency_id');
    }

    /**
     * Mutator para asegurar que el código/nombre se almacene siempre en mayúsculas.
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = strtoupper(trim($value));
    }
}
