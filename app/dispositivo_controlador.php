<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dispositivo_controlador extends Model
{
    //
    protected $table = 'dispositivo_controlador';
    protected $primaryKey = 'idDispositivo_controlador';
    protected $fillable = ['idDispositivo_controlador',
    'idDispositivos','idControladores','organi_id'];
    public $timestamps = false;
}
