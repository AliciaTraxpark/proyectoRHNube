<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class area extends Model
{
    //
    protected $table = 'area';
    protected $primaryKey = 'area_id';
    protected $fillable = ['area_id','area_descripcion'];
}
