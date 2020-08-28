<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class calendario extends Model
{
    //
    protected $table = 'calendario';
    protected $primaryKey = 'calen_id';
    protected $fillable = ['calen_id', 'users_id','calendario_nombre','organi_id'];
}
