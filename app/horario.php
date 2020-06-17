<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class horario extends Model
{
    //
    protected $table = 'horario';
    protected $primaryKey = 'horario_id';
    protected $fillable = ['horario_id','horario_sobretiempo','horario_tipo','horario_descripcion','horario_tolerancia'];
    public $timestamps = false;
}
