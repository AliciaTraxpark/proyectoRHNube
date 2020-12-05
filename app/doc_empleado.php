<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class doc_empleado extends Model
{
    //
    protected $table = 'doc_empleado';
    protected $primaryKey = 'iddoc_empleado';
    protected $fillable = ['iddoc_empleado','idhistorial_empleado','rutaDocumento'];
    public $timestamps = false;
}
