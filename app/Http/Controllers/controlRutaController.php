<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class controlRutaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        return view('ruta.rutaDiaria');
    }

    public function indexReporte()
    {
        $areas = DB::table('area as a')
            ->select('a.area_id', 'a.area_descripcion')
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->get();

        $cargos = DB::table('cargo as c')
            ->select('c.cargo_id', 'c.cargo_descripcion')
            ->where('c.organi_id', '=', session('sesionidorg'))
            ->get();

        return view('ruta.reporteSemanalRuta', ['areas' => $areas, 'cargos' => $cargos]);
    }

    public function reporte(Request $request)
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $fecha = $request->get('fecha');
        $fechaF = explode("a", $fecha);
        $area = $request->get('area');
        $cargo = $request->get('cargo');
        if (is_null($area) === true && is_null($cargo) === true) {
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();
                if ($invitado_empleadoIn != null) {
                    $empleados = DB::table('empleado as e')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                        ->groupBy('e.emple_id')
                        ->get();
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                        ->groupBy('e.emple_id')
                        ->get();
                }
            } else {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_id')
                    ->get();
            }
        } else {
            if (is_null($area) === false && is_null($cargo) === true) {
                if ($usuario_organizacion->rol_id == 3) {
                    $invitado = DB::table('invitado as in')
                        ->where('organi_id', '=', session('sesionidorg'))
                        ->where('rol_id', '=', 3)
                        ->where('in.user_Invitado', '=', Auth::user()->id)
                        ->get()->first();
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->whereIn('e.emple_area', $area)
                            ->groupBy('e.emple_id')
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->whereIn('e.emple_area', $area)
                            ->groupBy('e.emple_id')
                            ->get();
                    }
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_area', $area)
                        ->groupBy('e.emple_id')
                        ->get();
                }
            }
            if (is_null($area) === true && is_null($cargo) === false) {
                if ($usuario_organizacion->rol_id == 3) {
                    $invitado = DB::table('invitado as in')
                        ->where('organi_id', '=', session('sesionidorg'))
                        ->where('rol_id', '=', 3)
                        ->where('in.user_Invitado', '=', Auth::user()->id)
                        ->get()->first();
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->whereIn('e.emple_cargo', $cargo)
                            ->groupBy('e.emple_id')
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->whereIn('e.emple_cargo', $cargo)
                            ->groupBy('e.emple_id')
                            ->get();
                    }
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_cargo', $cargo)
                        ->groupBy('e.emple_id')
                        ->get();
                }
            }
            if (is_null($area) === false && is_null($cargo) === false) {
                if ($usuario_organizacion->rol_id == 3) {
                    $invitado = DB::table('invitado as in')
                        ->where('organi_id', '=', session('sesionidorg'))
                        ->where('rol_id', '=', 3)
                        ->where('in.user_Invitado', '=', Auth::user()->id)
                        ->get()->first();
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->whereIn('e.emple_cargo', $cargo)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_id')
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->whereIn('e.emple_cargo', $cargo)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_id')
                            ->get();
                    }
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_area', $area)
                        ->whereIn('e.emple_cargo', $cargo)
                        ->groupBy('e.emple_id')
                        ->get();
                }
            }
        }

        $respuesta = [];

        if (sizeof($empleados) > 0) {
            $sql = "IF(h.id is null,if(DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)) >= 0 , DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)), DAY(DATE(cp.hora_ini)) ),
        if(DATEDIFF('" . $fechaF[1] . "',DATE(h.start)) >= 0,DATEDIFF('" . $fechaF[1] . "',DATE(h.start)), DAY(DATE(h.start)) )) as dia";
            // DB::enableQueryLog();
            $horasTrabajadas = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->leftJoin('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                ->join('actividad as a', 'a.Activi_id', '=', 'cp.idActividad')
                ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
                ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
                ->select(
                    'e.emple_id',
                    'p.perso_nombre',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start)) as fecha'),
                    DB::raw('TIME(cp.hora_ini) as hora_ini'),
                    DB::raw('TIME_FORMAT(SEC_TO_TIME(SUM(promedio.tiempo_rango)), "%H:%i:%s") as Total_Envio'),
                    DB::raw('SUM(cp.actividad) as sumaA'),
                    DB::raw('SUM(promedio.tiempo_rango) as sumaR'),
                    DB::raw($sql),
                    DB::raw('DATE(cp.hora_fin) as fecha_captura')
                )
                ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '>=', $fechaF[0])
                ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '<=', $fechaF[1])
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id', DB::raw('DATE(cp.hora_ini)'))
                ->get();

            function agruparEmpleadosCaptura($array)
            {
                $resultado = array();
                foreach ($array as $empleado) {
                    $hora = explode(":", $empleado->hora_ini);
                    $fechaA = $empleado->dia;
                    if (!isset($resultado[$empleado->emple_id])) {
                        $resultado[$empleado->emple_id] = array("empleado" => $empleado->emple_id, "datos" => array());
                    }
                    if (!isset($resultado[$empleado->emple_id]["datos"][$fechaA])) {
                        $resultado[$empleado->emple_id]["datos"][$fechaA] = array();
                    }
                    if (!isset($resultado[$empleado->emple_id]["datos"][$fechaA][$hora[0]]["minuto"][$hora[1][0]])) {
                        $resultado[$empleado->emple_id]["datos"][$fechaA][$hora[0]]["minuto"][$hora[1][0]] = array();
                    }
                    array_push($resultado[$empleado->emple_id]["datos"][$fechaA][$hora[0]]["minuto"][$hora[1][0]], $empleado);
                }
                return array_values($resultado);
            }
            $sqlCaptura = "IF(h.id is null,if(DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)) >= 0 , DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)), DAY(DATE(cp.hora_ini)) ),
        if(DATEDIFF('" . $fechaF[1] . "',DATE(h.start)) >= 0,DATEDIFF('" . $fechaF[1] . "',DATE(h.start)), DAY(DATE(h.start)) )) as dia";
            $tiempoDiaCaptura = DB::table('empleado as e')
                ->leftJoin('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                ->join('actividad as a', 'a.Activi_id', '=', 'cp.idActividad')
                ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
                ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
                ->select(
                    'e.emple_id',
                    DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start)) as fecha'),
                    DB::raw('TIME(cp.hora_ini) as hora_ini'),
                    DB::raw('TIME(cp.hora_fin) as hora_fin'),
                    DB::raw($sqlCaptura),
                    'cp.actividad',
                    'promedio.tiempo_rango'
                )
                ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '>=', $fechaF[0])
                ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '<=', $fechaF[1])
                ->orderBy('cp.hora_ini', 'asc')
                ->get();
            $tiempoDiaCaptura = agruparEmpleadosCaptura($tiempoDiaCaptura);
            dd($tiempoDiaCaptura);
            // dd(DB::getQueryLog());
            $date1 = new DateTime($fechaF[0]);
            $date2 = new DateTime($fechaF[1]);
            $diff = $date1->diff($date2);
            //Array
            $horas = array();
            $dias = array();
            $sumaActividad = array();
            $sumaRango = array();

            for ($i = 0; $i <= $diff->days; $i++) {
                array_push($horas, "00:00:00");
                array_push($sumaActividad, "0");
                array_push($sumaRango, "0");
                $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));

                array_push($dias, date('Y-m-j', $dia));
            }

            foreach ($empleados as $empleado) {
                array_push($respuesta, array(
                    "id" => $empleado->emple_id,
                    "nombre" => $empleado->nombre,
                    "apPaterno" => $empleado->apPaterno,
                    "apMaterno" => $empleado->apMaterno,
                    "horas" => $horas,
                    "fechaF" => $dias,
                    "sumaActividad" => $sumaActividad,
                    "sumaRango" => $sumaRango
                ));
            }
            for ($j = 0; $j < sizeof($respuesta); $j++) {
                for ($i = 0; $i < sizeof($horasTrabajadas); $i++) {
                    if ($respuesta[$j]["id"] == $horasTrabajadas[$i]->emple_id) {
                        $respuesta[$j]["horas"][$horasTrabajadas[$i]->dia] = $horasTrabajadas[$i]->Total_Envio == null ? "00:00:00" : $horasTrabajadas[$i]->Total_Envio;
                        $respuesta[$j]["sumaActividad"][$horasTrabajadas[$i]->dia] = $horasTrabajadas[$i]->sumaA == null ? "0" : $horasTrabajadas[$i]->sumaA;
                        $respuesta[$j]["sumaRango"][$horasTrabajadas[$i]->dia] = $horasTrabajadas[$i]->sumaR == null ? "0" : $horasTrabajadas[$i]->sumaR;
                    }
                }
                $respuesta[$j]['horas'] = array_reverse($respuesta[$j]['horas']);
                $respuesta[$j]['sumaActividad'] = array_reverse($respuesta[$j]['sumaActividad']);
                $respuesta[$j]['sumaRango'] = array_reverse($respuesta[$j]['sumaRango']);
            }
        }
        return response()->json($respuesta, 200);
    }
}
