<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipo_biometrico extends Model
{
    //
    protected $table = 'tipo_biometrico';
    protected $primaryKey = 'idtipo_biometrico';
    protected $fillable = ['idtipo_biometrico', 'cod_tipo','nombre_tipo'];
    public $timestamps = false;
}
