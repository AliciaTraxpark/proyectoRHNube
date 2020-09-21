<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class nivel extends Model
{
    //
    protected $table = 'nivel';
    protected $primaryKey = 'nivel_id';
    protected $fillable = ['nivel_id','nivel_descripcion','organi_id'];
}
