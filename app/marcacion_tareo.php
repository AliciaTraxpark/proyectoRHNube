<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class marcacion_tareo extends Model
{
    //
    protected $table = 'marcacion_tareo';
    protected $primaryKey = 'idmarcaciones_tareo';
    protected $fillable = ['idmarcaciones_tareo','marcaTareo_entrada',
    'marcaTareo_idempleado',
    'idcontroladores_tareo','iddispositivos_tareo','organi_id',
    'horarioEmp_id','marcaTareo_salida',
    'marcaTareo_latitud','marcaTareo_longitud',
    'Activi_id','puntoC_id','centroC_id','idsubActividad'];
    public $timestamps = false;
}
