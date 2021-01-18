<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dispositivo_empleado extends Model
{
    //
    protected $table = 'dispositivo_empleado';
    protected $primaryKey = 'iddispositivo_empleado';
    protected $fillable = ['iddispositivo_empleado',
    'idDispositivos','emple_id'];
    public $timestamps = false;
}
