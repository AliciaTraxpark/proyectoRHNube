<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class reporte_marcacionesp extends Model
{
    //
    protected $table = 'reporte_marcacionesp';
    protected $primaryKey = 'idmarcacionesP_tipo';
    protected $fillable = ['idmarcacionesP_tipo','marcacion_entrada','marcacion_salida',
    'horasSitio', 'marcaMov_id'];
    public $timestamps = false;
}
