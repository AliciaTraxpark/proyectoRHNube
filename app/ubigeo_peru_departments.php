<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ubigeo_peru_departments extends Model
{
    protected $table = 'ubigeo_peru_departments';
    protected $primaryKey = 'id';
    protected $fillable = ['id','name'];
}
