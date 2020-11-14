<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ubicacion extends Model
{
    //
    protected $table = 'ubicacion';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'hora_ini',
        'hora_fin',
        'idHorario_dias',
        'idActividad',
        'idEmpleado',
        'actividad_ubicacion'
    ];
    public $timestamps = false;
}
