<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class incidencia_dias extends Model
{
    //
    protected $table = 'incidencia_dias';
    protected $primaryKey = 'inciden_dias_id';
    protected $fillable = ['inciden_dias_id','id_incidencia','inciden_dias_fechaI','inciden_dias_fechaF','id_empleado'];
    public $timestamps = false;
}
