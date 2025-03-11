<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPago extends Model
{
    //
    protected $table='plan_pagos';

    protected $primaryKey='plan_pagos_id';

    protected $fillable=[
        'prestamo_id',
        'fecha_pago',
        'cuota',
        'interes',
        'capital',
        'saldo',
        'estado',
    ];
    
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, 'prestamo_id');
    }
}
