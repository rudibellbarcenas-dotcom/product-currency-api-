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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('Nombre o código ISO de la divisa (ej. USD, EUR, MXN)');
            $table->string('symbol', 10)->comment('Símbolo monetario (ej. $, €, £)');
            $table->decimal('exchange_rate', 14, 6)->comment('Tasa de cambio respecto a la divisa base de referencia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
