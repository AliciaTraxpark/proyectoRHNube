<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class actividad extends Model
{
    protected $table = 'actividad';
    protected $primaryKey = 'Activi_id';
    protected $fillable = ['Activi_id','Activi_Nombre','Tarea_Tarea_id','empleado_emple_id'];
    public $timestamps = false;
}
