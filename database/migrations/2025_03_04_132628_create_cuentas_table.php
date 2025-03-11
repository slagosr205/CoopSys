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
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id('cuenta_id');
            $table->unsignedBigInteger('cliente_id'); // Tipo correcto
           $table->unsignedBigInteger('tasa_interes_id'); // Tipo correcto
            $table->double('saldo', 15, 2)->default(0);
            $table->string('estado_cuenta')->default('activo'); //indicara si la cuenta esta activa, bloqueada ;
            $table->timestamp('fecha_apertura')->useCurrent();
           
            $table->foreign('cliente_id')->references('cliente_id')->on('clientes')->onDelete('cascade');
            $table->foreign('tasa_interes_id')->references('tasa_id')->on('tasas_interes')->onDelete('cascade'); // Asegúrate que 'tasa_id' sea unsignedBigInteger
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
