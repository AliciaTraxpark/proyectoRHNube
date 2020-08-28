<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class condicion_pago extends Model
{
    protected $table = 'condicion_pago';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'condicion',
        'user_id',
        'organi_id'
    ];
    public $timestamps = false;
}
