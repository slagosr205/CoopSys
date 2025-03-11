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
        Schema::create('intereses', function (Blueprint $table) {
            $table->id('interes_id');
            $table->foreignId('cuenta_id')->constrained('cuentas', 'cuenta_id')->onDelete('cascade'); // Asegúrate que 'cuenta_id' sea unsignedBigInteger
            $table->timestamp('fecha_calculo')->default(now());
            $table->decimal('monto_interes', 10, 2);
            $table->double('saldo_anterior');
            $table->double('saldo_actualizado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interes');
    }
};
