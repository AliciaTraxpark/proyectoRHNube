<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class horario_empleado extends Model
{
    //
    protected $table = 'horario_empleado';
    protected $primaryKey = 'horarioEmp_id';
    protected $fillable = ['horarioEmp_id',	'horario_horario_id','empleado_emple_id',
    'horario_dias_id','fuera_horario','borderColor','horarioComp','horaAdic','nHoraAdic'];
    public $timestamps = false;
}
