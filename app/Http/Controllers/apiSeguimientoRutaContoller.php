<?php

namespace App\Http\Controllers;

use App\actividad_empleado;
use App\empleado;
use App\Mail\SoporteApi;
use App\Mail\SugerenciaApi;
use App\organizacion;
use App\persona;
use App\ubicacion;
use App\ubicacion_ruta;
use App\vinculacion_ruta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Mockery\Undefined;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class apiSeguimientoRutaContoller extends Controller
{
    public function login(Request $request)
    {
        $nroD = $request->get('nroDocumento');
        $codigo = $request->get('codigo');
        $vincu = vinculacion_ruta::where('hash', '=', $codigo)->get()->first();
        if ($vincu) {
            $decode = base_convert(intval($codigo), 10, 36);
            $explode = explode("d", $decode);
            $idOrganizacion = $explode[1];
            $idVinculacion = $explode[0];
            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('emple_nDoc', '=', $nroD)
                ->where('e.organi_id', '=', $idOrganizacion)
                ->where('e.emple_estado', '=', 1)
                ->get()->first();
            if ($empleado) {
                $vinculacion_ruta = vinculacion_ruta::where('id', '=', $idVinculacion)->get()->first();
                if ($vinculacion_ruta) {
                    if ($vinculacion_ruta->hash == $codigo) {
                        // * VERIFICAR IMEI O ANDROID ID
                        if (is_null($vinculacion_ruta->imei_androidID) == true) {
                            //? GUARDANDO MODELO DE CELULAR
                            $vinculacion_ruta->modelo = $request->get('modelo');
                            $vinculacion_ruta->imei_androidID = $request->get('imei_id');
                            $vinculacion_ruta->save();
                            // ? GENERANDO TOKEN
                            $factory = JWTFactory::customClaims([
                                'sub' => env('API_id'),
                            ]);
                            $payload = $factory->make();
                            $token = JWTAuth::encode($payload);
                            $organizacion = organizacion::where('organi_id', '=', $idOrganizacion)->get()->first();
                            return response()->json(array(
                                "corte" => $organizacion->corteCaptura,
                                "idEmpleado" => $empleado->emple_id,
                                "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
                                "idVinculacion" => $vinculacion_ruta->id,
                                "token" => $token->get()
                            ), 200);
                        } else {
                            if ($vinculacion_ruta->imei_androidID == $request->get('imei_id')) {
                                // ? GENERANDO TOKEN
                                $factory = JWTFactory::customClaims([
                                    'sub' => env('API_id'),
                                ]);
                                $payload = $factory->make();
                                $token = JWTAuth::encode($payload);
                                $organizacion = organizacion::where('organi_id', '=', $idOrganizacion)->get()->first();
                                return response()->json(array(
                                    "corte" => $organizacion->corteCaptura,
                                    "idEmpleado" => $empleado->emple_id,
                                    "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
                                    "idVinculacion" => $vinculacion_ruta->id,
                                    "token" => $token->get()
                                ), 200);
                            } else {
                                return response()->json(array("message" => "imeiId_erroneo"), 400);
                            }
                        }
                    } else {
                        return response()->json(array("message" => "codigo_erroneo"), 400);
                    }
                } else {
                    return response()->json(array("message" => "sin_dispositivo"), 400);
                }
            }
            return response()->json(array("message" => "empleado_no_exite"), 400);
        }
        return response()->json(array("message" => "codigo_no_exite"), 400);
    }

    // ? PRUEBA DE REGISTRO DE LATITUD Y LONGITUD
    public function registrarRuta(Request $request)
    {
        foreach ($request->all() as $key => $atributo) {
            $errores = [];
            $validacion = Validator::make($atributo, [
                'hora_ini' => 'required',
                'hora_fin' => 'required',
                'idActividad' => 'required',
                'idEmpleado' => 'required',
                'latitud_ini' => 'required',
                'longitud_ini' => 'required',
                'latitud_fin' => 'required',
                'longitud_fin' => 'required',
                'idVinculacion' => 'required'
            ], [
                'required' => ':attribute es obligatorio'
            ]);
            // dd($validacion->errors());
            if ($validacion->fails()) {
                //: ARRAY DE ERRORES
                // dd($validacion->failed());
                if (isset($validacion->failed()["hora_ini"])) {
                    array_push($errores, array("campo" => "hora_ini", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["hora_fin"])) {
                    array_push($errores, array("campo" => "hora_fin", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["idActividad"])) {
                    array_push($errores, array("campo" => "idActividad", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["idEmpleado"])) {
                    array_push($errores, array("campo" => "idEmpleado", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["latitud_ini"])) {
                    array_push($errores, array("campo" => "latitud_ini", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["longitud_ini"])) {
                    array_push($errores, array("campo" => "longitud_ini", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["latitud_fin"])) {
                    array_push($errores, array("campo" => "latitud_fin", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["longitud_fin"])) {
                    array_push($errores, array("campo" => "longitud_fin", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["idVinculacion"])) {
                    array_push($errores, array("campo" => "idVinculacion", "mensaje" => "Es obligatorio"));
                }
                return response()->json(array("errores" => $errores), 400);
            }
        }
        //: ARRAY DE RESPUESTA
        $respuesta = [];
        foreach ($request->all() as $ubicaciones) {
            //: OBTENER ACTIVIDAD PARA LA UBICACION
            $vinculacion_ruta = vinculacion_ruta::where('id', '=', $ubicaciones['idVinculacion'])->get()->first();
            if ($vinculacion_ruta) { //: Ingersamos si encontramos la vinculación
                //: Bsucar coincidencias de ubicaciones -> por motivos de bucles
                $buscarUbicacion = ubicacion::where('hora_ini', '=', $ubicaciones['hora_ini'])->where('hora_fin', '=', $ubicaciones['hora_fin'])
                    ->where('idEmpleado', '=', $ubicaciones['idEmpleado'])->get()->first();
                if (!$buscarUbicacion) {
                    //? GUARDAR UBICACIONES
                    $ubicacion = new ubicacion();
                    $ubicacion->hora_ini = $ubicaciones['hora_ini'];
                    $ubicacion->hora_fin = $ubicaciones['hora_fin'];
                    if (isset($ubicaciones['idHorario_dias'])) {
                        $ubicacion->idHorario_dias = $ubicaciones['idHorario_dias'];
                    }
                    $ubicacion->idActividad = $ubicaciones['idActividad'];
                    $ubicacion->idEmpleado = $ubicaciones['idEmpleado'];
                    //: validacion de hora final sea mayor  a hora inicial
                    $fecha = Carbon::parse($ubicaciones['hora_ini']);
                    $fecha1 = Carbon::parse($ubicaciones['hora_fin']);
                    //: ***************************************************
                    if ($fecha1->gt($fecha)) {
                        $ubicacion->actividad_ubicacion = $vinculacion_ruta->actividad;
                        $ubicacion->rango = $fecha1->diffInSeconds($fecha);
                    } else {
                        $ubicacion->actividad_ubicacion = 0;
                        $ubicacion->rango = 0;
                    }
                    $ubicacion->save();

                    $idUbicacion = $ubicacion->id;

                    //? UBICACION RUTA
                    $ubicacion_ruta = new ubicacion_ruta();
                    $ubicacion_ruta->idUbicacion = $idUbicacion;
                    $ubicacion_ruta->latitud_ini = $ubicaciones['latitud_ini'];
                    $ubicacion_ruta->longitud_ini = $ubicaciones['longitud_ini'];
                    $ubicacion_ruta->latitud_fin = $ubicaciones['latitud_fin'];
                    $ubicacion_ruta->longitud_fin = $ubicaciones['longitud_fin'];
                    $ubicacion_ruta->save();
                } else {
                    //? UBICACION RUTA
                    $ubicacion_ruta = new ubicacion_ruta();
                    $ubicacion_ruta->idUbicacion = $buscarUbicacion->id;
                    $ubicacion_ruta->latitud_ini = $ubicaciones['latitud_ini'];
                    $ubicacion_ruta->longitud_ini = $ubicaciones['longitud_ini'];
                    $ubicacion_ruta->latitud_fin = $ubicaciones['latitud_fin'];
                    $ubicacion_ruta->longitud_fin = $ubicaciones['longitud_fin'];
                    $ubicacion_ruta->save();
                }
            } else {
                array_push($respuesta, array("message" => "vinculacion_erroneo", "array" => $ubicaciones));
            }
        }

        if (sizeof($respuesta) == 0) {
            return response()->json($request->all(), 200);
        } else {
            return response()->json(array("errores" => $respuesta), 400);
        }
    }

    // ? LISTA DE ACTIVIDADES PARA CONTROL RUTA
    public function listaActividad(Request $request)
    {
        // ? ACTIVIDADES DEL EMPLEADO -> POR AHORA ACTIVIDADES DE CONTROL REMOTO
        $respuesta = [];
        $actividad_empleado = actividad_empleado::where('idEmpleado', '=', $request->get('idEmpleado'))->get();
        foreach ($actividad_empleado as $act) {
            $actividad = DB::table('actividad as a')
                ->select('a.Activi_id', 'a.Activi_Nombre')
                ->where('a.Activi_id', '=', $act->idActividad)
                ->get()
                ->first();
            $actividad->estado = $act->estado;
            array_push($respuesta, $actividad);
        }

        return response()->json($respuesta, 200);
    }

    // ? OBTENER TIRMPO DEL RHBOX Y HORA ACTUAL DEL SERVIDOR
    public function tiempoRHbox(Request $request)
    {
        $empleado = empleado::findOrFail($request->get('idEmpleado'));
        if ($empleado) {
            $respuesta = [];
            $fecha = Carbon::now('America/Lima');
            //* OBTENER HORAS DEL EMPLEADO
            $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
            $horas = DB::table('empleado as e')
                ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
                ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
                ->select(
                    DB::raw('TIME_FORMAT(SEC_TO_TIME(SUM(promedio.tiempo_rango)), "%H:%i:%s") as Total_Envio')
                )
                ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '=', $fechaHoy)
                ->where('e.emple_id', '=', $request->get('idEmpleado'))
                ->get()
                ->first();
            // * OBTENER HORA DEL SERVIDOR
            $horaActual = $fecha->timestamp;
            $respuesta["tiempo"] = $horas->Total_Envio == null ? "00:00:00" : $horas->Total_Envio;
            $respuesta["horaActual"] = $horaActual;
            return response()->json($respuesta, 200);
        }
        return response()->json(array("message" => "empleado_no_exite"), 400);
    }

    //? API DE HORARIO
    public function horario(Request $request)
    {
        $respuesta = [];
        $horario_empleado = DB::table('empleado as e')
            ->where('e.emple_id', '=', $request->get('idEmpleado'))
            ->get()
            ->first();
        if ($horario_empleado) {
            $horario = DB::table('horario_empleado as he')
                ->select('he.horario_dias_id', 'he.horario_horario_id', 'he.horarioComp', 'he.fuera_horario', 'he.horaAdic')
                ->where('he.empleado_emple_id', '=', $request->get('idEmpleado'))
                ->get();

            foreach ($horario as $resp) {
                $horario_dias = DB::table('horario_dias  as hd')
                    ->select(DB::raw('DATE(hd.start) as start'), 'hd.id')
                    ->where('hd.id', '=', $resp->horario_dias_id)
                    ->get()->first();
                $horario = DB::table('horario as h')
                    ->select('h.horario_id', 'h.horario_descripcion', 'h.horaI', 'h.horaF', 'h.horasObliga as horasObligadas', 'h.horario_tolerancia as tolerancia_inicio', 'h.horario_toleranciaF as tolerancia_final')
                    ->where('h.horario_id', '=', $resp->horario_horario_id)
                    ->get()->first();
                $pausas = DB::table('pausas_horario as ph')
                    ->select('ph.pausH_descripcion as decripcion', 'ph.pausH_Inicio as pausaI', 'ph.pausH_Fin as pausaF')
                    ->where('ph.horario_id', '=', $horario->horario_id)
                    ->get();
                $horario->idHorario_dias = $horario_dias->id;
                $horario->horarioCompensable = $resp->horarioComp;
                $horario->fueraHorario = $resp->fuera_horario;
                $horario->horaAdicional = $resp->horaAdic;
                $horario->pausas = $pausas;
                $segundos = Carbon::createFromTimestampUTC($horario->horasObligadas)->secondsSinceMidnight();
                $horario->horasObligadas = $segundos;
                $fecha = Carbon::now();
                $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                if ($horario_dias->start == $fechaHoy) {
                    array_push($respuesta, $horario);
                }
            }
            return response()->json($respuesta, 200);
        }
        return response()->json(array("message" => "empleado_no_exite"), 400);
    }

    //? TICKET DE SUGERENCIA
    public function ticketSoporte(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $tipo = $request->get('tipo');
        $contenido = $request->get('contenido');
        $asunto = $request->get('asunto');
        $celular = $request->get('celular');

        $empleado = empleado::findOrFail($idEmpleado);
        if ($empleado) {
            $persona = persona::findOrFail($empleado->emple_persona);
            $email = "info@rhnube.com.pe";

            if ($tipo == "soporte") {

                Mail::to($email)->queue(new SoporteApi($contenido, $persona, $asunto, $celular));
                return response()->json(array("mensaje" => "correo_enviado_con_éxito"), 200);
            }
            if ($tipo == "sugerencia") {
                Mail::to($email)->queue(new SugerenciaApi($contenido, $persona, $asunto, $celular));
                return response()->json(array("mensaje" => "correo_Enviado_con_éxito"), 200);
            }
        }

        return response()->json(array("mensaje" => "empleado_no_exite"), 400);
    }
}
