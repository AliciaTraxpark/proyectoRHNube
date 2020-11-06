<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ubicacion_ruta extends Model
{
    //
    protected $table = 'ubicacion_ruta';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'latitud_ini',
        'longitud_ini',
        'latitud_fin',
        'longitud_fin',
        'idUbicacion'
    ];
    public $timestamps = false;
}
