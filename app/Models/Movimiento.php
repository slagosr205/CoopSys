<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Movimiento extends Model
{
    //

    protected $table = 'movimientos'; // La tabla ahora se llama movimientos
    protected $primaryKey = 'movimiento_id';
    protected $fillable = [
                            'movimiento_prestamo_id', 
                            'movimiento_ahorro_id',
                            'cajero_id_registro', 
                            'monto_ingresar', 
                            'fecha_movimiento', 
                            'tipo_transaccion'
                        ];


     // Un pago pertenece a un movimiento
     public function prestamo()
     {
         return $this->belongsTo(Prestamo::class, 'movimiento_prestamo_id');
     }

     // Usuario que registró el pago
    public function usuario()
    {
        return $this->belongsTo(User::class, 'cajero_id_registro');
    }

    // Una transaccion de ahorro pertenece a un movimiento
    public function cuentaAhorro()
    {
        return $this->belongsTo(Transaccion::class, 'movimiento_ahorro_id');
    }


    public static function boot()
    {
        parent::boot();

        static::created(function ($movimiento) {
            // Obtener el préstamo relacionado
            $prestamo = Prestamo::find($movimiento->movimiento_prestamo_id);
            
            if ($prestamo) {
                // Buscar la cuota vigente (primera cuota con fecha de pago mayor o igual al inicio del mes actual)
                $cuotaVigente = $prestamo->planPagos
                    ->where('estado', 'pendiente')
                    ->whereBetween('fecha_pago', [now()->startOfMonth(), now()->endOfMonth()])
                    ->first();
               
                if ($cuotaVigente) {
                    // Marcar la cuota como pagada
                    $cuotaVigente->update(['estado' => 'pagado']);
                    $dataPrestamoPago=[
                        'prestamo_id'=>$movimiento->movimiento_prestamo_id,
                        'usuario_id_registro'=>$movimiento->cajero_id_registro,
                        'monto_pago'=>$movimiento->monto_ingresar,
                    ];

                    Pago::insert($dataPrestamoPago);
                    // Log para verificar la actualización (opcional)
                    Log::info("Plan de pago ID {$cuotaVigente->plan_pagos_id} actualizado a 'pagado'");
                }
            }
        });
    }



}
