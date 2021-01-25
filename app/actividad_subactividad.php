<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class actividad_subactividad extends Model
{
    //
    protected $table = 'actividad_subactividad';
    protected $primaryKey = 'idactividad_subactividad';
    protected $fillable = [
        'idactividad_subactividad',
        'Activi_id',
        'subActividad',
        'estado'
    ];
    public $timestamps = false;
}
