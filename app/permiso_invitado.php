<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class permiso_invitado extends Model
{
    //
    protected $table = 'permiso_invitado';
    protected $primaryKey = 'idpermiso_invitado';
    protected $fillable = ['idpermiso_invitado', 'idinvitado',	'agregarEmp', 'modifEmp',
        'bajaEmp', 'GestActEmp', 'agregarActi',	'modifActi', 'agregarContract',	'modifContract', 'agregarHb',	'modifHb', 'bajaActi', 'verPuerta',
        'agregarPuerta', 'modifPuerta','verModoTareo','agregarModoTareo','modifModoTareo'];
    public $timestamps = false;
}
