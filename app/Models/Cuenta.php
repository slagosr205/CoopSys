<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    //

    protected $table = 'cuentas';
    protected $primaryKey = 'cuenta_id';
    protected $fillable = ['cliente_id', 'saldo', 'fecha_apertura', 'tasa_interes_id'];

      // Relación con cliente (Una cuenta pertenece a un cliente)
      public function cliente() {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    // Relación con transacciones (Una cuenta tiene muchas transacciones)
    public function transacciones() {
        return $this->hasMany(Transaccion::class, 'cuenta_id');
    }

     // Relación con cliente (Una cuenta pertenece a un cliente)
     public function tasasInteres() {
        return $this->belongsTo(TasaInteres::class, 'tasa_interes_id');
    }

    // Evento que se ejecuta al crear una cuenta
    protected static function boot()
    {
        parent::boot();

        static::created(function ($cuenta) {
            // Crear una transacción inicial con el saldo de la cuenta
            Transaccion::create([
                'cuenta_id' => $cuenta->cuenta_id,
                'usuario_id_registro' => auth()->id(), // Usuario que la creó
                'tipo' => 'Deposito',
                'monto' => $cuenta->saldo,
                'fecha_transaccion' => now()->toDateTimeString(),
                
            ]);
        });
    }

}
