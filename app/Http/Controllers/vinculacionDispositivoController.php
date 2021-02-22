<?php

namespace App\Http\Controllers;

use App\licencia_empleado;
use App\Mail\CorreoEmpleadoMail;
use App\modo;
use App\organizacion;
use App\persona;
use App\tipo_dispositivo;
use App\vinculacion;
use App\vinculacion_ruta;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class vinculacionDispositivoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function vinculacionAndroid(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $contar = DB::table('vinculacion_ruta as vr')
            ->join('modo as m', 'm.id', '=', 'vr.idModo')
            ->select(DB::raw('COUNT(m.idTipoDispositivo) as total'))
            ->where('vr.idEmpleado', '=', $idEmpleado)
            ->where('m.idTipoDispositivo', '=', 2)
            ->get();
        if ($contar[0]->total == 2) {
            return 1;
        } else {
            $celular = DB::table('empleado as e')
                ->select('e.emple_celular as numero')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get()
                ->first();
            $modo = new modo();
            $modo->idTipoModo = 2;
            $modo->idTipoDispositivo = 2;
            $modo->idEmpleado = $idEmpleado;
            $modo->save();
            $idModo = $modo->id;
            $codigoEmpresa = session('sesionidorg');
            $vinculacion = new vinculacion_ruta();
            $vinculacion->idEmpleado = $idEmpleado;
            $vinculacion->envio = 0;
            $vinculacion->idModo = $idModo;
            $vinculacion->celular = $celular->numero;
            $vinculacion->disponible = 'c';
            $vinculacion->actividad = 50;
            $vinculacion->save();

            $idVinculacion = $vinculacion->id;
            $codigo =  $idVinculacion . "d" . $codigoEmpresa . "d";
            $vinc = vinculacion_ruta::where('id', '=', $idVinculacion)->get()->first();
            $encode = intval($codigo, 36);
            $vinc->hash = $encode;
            $vinc->save();
            $tipo_modo = tipo_dispositivo::where('id', '=', 2)->get()->first();
            $respuesta = [];
            $respuesta['dispositivo_descripcion'] = $tipo_modo->dispositivo_descripcion;
            $respuesta['codigo'] = $codigo;
            $respuesta['envio'] = 0;
            $respuesta['idVinculacion'] = $idVinculacion;
            $respuesta['contar'] = $contar[0]->total;
            $respuesta['numero'] = $celular->numero;
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
            $vinculacion->descarga = STR::random(25);
            $vinculacion->idModo = $idModo;
            $vinculacion->idLicencia = $idLicencia;
            $vinculacion->save();

            $idVinculacion = $vinculacion->id;

            $vinc = vinculacion::where('id', '=', $idVinculacion)->get()->first();
            $codigoHash = $codigoEmpresa . "s" . $idVinculacion . "s" . $codigoEmpresa . $idEmpleado;
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

    public function vinculacionAndroidTabla(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');

        $empleado = DB::table('empleado as e')
            ->select('e.emple_celular')
            ->where('e.emple_id', '=', $idEmpleado)
            ->get()->first();
        if ($empleado->emple_celular != "") {
            //MODO
            $modo = new modo();
            $modo->idTipoModo = 2;
            $modo->idTipoDispositivo = 2;
            $modo->idEmpleado = $idEmpleado;
            $modo->save();
            $idModo = $modo->id;
            //* VINCULACION RUTA
            $vinculacion = new vinculacion_ruta();
            $vinculacion->idEmpleado = $idEmpleado;
            $vinculacion->envio = 0;
            $vinculacion->idModo = $idModo;
            $vinculacion->celular = $empleado->emple_celular;
            $vinculacion->disponible = 'c';
            $vinculacion->actividad = 50;
            $vinculacion->save();

            $idVinculacion = $vinculacion->id;

            $vinc = vinculacion_ruta::where('id', '=', $idVinculacion)->get()->first();
            $codigoEmpresa = session('sesionidorg');
            $codigo =  $idVinculacion . "d" . $codigoEmpresa . "d";
            $encode = intval($codigo, 36);
            $vinc->hash = $encode;
            $vinc->save();

            //* ENVIAR SMS
            $mensaje = "RH nube - Codigo de validacion de Inicio:" . $vinc->hash;
            $cel = explode("+", $vinc->celular);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.broadcastermobile.com/brdcstr-endpoint-web/services/messaging/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_FOLLOWLOCATION => TRUE,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => '{
                "apiKey":2308,
                "country":"PE",
                "dial":38383,
                "message":"' . $mensaje . '",
                "msisdns":[' . $cel[1] . '],
                "tag":"tag-prueba"
            }',
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization:zAS+nYnqJ+zX8KBr05ojMufSWuo=",
                    "Cache-Control: no-cache"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            if ($err) {
                $vinc->disponible = 'e';
                $vinc->save();
            } else {
                $vinc->disponible = 'e';
                $vinc->envio = $vinc->envio + 1;
                $vinc->fecha_envio = Carbon::now();
                $vinc->save();
            }

            return response()->json($vinculacion, 200);
        } else {
            return 1;
        }
    }
    public function editarNumeroV(Request $request)
    {
        $idV = $request->get('id');
        $numero = $request->get('numero');

        $vinculacion_ruta = vinculacion_ruta::findOrFail($idV);
        if ($vinculacion_ruta) {
            $vinculacion_ruta->celular = "+51" . $numero;
            $vinculacion_ruta->modelo = NULL;
            $vinculacion_ruta->imei_androidID = NULL;
            $vinculacion_ruta->save();
            return response()->json($vinculacion_ruta, 200);
        }
    }

    public function editarActividadV(Request $request)
    {
        $idV = $request->get('id');
        $actividad = $request->get('actividad');

        // dd($actividad);
        $vinculacion_ruta = vinculacion_ruta::findOrFail($idV);
        if ($vinculacion_ruta) {
            $vinculacion_ruta->actividad = $actividad;
            $vinculacion_ruta->save();

            return response()->json($vinculacion_ruta, 200);
        }
    }

    public function listaVinculacionW(Request $request)
    {
        $idempleado = $request->get('idE');
        // ? VINCULACIÓN DE CONTROL REMOTO
        $vinculacion = DB::table('vinculacion as v')
            ->join('modo as m', 'm.id', '=', 'v.idModo')
            ->join('tipo_dispositivo as td', 'td.id', 'm.idTipoDispositivo')
            ->join('licencia_empleado as le', 'le.id', '=', 'v.idLicencia')
            ->select(
                'v.id as idVinculacion',
                'v.pc_mac as pc',
                'v.envio as envio',
                'v.hash as codigo',
                'le.idEmpleado',
                'le.licencia as licencia',
                'le.id as idLicencia',
                'le.disponible as disponible',
                'td.dispositivo_descripcion as dispositivoD'
            )
            ->where('v.idEmpleado', '=', $idempleado)
            ->get();
        return response()->json($vinculacion, 200);
    }

    public function listaVinculacionA(Request $request)
    {
        $idempleado = $request->get('idE');
        // ? VINCULACIÓN DE RUTA
        $vinculacionRuta = DB::table('vinculacion_ruta as vr')
            ->join('modo as m', 'm.id', '=', 'vr.idModo')
            ->join('tipo_dispositivo as td', 'td.id', 'm.idTipoDispositivo')
            ->select(
                'vr.id as idV',
                'vr.modelo as modelo',
                'vr.envio as envio',
                'vr.hash as codigo',
                'vr.idEmpleado',
                'td.dispositivo_descripcion as dispositivoD',
                'vr.celular as numero',
                'vr.actividad as actividad',
                'vr.disponible'
            )
            ->where('vr.idEmpleado', '=', $idempleado)
            ->groupBy('vr.id')
            ->get();
        return response()->json($vinculacionRuta, 200);
    }
}
