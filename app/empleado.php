<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class empleado extends Model
{
    //
    protected $table = 'empleado';
    protected $primaryKey = 'emple_id';
    protected $fillable = ['emple_id','emple_tipoDoc','emple_nDoc',
    'emple_persona','emple_cargo','emple_area',
    	'emple_centCosto'];
}
