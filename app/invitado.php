<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class invitado extends Model
{
    protected $table = 'invitado';
    protected $primaryKey = 'idinvitado';
    protected $fillable = [
        'idinvitado',
        'organi_id',
        'rol_id',
        'email_inv',
        'estado',
        'users_id',
        'user_Invitado',
        'dashboard',
        'estado_condic',
        'permiso_Emp',
        'verTodosEmps',
        'modoCR',
        'gestionActiv',
        'asistePuerta',
        'reporteAsisten',
        'ControlRuta',
        'ModificarReportePuerta',
        'extractorRH'
    ];

}
