<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class eventos_usuario extends Model
{
    //
    protected $table = 'eventos_usuario';
    protected $primaryKey = 'id';
    protected $fillable = ['id','title','color','textColor','start','end','tipo','users_id','id_calendario','organi_id'];
}
