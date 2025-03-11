<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Prestamo extends Model
{
    //
    protected $table = 'prestamos';
    protected $primaryKey = 'prestamo_id';
    protected $fillable = [
        'cliente_id', 'monto_solicitado', 'fecha_solicitud',
        'estado', 'fecha_de_aprobacion', 'monto_aprobado',
        'tasa_interes_id','comentarios','path_contract','plazo_meses',
    ];

    // Relación con cliente (Un préstamo pertenece a un cliente)
    public function cliente() {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

     // Relación con cliente (Una cuenta pertenece a un cliente)
     public function tasasInteres() {
        return $this->belongsTo(TasaInteres::class, 'tasa_interes_id');
    }

    // Relación con pagos (Un préstamo tiene muchos pagos)
    public function pagos() {
        return $this->hasMany(Pago::class, 'prestamo_id');
    }

    public function planPagos()
    {
        return $this->hasMany(PlanPago::class, 'prestamo_id');
    }

   

    // Evento que se ejecuta al actualizar el estado de un prestamo
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($prestamo) {
            // Si el préstamo es aprobado, generar plan de pago
            if ($prestamo->estado === 'aprobado') {
                $tasa_interes = TasaInteres::where('tasa_id', $prestamo->tasa_interes_id)->first();
                if (!$tasa_interes) {
                    return; // Evitar error si la tasa no existe
                }

                $interes_anual = $tasa_interes->porcentaje;
                $monto_aprobado = $prestamo->monto_aprobado;
                $num_meses = $prestamo->plazo_meses;

                // Calcular tasa de interés mensual
                $tasa_mensual = ($interes_anual / 100) / 12;

                // Calcular cuota fija mensual usando el método francés
                $cuota_mensual = ($monto_aprobado * $tasa_mensual) / 
                                 (1 - pow(1 + $tasa_mensual, -$num_meses));

                // Fecha inicial de pago
                $fecha_pago = Carbon::parse($prestamo->fecha_de_aprobacion);
                $saldo = $monto_aprobado;
                $plan_pagos = [];

                for ($i = 1; $i <= $num_meses; $i++) {
                    $interes = $saldo * $tasa_mensual;
                    $capital = $cuota_mensual - $interes;
                    $saldo -= $capital;

                    $plan_pagos[] = [
                        'prestamo_id' => $prestamo->prestamo_id,
                        'fecha_pago' => $fecha_pago->copy()->addMonths($i),
                        'cuota' => round($cuota_mensual, 2),
                        'interes' => round($interes, 2),
                        'capital' => round($capital, 2),
                        'saldo' => round($saldo, 2),
                        'estado' => 'pendiente',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Insertar los pagos en la base de datos
                PlanPago::insert($plan_pagos);
            }
        });
    }
}
