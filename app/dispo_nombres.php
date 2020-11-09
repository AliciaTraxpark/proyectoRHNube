<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dispo_nombres extends Model
{
    //
    protected $table = 'dispo_nombres';
    protected $primaryKey = 'idDispo_codigos';
    protected $fillable = [
        'idDispo_codigos',
        'dispo_CodigoNombre',
        'idDispositivos'
    ];
    public $timestamps = false;
}
