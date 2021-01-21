<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class plantilla_empleadobio extends Model
{
    //
    protected $table = 'plantilla_empleadobio';
    protected $primaryKey = 'id';
    protected $fillable = ['id','idempleado','posicion_huella','tipo_registro','path'];
    public $timestamps = false;
}
