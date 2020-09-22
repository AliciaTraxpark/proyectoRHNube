<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class local extends Model
{
    //
    protected $table = 'local';
    protected $primaryKey = 'local_id';
    protected $fillable = ['local_id','local_descripcion','organi_id'];
}
