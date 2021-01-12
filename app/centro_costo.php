<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class centro_costo extends Model
{
    protected $table = 'centro_costo';
    protected $primaryKey = 'centroC_id';
    protected $fillable = ['centroC_id', 'centroC_descripcion', 'organi_id', 'estado'];
}
