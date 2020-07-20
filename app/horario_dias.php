<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class horario_dias extends Model
{
    //
    protected $table = 'horario_dias';
    protected $primaryKey = 'id';
    protected $fillable = ['id','title','color','textColor','start','end','users_id','paises_id','ubigeo_peru_departments_id'];
    public $timestamps = false;
}
