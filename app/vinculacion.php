<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class vinculacion extends Model
{
    //
    protected $table = 'vinculacion';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'hash', 'estado', 'envio', 'reenvio'];
    public $timestamps = false;
}
