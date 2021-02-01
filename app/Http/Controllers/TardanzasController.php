<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\dispositivo_area;
use App\dispositivo_controlador;
use App\dispositivo_empleado;
use App\dispositivos;
use App\horario;
use App\marcacion_puerta;
use App\pausas_horario;
use App\tardanza;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use LengthException;
use Illuminate\Database\Eloquent\Collection;
use DateTime;

class TardanzasController extends Controller
{

    public function reporteTablaEmpCR(Request $request)
    {
        $emple_id = $request->idemp;
        $fecha1 = $request->fecha1;
        $fechaR = Carbon::create($fecha1);

        $fecha2 = $request->fecha2;
        $fechaF = Carbon::create($fecha2);

        //$marcaciones = new Collection();
        $datos = new Collection();

        $organizacion = DB::table('organizacion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();

        if($emple_id == 0){
            $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                    ->leftjoin('horario_empleado as he', 'he.empleado_emple_id', '=', 'e.emple_id')
                    ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                    ->select(
                        'p.perso_nombre', 
                        'p.perso_apPaterno', 
                        'p.perso_apMaterno', 
                        'e.emple_nDoc', 
                        'a.area_descripcion', 
                        'c.cargo_descripcion', 
                        'e.emple_codigo', 
                        'e.emple_id',
                        'cp.hora_ini',
                        'ho.horaI as entrada',
                        DB::raw('TIME(cp.hora_ini) as hora'),
                        DB::raw('DATE(cp.hora_ini) as dia'),
                        DB::raw("IF(TIMEDIFF(MIN(TIME(cp.hora_ini)), ADDDATE(TIME(ho.horaI), INTERVAL ho.horario_tolerancia minute)) > 0, 1, 0) as marcaTardanza")
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('ho.hora_contTardanza', '=', 1)
                    ->groupBy(DB::raw('DATE(cp.hora_ini)'), 'e.emple_id')
                    ->orderBy('e.emple_id')
                    ->get();
        } else {
            $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                    ->leftjoin('horario_empleado as he', 'he.empleado_emple_id', '=', 'e.emple_id')
                    ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                    ->select(
                        'p.perso_nombre', 
                        'p.perso_apPaterno', 
                        'p.perso_apMaterno', 
                        'e.emple_nDoc', 
                        'a.area_descripcion', 
                        'c.cargo_descripcion', 
                        'e.emple_codigo', 
                        'e.emple_id',
                        'cp.hora_ini',
                        'ho.horaI as entrada',
                        DB::raw('TIME(cp.hora_ini) as hora'),
                        DB::raw('DATE(cp.hora_ini) as dia'),
                        DB::raw("IF(TIMEDIFF(MIN(TIME(cp.hora_ini)), ADDDATE(TIME(ho.horaI), INTERVAL ho.horario_tolerancia minute)) > 0, '1', '0') as marcaTardanza")
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('ho.hora_contTardanza', '=', 1)
                    ->where('e.emple_id', '=', $emple_id)
                    ->groupBy(DB::raw('DATE(cp.hora_ini)'), 'e.emple_id')
                    ->orderBy('e.emple_id')
                    ->get();
        }
    
        //HORARIO DE CADA EMPLEADO
        $employee = -1;
        $sumTardanza = 0;
        $i = 0;
        $dEmployee = 0;
        $len = count($empleados);

        foreach($empleados as $key => $empleado){
            $dia = Carbon::create($empleado->dia);
            // FECHAS DENTRO DEL RANGO ELEGIDO
            if ($dia >= $fechaR && $dia <= $fechaF) {

                if ($i == 0) {
                    $employee = $empleado->emple_id;
                }

                if ($employee != $empleado->emple_id) {
                    $datos->push($obj);
                    $employee = $empleado->emple_id;
                    $sumTardanza = 0;
                    $dEmployee++;
                }
                $sumTardanza += $empleado->marcaTardanza;
                if(isset($empleado->area_descripcion))
                    $area = $empleado->area_descripcion;
                else 
                    $area = "--";

                if (isset($empleado->cargo_descripcion))
                    $cargo = $empleado->cargo_descripcion;
                else 
                    $cargo = "--";

                if (isset($empleado->emple_codigo))
                    $codigo = $empleado->emple_codigo;
                else 
                    $codigo = $empleado->emple_nDoc;

                $obj = (object) array(
                    'area_descripcion' => $area,
                    'cargo_descripcion' => $cargo,
                    'empleado_id' => $empleado->emple_id,
                    'emple_code' => $codigo,
                    'emple_nDoc' => $empleado->emple_nDoc,
                    'entradaModif' => $empleado->hora_ini,
                    'horario' => "Horario",
                    'idhorario' => 1,
                    'tardanzas' => $sumTardanza,
                    'marcaciones' => (object) array(
                        'entrada' => $empleado->hora_ini,
                        'idMarcacion' => 1,
                        'salida' => $empleado->hora_ini
                    ),
                    'organi_id' => session('sesionidorg'),
                    'organi_razonSocial' => $organizacion->organi_razonSocial,
                    'organi_direccion' => $organizacion->organi_direccion,
                    'organi_ruc' => $organizacion->organi_ruc,
                    'fecha' => now()->format('d-m-Y H:i:s'),
                    'fechaD' => $fecha1,
                    'fechaH' => $fecha2,
                    'perso_apMaterno' => $empleado->perso_apMaterno,
                    'perso_apPaterno' => $empleado->perso_apPaterno,
                    'perso_nombre' => $empleado->perso_nombre
                );
                $i++;
            }

            if ($len - 1 == $key && $i > 0) {
                $datos->push($obj);
            }
        }

        //dd($datos);

        //$marcacionesX = Arr::flatten($marcaciones);
        return response()->json($datos, 200);
    }

    public function EmpleadoReporteTardanzas(Request $request)
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->leftjoin('organizacion as o', 'o.organi_id', '=', 'uso.organi_id')
            ->select('uso.*', 'o.organi_ruc as ruc', 'o.organi_razonSocial as razonSocial', 'o.organi_direccion as direccion')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $fecha = $request->get('fecha');
        $fechaF = explode("a", $fecha);
        $area = $request->get('area');
        $empleadoL = $request->get('empleadoL');
        if (is_null($area) === true && is_null($empleadoL) === true) {
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();

                if ($invitado->verTodosEmps == 1) {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->groupBy('e.emple_id')
                        ->get();
                } else {
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
                            ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
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
                            ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_id')
                            ->get();
                    }
                }
            } else {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_id')
                    ->get();
            }
        } else {
            if (is_null($area) === false && is_null($empleadoL) === true) {
                if ($usuario_organizacion->rol_id == 3) {
                    $invitado = DB::table('invitado as in')
                        ->where('organi_id', '=', session('sesionidorg'))
                        ->where('rol_id', '=', 3)
                        ->where('in.user_Invitado', '=', Auth::user()->id)
                        ->get()->first();

                    if ($invitado->verTodosEmps == 1) {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->groupBy('e.emple_id')
                            ->get();
                    } else {

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
                                ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
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
                                ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->whereIn('e.emple_area', $area)
                                ->groupBy('e.emple_id')
                                ->get();
                        }
                    }
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_area', $area)
                        ->groupBy('e.emple_id')
                        ->get();
                }
            }
            if (is_null($area) === true && is_null($empleadoL) === false) {
                if ($usuario_organizacion->rol_id == 3) {
                    $invitado = DB::table('invitado as in')
                        ->where('organi_id', '=', session('sesionidorg'))
                        ->where('rol_id', '=', 3)
                        ->where('in.user_Invitado', '=', Auth::user()->id)
                        ->get()->first();

                    if ($invitado->verTodosEmps == 1) {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->groupBy('e.emple_id')
                            ->get();
                    } else {
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
                                ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->groupBy('e.emple_id')
                                ->get();
                        } else {
                            $empleados = DB::table('empleado as e')
                                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->groupBy('e.emple_id')
                                ->get();
                        }
                    }
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_id', $empleadoL)
                        ->groupBy('e.emple_id')
                        ->get();
                }
            }
            if (is_null($area) === false && is_null($empleadoL) === false) {
                if ($usuario_organizacion->rol_id == 3) {
                    $invitado = DB::table('invitado as in')
                        ->where('organi_id', '=', session('sesionidorg'))
                        ->where('rol_id', '=', 3)
                        ->where('in.user_Invitado', '=', Auth::user()->id)
                        ->get()->first();

                    if ($invitado->verTodosEmps == 1) {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->groupBy('e.emple_id')
                            ->get();
                    } else {
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
                                ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_area', $area)
                                ->whereIn('e.emple_id', $empleadoL)
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
                                ->select('e.emple_id', 'e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_area', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->groupBy('e.emple_id')
                                ->get();
                        }
                    }
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id','e.emple_nDoc', 'e.emple_codigo', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_area', $area)
                        ->whereIn('e.emple_id', $empleadoL)
                        ->groupBy('e.emple_id')
                        ->get();
                }
            }
        }

        $respuesta = [];

        if (sizeof($empleados) > 0) {
            $sql = "IF(h.id is null,if(DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)) >= 0 , DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)), DAY(DATE(cp.hora_ini)) ), if(DATEDIFF('" . $fechaF[1] . "',DATE(h.start)) >= 0,DATEDIFF('" . $fechaF[1] . "',DATE(h.start)), DAY(DATE(h.start)) )) as dia";
            // DB::enableQueryLog();

            $tardanzasTotales = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->leftJoin('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                ->join('actividad as a', 'a.Activi_id', '=', 'cp.idActividad')
                ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
                ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
                ->leftjoin('horario_empleado as he', 'he.empleado_emple_id', '=', 'e.emple_id')
                ->leftjoin('horario as ho', 'ho.horario_id', '=', 'he.horario_horario_id')
                ->select(
                    'e.emple_id',
                    'p.perso_nombre',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    DB::raw('IF(e.emple_codigo is null, e.emple_nDoc , e.emple_codigo ) as codigo'),
                    DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start)) as fecha'),
                    DB::raw('TIME(cp.hora_ini) as hora_ini'),
                    DB::raw('IF(TIMEDIFF(TIME(MIN(cp.hora_ini)), ADDDATE(TIME(ho.horaI), INTERVAL ho.horario_tolerancia minute)) > 0, TIMEDIFF(TIME(MIN(cp.hora_ini)), TIME(ho.horaI)), NULL) as Total_Envio'),
                    DB::raw('SUM(cp.actividad) as sumaA'),
                    DB::raw('SUM(promedio.tiempo_rango) as sumaR'),
                    DB::raw($sql),
                    DB::raw('DATE(cp.hora_fin) as fecha_captura')
                )
                ->where('ho.hora_contTardanza', '=', 1)
                ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '>=', $fechaF[0])
                ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '<=', $fechaF[1])
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id', DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'))
                ->get();
            // dd(DB::getQueryLog());
            //dd($horasTrabajadas);
            $date1 = new DateTime($fechaF[0]);
            $date2 = new DateTime($fechaF[1]);
            $diff = $date1->diff($date2);
            //Array
            $horas = array();
            $dias = array();

            for ($i = 0; $i <= $diff->days; $i++) {
                array_push($horas, "00:00:00");
                $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));

                array_push($dias, date('Y-m-j', $dia));
            }

            foreach ($empleados as $empleado) {
                array_push($respuesta, array(
                    "id" => $empleado->emple_id, "nombre" => $empleado->nombre, "apPaterno" => $empleado->apPaterno,
                    "apMaterno" => $empleado->apMaterno, "horas" => $horas, "fechaF" => $dias, "ruc" => $usuario_organizacion->ruc, "razonSocial" => $usuario_organizacion->razonSocial, "direccion" => $usuario_organizacion->direccion,"codigo" => $empleado->emple_codigo, "documento" => $empleado->emple_nDoc , "fecha" => now()->format('d-m-Y H:i:s'), "fechaD" => $fechaF[0], "fechaH" => $fechaF[1]
                ));
            }
            for ($j = 0; $j < sizeof($respuesta); $j++) {
                for ($i = 0; $i < sizeof($tardanzasTotales); $i++) {
                    if ($respuesta[$j]["id"] == $tardanzasTotales[$i]->emple_id) {
                        $respuesta[$j]["horas"][$tardanzasTotales[$i]->dia] = $tardanzasTotales[$i]->Total_Envio == null ? "00:00:00" : $tardanzasTotales[$i]->Total_Envio;
                    }
                }
                $respuesta[$j]['horas'] = array_reverse($respuesta[$j]['horas']);
            }
        }
        return response()->json($respuesta, 200);
    }

    public function reporteMatrizTardanzas()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
            $usuario_organizacion = DB::table('usuario_organizacion as uso')
                ->where('uso.organi_id', '=', session('sesionidorg'))
                ->where('uso.user_id', '=', Auth::user()->id)
                ->get()->first();
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                if ($invitado->verTodosEmps == 1) {
                    //* EMPLEADO
                    $empleado = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->groupBy('e.emple_id')
                        ->get();

                    //? AREA
                    $areas =  DB::table('empleado as e')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->select(
                            'a.area_id',
                            'a.area_descripcion'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->groupBy('e.emple_area')
                        ->get();
                } else {
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();

                    if ($invitado_empleadoIn != null) {
                        //* EMPLEADO
                        $empleado = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_id')
                            ->get();

                        //? AREA
                        $areas =  DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->select(
                                'a.area_id',
                                'a.area_descripcion'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_area')
                            ->get();
                    } else {
                        //* EMPLEADO
                        $empleado = DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_id')
                            ->get();

                        //? AREA
                        $areas =  DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->select(
                                'a.area_id',
                                'a.area_descripcion'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy('e.emple_area')
                            ->get();
                    }
                }
            } else {
                // * EMPLEADO
                $empleado = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_id')
                    ->get();
                //? AREA
                $areas =  DB::table('empleado as e')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                    ->select(
                        'a.area_id',
                        'a.area_descripcion'
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_area')
                    ->get();
            }

            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('rol_id', '=', 3)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    if ($invitadod->modoCR == 1) {

                        return view('tareas.reporteMatrizTardanzas', ['empleado' => $empleado, 'areas' => $areas]);
                    } else {
                        return redirect('/dashboard');
                    }
                    /*   */
                } else {
                    return view('tareas.reporteMatrizTardanzas', ['empleado' => $empleado, 'areas' => $areas]);
                }
            } else {
                return view('tareas.reporteMatrizTardanzas', ['empleado' => $empleado, 'areas' => $areas]);
            }
        }
    }

    public function RTardanzas()
    {
        if(session('sesionidorg')==NULL){
            return redirect('/');
        }

        $organizacion = DB::table('organizacion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();
        $nombreOrga = $organizacion->organi_razonSocial;
        $ruc = $organizacion->organi_ruc;
        $direccion = $organizacion->organi_direccion;

        $empleado_id = 2;

        $empleados = DB::table('empleado as e')
        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
        ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_id')
        ->get();

        
        

        return view('tareas.reporteTardanzas', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion]);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
