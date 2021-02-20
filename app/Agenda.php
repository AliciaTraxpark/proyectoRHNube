<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agenda';
    protected $primaryKey = 'id';
    protected $fillable = ['nombres','telefono','correo', 'empresa', 'cargo', 'colaboradores', 'fecha', 'comentario'];
}
