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
        'ubicacion_ini',
        'ubicacion_fin',
        'idUbicacion'
    ];
    public $timestamps = false;
}
