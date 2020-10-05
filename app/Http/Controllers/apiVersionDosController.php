<?php

namespace App\Http\Controllers;

use App\actividad;
use App\captura;
use App\captura_imagen;
use App\empleado;
use App\licencia_empleado;
use App\Mail\SoporteApi;
use App\Mail\SugerenciaApi;
use App\organizacion;
use App\persona;
use App\promedio_captura;
use App\vinculacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class apiVersionDosController extends Controller
{

    public function verificacion(Request $request)
    {
        $nroD = $request->get('nroDocumento');
        $codigo = $request->get('codigo');
        $serieD = $request->get('serieD');
        $decode = base_convert(intval($codigo), 10, 36);
        $explode = explode("s", $decode);
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
            ->where('emple_nDoc', '=', $nroD)
            ->where('e.organi_id', '=', $explode[0])
            ->where('e.emple_estado', '=', 1)
            ->get()->first();

        $idOrganizacion = $explode[0];

        if ($empleado) {
            $vinculacion = vinculacion::where('id', '=', $explode[1])->get()->first();
            if ($vinculacion) {
                $licencia = licencia_empleado::where('id', '=', $vinculacion->idLicencia)->where('disponible', '!=', 'i')->get()->first();
                if ($licencia) {
                    if ($vinculacion->hash == $request->get('codigo')) {
                        if ($vinculacion->serieDisco ==  null) {
                            $vinculacion->pc_mac = $request->get('pc_mac');
                            $vinculacion->serieDisco = $serieD;
                            $factory = JWTFactory::customClaims([
                                'sub' => env('API_id'),
                            ]);
                            $payload = $factory->make();
                            $token = JWTAuth::encode($payload);
                            $organizacion = organizacion::where('organi_id', '=', $idOrganizacion)->get()->first();
                            return response()->json(array(
                                "corte" => $organizacion->corteCaptura, "idEmpleado" => $empleado->emple_id, "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
                                'idUser' => $idOrganizacion, 'token' => $token->get()
                            ), 200);
                        } else {
                            if ($vinculacion->serieDisco == $serieD) {
                                $vinculacion->pc_mac = $request->get('pc_mac');
                                $vinculacion->save();
                                $factory = JWTFactory::customClaims([
                                    'sub' => env('API_id'),
                                ]);
                                $payload = $factory->make();
                                $token = JWTAuth::encode($payload);
                                $organizacion = organizacion::where('organi_id', '=', $idOrganizacion)->get()->first();
                                return response()->json(array(
                                    "corte" => $organizacion->corteCaptura, "idEmpleado" => $empleado->emple_id, "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
                                    'idUser' => $idOrganizacion, 'token' => $token->get()
                                ), 200);
                            }else{
                                return response()->json("disco_erroneo", 400);
                            }
                        }
                    }
                    return response()->json("codigo_erroneo", 400);
                }
                return response()->json("licencia_de_baja", 400);
            }
            return response()->json("sin_dispositivo", 400);
        }
        return response()->json("empleado_no_exite", 400);
    }

    function selectActividad(Request $request)
    {
        $empleado = $request->get('emple_id');
        $respuesta = [];
        $actividad = actividad::where('empleado_emple_id', '=', $empleado)->get();
        foreach ($actividad as $act) {
            array_push($respuesta, $act);
        }
        return response()->json($respuesta, 200);
    }

    function actividad(Request $request)
    {
        $cambio = $request->get('cambio');
        if ($cambio == 'n') {
            $actividad = new actividad();
            $actividad->Activi_Nombre = $request['Activi_Nombre'];
            if ($request['Tarea_Tarea_id'] != '') {
                $actividad->Tarea_Tarea_id = $request['Tarea_Tarea_id'];
            }
            $actividad->empleado_emple_id = $request['emple_id'];
            $actividad->save();
        }
        if ($cambio == 'm') {
            $Activi_id = $request['Activi_id'];
            $actividad = actividad::where('Activi_id', $Activi_id)->first();
            if ($actividad) {
                $actividad->Activi_Nombre = $request['Activi_Nombre'];
                $actividad->save();
            }
        }
        if ($cambio == 'e') {
            $actividad = actividad::where('Activi_id', $request->get('idActividad'))->first();
            if ($actividad) {
                $actividad->estado = 0;
                $actividad->save();
            }
        }
        return response()->json($actividad, 200);
    }

    public function captura(Request $request)
    {
        $capturaBuscar = captura::where("idEmpleado", "=", $request->get('idEmpleado'))
            ->where('hora_ini', '=', $request->get('hora_ini'))
            ->where('actividad', '=', $request->get('actividad'))
            ->get()
            ->first();

        if ($capturaBuscar) {
            $captura_imagen = new captura_imagen();
            $captura_imagen->idCaptura = $capturaBuscar->idCaptura;
            $captura_imagen->miniatura = $request->get('miniatura');
            $captura_imagen->imagen = $request->get('imagen');
            $captura_imagen->save();
            return response()->json($capturaBuscar, 200);
        } else {
            $captura = new captura();
            $captura->estado = $request->get('estado');
            $captura->actividad = $request->get('actividad');
            $captura->hora_ini = $request->get('hora_ini');
            $captura->hora_fin = $request->get('hora_fin');
            $captura->ultimo_acumulado = $request->get('ultimo_acumulado');
            $captura->acumulador = $request->get('acumulador');
            $captura->idHorario_dias = $request->get('idHorario_dias');
            $captura->idActividad = $request->get('idActividad');
            $captura->idEmpleado = $request->get('idEmpleado');
            $captura->save();

            $idCaptura = $captura->idCaptura;

            $captura_imagen = new captura_imagen();
            $captura_imagen->idCaptura = $idCaptura;
            $captura_imagen->miniatura = $request->get('miniatura');
            $captura_imagen->imagen = $request->get('imagen');
            $captura_imagen->save();

            $idHorario = $captura->idHorario_dias;

            //  PROMEDIO CAPTURA
            $capturaRegistrada = captura::where('idCaptura', '=', $idCaptura)->get()->first();
            $idHorario_dias = $idHorario;
            //RESTA POR FECHA HORA DE   CAPTURAS
            // $fecha = Carbon::create($capturaRegistrada->hora_ini)->format('H:i:s');
            // $explo = explode(":", $fecha);
            // $calSegund = $explo[0] * 3600 + $explo[1] * 60 + $explo[2];
            // $fecha1 = Carbon::create($capturaRegistrada->hora_fin)->format('H:i:s');
            // $explo1 = explode(":", $fecha1);
            // $calSegund1 = $explo1[0] * 3600 + $explo1[1] * 60 + $explo1[2];
            // $totalP = $calSegund1 - $calSegund;
            $fecha = Carbon::parse($capturaRegistrada->hora_ini);
            $fecha1 = Carbon::parse($capturaRegistrada->hora_fin);
            $totalP = $fecha1->diffInSeconds($fecha);
            // ACTIVIDAD DE CAPTURA
            $activ = $capturaRegistrada->actividad;
            //VALIDACION DE CERO
            if ($totalP == 0) {
                $round = 0;
            } else {
                //PROMEDIO
                $promedio = floatval($activ / $totalP);
                $promedioFinal = $promedio * 100;
                $round = round($promedioFinal, 2);
            }
            $promedio_captura = new promedio_captura();
            $promedio_captura->idCaptura = $idCaptura;
            $promedio_captura->idHorario = $idHorario_dias;
            $promedio_captura->promedio = $round;
            $promedio_captura->tiempo_rango = $totalP;
            $promedio_captura->save();
            return response()->json($captura, 200);
        }
    }

    public function capturaArray(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            $capturaBuscar = captura::where("idEmpleado", "=", $value['idEmpleado'])
                ->where('hora_ini', '=', $value['hora_ini'])
                ->where('actividad', '=', $value['actividad'])
                ->get()
                ->first();
            if ($capturaBuscar) {
                $captura_imagen = new captura_imagen();
                $captura_imagen->idCaptura = $capturaBuscar->idCaptura;
                $captura_imagen->miniatura = $value['miniatura'];
                $captura_imagen->imagen = $value['imagen'];
                $captura_imagen->save();
            } else {
                $captura = new captura();
                $captura->estado = $value['estado'];
                $captura->actividad = $value['actividad'];
                $captura->hora_ini = $value['hora_ini'];
                $captura->hora_fin = $value['hora_fin'];
                $captura->ultimo_acumulado = $value['ultimo_acumulado'];
                $captura->acumulador = $value['acumulador'];
                $captura->idHorario_dias = $value['idHorario_dias'];
                $captura->idActividad = $value['idActividad'];
                $captura->idEmpleado = $value['idEmpleado'];
                $captura->save();

                $idCaptura = $captura->idCaptura;
                $idHorario = $captura->idHorario_dias;

                //CAPTURA_IMAGEN
                $captura_imagen = new captura_imagen();
                $captura_imagen->idCaptura = $idCaptura;
                $captura_imagen->miniatura = $value['miniatura'];
                $captura_imagen->imagen = $value['imagen'];
                $captura_imagen->save();

                //  PROMEDIO CAPTURA
                $capturaRegistrada = captura::where('idCaptura', '=', $idCaptura)->get()->first();
                $idHorario_dias = $idHorario;
                //RESTA POR FECHA HORA DE   CAPTURAS
                $fecha = Carbon::parse($capturaRegistrada->hora_ini);
                $fecha1 = Carbon::parse($capturaRegistrada->hora_fin);
                $totalP = $fecha1->diffInSeconds($fecha);
                // ACTIVIDAD DE CAPTURA
                $activ = $capturaRegistrada->actividad;
                //VALIDACION DE CERO
                if ($totalP == 0) {
                    $round = 0;
                } else {
                    //PROMEDIO
                    $promedio = floatval($activ / $totalP);
                    $promedioFinal = $promedio * 100;
                    $round = round($promedioFinal, 2);
                }
                $promedio_captura = new promedio_captura();
                $promedio_captura->idCaptura = $idCaptura;
                $promedio_captura->idHorario = $idHorario_dias;
                $promedio_captura->promedio = $round;
                $promedio_captura->tiempo_rango = $totalP;
                $promedio_captura->save();
            }
        }
        return response()->json($request->all(), 200);
    }

    public function ticketSoporte(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $tipo = $request->get('tipo');
        $contenido = $request->get('contenido');
        $asunto = $request->get('asunto');
        $celular = $request->get('celular');
        $cont = $request->get('contenido');
        $asunt = $request->get('asunto');
        $cel = $request->get('celular');

        $empleado = empleado::findOrFail($idEmpleado);
        if ($empleado) {
            $persona = persona::findOrFail($empleado->emple_persona);
            $email = $email = env('MAIL_FROM_ADDRESS');

            if ($tipo == "soporte") {

                Mail::to($email)->queue(new SoporteApi($contenido, $persona, $asunto, $celular));
                return response()->json("Correo Enviado con éxito", 200);
            }
            if ($tipo == "sugerencia") {
                Mail::to($email)->queue(new SugerenciaApi($contenido, $persona, $asunto, $celular));
                return response()->json("Correo Enviado con éxito", 200);
            }
        }

        return response()->json("Empleado no se encuentra registrado.", 400);
    }
}
