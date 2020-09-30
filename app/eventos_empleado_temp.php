<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class eventos_empleado_temp extends Model
{
    //
    protected $table = 'eventos_empleado_temp';
    protected $primaryKey = 'evEmpleadoT_id';
    protected $fillable = ['evEmpleadoT_id','title','color','textColor','start','end',
    'tipo_ev','users_id',	'calendario_calen_id','id_horario','fuera_horario','borderColor','organi_id'];
    public $timestamps = false;
}
