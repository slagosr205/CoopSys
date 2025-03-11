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
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id('prestamo_id');
            $table->foreignId('cliente_id')->constrained('clientes', 'cliente_id')->onDelete('cascade'); // Asegúrate que 'cliente_id' es unsignedBigInteger
            $table->double('monto_solicitado', 15, 2);
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->string('estado', 20);
            $table->timestamp('fecha_de_aprobacion')->nullable();
            $table->decimal('monto_aprobado', 15, 2)->nullable();
            $table->integer('plazo_meses')->nullable();
            $table->text('comentarios')->nullable();
            $table->string('path_contract')->nullable();
            $table->timestamps();
            $table->foreignId('tasa_interes_id')->constrained('tasas_interes', 'tasa_id')->onDelete('cascade'); // Asegúrate que 'tasa_id' sea unsignedBigInteger
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
