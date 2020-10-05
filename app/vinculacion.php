<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class vinculacion extends Model
{
    //
    protected $table = 'vinculacion';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'idEmpleado', 'hash', 'descarga', 'fecha_descarga', 'envio', 'pc_mac', 'idModo', 'idLicencia','serieD'];
    public $timestamps = false;
}
