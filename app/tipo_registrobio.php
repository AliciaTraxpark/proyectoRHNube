<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipo_registrobio extends Model
{
    //
    protected $table = 'tipo_registrobio';
    protected $primaryKey = 'idtipo_registro';
    protected $fillable = ['idtipo_registro','descripcion'];
    public $timestamps = false;
}
