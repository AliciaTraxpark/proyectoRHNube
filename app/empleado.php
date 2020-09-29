<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class empleado extends Model
{
    //
    protected $table = 'empleado';
    protected $primaryKey = 'emple_id';
    protected $fillable = [
        'emple_id',
        'emple_tipoDoc',
        'emple_nDoc',
        'emple_persona',
        'emple_departamento',
        'emple_provincia',
        'emple_distrito',
        'emple_cargo',
        'emple_area',
        'emple_centCosto',
        'emple_departamentoN',
        'emple_provinciaN',
        'emple_distritoN',
        'emple_local',
        'emple_nivel',
        'emple_foto',
        'emple_pasword',
        'emple_Correo',
        'emple_celular',
        'emple_telefono',
        'emple_estado',
        'users_id',
        'emple_codigo',
        'id_contrato',
        'asistencia_puerta',
        'organi_id'
    ];
}
