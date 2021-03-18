<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class crd extends Model
{
    //
    protected $table = 'crd';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'fecha_registro',
        'usuario',
        'clave',
        'estado'
    ];
    public $timestamps = false;
}
