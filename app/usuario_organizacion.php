<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class usuario_organizacion extends Model
{
    //
    protected $table = 'usuario_organizacion';
    protected $primaryKey = 'usua_orga_id';
    protected $fillable = ['usua_orga_id','user_id','organi_id'];
}
