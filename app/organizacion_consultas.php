<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class organizacion_consultas extends Model
{
    //
    protected $table = 'organizacion_consultas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'fecha',
        'saldo_identidad',
        'saldo_policial',
        'saldo_penal',
        'saldo_crediticio',
        'organi_id'
    ];
    public $timestamps = false;
}
