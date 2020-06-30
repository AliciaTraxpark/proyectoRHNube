<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipo_dispositivo extends Model
{
    //
    protected $table = 'tipo_dispositivo';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'dispositivo_descripcion'];
    public $timestamps = false;
}
