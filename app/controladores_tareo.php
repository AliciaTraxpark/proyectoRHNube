<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class controladores_tareo extends Model
{
    //
    protected $table = 'controladores_tareo';
    protected $primaryKey = 'idcontroladores_tareo';
    protected $fillable = [
        'idcontroladores_tareo',
        'contrT_codigo',
        'contrT_nombres',
        'contrT_ApPaterno',
        'contrT_ApMaterno',
        'contrT_correo',
        'contrT_estado',
        'organi_id'
    ];
}
