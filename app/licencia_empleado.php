<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class licencia_empleado extends Model
{
    //
    protected $table = 'licencia_empleado';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'idEmpleado', 'licencia', 'disponible'];
    public $timestamps = false;
}
