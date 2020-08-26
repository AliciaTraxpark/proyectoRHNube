<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tarea extends Model
{
    protected $table = 'tarea';
    protected $primaryKey = 'Tarea_id';
    protected $fillable = ['Tarea_id','Tarea_Nombre','Proyecto_Proye_id','empleado_emple_id','estado'];
    public $timestamps = false;
}
