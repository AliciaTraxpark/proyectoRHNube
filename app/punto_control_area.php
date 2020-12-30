<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class punto_control_area extends Model
{
    //
    protected $table = 'punto_control_area';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idPuntoControl',
        'idArea',
        'estado'
    ];
    public $timestamps = false;
}
