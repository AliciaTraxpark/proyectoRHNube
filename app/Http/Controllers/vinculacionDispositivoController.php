<?php

namespace App\Http\Controllers;

use App\licencia_empleado;
use App\Mail\CorreoEmpleadoMail;
use App\modo;
use App\organizacion;
use App\persona;
use App\tipo_dispositivo;
use App\vinculacion;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class vinculacionDispositivoController extends Controller
{
    public function vinculacionAndroid(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $contar = DB::table('vinculacion as v')
            ->join('modo as m', 'm.id', '=', 'v.idModo')
            ->select(DB::raw('COUNT(m.idTipoDispositivo) as total'))
            ->where('v.idEmpleado', '=', $idEmpleado)
            ->where('m.idTipoDispositivo', '=', 2)
            ->get();
        if ($contar[0]->total == 1) {
            return 1;
        } else {
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
    }

    public function vinculacionWindows(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $contar = DB::table('vinculacion as v')
            ->join('modo as m', 'm.id', '=', 'v.idModo')
            ->select(DB::raw('COUNT(m.idTipoDispositivo) as total'))
            ->where('v.idEmpleado', '=', $idEmpleado)
            ->where('m.idTipoDispositivo', '=', 1)
            ->get();
        if ($contar[0]->total == 3) {
            return 1;
        } else {
            //MODO
            $modo = new modo();
            $modo->idTipoModo = 1;
            $modo->idTipoDispositivo = 1;
            $modo->idEmpleado = $idEmpleado;
            $modo->save();
            $idModo = $modo->id;
            //LICENCIA
            $codigoEmpresa = session('sesionidorg');
            $codigoEmpleado = DB::table('empleado as e')
                ->select('e.emple_codigo', 'e.emple_persona', 'e.created_at')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get();
            $nuevaLivencia = STR::random(4);
            $codigoLicencia = $idEmpleado . '.' . $codigoEmpleado[0]->created_at . $codigoEmpresa . $nuevaLivencia;
            $encodeLicencia = rtrim(strtr(base64_encode($codigoLicencia), '+/', '-_'));
            $licencia = new licencia_empleado();
            $licencia->idEmpleado = $idEmpleado;
            $licencia->licencia = $encodeLicencia;
            $licencia->disponible = 'c';
            $licencia->save();
            $idLicencia = $licencia->id;
            //VINCULACION
            $vinculacion = new vinculacion();
            $vinculacion->idEmpleado = $idEmpleado;
            $vinculacion->envio = 0;
            $vinculacion->idModo = $idModo;
            $vinculacion->idLicencia = $idLicencia;
            $vinculacion->save();

            $idVinculacion = $vinculacion->id;

            $vinc = vinculacion::where('id', '=', $idVinculacion)->get()->first();
            $codigoHash = $codigoEmpresa . "s" . $idVinculacion . "s" . $codigoEmpresa . $idEmpleado;
            $encode = intval($codigoHash, 36);
            $vinc->hash = $encode;
            $vinc->save();
            $tipo_modo = tipo_dispositivo::where('id', '=', 1)->get()->first();
            $respuesta = [];
            $respuesta['dispositivo_descripcion'] = $tipo_modo->dispositivo_descripcion;
            $respuesta['licencia'] = $encodeLicencia;
            $respuesta['codigo'] = $encode;
            $respuesta['envio'] = 0;
            $respuesta['idVinculacion'] = $idVinculacion;
            $respuesta['contar'] = $contar[0]->total;

            return response()->json($respuesta, 200);
        }
    }

    public function vinculacionWindowsTabla(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');

        $empleado = DB::table('empleado as e')
            ->select('e.emple_Correo')
            ->where('e.emple_id', '=', $idEmpleado)
            ->get()->first();
        if ($empleado->emple_Correo != "") {
            //MODO
            $modo = new modo();
            $modo->idTipoModo = 1;
            $modo->idTipoDispositivo = 1;
            $modo->idEmpleado = $idEmpleado;
            $modo->save();
            $idModo = $modo->id;
            //LICENCIA
            $codigoEmpresa = session('sesionidorg');
            $codigoEmpleado = DB::table('empleado as e')
                ->select('e.emple_codigo', 'e.emple_persona', 'e.created_at')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get();
            $nuevaLivencia = STR::random(4);
            $codigoLicencia = $idEmpleado . '.' . $codigoEmpleado[0]->created_at . $codigoEmpresa . $nuevaLivencia;
            $encodeLicencia = rtrim(strtr(base64_encode($codigoLicencia), '+/', '-_'));
            $licencia = new licencia_empleado();
            $licencia->idEmpleado = $idEmpleado;
            $licencia->licencia = $encodeLicencia;
            $licencia->disponible = 'c';
            $licencia->save();
            $idLicencia = $licencia->id;
            //VINCULACION
            $vinculacion = new vinculacion();
            $vinculacion->idEmpleado = $idEmpleado;
            $vinculacion->envio = 0;
            $vinculacion->idModo = $idModo;
            $vinculacion->idLicencia = $idLicencia;
            $vinculacion->save();

            $idVinculacion = $vinculacion->id;

            $vinc = vinculacion::where('id', '=', $idVinculacion)->get()->first();
            $codigoHash = $codigoEmpresa. "s" . $idVinculacion . "s" . $codigoEmpresa . $idEmpleado;
            $encode = intval($codigoHash, 36);
            $vinc->hash = $encode;
            $vinc->save();
            $datos = [];
            $datos["correo"] = $empleado->emple_Correo;
            $email = array($datos["correo"]);
            $codigoP = DB::table('empleado as e')
                ->select('emple_persona', 'e.users_id')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get()->first();
            $persona = persona::find($codigoP->emple_persona);
            $licencia_empleado = licencia_empleado::find($licencia->id);
            $organizacion = organizacion::where('organi_id', '=', session('sesionidorg'))->get()->first();

            Mail::to($email)->queue(new CorreoEmpleadoMail($vinculacion, $persona, $licencia_empleado, $organizacion));
            $vinculacion->fecha_entrega = Carbon::now();
            $envio = $vinculacion->envio;
            $suma = $envio + 1;
            $licencia_empleado->disponible = 'e';
            $licencia_empleado->save();
            $vinculacion->envio = $suma;
            $vinculacion->save();

            return response()->json($vinculacion, 200);
        } else {
            return 1;
        }
    }
}
