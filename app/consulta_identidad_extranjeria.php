<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class consulta_identidad_extranjeria extends Model
{
    //
    protected $table = 'consulta_identidad_extranjeria';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'type_documento',
        'numero_documento',
        'nombres',
        'apellidos'
    ];
    public $timestamps = false;
}
