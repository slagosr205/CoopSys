<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferenciaPersonal extends Model
{
    //
    protected $table = 'referencias_personales';

    protected $primaryKey='referencias_id';

    protected $fillable = [
        'cliente_id',
        'nombre_completo',
        'relacion',
        'fecha_nacimiento',
        'porcentaje_beneficio',
    ];

    /**
     * Relación con el modelo Cliente.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
