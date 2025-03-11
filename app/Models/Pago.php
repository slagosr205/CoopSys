<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    //
    protected $table = 'pagos';
    protected $primaryKey = 'pago_id';
    protected $fillable = ['prestamo_id', 'monto_pago', 'fecha_pago','usuario_id_registro'];

    // Relación con préstamo (Un pago pertenece a un préstamo)
    public function prestamo() {
        return $this->belongsTo(Prestamo::class, 'prestamo_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pago) {
            $dataMovimiento=[
                'movimiento_prestamo_id'=>$pago->prestamo->prestamo_id,
                'cajero_id_registro'=>$pago->usuario_id_registro,
                'monto_ingresar'=>$pago->monto,
                'tipo_transaccion'=>'pago_prestamo',
            ];
            Movimiento::insert($dataMovimiento);
        });
    }
    
}
