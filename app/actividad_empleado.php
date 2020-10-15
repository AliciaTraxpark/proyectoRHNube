<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class actividad_empleado extends Model
{
    protected $table = 'actividad_empleado';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idActividad',
        'idEmpleado',
        'estado',
        'eliminacion'
    ];
    public $timestamps = false;
}
