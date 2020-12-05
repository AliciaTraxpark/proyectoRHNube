<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class historial_empleado extends Model
{
    //
    protected $table = 'historial_empleado';
    protected $primaryKey = 'idhistorial_empleado';
    protected $fillable = ['idhistorial_empleado','emple_id','tipo_Hist','fecha_historial'];
    public $timestamps = false;
}
