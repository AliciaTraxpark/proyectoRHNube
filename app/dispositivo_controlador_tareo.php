<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dispositivo_controlador_tareo extends Model
{
    //
    protected $table = 'dispositivo_controlador_tareo';
    protected $primaryKey = 'iddispositivo_controlador_tareo';
    protected $fillable = ['iddispositivo_controlador_tareo',
    'iddispositivos_tareo','idcontroladores_tareo','organi_id'];
    public $timestamps = false;
}
