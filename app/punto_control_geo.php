<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class punto_control_geo extends Model
{
    //
    protected $table = 'punto_control_geo';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idPuntoControl',
        'latitud',
        'longitud',
        'radio'
    ];
    public $timestamps = false;
}
