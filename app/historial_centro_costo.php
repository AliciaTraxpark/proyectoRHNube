<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class historial_centro_costo extends Model
{
    protected $table = 'historial_centro_costo';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idCentro',
        'fecha_alta',
        'fecha_baja'
    ];
    public $timestamps = false;
}
