<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipo_incidencia extends Model
{
    //
    protected $table = 'tipo_incidencia';
    protected $primaryKey = 'idtipo_incidencia';
    protected $fillable = ['idtipo_incidencia',
                           'tipoInc_codigo',
                           'tipoInc_descripcion',
                           'tipoInc_activo',
                           'sistema',
                            'organi_id'];
    public $timestamps = false;
}
