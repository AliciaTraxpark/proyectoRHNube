<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipo_contrato extends Model
{
    //
    protected $table = 'tipo_contrato';
    protected $primaryKey = 'contrato_id';
    protected $fillable = ['contrato_id', 'contrato_descripcion'];
    public $timestamps = false;
}
