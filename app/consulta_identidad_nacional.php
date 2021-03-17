<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class consulta_identidad_nacional extends Model
{
    //
    protected $table = 'consulta_identidad_nacional';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'type_documento',
        'numero_documento',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'genero',
        'estado_civil',
        'fecha_nacimiento',
        'edad',
        'codigo_verificacion',
        'nombre_padre',
        'nombre_madre',
        'ubigeo_nacimiento',
        'codigo_ubigeo_nacimiento',
        'direccion',
        'direccion_ubigueo',
        'foto_perfil'
    ];
    public $timestamps = false;
}
