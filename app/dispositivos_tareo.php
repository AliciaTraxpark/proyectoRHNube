<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dispositivos_tareo extends Model
{
    protected $table = 'dispositivos_tareo';
    protected $primaryKey = 'iddispositivos_tareo';
    protected $fillable = [
        'iddispositivos_tareo', 'tipo_dispositivo_id',
        'dispoT_descripUbicacion',
        'dispoT_movil',
        'dispoT_tSincro',
        'dispoT_tMarca',
        'dispoT_codigo',
        'dispoT_estado',
        'organi_id',
       'dispoT_Data',
       'dispoT_Manu',
       'dispoT_Scan',
       'dispoT_Cam',
       'dispoT_estadoActivo'

    ];
}
