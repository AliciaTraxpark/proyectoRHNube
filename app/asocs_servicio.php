<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class asocs_servicio extends Model
{
    //
    protected $table = 'asocs_servicio';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'tipo', 'codigo', 'activo'];
    public $timestamps = false;
}
