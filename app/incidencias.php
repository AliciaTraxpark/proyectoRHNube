<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class incidencias extends Model
{
    //
    protected $table = 'incidencias';
    protected $primaryKey = 'inciden_id';
    protected $fillable = ['inciden_id','inciden_descripcion','inciden_descuento',	'inciden_dias_id','emple_id'];
}
