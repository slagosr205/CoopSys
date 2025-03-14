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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('pago_id');
            $table->foreignId('prestamo_id')->constrained('prestamos', 'prestamo_id')->onDelete('cascade'); // Asegúrate que 'prestamo_id' sea unsignedBigInteger
            $table->foreignId('usuario_id_registro')->constrained('users', 'id')->onDelete('cascade'); // Asegúrate que 'usuario_id' sea unsignedBigInteger
            $table->decimal('monto_pago', 15, 2);
            $table->timestamp('fecha_pago')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
