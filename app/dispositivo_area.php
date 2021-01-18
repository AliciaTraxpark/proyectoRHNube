<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dispositivo_area extends Model
{
    //
    protected $table = 'dispositivo_area';
    protected $primaryKey = 'iddispositivo_area';
    protected $fillable = ['iddispositivo_area',
    'idDispositivos','area_id'];
    public $timestamps = false;
}
