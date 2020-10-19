<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class software_vinculacion extends Model
{
    //
    protected $table = 'software_vinculacion';
    protected $primaryKey = 'id';
    protected $fillable = ['id','version','comentario','fechaActualizacion'];
    public $timestamps = false;
}
