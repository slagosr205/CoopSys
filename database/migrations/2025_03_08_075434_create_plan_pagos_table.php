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
        Schema::create('plan_pagos', function (Blueprint $table) {
            $table->id('plan_pagos_id');
            $table->unsignedBigInteger('prestamo_id');
            $table->foreign('prestamo_id')->references('prestamo_id')->on('prestamos')->onDelete('cascade');
            $table->date('fecha_pago');
            $table->decimal('cuota', 15, 2);
            $table->decimal('interes', 15, 2);
            $table->decimal('capital', 15, 2);
            $table->decimal('saldo', 15, 2);
            $table->enum('estado', ['pendiente', 'pagado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_pagos');
    }
};
