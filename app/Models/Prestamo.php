<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    //
    protected $table = 'prestamos';
    protected $primaryKey = 'prestamo_id';
    protected $fillable = [
        'cliente_id', 'monto_solicitado', 'fecha_solicitud',
        'estado', 'fecha_de_aprobacion', 'monto_aprobado',
        'tasa_interes_id','comentarios',
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
}
