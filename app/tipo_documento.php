<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipo_documento extends Model
{
    //
    protected $table = 'tipo_documento';
    protected $primaryKey = 'tipoDoc_id';
    protected $fillable = ['tipoDoc_id','tipoDoc_descripcion'];
}
