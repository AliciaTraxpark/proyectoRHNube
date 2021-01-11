<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class marcacion_biometrico extends Model
{
    //
    protected $table = 'marcacion_biometrico';
    protected $primaryKey = 'idmarcacion_biometrico';
    protected $fillable = ['idmarcacion_biometrico','tipoMarcacion','fechaMarcacion',
    'idEmpleado','idDisposi','organi_id','idHoraEmp'];
    public $timestamps = false;
}
