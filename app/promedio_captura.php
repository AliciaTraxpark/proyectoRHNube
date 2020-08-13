<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class promedio_captura extends Model
{
    protected $table = 'promedio_captura';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'idCaptura', 'idHorario', 'promedio','tiempo_rango'];
    public $timestamps = false;
}
