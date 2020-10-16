<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class actividad extends Model
{
    protected $table = 'actividad';
    protected $primaryKey = 'Activi_id';
    protected $fillable = [
        'Activi_id',
        'Activi_Nombre',
        'empleado_emple_id',
        'controlRemoto',
        'asistenciaPuerta',
        'estado',
        'eliminacion',
        'organi_id',
        'codigoActividad'
    ];
    public $timestamps = false;
}
