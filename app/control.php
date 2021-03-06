<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class control extends Model
{
    protected $table = 'control';
    protected $primaryKey = 'Cont_id';
    protected $fillable = [
        'Cont_id',
        'idEmpleado',
        'idCaptura',
        'Proyecto_Proye_id',
        'fecha_ini',
        'Fecha_fin',
        'hora_ini',
        'hora_fin',
        'Tarea_Tarea_id ',
        'Actividad_Activi_id',
        'acumulado'
    ];
    public $timestamps = false;
}
