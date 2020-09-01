<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class captura extends Model
{
    protected $table = 'captura';
    protected $primaryKey = 'idCaptura';
    protected $fillable = [
        'idCaptura',
        'estado',
        'imagen',
        'actividad',
        'hora_ini',
        'hora_fin',
        'ultimo_acumulado',
        'acumulador',
        'idHorario_dias'
    ];
    public $timestamps = false;
}
