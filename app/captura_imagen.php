<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class captura_imagen extends Model
{
    protected $table = 'captura_imagen';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idCaptura',
        'miniatura',
        'imagen'
    ];
    public $timestamps = false;
}
