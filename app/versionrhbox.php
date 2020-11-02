<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class versionrhbox extends Model
{
    //
    protected $table = 'versionrhbox';
    protected $primaryKey = 'id';
    protected $fillable = ['id','descripcion','fechaActualizacion'];
    public $timestamps = false;
}
