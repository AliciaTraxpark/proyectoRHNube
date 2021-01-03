<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ubicacion_sin_procesar extends Model
{
    //
    protected $table = 'ubicacion_sin_procesar';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'hora_ini',
        'hora_fin',
        'idHorario_dias',
        'idActividad',
        'idEmpleado',
        'actividad_ubicacion',
        'rango',
        'latitud_ini',
        'longitud_ini',
        'latitud_fin',
        'longitud_fin'
    ];
    public $timestamps = false;
}
