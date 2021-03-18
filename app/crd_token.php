<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class crd_token extends Model
{
    //
    protected $table = 'crd_token';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_crd',
        'token',
        'token_type',
        'fecha'
    ];
    public $timestamps = false;
}
