<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class marcacion_puerta extends Model
{
    //
    protected $table = 'marcacion_puerta';
    protected $primaryKey = 'marcaMov_id';
    protected $fillable = ['marcaMov_id','marcaMov_emple_id ','tipoMarcacion','marcaMov_fecha',
    'horarioEmp_id','controladores_idControladores','dispositivos_idDispositivos','organi_id',
    'marca_latitud','marca_longitud'];
    public $timestamps = false;
}
