<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class reglas_horasextras extends Model
{
    //
    protected $table = 'reglas_horasextras';
    protected $primaryKey = 'idreglas_horasExtras';
    protected $fillable = [
        'idreglas_horasExtras',
        'idTipoRegla',
        'tipo_regla',
        'reglas_descripcion',
        'lleno25',
        'lleno35',
        'lleno100',
        'activo',
        'organi_id'
    ];
    public $timestamps = false;
}
