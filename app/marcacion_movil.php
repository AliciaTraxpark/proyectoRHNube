<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class marcacion_movil extends Model
{
    //
    protected $table = 'marcacion_movil';
    protected $primaryKey = 'marcaMov_id';
    protected $fillable = ['marcaMov_id','marcaMov_salida','marcaMov_fecha','marcaMov_emple_id',
    'controladores_idControladores','dispositivos_idDispositivos','organi_id','horarioEmp_id'];
    public $timestamps = false;
}
