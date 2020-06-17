<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class temporal_eventos extends Model
{
    //
    protected $table = 'temporal_eventos';
    protected $primaryKey = 'id';
    protected $fillable = ['id','title','color',	'textColor','start','end','users_id','paises_id','ubigeo_peru_departments_id','temp_horaI','temp_horaF'];
    public $timestamps = false;
}
