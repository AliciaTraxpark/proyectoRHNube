<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class centrocosto_empleado extends Model
{
    //
    protected $table = 'centrocosto_empleado';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idCentro',
        'idEmpleado',
        'fecha_alta',
        'fecha_baja',
        'estado'
    ];
}
