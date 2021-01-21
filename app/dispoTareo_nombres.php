<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dispoTareo_nombres extends Model
{
    //
    protected $table = 'dispotareo_nombres';
    protected $primaryKey = 'iddispoTareo_nombres';
    protected $fillable = [
        'iddispoTareo_nombres',
        'dispoT_CodigoNombre',
        'iddispositivos_tareo'
    ];
    public $timestamps = false;
}
