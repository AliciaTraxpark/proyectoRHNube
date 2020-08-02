<?php

namespace App\Http\Controllers;

use App\modo;
use App\tipo_dispositivo;
use App\vinculacion;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class vinculacionDispositivoController extends Controller
{
    public function vinculacionAndroid(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $modo = new modo();
        $modo->idTipoModo = 1;
        $modo->idTipoDispositivo = 2;
        $modo->idEmpleado = $idEmpleado;
        $modo->save();
        $idModo = $modo->id;
        $vinculacion = new vinculacion();
        $vinculacion->idEmpleado = $idEmpleado;
        $vinculacion->envio = 0;
        $vinculacion->idModo = $idModo;
        $vinculacion->save();

        $idVinculacion = $vinculacion->id;

        

        $tipo_modo = tipo_dispositivo::where('id', '=', 2)->get()->first();
        $licencia = STR::random(20);
        $codigo = intval($licencia, 36);
        $tipo_modo->licencia = $licencia;
        $tipo_modo->codigo = $codigo;
        $tipo_modo->envio = $vinculacion->envio;
        $tipo_modo->idVinculacion = $idVinculacion;
        return response()->json($tipo_modo, 200);
    }
}
