<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class vinculacion extends Model
{
    //
    protected $table = 'vinculacion';
    protected $primaryKey = 'id';
    protected $fillable = ['id','idEmpleado', 'hash', 'descarga', 'fecha_descarga','envio', 'reenvio','pc_mac'];
    public $timestamps = false;
}
