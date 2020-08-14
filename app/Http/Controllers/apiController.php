<?php

namespace App\Http\Controllers;

use App\actividad;
use App\captura;
use App\control;
use App\empleado;
use App\envio;
use App\horario;
use App\horario_dias;
use App\horario_empleado;
use App\licencia_empleado;
use App\promedio_captura;
use App\proyecto;
use App\proyecto_empleado;
use App\tarea;
use App\User;
use App\vinculacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use phpDocumentor\Reflection\Types\True_;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class apiController extends Controller
{
    public function api()
    {
        $empleado = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->select(
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'c.cargo_descripcion',
                'a.area_descripcion',
                'cc.centroC_descripcion',
                'e.emple_id'
            )
            ->get();
        return $empleado;
    }

    public function licenciaProducto(Request $request)
    {
        $licencia = $request->get('licencia');
        $licencia_empleado = licencia_empleado::where('licencia', '=', $licencia)->get()->first();
        if ($licencia_empleado) {
            if ($licencia_empleado->disponible == 'e') {
                $licencia_empleado->disponible = 'a';
                $licencia_empleado->save();
                return response()->json("Licencia Correcta", 200);
            }
            return response()->json("Licencia no disponible", 400);
        }
        return response()->json("Licencia incorrecta", 400);
    }

    public function verificacion(Request $request)
    {
        $nroD = $request->get('nroDocumento');
        $codigo = $request->get('codigo');
        $decode = base_convert(intval($codigo), 10, 36);
        $explode = explode("s", $decode);
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
            ->where('emple_nDoc', '=', $nroD)
            ->where('e.users_id', '=', $explode[0])
            ->where('e.emple_estado', '=', 1)
            ->get()->first();

        $idUser = $explode[0];

        if ($empleado) {
            $vinculacion = vinculacion::where('id', '=', $explode[1])->get()->first();
            if ($vinculacion) {
                if ($vinculacion->hash == $request->get('codigo')) {
                    if ($vinculacion->pc_mac !=  null) {
                        if ($vinculacion->pc_mac == $request->get('pc_mac')) {
                            $factory = JWTFactory::customClaims([
                                'sub' => env('API_id'),
                            ]);
                            $payload = $factory->make();
                            $token = JWTAuth::encode($payload);
                            $user = User::where('id', '=', $idUser)->get()->first();
                            return response()->json(array(
                                "corte" => $user->corteCaptura, "idEmpleado" => $empleado->emple_id, "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
                                'idUser' => $idUser, 'token' => $token->get()
                            ), 200);
                        } else {
                            return response()->json("Pc no coinciden", 400);
                        }
                    } else {
                        $vinculacion->pc_mac = $request->get('pc_mac');
                        $vinculacion->save();
                        $factory = JWTFactory::customClaims([
                            'sub' => env('API_id'),
                        ]);
                        $payload = $factory->make();
                        $token = JWTAuth::encode($payload);
                        return response()->json(array(
                            "idEmpleado" => $empleado->emple_id, "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
                            'idUser' => $idUser, 'token' => $token->get()
                        ), 200);
                    }
                }
                return response()->json("Código erróneo", 400);
            }
            return response()->json("Aún no a enviado correo empleado.", 400);
        }
        return response()->json("Empleado no registrado", 400);
    }

    public function selectProyecto(Request $request)
    {
        $empleado = $request->get('emple_id');

        $proyecto_empleado = DB::table('proyecto_empleado as pe')
            ->where('empleado_emple_id', $empleado)
            ->get()->first();

        if ($proyecto_empleado) {
            //PROYECTO
            $datos = DB::table('empleado as e')
                ->join('proyecto_empleado as pe', 'pe.empleado_emple_id', '=', 'e.emple_id')
                ->join('proyecto as pr', 'pr.Proye_id', '=', 'pe.Proyecto_Proye_id')
                ->leftJoin('tarea as t', 't.Proyecto_Proye_id', '=', 'pr.Proye_id')
                ->leftJoin('actividad as ac', 'ac.Tarea_Tarea_id', '=', 't.Tarea_id')
                ->select('pr.Proye_id', 'pr.Proye_Nombre')
                ->where('e.emple_id', '=', $empleado)
                ->where('pr.Proye_estado', '=', 1)
                ->groupBy('pr.Proye_id')
                ->get();

            $respuesta = [];

            foreach ($datos as $dato) {
                array_push($respuesta, array("Tarea_id" => $dato->Proye_id, "Tarea_Nombre" => $dato->Proye_Nombre));
                //TAREAS
                /*$tareas = DB::table('empleado as e')
                    ->join('proyecto_empleado as pe', 'pe.empleado_emple_id', '=', 'e.emple_id')
                    ->join('proyecto as pr', 'pr.Proye_id', '=', 'pe.Proyecto_Proye_id')
                    ->leftJoin('tarea as t', 't.Proyecto_Proye_id', '=', 'pr.Proye_id')
                    ->leftJoin('actividad as ac', 'ac.Tarea_Tarea_id', '=', 't.Tarea_id')
                    ->select('t.Tarea_id', 't.Tarea_Nombre')
                    ->where('e.emple_id', '=', $empleado)
                    ->groupBy('t.Tarea_id')
                    ->get();

                $elemento = [];
                foreach ($tareas as $tarea) {
                    array_push($elemento, array("idTarea" => $tarea->Tarea_id, "Tarea" => $tarea->Tarea_Nombre));
                }

                //ACTIVIDAD
                $actividad = DB::table('empleado as e')
                    ->join('proyecto_empleado as pe', 'pe.empleado_emple_id', '=', 'e.emple_id')
                    ->join('proyecto as pr', 'pr.Proye_id', '=', 'pe.Proyecto_Proye_id')
                    ->leftJoin('tarea as t', 't.Proyecto_Proye_id', '=', 'pr.Proye_id')
                    ->leftJoin('actividad as ac', 'ac.Tarea_Tarea_id', '=', 't.Tarea_id')
                    ->select('ac.Activi_id', 'ac.Activi_Nombre', 't.Tarea_id')
                    ->where('e.emple_id', '=', $empleado)
                    ->get();

                $elementoA = [];
                foreach ($actividad as $activ) {
                    array_push($elementoA, array("idActividad" => $activ->Activi_id, "Actividad" => $activ->Activi_Nombre, "Tarea_id" => $activ->Tarea_id));
                }
                array_push($respuesta, array("Proye_id" => $dato->Proye_id, "Proye_Nombre" => $dato->Proye_Nombre, "Tareas" => $elemento, "Actividades" => $elementoA));*/
            }
            return response()->json($respuesta, 200);
        }
        return response()->json(null, 400);
    }

    public function agregarProyecto(Request $request)
    {
        $proyecto = new proyecto();
        $proyecto->Proye_Nombre = $request->get('Proye_Nombre');
        $proyecto->Proye_Detalle = $request->get('Proye_Detalle');
        $proyecto->Proye_estado = 1;
        $proyecto->idUser = $request->get('idUser');
        $proyecto->save();

        $idProyecto = $proyecto->Proye_id;

        $proyectoE = new proyecto_empleado();
        $proyectoE->Proyecto_Proye_id = $idProyecto;
        $proyectoE->empleado_emple_id = $request->get('idEmpleado');
        $proyectoE->Fecha_Ini = $request->get('Fecha_Ini');
        $proyectoE->Fecha_Fin = $request->get('Fecha_Fin');
        $proyectoE->save();

        return response()->json($idProyecto, 200);
    }

    public function editarProyecto(Request $request)
    {
        $proyecto = proyecto::where('Proye_id', $request->get('Proye_id'))->get()->first();
        if ($proyecto) {
            $proyecto->Proye_Nombre = $request->get('Proye_Nombre');
            $proyecto->Proye_Detalle = $request->get('Proye_Detalle');
            $proyecto->save();
            return response()->json($proyecto, 200);
        }
        return response()->json("Proyecto no encontrado", 400);
    }

    public function cambiarEstadoProyecto(Request $request)
    {
        $proyectoEmpleado = DB::table('proyecto_empleado as pe')
            ->select('pe.Proyecto_Proye_id')
            ->where('pe.Proyecto_Proye_id', '=', $request->get('idProyecto'))
            ->where('pe.empleado_emple_id', '=', $request->get('idEmpleado'))
            ->get()
            ->first();
        if ($proyectoEmpleado) {
            $proyecto = proyecto::findOrFail($proyectoEmpleado->Proyecto_Proye_id);
            $proyecto->Proye_estado = 0;
            $proyecto->save();
            return response()->json($request->get('idEmpleado'), 200);
        }
        return response()->json("Proyecto no encontrado", 400);
    }

    public function horario(Request $request)
    {
        $respuesta = [];
        $horario_empleado = DB::table('empleado as e')
            ->where('e.emple_id', '=', $request->get('idEmpleado'))
            ->get()
            ->first();
        if ($horario_empleado) {
            $horario = DB::table('horario_empleado as he')
                ->select('he.horario_dias_id', 'he.horario_horario_id')
                ->where('he.empleado_emple_id', '=', $request->get('idEmpleado'))
                ->get();

            foreach ($horario as $resp) {
                $horario_dias = DB::table('horario_dias  as hd')
                    ->select(DB::raw('DATE(hd.start) as start'), 'hd.id')
                    ->where('hd.id', '=', $resp->horario_dias_id)
                    ->get()->first();
                $horario = DB::table('horario as h')
                    ->select('h.horario_id', 'h.horario_descripcion', 'h.horaI', 'h.horaF')
                    ->where('h.horario_id', '=', $resp->horario_horario_id)
                    ->get()->first();
                $horario->idHorario_dias = $horario_dias->id;
                $fecha = Carbon::now();
                $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                if ($horario_dias->start == $fechaHoy) {
                    array_push($respuesta, $horario);
                }
            }
            return response()->json($respuesta, 200);
        }
        return response()->json("Empleado no encontrado", 400);
    }

    public function ultimoHorario(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $fecha = $request->get('fecha');
        $carbon = Carbon::create($fecha);
        $fechaAnterior = $carbon->add(-1, 'day');
        $fechaBuscar = $fechaAnterior->isoFormat('YYYY-MM-DD');
        $respuesta = [];
        $horarioD = [];
        $horarioEmpleado = DB::table('horario_empleado as he')
            ->select('he.horario_dias_id', 'he.horario_horario_id')
            ->where('he.empleado_emple_id', '=', $idEmpleado)
            ->get();
        foreach ($horarioEmpleado as $he) {
            $horarioDias = DB::table('horario_dias as hd')
                ->select(DB::raw('DATE(hd.start) as start'), 'hd.id')
                ->where('hd.id', '=', $he->horario_dias_id)
                ->get();
            foreach ($horarioDias as $hd) {
                if ($hd->start == $fechaBuscar) {
                    array_push($horarioD, $hd->id);
                }
            }
            foreach ($horarioD as $arrayH) {
                $hora = '00:00:00';
                $horarioE = horario_empleado::where('horario_dias_id', $arrayH)->where('empleado_emple_id', $idEmpleado)->get()->first();
                $horario = horario::where('horario_id', '=', $horarioE->horario_horario_id)->get()->first();
                if ($horario->horaI > $hora) {
                    $hora = $horario->horaI;
                    unset($respuesta);
                    $respuesta = array();
                    array_push($respuesta, $horario);
                }
            }
        }
        return response()->json($respuesta, 200);
    }

    public function apiTarea(Request $request)
    {
        $Proye_id = $request['Proye_id'];
        $proyecto = proyecto::where('Proye_id', $Proye_id)->first();
        if ($proyecto) {
            $tarea = new tarea();
            $tarea->Tarea_Nombre = $request['Tarea_Nombre'];
            $tarea->Proyecto_Proye_id = $Proye_id;
            $tarea->empleado_emple_id = $request['emple_id'];
            $tarea->save();
            $Tarea_Tarea_id = $tarea->Tarea_id;
            if ($request['Activi_Nombre'] != "") {
                $actividad = new actividad();
                $actividad->Activi_Nombre = $request['Activi_Nombre'];
                $actividad->Tarea_Tarea_id = $Tarea_Tarea_id;
                $actividad->empleado_emple_id = $request['emple_id'];
                $actividad->save();
                return response()->json([$tarea, $actividad], 200);
            }
            return response()->json($tarea, 200);
        }

        return response()->json($proyecto, 400);
    }

    public function apiActividad(Request $request)
    {
        $actividad = new actividad();
        $actividad->Activi_Nombre = $request['Activi_Nombre'];
        $actividad->Tarea_Tarea_id = $request['Tarea_Tarea_id'];
        $actividad->empleado_emple_id = $request['emple_id'];
        $actividad->save();
        return response()->json($actividad, 200);
    }

    public function editarApiTarea(Request $request)
    {
        $Tarea_id = $request['Tarea_id'];
        $tarea = tarea::where('Tarea_id', $Tarea_id)->first();
        if ($tarea) {
            $tarea->Tarea_Nombre = $request['Tarea_Nombre'];
            $tarea->save();
            return response()->json($tarea, 200);
        }
        return response()->json($tarea, 400);
    }

    public function editarApiActividad(Request $request)
    {
        $Activi_id = $request['Activi_id'];
        $actividad = actividad::where('Activi_id', $Activi_id)->first();
        if ($actividad) {
            $actividad->Activi_Nombre = $request['Activi_Nombre'];
            $actividad->save();
            return response()->json($actividad, 200);
        }
        return response()->json($actividad, 400);
    }

    public function envio(Request $request)
    {
        $envio = new envio();
        $envio->hora_Envio = $request->get('hora_Envio');
        $envio->Total_Envio = $request->get('Total_Envio');
        $envio->idEmpleado = $request->get('idEmpleado');
        $envio->save();
        $idEnvio = $envio->idEnvio;

        return response()->json($idEnvio, 200);
    }

    public function control(Request $request)
    {
        $idEnvio = $request['idEnvio'];
        $control = new control();
        $control->Proyecto_Proye_id = $request->get('Proyecto_Proye_id');
        $control->fecha_ini = $request->get('fecha_ini');
        $control->Fecha_fin = $request->get('Fecha_fin');
        $control->hora_ini = $request->get('hora_ini');
        $control->hora_fin = $request->get('hora_fin');
        $control->idEnvio = $idEnvio;
        if ($request->get('Tarea_Tarea_id') != '') {
            $control->Tarea_Tarea_id = $request->get('Tarea_Tarea_id');
        }
        if ($request->get('Actividad_Activi_id') != '') {
            $control->Actividad_Activi_id = $request->get('Actividad_Activi_id');
        }
        $control->idHorario_dias = $request->get('idHorario');
        $control->acumulado = $request->get('acumulado');
        $control->save();
        return response()->json($control, 200);
    }

    public function captura(Request $request)
    {
        $idEnvio = $request['idEnvio'];
        $promedioG = [];
        $captura = new captura();
        $captura->idEnvio = $idEnvio;
        $captura->estado = $request->get('estado');
        $captura->fecha_hora = $request->get('fecha_hora');
        $captura->imagen = $request->get('imagen');
        $captura->promedio = $request->get('promedio');
        $captura->save();

        $idCaptura = $captura->idCaptura;

        $control = control::where('idEnvio', '=', $idEnvio)->get()->first();
        $envio = envio::where('idEnvio', '=', $idEnvio)->get()->first();
        if ($control) {
            $idHorario_dias = $control->idHorario_dias;
            $busquedaUltimoControl = DB::table('control as c')
                ->join('envio as en', 'c.idEnvio', '=', 'en.idEnvio')
                ->select(DB::raw('MAX(c.idEnvio) as idEnvio'), DB::raw('COUNT(c.idHorario_dias) as total'))
                ->where('c.idHorario_dias', '=', $idHorario_dias)
                ->where('en.idEmpleado', '=', $envio->idEmpleado)
                ->get()
                ->first();
            if ($busquedaUltimoControl->total != 1) {
                $busquedaC = DB::table('control as c')
                    ->join('envio as en', 'c.idEnvio', '=', 'en.idEnvio')
                    ->select(DB::raw('MAX(c.idEnvio) as idEnvio'), DB::raw('COUNT(c.idHorario_dias) as total'))
                    ->where('c.idHorario_dias', '=', $idHorario_dias)
                    ->where('c.idHorario_dias', '!=', null)
                    ->where('en.idEmpleado', '=', $envio->idEmpleado)
                    ->where('c.Cont_id', '!=', $control->Cont_id)
                    ->get()
                    ->first();
                $capturaBusqueda = captura::where('idEnvio', '=', $busquedaC->idEnvio)->get();
                foreach ($capturaBusqueda as $cb) {
                    $promedio = DB::table('captura as c')
                        ->select('c.promedio', 'c.fecha_hora')
                        ->where('c.idCaptura', '=', $cb->idCaptura)
                        ->get()
                        ->first();
                    if ($promedio) {
                        $capturaRegistrada = captura::where('idCaptura', '=', $idCaptura)->get()->first();
                        //PROMEDIOS DE CAPTURAS
                        $explode2 = explode(":", $capturaRegistrada->promedio);
                        $calSeg2 = $explode2[0] * 3600 + $explode2[1] * 60 + $explode2[2];
                        $totalP = $calSeg2;
                        //RESTA POR FECHA HORA DE   CAPTURAS
                        $fecha = Carbon::create($capturaRegistrada->fecha_hora)->format('H:i:s');
                        $explo1 = explode(":", $fecha);
                        $calSegund = $explo1[0] * 3600 + $explo1[1] * 60 + $explo1[2];
                        $fecha1 = Carbon::create($promedio->fecha_hora)->format('H:i:s');
                        $explo2 = explode(":", $fecha1);
                        $calSegund2 = $explo2[0] * 3600 + $explo2[1] * 60 + $explo2[2];
                        $totalP1 = $calSegund - $calSegund2;
                        //VALIDACION DE CERO
                        if ($calSeg2 == 0) {
                            $round = 0;
                        } else {
                            //PROMEDIO
                            $promedio = floatval($totalP / $totalP1);
                            $promedioFinal = $promedio * 100;
                            $round = round($promedioFinal, 2);
                        }
                        //TABLA PROMEDIO_CAPTURA
                        $promedio_captura = new promedio_captura();
                        $promedio_captura->idCaptura = $idCaptura;
                        $promedio_captura->idHorario = $idHorario_dias;
                        $promedio_captura->promedio = $round;
                        $promedio_captura->tiempo_rango = $totalP1;
                        $promedio_captura->save();
                        array_push($promedioG, $promedio_captura);
                    }
                }
            } else {
                $promedio_captura = new promedio_captura();
                $promedio_captura->idCaptura = $idCaptura;
                $promedio_captura->idHorario = $idHorario_dias;
                $promedio_captura->promedio = 0;
                $promedio_captura->tiempo_rango = 0;
                $promedio_captura->save();
                array_push($promedioG, $promedio_captura);
            }
            if ($idHorario_dias == null) {
                $busquedaUltimaCaptura = DB::table('control as c')
                    ->join('envio as en', 'en.idEnvio', '=', 'c.idEnvio')
                    ->join('captura as cp', 'cp.idEnvio', '=', 'en.idEnvio')
                    ->select(DB::raw('MAX(c.idEnvio) as idEnvio'), 'c.idEnvio', DB::raw('COUNT(DATE(cp.fecha_hora)) as total'))
                    ->where('en.idEmpleado', '=', $envio->idEmpleado)
                    ->where('c.idHorario_dias', '=', $idHorario_dias)
                    ->groupBy(DB::raw('DATE(cp.fecha_hora)'))
                    ->get()
                    ->first();
                if ($busquedaUltimaCaptura->total != 1) {
                    $busquedaC = DB::table('control as c')
                        ->join('envio as en', 'en.idEnvio', '=', 'c.idEnvio')
                        ->join('captura as cp', 'cp.idEnvio', '=', 'en.idEnvio')
                        ->select(DB::raw('MAX(c.idEnvio) as idEnvio'), DB::raw('COUNT(DATE(cp.fecha_hora)) as total'))
                        ->where('c.idHorario_dias', '=', $idHorario_dias)
                        ->where('en.idEmpleado', '=', $envio->idEmpleado)
                        ->where('c.Cont_id', '!=', $control->Cont_id)
                        ->groupBy(DB::raw('DATE(cp.fecha_hora)'))
                        ->get()
                        ->first();
                    $capturaBusqueda = captura::where('idEnvio', '=', $busquedaC->idEnvio)->get();
                    foreach ($capturaBusqueda as $cb) {
                        $promedio = DB::table('captura as c')
                            ->select('c.promedio', 'c.fecha_hora')
                            ->where('c.idCaptura', '=', $cb->idCaptura)
                            ->get()
                            ->first();
                        if ($promedio) {
                            //RESTA DE PROMEDIOS DE CAPTURAS
                            $capturaRegistrada = captura::where('idCaptura', '=', $idCaptura)->get()->first();
                            $explode2 = explode(":", $capturaRegistrada->promedio);
                            $calSeg2 = $explode2[0] * 3600 + $explode2[1] * 60 + $explode2[2];
                            $totalP = $calSeg2;
                            //RESTA POR FECHA HORA DE   CAPTURAS
                            $fecha = Carbon::create($capturaRegistrada->fecha_hora)->format('H:i:s');
                            $explo1 = explode(":", $fecha);
                            $calSegund = $explo1[0] * 3600 + $explo1[1] * 60 + $explo1[2];
                            $fecha1 = Carbon::create($promedio->fecha_hora)->format('H:i:s');
                            $explo2 = explode(":", $fecha1);
                            $calSegund2 = $explo2[0] * 3600 + $explo2[1] * 60 + $explo2[2];
                            $totalP1 = $calSegund - $calSegund2;
                            if ($calSeg2 == 0) {
                                $round = 0;
                            } else {
                                //PROMEDIO
                                $promedio = floatval($totalP / $totalP1);
                                $promedioFinal = $promedio * 100;
                                $round = round($promedioFinal, 2);
                            }
                            //TABLA PROMEDIO_CAPTURA
                            $promedio_captura = new promedio_captura();
                            $promedio_captura->idCaptura = $idCaptura;
                            $promedio_captura->promedio = $round;
                            $promedio_captura->tiempo_rango = $totalP1;
                            $promedio_captura->save();
                        }
                    }
                } else {
                    $promedio_captura = new promedio_captura();
                    $promedio_captura->idCaptura = $idCaptura;
                    $promedio_captura->promedio = 0;
                    $promedio_captura->tiempo_rango = 0;
                    $promedio_captura->save();
                }
            }
        }

        return response()->json($captura, 200);
    }
}
