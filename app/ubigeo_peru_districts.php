<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ubigeo_peru_districts extends Model
{
    protected $table = 'ubigeo_peru_districts';
    protected $primaryKey = 'id';
    protected $fillable = ['id','name','province_id'];
}
