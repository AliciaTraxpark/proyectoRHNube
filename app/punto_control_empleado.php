<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class punto_control_empleado extends Model
{
    //
    protected $table = 'punto_control_empleado';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idPuntoControl',
        'idEmpleado',
        'estado'
    ];
    public $timestamps = false;
}
