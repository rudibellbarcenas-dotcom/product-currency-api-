<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete()
                  ->comment('Producto al que pertenece el precio');
            $table->foreignId('currency_id')
                  ->constrained('currencies')
                  ->restrictOnDelete()
                  ->comment('Divisa en la que se fija el precio');
            $table->decimal('price', 12, 4)->comment('Importe del precio en la divisa especificada');
            $table->timestamps();

            // Garantiza unicidad a nivel de motor de base de datos
            $table->unique(['product_id', 'currency_id'], 'uq_product_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
