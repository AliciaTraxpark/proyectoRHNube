<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class proyecto_empleado extends Model
{
    //
    protected $table = 'proyecto_empleado';
    protected $primaryKey = 'proye_empleado_id';
    protected $fillable = ['proye_empleado_id','Proyecto_Proye_id','empleado_emple_id ','Fecha_Ini','Fecha_Fin'];
    public $timestamps = false;
}
