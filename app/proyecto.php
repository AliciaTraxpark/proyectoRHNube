<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class proyecto extends Model
{
    //
    protected $table = 'proyecto';
    protected $primaryKey = 'Proye_id';
    protected $fillable = ['Proye_id','Proye_Nombre','Proye_Detalle'];
}
