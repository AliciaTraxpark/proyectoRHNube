<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class proyecto_empleado extends Model
{
    //
    protected $table = 'proyecto_empleado';
    protected $primaryKey = 'Proyecto_Proye_id';
    protected $fillable = ['Proyecto_Proye_id','empleado_emple_id ','Fecha_Ini','Fecha_Fin'];
    public $timestamps = false;
}
