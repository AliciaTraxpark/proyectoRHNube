<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class eventos_empleado extends Model
{
    protected $table = 'eventos_empleado';
    protected $primaryKey = 'evEmpleado_id';
    protected $fillable = ['evEmpleado_id',	'title', 'color', 'textColor', 'start',	'end',	'paises_id','ubigeo_peru_departments_id','id_empleado','tipo_ev','id_calendario'];
    public $timestamps = false;
}
