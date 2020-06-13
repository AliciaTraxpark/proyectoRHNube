<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class captura extends Model
{
    protected $table = 'captura';
    protected $primaryKey = 'idCaptura';
    protected $fillable = ['idCaptura','idEnvio','estado','fecha_hora','imagen','promedio'];
    public $timestamps =false;
}
