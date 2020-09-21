<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class temporal_eventos extends Model
{
    //
    protected $table = 'temporal_eventos';
    protected $primaryKey = 'id';
    protected $fillable = ['id','title','color','textColor','start','end',
    'users_id','temp_horaI','temp_horaF','id_horario','fuera_horario',
      'borderColor'];
    public $timestamps = false;
}
