<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TasaInteres extends Model
{
    //

    protected $table='tasas_interes';
    protected $primaryKey='tasa_id';

    protected $fillable=[
        'porcentaje',
        'tipo_tasa',
        'fecha_inicio',
        'fecha_fin',
        'destino', //enum('ahorro','prestamo') 
        'tipo_prestamo', //enum('automatico','personal','garantia') 
       // 'cuenta_id', //bigint(20) UN 
       // 'prestamo_id',// bigint(20) UN 
        

    ];

    // Relación con cuentas (Una tasa puede estar en muchas cuentas)
    public function cuentas() {
        return $this->hasMany(Cuenta::class, 'tasa_interes_id');
    }

    // Relación con cuentas (Una tasa puede estar en muchas prestamos)
    public function prestamos() {
        return $this->hasMany(Prestamo::class, 'tasa_interes_id');
    }
}
