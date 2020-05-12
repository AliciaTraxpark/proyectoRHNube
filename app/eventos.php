<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class eventos extends Model
{
    //
    protected $table = 'eventos';
    protected $primaryKey = 'id';
    protected $fillable = ['id','title','color','textColor','start','end','tipo'];

}

