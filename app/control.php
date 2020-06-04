<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class control extends Model
{
    protected $table = 'control';
    protected $primaryKey = 'Cont_id';
    protected $fillable = ['Cont_id','Proyecto_Proye_id','fecha i','Fecha f','hora i','hora f',
                            'Imag'];
}
