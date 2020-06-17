<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class temporal_eventos extends Model
{
    //
    protected $table = 'temporal_eventos';
    protected $primaryKey = 'tempEv_id';
    protected $fillable = ['tempEv_id','title','color',	'textColor','start','end','users_id'];
    public $timestamps = false;
}
