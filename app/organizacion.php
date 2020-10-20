<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class organizacion extends Model
{
    protected $table = 'organizacion';
    protected $primaryKey = 'organi_id';
    protected $fillable = ['organi_id','organi_ruc','organi_razonSocial','organi_direccion',
                        'organi_departamento','organi_provincia','organi_distrito','organi_nempleados',
                        'organi_pagWeb','organi_tipo','organi_estado'];
}
