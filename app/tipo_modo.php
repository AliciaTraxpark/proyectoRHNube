<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipo_modo extends Model
{
    //
    protected $table = 'tipo_modo';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'modo_descripcion'];
    public $timestamps = false;
}
