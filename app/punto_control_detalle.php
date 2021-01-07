<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class punto_control_detalle extends Model
{
    //
    protected $table = 'punto_control_detalle';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idPuntoControl',
        'descripcion'
    ];
    public $timestamps = false;
}
