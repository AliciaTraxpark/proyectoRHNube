<?php

namespace App\Http\Controllers;

use App\empleado;
use App\licencia_empleado;
use App\Mail\AndroidMail;
use App\Mail\correoAdministrativo;
use App\Mail\CorreoEmpleadoMail;
use App\Mail\CorreoMasivoMail;
use App\Mail\MasivoWindowsMail;
use App\modo;
use App\organizacion;
use App\persona;
use App\User;
use App\usuario_organizacion;
use App\vinculacion;
use App\vinculacion_ruta;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Claims\DatetimeTrait;

class correosEmpleadoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function envioWindows(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $idVinculacion = $request->get('idVinculacion');
        $empleado = DB::table('empleado as e')
            ->select('e.emple_Correo')
            ->where('e.emple_id', '=', $idEmpleado)
            ->get()->first();
        // DB::enableQueryLog();
        $contar = DB::table('vinculacion as v')
            ->select(DB::raw('COUNT(v.id) as cantidad'))
            ->where('v.idEmpleado', '=', $idEmpleado)
            ->where('v.id', '<', $idVinculacion)
            ->get()
            ->first();
        // dd(DB::getQueryLog());
        $vinculacion = vinculacion::findOrFail($idVinculacion);
        if ($empleado->emple_Correo != "") {
            $licencia_empleado = licencia_empleado::findOrFail($vinculacion->idLicencia);
            $vinculacion->descarga = STR::random(25);
            $vinculacion->save();
            $vinculacion->pc_mac = $vinculacion->pc_mac == null ? "PC " . $contar->cantidad : $vinculacion->pc_mac;
            $datos = [];
            $datos["correo"] = $empleado->emple_Correo;
            $email = array($datos["correo"]);
            $codigoP = DB::table('empleado as e')
                ->select('emple_persona', 'e.users_id')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get();
            $codP = [];
            $codP["id"] = $codigoP[0]->emple_persona;
            $persona = persona::find($codP["id"]);
            $organizacion = organizacion::where('organi_id', '=', session('sesionidorg'))->get()->first();
            Mail::to($email)->queue(new CorreoEmpleadoMail($vinculacion, $persona, $licencia_empleado, $organizacion));
            $vinculacion->fecha_entrega = Carbon::now();
            $envio = $vinculacion->envio;
            $suma = $envio + 1;
            $licencia_empleado->disponible = 'e';
            $licencia_empleado->save();
            $vinculacion->envio = $suma;
            $vinculacion->save();
            $respuesta = [];
            $respuesta['envio'] = $vinculacion->envio;
            $respuesta['disponible'] = $licencia_empleado->disponible;
            return response()->json($respuesta, 200);
        }
        return response()->json(null, 400);
    }

    public function smsAndroid(Request $request)
    {
        $idV = $request->get('id');
        $vinculacion_ruta = vinculacion_ruta::findOrFail($idV);
        if (!empty($vinculacion_ruta->celular)) {
            $mensaje = "RH nube - Codigo de validacion de Inicio:" . $vinculacion_ruta->hash;
            $cel = explode("+", $vinculacion_ruta->celular);
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
                    "Authorization:67p7e5ONkalvrKLDQh3RaONgSFs=",
                    "Cache-Control: no-cache"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            if ($err) {
                $vinculacion_ruta->disponible = 'e';
                $vinculacion_ruta->save();
                return 1;
            } else {
                $vinculacion_ruta->disponible = 'e';
                $vinculacion_ruta->envio = $vinculacion_ruta->envio + 1;
                $vinculacion_ruta->fecha_envio = Carbon::now();
                $vinculacion_ruta->save();
                return response()->json($vinculacion_ruta, 200);
            }
        } else {
            return 0;
        }
    }
}
