<?php

namespace App\Http\Controllers;

use App\empleado;
use App\licencia_empleado;
use App\vinculacion;
use Illuminate\Http\Request;

class detallesActivacionController extends Controller
{
    public function cambiarEstadoLicencia(Request $request)
    {
        $vinculacion = vinculacion::where('id', '=', $request->get('idV'))->get()->first();
        if ($vinculacion) {
            $empleado = empleado::where('emple_id', '=', $request->get('idE'))->get()->first();
            $licencia = licencia_empleado::where('id', '=', $vinculacion->idLicencia)->get()->first();
            $licencia->disponible = 'i';
            $licencia->save();
            return json_encode(array("result" => true));
        }
    }
}
