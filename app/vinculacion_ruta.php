<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class vinculacion_ruta extends Model
{
    //
    protected $table = 'vinculacion_ruta';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idEmpleado',
        'hash',
        'fecha_envio',
        'envio',
        'modelo',
        'idModo',
        'celular',
        'imei_androidID',
        'actividad',
        'disponible'
    ];
    public $timestamps = false;
}
