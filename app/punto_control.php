<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class punto_control extends Model
{
    //
    protected $table = 'punto_control';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'descripcion',
        'codigoControl',
        'controlRuta',
        'asistenciaPuerta',
        'porEmpleados',
        'porAreas',
        'organi_id',
        'estado',
        'ModoTareo'
    ];
    public $timestamps = false;
}
