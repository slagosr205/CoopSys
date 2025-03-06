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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id('cliente_id');
            $table->string('nombre', 45);
            $table->string('identificacion', 20)->unique();
            $table->string('tipo_identificacion', 20);
            $table->date('fecha_nacimiento');
            $table->char('genero', 1);
            $table->string('direccion', 250);
            $table->string('telefono', 20);
            $table->boolean('es_socio')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
