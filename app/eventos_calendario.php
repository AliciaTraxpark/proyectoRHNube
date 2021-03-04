<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class eventos_calendario extends Model
{
    //
    protected $table = 'eventos_calendario';
    protected $primaryKey = 'id';
    protected $fillable = ['id',
                        'color',
                        'textColor',
                        'start',
                        'end',
                        'users_id',
                        'id_calendario',
                        'organi_id',
                        'laborable','inciden_id'];
}
