<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class incidencia_dias extends Model
{
    //
    protected $table = 'incidencia_dias';
    protected $primaryKey = 'inciden_dias_id';
    protected $fillable = ['inciden_dias_id','inciden_dias_fechaI','inciden_dias_fechaF','inciden_dias_hora'];
    public $timestamps = false;
}
