<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pausas_horario extends Model
{
    //
    protected $table = 'pausas_horario';
    protected $primaryKey = 'idpausas_horario';
    protected $fillable = ['idpausas_horario', 'pausH_descripcion', 'pausH_Inicio',
     'pausH_Fin','horario_id'];
    public $timestamps = false;
}
