<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dispositivos extends Model
{
    //
    protected $table = 'dispositivos';
    protected $primaryKey = 'idDispositivos';
    protected $fillable = [
        'idDispositivos',
        'dispo_descripUbicacion',
        'dispo_movil',
        'dispo_tSincro',
        'dispo_tMarca',
        'dispo_codigo',
        'dispo_estado',
        'organi_id',
       'dispo_codigoNombre'
    ];
}