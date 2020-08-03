<?php

namespace App\Http\Controllers;

use App\licencia_empleado;
use App\modo;
use App\tipo_dispositivo;
use App\vinculacion;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $licencia = new licencia_empleado();
        $licencia->idEmpleado = $idEmpleado;
        $encodeLicencia = STR::random(20);
        $licencia->licencia = $encodeLicencia;
        $licencia->disponible = 'c';
        $licencia->save();
        $idLicencia = $licencia->id;
        $vinculacion = new vinculacion();
        $codigo = intval($encodeLicencia, 36);
        $vinculacion->hash = $codigo;
        $vinculacion->idEmpleado = $idEmpleado;
        $vinculacion->envio = 0;
        $vinculacion->idModo = $idModo;
        $vinculacion->idLicencia = $idLicencia;
        $vinculacion->save();

        $idVinculacion = $vinculacion->id;

        $tipo_modo = tipo_dispositivo::where('id', '=', 2)->get()->first();
        $respuesta = [];
        $respuesta['dispositivo_descripcion'] = $tipo_modo->dispositivo_descripcion;
        $respuesta['licencia'] = $encodeLicencia;
        $respuesta['codigo'] = $codigo;
        $respuesta['envio'] = 0;
        $respuesta['idVinculacion'] = $idVinculacion;
        return response()->json($respuesta, 200);
    }

    public function vinculacionWindows(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        //MODO
        $modo = new modo();
        $modo->idTipoModo = 1;
        $modo->idTipoDispositivo = 1;
        $modo->idEmpleado = $idEmpleado;
        $modo->save();
        $idModo = $modo->id;
        //LICENCIA
        $codigoEmpresa = DB::table('users as u')
            ->join('usuario_organizacion as uo', 'uo.user_id', '=', 'u.id')
            ->select('uo.organi_id')
            ->where('u.id', '=', Auth::user()->id)
            ->get();
        $codigoEmpleado = DB::table('empleado as e')
            ->select('e.emple_codigo', 'e.emple_persona', 'e.created_at')
            ->where('e.emple_id', '=', $idEmpleado)
            ->get();
        $nuevaLivencia = STR::random(4);
        $codigoLicencia = $idEmpleado . '.' . $codigoEmpleado[0]->created_at . $codigoEmpresa[0]->organi_id . $nuevaLivencia;
        $encodeLicencia = rtrim(strtr(base64_encode($codigoLicencia), '+/', '-_'));
        $licencia = new licencia_empleado();
        $licencia->idEmpleado = $idEmpleado;
        $licencia->licencia = $encodeLicencia;
        $licencia->disponible = 'c';
        $licencia->save();
        $idLicencia = $licencia->id;
        //VINCULACION
        $codigoU = Auth::user()->id;
        $diferente = STR::random(4);
        $codigoHash = $codigoU . "s" . $codigoEmpresa[0]->organi_id . $idEmpleado . $diferente;
        $encode = intval($codigoHash, 36);
        $vinculacion = new vinculacion();
        $vinculacion->idEmpleado = $idEmpleado;
        $vinculacion->hash = $encode;
        $vinculacion->envio = 0;
        $vinculacion->idModo = $idModo;
        $vinculacion->idLicencia = $idLicencia;
        $vinculacion->save();

        $idVinculacion = $vinculacion->id;

        $tipo_modo = tipo_dispositivo::where('id', '=', 1)->get()->first();
        $respuesta = [];
        $respuesta['dispositivo_descripcion'] = $tipo_modo->dispositivo_descripcion;
        $respuesta['licencia'] = $encodeLicencia;
        $respuesta['codigo'] = $encode;
        $respuesta['envio'] = 0;
        $respuesta['idVinculacion'] = $idVinculacion;

        return response()->json($respuesta, 200);
    }
}
