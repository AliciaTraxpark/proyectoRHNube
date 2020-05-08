<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ubigeo_peru_provinces extends Model
{
    protected $table = 'ubigeo_peru_provinces';
    protected $primaryKey = 'id';
    protected $fillable = ['id','name','departamento_id'];
}
