<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class subactividad extends Model
{
    //
    protected $table = 'subactividad';
    protected $primaryKey = 'idsubActividad';
    protected $fillable = [
        'idsubActividad',
        'subAct_nombre',
        'subAct_codigo',
        'estado',
        'modoTareo',
        'organi_id'
    ];
    public $timestamps = false;
}
