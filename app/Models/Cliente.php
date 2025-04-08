<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    //

    protected $table = 'clientes';
    protected $primaryKey = 'cliente_id';
    protected $fillable = [
        'nombre', 'identificacion', 'tipo_identificacion', 'fecha_nacimiento',
        'genero', 'direccion', 'telefono', 'es_socio','archivos'
    ];

    protected $casts = [
        'archivos' => 'array', // Para que Laravel maneje `archivos` como un array
    ];

    // Relación con cuentas (Un cliente puede tener muchas cuentas)
    public function cuentas() {
        return $this->hasMany(Cuenta::class, 'cliente_id');
    }

    // Relación con préstamos (Un cliente puede tener muchos préstamos)
    public function prestamos() {
        return $this->hasMany(Prestamo::class, 'cliente_id');
    }

    public function referencias_personales()
    {
        return $this->hasMany(ReferenciaPersonal::class, 'cliente_id');
    }
}
