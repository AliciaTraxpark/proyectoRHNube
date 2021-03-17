<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class persona_verify extends Model
{
    //
    protected $table = 'persona_verify';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'tipo_documento',
        'tipo_verify',
        'numero_documento',
        'nombres',
        'apellidos',
        'id_empleado',
        'tipo_nacional',
        'id_consulta',
        'organi_id'
    ];
    public $timestamps = false;
}
