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
        Schema::create('referencias_personales', function (Blueprint $table) {
            $table->id('referencias_id');
            $table->unsignedBigInteger('cliente_id'); // Tipo correcto
            $table->foreign('cliente_id')->references('cliente_id')->on('clientes')->onDelete('cascade');
            $table->string('nombre_completo');
            $table->enum('relacion', ['hermano', 'esposo', 'esposa', 'amigo', 'otro','madre','padre','hijo']);
            $table->date('fecha_nacimiento');
            $table->decimal('porcentaje_beneficio', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referencias_personales');
    }
};
