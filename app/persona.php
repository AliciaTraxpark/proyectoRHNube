<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class persona extends Model
{
    //
    protected $table = 'persona';
    protected $primaryKey = 'perso_id';
    protected $fillable = ['perso_id','perso_nombre','perso_apPaterno','perso_apMaterno','perso_direccion','perso_fechaNacimiento','perso_sexo'];
}
