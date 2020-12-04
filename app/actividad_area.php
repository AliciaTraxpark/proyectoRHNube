<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class actividad_area extends Model
{
    protected $table = 'actividad_area';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idArea',
        'idActividad',
        'estado'
    ];
    public $timestamps = false;
}
