<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tardanza extends Model
{
    //
    protected $table = 'tardanza';
    protected $primaryKey = 'idtardanza';
    protected $fillable = ['idtardanza','emple_id','fecha','tiempoTardanza'];
    public $timestamps = false;
}
