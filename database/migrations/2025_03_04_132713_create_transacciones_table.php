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
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id('transaccion_id');
            $table->foreignId('cuenta_id')->constrained('cuentas', 'cuenta_id')->onDelete('cascade'); // Asegúrate que 'cuenta_id' sea unsignedBigInteger
            $table->foreignId('usuario_id_registro')->constrained('users', 'id')->onDelete('cascade'); // Asegúrate que 'usuario_id' sea unsignedBigInteger
            $table->string('tipo', 10);
            $table->decimal('monto', 15, 2);
            $table->timestamp('fecha_transaccion')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};
