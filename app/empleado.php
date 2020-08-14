<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class empleado extends Model
{
    //
    protected $table = 'empleado';
    protected $primaryKey = 'emple_id';
    protected $fillable = ['emple_id','emple_tipoDoc','emple_nDoc',
    'emple_persona','emple_departamento','emple_provincia','emple_distrito','emple_cargo','emple_area','emple_Correo',
        'emple_centCosto','emple_departamentoN','emple_provinciaN',	'emple_distritoN','emple_tipoContrato',
        'emple_celular','emple_telefono',
        'emple_local','emple_estado','emple_nivel','emple_foto','users_id','emple_pasword'];
}
