<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class incidencias extends Model
{
    //
    protected $table = 'incidencias';
    protected $primaryKey = 'inciden_id';
    protected $fillable = ['inciden_id',
                           'idtipo_incidencia',
                           'inciden_codigo',
                            'inciden_descripcion',
                            'inciden_pagado',
                            'inciden_hora',
                            'users_id',
                            'organi_id',
                            'estado',
                            'sistema'];
}
