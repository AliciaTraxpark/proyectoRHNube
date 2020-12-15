<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class historial_empleado extends Model
{
    //
    protected $table = 'historial_empleado';
    protected $primaryKey = 'idhistorial_empleado';
    protected $fillable = [
        'idhistorial_empleado',
        'emple_id',
        'fecha_alta',
        'fecha_baja',
        'idContrato'
    ];
    public $timestamps = false;
}
