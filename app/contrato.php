<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class contrato extends Model
{
    protected $table = 'contrato';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_tipoContrato',
        'id_condicionPago',
        'fechaInicio',
        'fechaFinal',
        'monto',
        'idEmpleado',
        'estado'
    ];
    public $timestamps = false;
}
