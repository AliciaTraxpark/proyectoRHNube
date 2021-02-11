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
use App\ubicacion_sin_procesar;
use App\vinculacion_ruta;
use Carbon\Carbon;
use DateTime;
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
                    if ($vinculacion_ruta->disponible != "i") {
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
                        return response()->json(array("message" => "dispositivo_de_baja"), 400);
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
                // ! DATA PARA TABLA PROVISIONAL
                $ubicacionSinProcesar = new ubicacion_sin_procesar();
                $ubicacionSinProcesar->hora_ini = $ubicaciones["hora_ini"];
                $ubicacionSinProcesar->hora_fin = $ubicaciones["hora_fin"];
                if (isset($ubicaciones['idHorario_dias'])) {
                    $ubicacionSinProcesar->idHorario_dias = $ubicaciones["idHorario_dias"];
                }
                $ubicacionSinProcesar->idActividad = $ubicaciones["idActividad"];
                $ubicacionSinProcesar->idEmpleado = $ubicaciones["idEmpleado"];
                $ubicacionSinProcesar->latitud_ini = $ubicaciones["latitud_ini"];
                $ubicacionSinProcesar->longitud_ini = $ubicaciones["longitud_ini"];
                $ubicacionSinProcesar->latitud_fin = $ubicaciones["latitud_fin"];
                $ubicacionSinProcesar->longitud_fin = $ubicaciones["longitud_fin"];
                $fechaP = Carbon::parse($ubicaciones['hora_ini']);
                $fechaP1 = Carbon::parse($ubicaciones['hora_fin']);
                $ubicacionSinProcesar->rango = $fechaP1->diffInSeconds($fechaP);
                $promedio = floatval($fechaP1->diffInSeconds($fechaP) * $vinculacion_ruta->actividad);
                $activi = round(($promedio / 100), 2);
                $ubicacionSinProcesar->actividad_ubicacion = $activi;
                $ubicacionSinProcesar->save();
                // ! FINALIZACION
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
                        $promedio = floatval($fecha1->diffInSeconds($fecha) * $vinculacion_ruta->actividad);
                        $activi = round(($promedio / 100), 2);
                        $ubicacion->actividad_ubicacion = $activi;
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
        // ? ACTIVIDADES DEL EMPLEADO
        $respuesta = [];
        $actividad_empleado = actividad_empleado::where('idEmpleado', '=', $request->get('idEmpleado'))->get();
        foreach ($actividad_empleado as $act) {
            $actividad = DB::table('actividad as a')
                ->select('a.Activi_id', 'a.Activi_Nombre', 'a.controlRuta')
                ->where('a.Activi_id', '=', $act->idActividad)
                ->where('a.controlRuta', '=', 1)
                ->where('a.estado', '=', 1)
                ->get()
                ->first();
            if ($actividad) {
                $actividad->estado = $act->estado;
                array_push($respuesta, $actividad);
            }
        }

        return response()->json($respuesta, 200);
    }

    // ? OBTENER TIRMPO DEL RHBOX Y HORA ACTUAL DEL SERVIDOR
    public function tiempoRuta(Request $request)
    {
        $empleado = empleado::findOrFail($request->get('idEmpleado'));
        if ($empleado) {
            $respuesta = [];
            $fecha = Carbon::now('America/Lima');
            //* OBTENER HORAS DEL EMPLEADO EN RHBOX
            $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
            // * FUNCION PARA UNIR DATOS POR HORAS Y MINUTOS
            $horasRHbox = DB::table('empleado as e')
                ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
                ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
                ->select(
                    'cp.actividad',
                    DB::raw('TIME(cp.hora_ini) as hora_ini'),
                    DB::raw('TIME(cp.hora_fin) as hora_fin'),
                    DB::raw('DATE(cp.hora_ini) as fecha'),
                    DB::raw('TIME(cp.hora_ini) as hora'),
                    'promedio.tiempo_rango as rango'
                )
                ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '=', $fechaHoy)
                ->where('e.emple_id', '=', $empleado->emple_id)
                ->orderBy('cp.hora_ini', 'asc')
                ->get();
            $horasRHbox = horasRemotoRutaJson($horasRHbox);
            //* OBTENER HORAS DEL EMPLEADO EN RUTA
            $horasRuta = DB::table('empleado as e')
                ->join('ubicacion as u', 'u.idEmpleado', '=', 'e.emple_id')
                ->leftJoin('horario_dias as h', 'h.id', '=', 'u.idHorario_dias')
                ->select(
                    'u.actividad_ubicacion as actividad',
                    DB::raw('TIME(u.hora_ini) as hora_ini'),
                    DB::raw('TIME(u.hora_fin) as hora_fin'),
                    DB::raw('DATE(u.hora_ini) as fecha'),
                    DB::raw('TIME(u.hora_ini) as hora'),
                    'u.rango as rango'
                )
                ->where(DB::raw('IF(h.id is null, DATE(u.hora_ini), DATE(h.start))'), '=', $fechaHoy)
                ->where('e.emple_id', '=', $empleado->emple_id)
                ->orderBy('u.hora_ini', 'asc')
                ->get();
            $horasRuta = horasRemotoRutaJson($horasRuta);
            //* ******************************************
            if (sizeof($horasRHbox) != 0 && sizeof($horasRuta) != 0) {
                $rango = 0;
                $actividad = 0;
                for ($hora = 0; $hora < 24; $hora++) {
                    $busquedaHora = true;
                    for ($i = 0; $i < sizeof($horasRHbox); $i++) {
                        for ($j = 0; $j < sizeof($horasRuta); $j++) {
                            //* RECORREMOS EN FORMATO HORAS
                            if ($horasRHbox[$i]["hora"] == $hora && $horasRuta[$j]["hora"] == $hora) {
                                $busquedaHora = false;
                                //* RECORREMOS EN FORMATO MINUTOS
                                for ($m = 0; $m < 6; $m++) {
                                    if (isset($horasRHbox[$i]["minuto"][$m]) && isset($horasRuta[$j]["minuto"][$m])) {
                                        $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                        $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                        //: DATOS DE RH BOX
                                        $horaInicioRHbox = "23:00:00";
                                        $horaFinRHbox = "00:00:00";
                                        $rangoRHbox = 0;
                                        $actividadRHbox = 0;
                                        //: DATOS DE RUTA
                                        $horaInicioRuta = "23:00:00";
                                        $horaFinRuta = "00:00:00";
                                        $rangoRuta = 0;
                                        $actividadRuta = 0;
                                        //* RECORREMOS MINUTOS RH BOX
                                        for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                            if (Carbon::parse($horaInicioRHbox) > Carbon::parse($arrayMinutoRHbox[$index]->hora_ini)) {
                                                $horaInicioRHbox = $arrayMinutoRHbox[$index]->hora_ini;
                                            }
                                            if (Carbon::parse($horaFinRHbox) < Carbon::parse($arrayMinutoRHbox[$index]->hora_fin)) {
                                                $horaFinRHbox = $arrayMinutoRHbox[$index]->hora_fin;
                                            }
                                            $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                            $actividadRHbox = $actividadRHbox + $arrayMinutoRHbox[$index]->actividad;
                                        }
                                        //* RECORREMOS MINUTOS RUTA
                                        for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                            if (Carbon::parse($horaInicioRuta) > Carbon::parse($arrayMinutoRuta[$element]->hora_ini)) {
                                                $horaInicioRuta = $arrayMinutoRuta[$element]->hora_ini;
                                            }
                                            if (Carbon::parse($horaFinRuta) < Carbon::parse($arrayMinutoRuta[$element]->hora_fin)) {
                                                $horaFinRuta = $arrayMinutoRuta[$element]->hora_fin;
                                            }
                                            $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                            $actividadRuta = $actividadRuta + $arrayMinutoRuta[$element]->actividad;
                                        }
                                        //* COMPARAMOS TIEMPOS
                                        if (Carbon::parse($horaInicioRHbox) < Carbon::parse($horaInicioRuta)) {
                                            //* PARAMETROS PARA ENVIAR A FUNCION
                                            $horaInicioRango = $horaInicioRHbox;
                                            $horaFinRango = $horaFinRHbox;
                                            $horaNowRango = $horaInicioRuta;
                                            //* *********************************
                                            $check = checkHora($horaInicioRango, $horaFinRango, $horaNowRango);
                                            if ($check) {
                                                // ! RANGOS
                                                $nuevoRango = ($rangoRHbox + $rangoRuta) / 2;
                                                $rango = $rango + $nuevoRango;
                                                // ! ACTIVIDAD
                                                $nuevaActividad = ($actividadRHbox + $actividadRuta) / 2;
                                                $actividad = $actividad + $nuevaActividad;
                                            } else {
                                                // ! RANGOS
                                                $nuevoRango = $rangoRHbox + $rangoRuta;
                                                $rango = $rango + $nuevoRango;
                                                // ! ACTIVIDAD
                                                $nuevaActividad = $actividadRHbox + $actividadRuta;
                                                $actividad = $actividad + $nuevaActividad;
                                            }
                                        } else {
                                            //* PARAMETROS PARA ENVIAR A FUNCION
                                            $horaInicioRango = $horaInicioRuta;
                                            $horaFinRango = $horaFinRuta;
                                            $horaNowRango = $horaInicioRHbox;
                                            //* *********************************
                                            $check = checkHora($horaInicioRango, $horaFinRango, $horaNowRango);
                                            if ($check) {
                                                // ! RANGOS
                                                $nuevoRango = ($rangoRHbox + $rangoRuta) / 2;
                                                $rango = $rango + $nuevoRango;
                                                // ! ACTIVIDAD
                                                $nuevaActividad = ($actividadRHbox + $actividadRuta) / 2;
                                                $actividad = $actividad + $nuevaActividad;
                                            } else {
                                                // ! RANGOS
                                                $nuevoRango = $rangoRHbox + $rangoRuta;
                                                $rango = $rango + $nuevoRango;
                                                // ! ACTIVIDAD
                                                $nuevaActividad = $actividadRHbox + $actividadRuta;
                                                $actividad = $actividad + $nuevaActividad;
                                            }
                                        }
                                    } else {
                                        if (isset($horasRHbox[$i]["minuto"][$m])) {
                                            $rangoRHbox = 0;
                                            $actividadRHbox = 0;
                                            $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                            //* RECORREMOS MINUTOS RH BOX
                                            for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                                $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                                $actividadRHbox = $actividadRHbox + $arrayMinutoRHbox[$index]->actividad;
                                            }
                                            $rango = $rango + $rangoRHbox;               //: -> RANGO
                                            $actividad = $actividad + $actividadRHbox;   //: -> ACTIVIDAD
                                        } else {
                                            if (isset($horasRuta[$j]["minuto"][$m])) {
                                                $rangoRuta = 0;
                                                $actividadRuta = 0;
                                                $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                                //* RECORREMOS MINUTOS RUTA
                                                for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                                    $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                                    $actividadRuta = $actividadRuta + $arrayMinutoRuta[$element]->actividad;
                                                }
                                                $rango = $rango + $rangoRuta;                //: -> RANGO
                                                $actividad = $actividad + $actividadRuta;    //: -> ACTIVIDAD
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($busquedaHora) {
                        for ($i = 0; $i < sizeof($horasRHbox); $i++) {
                            if ($horasRHbox[$i]["hora"] == $hora) {
                                //* RECORREMOS EN FORMATO MINUTOS
                                for ($m = 0; $m < 6; $m++) {
                                    if (isset($horasRHbox[$i]["minuto"][$m])) {
                                        $rangoRHbox = 0;
                                        $actividadRHbox = 0;
                                        $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                        //* RECORREMOS MINUTOS RH BOX
                                        for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                            $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                            $actividadRHbox = $actividadRHbox + $arrayMinutoRHbox[$index]->actividad;
                                        }
                                        $rango = $rango + $rangoRHbox;                //: -> RANGO
                                        $actividad = $actividad + $actividadRHbox;   //: -> ACTIVIDAD
                                    }
                                }
                            }
                        }
                        for ($j = 0; $j < sizeof($horasRuta); $j++) {
                            if ($horasRuta[$j]["hora"] == $hora) {
                                //* RECORREMOS EN FORMATO MINUTOS
                                for ($m = 0; $m < 6; $m++) {
                                    if (isset($horasRuta[$j]["minuto"][$m])) {
                                        $rangoRuta = 0;
                                        $actividadRuta = 0;
                                        $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                        //* RECORREMOS MINUTOS RUTA
                                        for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                            $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                            $actividadRuta = $actividadRuta + $arrayMinutoRuta[$element]->actividad;
                                        }
                                        $rango = $rango + $rangoRuta;               //: -> RANGO
                                        $actividad = $actividad + $actividadRuta;   //: -> ACTIVIDAD
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if (sizeof($horasRHbox) != 0) {
                    $rango = 0;
                    $actividad = 0;
                    for ($i = 0; $i < sizeof($horasRHbox); $i++) {
                        //* RECORREMOS EN FORMATO HORAS
                        for ($hora = 0; $hora < 24; $hora++) {
                            if ($horasRHbox[$i]["hora"] == $hora) {
                                //* RECORREMOS EN FORMATO MINUTOS
                                for ($m = 0; $m < 6; $m++) {
                                    if (isset($horasRHbox[$i]["minuto"][$m])) {
                                        $rangoRHbox = 0;
                                        $actividadRHbox = 0;
                                        $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                        //* RECORREMOS MINUTOS RH BOX
                                        for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                            $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                            $actividadRHbox = $actividadRHbox + $arrayMinutoRHbox[$index]->actividad;
                                        }
                                        $rango = $rango + $rangoRHbox;               //: -> RANGO
                                        $actividad = $actividad + $actividadRHbox;   //: -> ACTIVIDAD
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if (sizeof($horasRuta) != 0) {
                        $rango = 0;
                        $actividad = 0;
                        for ($j = 0; $j < sizeof($horasRuta); $j++) {
                            //* RECORREMOS EN FORMATO HORAS
                            for ($hora = 0; $hora < 24; $hora++) {
                                if ($horasRuta[$j]["hora"] == $hora) {
                                    //* RECORREMOS EN FORMATO MINUTOS
                                    for ($m = 0; $m < 6; $m++) {
                                        if (isset($horasRuta[$j]["minuto"][$m])) {
                                            $rangoRuta = 0;
                                            $actividadRuta = 0;
                                            $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                            //* RECORREMOS MINUTOS RUTA
                                            for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                                $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                                $actividadRuta = $actividadRuta + $arrayMinutoRuta[$element]->actividad;
                                            }
                                            $rango = $rango + $rangoRuta;
                                            $actividad = $actividad + $actividadRuta;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $rango = 0;
                        $actividad = 0;
                    }
                }
            }
            $productividad = 0;
            if ($rango != 0) {
                $productividad = ($actividad / $rango) * 100;
                $productividad = (float) number_format($productividad, 2);
            }
            // * OBTENER HORA DEL SERVIDOR
            $horaActual = $fecha->isoFormat('YYYY-MM-DDTHH:mm:ss');
            $respuesta["tiempo"] = gmdate('H:i:s', $rango);
            $respuesta["horaActual"] = $horaActual;
            $respuesta["productividad"] = $productividad;
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
            $fecha = Carbon::now('America/Lima');
            $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
            $horarioG = DB::table('horario_empleado as he')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->select('he.horario_dias_id', 'he.horario_horario_id', 'he.horarioComp', 'he.fuera_horario', 'he.horaAdic', 'he.nHoraAdic')
                ->where('he.empleado_emple_id', '=', $request->get('idEmpleado'))
                ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                ->where('he.estado', '=', 1)
                ->get();
            foreach ($horarioG as $resp) {
                $horario_dias = DB::table('horario_dias  as hd')
                    ->select(DB::raw('DATE(hd.start) as start'), 'hd.id')
                    ->where('hd.id', '=', $resp->horario_dias_id)
                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
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
                $horario->numeroHorasAdicional = $resp->nHoraAdic == null ? 0 : $resp->nHoraAdic;
                foreach ($pausas as $p) {
                    $p->pausaI = $p->pausaI == null ? 0 : $p->pausaI;
                    $p->pausaF = $p->pausaF == null ? 0 : $p->pausaF;
                }
                $horario->pausas = $pausas;
                $horaN = DateTime::createFromFormat("H:i:s", $horario->horasObligadas);
                $horario->horasObligadas = $horaN->format("H") * 60 + $horaN->format("i") + $horaN->format("s") / 60;
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

    //? API DE PUNTOS DE CONTROL
    public function puntoControlRuta(Request $request)
    {
        /* OBTENEMOS EL ID DE EMPLEADO */
        $idEmpleado = $request->idEmpleado;
        /* ------------------------------- */

        /* OBTENEMOS PUNTOS DE CONTROL DE EMPLEADO */

                $punto_control = DB::table('punto_control as pc')
                ->join('punto_control_empleado as pce','pc.id','=','pce.idPuntoControl')
                ->select(
                    'pc.id',
                    'pc.descripcion',
                    'pc.codigoControl',
                    'pc.verificacion',
                    'pc.estado'
                )
                ->where('pce.idEmpleado','=',$idEmpleado)
                ->where('pce.estado','=',1)
                ->where('pc.controlRuta', '=', 1)
                ->where('pc.estado', '=', 1)
                ->get();


                      /* recorremos punto de de geo de cada punto de control */
                foreach ($punto_control as $tab) {
                    $punto_control_geo = DB::table('punto_control_geo as pcg')
                        ->select('pcg.id', 'pcg.latitud', 'pcg.longitud', 'pcg.radio')
                        ->where('pcg.idPuntoControl', '=', $tab->id)
                        ->distinct('pcg.id')
                        ->get();

                    /* INSERTAMOS PUNTOS GEO */
                    $tab->puntosGeo = $punto_control_geo;

                }

        /* ------------------------------------------------- */

        /* ENVIAMOS DATA */
                //? SI NO HAY PUNTOS DE CONTROL ENVIA VACIO []
            return response()->json(array('status' => 200, "puntosControl" => $punto_control));


    }
}
