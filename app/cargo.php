<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cargo extends Model
{
    protected $table = 'cargo';
    protected $primaryKey = 'cargo_id';
    protected $fillable = ['cargo_id','cargo_descripcion','organi_id'];
}
