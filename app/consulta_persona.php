<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class consulta_persona extends Model
{
    //
    protected $table = 'consulta_persona';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_identidad_nacional',
        'id_identidad_extranjeria',
        'estado_policial',
        'estado_penal',
        'estado_judicial',
        'estado_crediticio'
    ];
    public $timestamps = false;
}
