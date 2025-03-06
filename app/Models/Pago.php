<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    //
    protected $table = 'pagos';
    protected $primaryKey = 'pago_id';
    protected $fillable = ['prestamo_id', 'monto_pago', 'fecha_pago'];

    // Relación con préstamo (Un pago pertenece a un préstamo)
    public function prestamo() {
        return $this->belongsTo(Prestamo::class, 'prestamo_id');
    }
}
