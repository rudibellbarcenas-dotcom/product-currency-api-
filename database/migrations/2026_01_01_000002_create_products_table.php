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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->index()->comment('Nombre del producto');
            $table->text('description')->nullable()->comment('Descripción detallada del producto');
            $table->decimal('price', 12, 4)->comment('Precio de venta en la divisa base del producto');
            $table->foreignId('currency_id')
                  ->constrained('currencies')
                  ->restrictOnDelete()
                  ->comment('Divisa base asociada al producto');
            $table->decimal('tax_cost', 12, 4)->default(0.0000)->comment('Costo de impuestos aplicable');
            $table->decimal('manufacturing_cost', 12, 4)->default(0.0000)->comment('Costo de fabricación del producto');
            $table->timestamps();

            $table->index(['currency_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
