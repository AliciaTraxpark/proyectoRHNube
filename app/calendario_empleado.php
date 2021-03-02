<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class calendario_empleado extends Model
{
    //
    protected $table = 'calendario_empleado';
    protected $primaryKey = 'idcalendario_empleado';
    protected $fillable = [
        'idcalendario_empleado',
        'emple_id',
        'calen_id'
    ];
    public $timestamps = false;
}
