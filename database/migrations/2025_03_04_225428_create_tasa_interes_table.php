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
        Schema::create('tasas_interes', function (Blueprint $table) {
            $table->id('tasa_id'); // Identificador único de la tasa de interés
            $table->decimal('porcentaje', 5, 2); // Tasa de interés en porcentaje (ej. 3.50)
            $table->enum('tipo_tasa', ['fija', 'variable'])->default('fija'); // Tipo de tasa
            $table->timestamp('fecha_inicio')->useCurrent(); // Fecha de inicio de la tasa
            $table->timestamp('fecha_fin')->nullable(); // Fecha de fin de la tasa (si aplica)
            $table->enum('destino', ['ahorro', 'prestamo']); // Ahorro o préstamo
            $table->enum('tipo_prestamo', ['automatico', 'personal', 'garantia'])->nullable(); // Tipo de préstamo
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasa_interes');
    }
};
