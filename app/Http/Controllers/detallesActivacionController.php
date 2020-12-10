<?php

namespace App\Http\Controllers;

use App\licencia_empleado;
use App\vinculacion;
use App\vinculacion_ruta;
use Illuminate\Http\Request;

class detallesActivacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function cambiarEstadoLicencia(Request $request)
    {
        $vinculacion = vinculacion::where('id', '=', $request->get('idV'))->get()->first();
        if ($vinculacion) {
            $vinculacion->pc_mac = NULL;
            $vinculacion->serieDisco = NULL;
            $vinculacion->save();
            $licencia = licencia_empleado::where('id', '=', $vinculacion->idLicencia)->get()->first();
            $licencia->disponible = 'i';
            $licencia->save();
            return json_encode(array("result" => true));
        }
    }

    public function cambiarEstadoAndroid(Request $request)
    {
        $vinculacion_ruta = vinculacion_ruta::where('id', '=', $request->get('idV'))->get()->first();
        if ($vinculacion_ruta) {
            $vinculacion_ruta->modelo = NULL;
            $vinculacion_ruta->disponible = 'i';
            $vinculacion_ruta->imei_androidID = NULL;
            $vinculacion_ruta->save();
            return json_encode(array("result" => true));
        }
    }
}
