<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class modo extends Model
{
    //
    protected $table = 'modo';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'idEmpleado','idTipoModo','idTipoDispositivo'];
    public $timestamps = false;
}
