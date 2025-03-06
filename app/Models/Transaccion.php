<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    //
    protected $table = 'transacciones';
    protected $primaryKey = 'transaccion_id';
    protected $fillable = ['cuenta_id', 'usuario_id_registro', 'tipo', 'monto', 'fecha_transaccion'];

    // Relación con cuenta (Una transacción pertenece a una cuenta)
    public function cuenta() {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }

    // Relación con usuario (Una transacción fue registrada por un usuario)
    public function usuario() {
        return $this->belongsTo(User::class, 'usuario_id_registro');
    }

    // Evento para actualizar el saldo antes de guardar la transacción
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaccion) {
           
            $cuenta = Cuenta::find($transaccion->cuenta_id); // Recupera la cuenta manualmente

            if ($cuenta) {
                // Verificar si esta transacción es la primera (saldo inicial)
                $esSaldoInicial = Transaccion::where('cuenta_id', $cuenta->cuenta_id)->count() === 0;

                if (!$esSaldoInicial) {
                    if ($transaccion->tipo === 'Retiro') {
                        if ($cuenta->saldo >= $transaccion->monto) {
                            $cuenta->saldo -= $transaccion->monto;
                        } else {
                            throw new \Exception('Saldo insuficiente para realizar el retiro.');
                        }
                    } else {
                        $cuenta->saldo += $transaccion->monto;
                    }

                    $cuenta->save(); // Guardar el nuevo saldo
                }
            }
        });
    }

}
