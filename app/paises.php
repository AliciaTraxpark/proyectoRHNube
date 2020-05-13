<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class paises extends Model
{
    protected $table = 'paises';
    protected $primaryKey = 'id';
    protected $fillable = ['id','iso','nombre'];
}
