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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id('movimiento_id');
            $table->unsignedBigInteger('movimiento_prestamo_id')->nullable()->constrained('pagos')->nullOnDelete();
            $table->unsignedBigInteger('movimiento_ahorro_id')->nullable()->constrained('transacciones')->nullOnDelete();
            $table->unsignedBigInteger('cajero_id_registro')->constrained('users')->cascadeOnDelete();
            $table->decimal('monto_ingresar', 10, 2);
            $table->timestamp('fecha_movimiento');
            $table->enum('tipo_transaccion', ['pago_prestamo', 'deposito_ahorro', 'retiro_ahorro']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
