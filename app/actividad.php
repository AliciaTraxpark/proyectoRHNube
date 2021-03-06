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
        'controlRemoto',
        'controlRuta',
        'asistenciaPuerta',
        'estado',
        'eliminacion',
        'organi_id',
        'codigoActividad',
        'porEmpleados',
        'porAreas',
        'globalEmpleado',
        'globalArea',
        'modoTareo'
    ];
    public $timestamps = false;
}
