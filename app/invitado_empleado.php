<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class invitado_empleado extends Model
{
    protected $table = 'invitado_empleado';
    protected $primaryKey = 'idinvitado_empleado';
    protected $fillable = [
        'idinvitado_empleado',
        'idinvitado',
        'emple_id',
        'area_id'
    ];
    public $timestamps = false;
}
