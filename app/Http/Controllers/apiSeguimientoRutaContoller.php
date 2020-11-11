<?php

namespace App\Http\Controllers;

use App\actividad_empleado;
use App\empleado;
use App\ubicacion;
use App\ubicacion_ruta;
use App\vinculacion_ruta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

                            return response()->json(array(
                                "idEmpleado" => $empleado->emple_id,
                                "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
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

                                return response()->json(array(
                                    "idEmpleado" => $empleado->emple_id,
                                    "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
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
            $validacion = Validator::make($atributo, [
                'hora_ini' => 'required',
                'hora_fin' => 'required',
                'idActividad' => 'required',
                'idEmpleado' => 'required',
                'latitud_ini' => 'required',
                'longitud_ini' => 'required',
                'latitud_fin' => 'required',
                'longitud_fin' => 'required'
            ], [
                'required' => ':attribute es obligatorio'
            ]);
            if ($validacion->fails()) {
                return response()->json($validacion->errors(), 400);
            }
        }
        foreach ($request->all() as $ubicaciones) {
            //? GUARDAR UBICACIONES
            $ubicacion = new ubicacion();
            $ubicacion->hora_ini = $ubicaciones['hora_ini'];
            $ubicacion->hora_fin = $ubicaciones['hora_fin'];
            if (isset($ubicaciones['idHorario_dias'])) {
                $ubicacion->idHorario_dias = $ubicaciones['idHorario_dias'];
            }
            $ubicacion->idActividad = $ubicaciones['idActividad'];
            $ubicacion->idEmpleado = $ubicaciones['idEmpleado'];
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
        }

        return response()->json($ubicacion, 200);
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
}
