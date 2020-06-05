<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class envio extends Model
{
    protected $table = 'envio';
    protected $primaryKey = 'idEnvio';
    protected $fillable = ['idEnvio','hora_Envio','Total_Envio','idEmpleado'];
}
