<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class historial_horarioempleado extends Model
{
    //
    protected $table = 'historial_horarioempleado';
    protected $primaryKey = 'idhistorial_horarioEmpleado';
    protected $fillable = [
        'idhistorial_horarioEmpleado',
        'horarioEmp_id',
        'fechaCambio',
        'estadohorarioEmp'
    ];
    public $timestamps = false;
}
