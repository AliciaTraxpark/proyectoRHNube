<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class controladores extends Model
{
    //
    protected $table = 'controladores';
    protected $primaryKey = 'idControladores';
    protected $fillable = [
        'idControladores',
        'cont_codigo',
        'cont_nombres',
        'cont_ApPaterno',
        'cont_ApMaterno',
        'cont_correo',
        'cont_estado',
        'organi_id'
    ];
}
