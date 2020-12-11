<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\organizacion;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Image;

class ControlController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function index()
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
                    $empleado = DB::table('empleado as e')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->groupBy('p.perso_id')
                        ->get();
                } else {
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    if ($invitado_empleadoIn != null) {
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
                            ->groupBy('p.perso_id')
                            ->get();
                    } else {
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
                            ->groupBy('p.perso_id')
                            ->get();
                    }
                }
            } else {
                $empleado = DB::table('empleado as e')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('p.perso_id')
                    ->get();
            }


            /*  return view('tareas.tareas', ['empleado' => $empleado]); */
            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('rol_id', '=', 3)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();
            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    if ($invitadod->modoCR == 1) {

                        return view('tareas.tareas', ['empleado' => $empleado]);
                    } else {
                        return redirect('/dashboard');
                    }
                    /*   */
                } else {
                    return view('tareas.tareas', ['empleado' => $empleado]);
                }
            } else {
                return view('tareas.tareas', ['empleado' => $empleado]);
            }
        }
    }

    public function ReporteS()
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
                    $empleado = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
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
                    } else {
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
                    }
                }
            } else {
                $empleado = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_id')
                    ->get();
            }

            $areas = DB::table('area as a')
                ->select('a.area_id', 'a.area_descripcion')
                ->where('a.organi_id', '=', session('sesionidorg'))
                ->get();

            $cargos = DB::table('cargo as c')
                ->select('c.cargo_id', 'c.cargo_descripcion')
                ->where('c.organi_id', '=', session('sesionidorg'))
                ->get();

            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('rol_id', '=', 3)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    if ($invitadod->modoCR == 1) {

                        return view('tareas.reporteSemanal', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos]);
                    } else {
                        return redirect('/dashboard');
                    }
                    /*   */
                } else {
                    return view('tareas.reporteSemanal', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos]);
                }
            } else {
                return view('tareas.reporteSemanal', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos]);
            }
        }
    }

    public function ReporteM()
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
                    $empleado = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
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
                    } else {
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
                    }
                }
            } else {
                $empleado = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_id')
                    ->get();
            }

            $areas = DB::table('area as a')
                ->select('a.area_id', 'a.area_descripcion')
                ->where('a.organi_id', '=', session('sesionidorg'))
                ->get();

            $cargos = DB::table('cargo as c')
                ->select('c.cargo_id', 'c.cargo_descripcion')
                ->where('c.organi_id', '=', session('sesionidorg'))
                ->get();

            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('rol_id', '=', 3)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            /*  return view('dashboardCR', ['areas' => $areas]); */

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    if ($invitadod->modoCR == 1) {

                        return view('tareas.reporteMensual', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos]);
                    } else {
                        return redirect('/dashboard');
                    }
                    /*   */
                } else {
                    return view('tareas.reporteMensual', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos]);
                }
            } else {
                return view('tareas.reporteMensual', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos]);
            }
        }
    }

    public function empleadoRefresh()
    {
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
                $empleado = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
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
                } else {
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
                }
            }
        } else {
            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        }


        return response()->json($empleado, 200);
    }

    public function EmpleadoReporte(Request $request)
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

                if ($invitado->verTodosEmps == 1) {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
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

                    if ($invitado->verTodosEmps == 1) {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
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

                    if ($invitado->verTodosEmps == 1) {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_cargo', $cargo)
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

                    if ($invitado->verTodosEmps == 1) {
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
                ->groupBy('e.emple_id', DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'))
                ->get();
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
                    "id" => $empleado->emple_id, "nombre" => $empleado->nombre, "apPaterno" => $empleado->apPaterno,
                    "apMaterno" => $empleado->apMaterno, "horas" => $horas, "fechaF" => $dias, "sumaActividad" => $sumaActividad, "sumaRango" => $sumaRango
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

    public function proyecto(Request $request)
    {
        $idempleado = $request->get('value');
        $proyecto = DB::table('empleado as e')
            ->join('proyecto_empleado as pe', 'pe.empleado_emple_id', '=', 'e.emple_id')
            ->join('proyecto as p', 'p.Proye_id', '=', 'pe.Proyecto_Proye_id')
            ->select('P.Proye_id', 'P.Proye_Nombre')
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_id', '=', $idempleado)
            ->get();
        return response()->json($proyecto, 200);
    }

    public function show(Request $request)
    {

        function controlAJson($array)
        {
            $resultado = array();

            foreach ($array as $captura) {
                $horaCaptura = explode(":", $captura->hora);
                if (!isset($resultado[$horaCaptura[0]])) {
                    $resultado[$horaCaptura[0]] = array("horaCaptura" => $horaCaptura[0], "minutos" => array());
                }
                if (!isset($resultado[$horaCaptura[0]]["minutos"][$horaCaptura[1][0]])) {
                    $resultado[$horaCaptura[0]]["minutos"][$horaCaptura[1][0]] = array();
                }
                array_push($resultado[$horaCaptura[0]]["minutos"][$horaCaptura[1][0]], $captura);
            }
            return array_values($resultado);
        }

        $idempleado = $request->get('value');
        $fecha = $request->get('fecha');
        $control = DB::table('empleado as e')
            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
            ->join('actividad as a', 'a.Activi_id', '=', 'cp.idActividad')
            ->join('promedio_captura as pc', 'pc.idCaptura', '=', 'cp.idCaptura')
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'pc.idHorario')
            ->select(
                DB::raw('IF(hd.id is null, DATE(cp.hora_ini), DATE(hd.start))'),
                'a.Activi_id',
                'a.Activi_Nombre',
                'a.estado',
                'cp.idCaptura',
                'cp.actividad',
                'cp.hora_fin',
                DB::raw('DATE(cp.hora_ini) as fecha'),
                DB::raw('TIME(cp.hora_ini) as hora'),
                'pc.promedio as prom',
                'pc.tiempo_rango as rango',
                DB::raw('TIME(cp.hora_ini) as hora_ini'),
                DB::raw('TIME(cp.hora_fin) as hora_fin'),
                'cp.actividad as tiempoA'
            )
            ->where(DB::raw('IF(hd.id is null, DATE(cp.hora_ini), DATE(hd.start))'), '=', $fecha)
            ->where('e.emple_id', '=', $idempleado)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->orderBy('cp.hora_ini', 'asc')
            ->get();
        foreach ($control as $c) {
            $capturas = DB::table('captura_imagen as ci')
                ->select('ci.id as idImagen', 'ci.miniatura as imagen')
                ->where('ci.idCaptura', '=', $c->idCaptura)
                ->get();
            $datos = [];
            foreach ($capturas as $cp) {
                array_push($datos, $cp);
            }
            $c->imagen = $datos;
        }
        $control = controlAJson($control);
        return response()->json($control, 200);
    }

    public function apiMostrarCapturas($url)
    {
        $res = base64_decode($url);
        $resultado = str_replace("-", "/", $res);
        $appPath = app_path($resultado);
        return Image::make($appPath)->response();
    }

    public function mostrarCapturas(Request $request)
    {
        $idCaptura = $request->get('idCaptura');
        $respuesta = [];
        $captura = DB::table('captura as cp')
            ->join('captura_imagen as ci', 'ci.idCaptura', '=', 'cp.idCaptura')
            ->select('ci.imagen', 'cp.hora_fin')
            ->where('ci.id', '=', $idCaptura)
            ->get()
            ->first();
        array_push($respuesta, $captura);

        return response()->json($respuesta, 200);
    }

    // REPORTES PERSONALIZADOS
    public function vistaReporte()
    {
        $organizacion = organizacion::all('organi_id', 'organi_razonSocial');
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', null)
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion) {
            if ($usuario_organizacion->rol_id == 4) {
                return view('tareas.reportePersonalizado', ['organizacion' => $organizacion, 'idrol' => $usuario_organizacion->rol_id]);
            }
        } else {
            if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
                return redirect('/elegirorganizacion');
            } else {
                $usuario_organizacionR = DB::table('usuario_organizacion as uso')
                    ->where('uso.organi_id', '=', session('sesionidorg'))
                    ->where('uso.user_id', '=', Auth::user()->id)
                    ->get()->first();
                return view('tareas.reportePersonalizado', ['organizacion' => $organizacion, 'idrol' => $usuario_organizacionR->rol_id]);
            }
        }
    }
    public function selctEmpleado($id)
    {
        // $respuesta = [];
        $empleados = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->join('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->where('e.organi_id', '=', $id)
            ->whereNotNull('v.pc_mac')
            ->groupBy('e.emple_id')
            ->get();

        // foreach ($empleados as $empleado) {
        //     array_push($respuesta, array("id" => $empleado->emple_id, "text" => $empleado->nombre . " " . $empleado->apPaterno . " " . $empleado->apMaterno));
        // }

        return response()->json($empleados, 200);
    }
    public function vistaReporteEmpleado()
    {
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
            ->get();

        return response()->json($empleado, 200);
    }
    public function retornarDatos(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $fecha = $request->get('fecha');
        // DB::enableQueryLog();
        $captura = DB::table('captura as cp')
            ->leftJoin('captura_imagen as ci', 'ci.idCaptura', '=', 'cp.idCaptura')
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
            ->select(
                'cp.idCaptura',
                'cp.hora_ini',
                'cp.hora_fin',
                'cp.actividad',
                DB::raw("CASE WHEN(ci.imagen) IS NULL THEN 'NO' ELSE 'SI' END AS respuestaI"),
                DB::raw("CASE WHEN(ci.miniatura) IS NULL THEN 'NO' ELSE 'SI' END AS respuestaM"),
                DB::raw("CASE WHEN(ci.idCaptura) IS NULL THEN 0 ELSE COUNT('ci.idCaptura') END AS cantidadI"),
                DB::raw("CASE WHEN (cp.idHorario_dias) IS NULL THEN 0 ELSE DATE(hd.start) END AS horario")
            )
            ->where('cp.idEmpleado', '=', $idEmpleado)
            ->where(DB::raw('DATE(cp.hora_ini)'), '=', $fecha)
            ->groupBy('cp.idCaptura')
            ->get();
        // dd(DB::getQueryLog());
        $dispositivos = DB::table('vinculacion as v')
            ->leftJoin('software_vinculacion as sv', 'sv.id', '=', 'v.idSoftware')
            ->select(
                DB::raw("CASE WHEN(v.pc_mac) IS NULL THEN 0 ELSE v.pc_mac END AS nombrePC"),
                DB::raw("CASE WHEN(v.idSoftware) IS NULL THEN '1.10.09' ELSE sv.version END AS version")
            )
            ->where('v.idEmpleado', '=', $idEmpleado)
            ->groupBy('v.id')
            ->get();
        return response()->json(array("captura" => $captura, "dispositivo" => $dispositivos), 200);
    }

    // REPORTE PEROSNALIZADO TRAZABILIDAD DE CAPTURAS
    public function vistaTrazabilidad()

    {
        $organizacion = organizacion::all('organi_id', 'organi_razonSocial');
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', null)
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if ($usuario_organizacion) {
            if ($usuario_organizacion->rol_id == 4) {
                return view('tareas.reporteTrazabilidadC', ['organizacion' => $organizacion, 'idrol' => $usuario_organizacion->rol_id]);
            }
        } else {
            if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
                return redirect('/elegirorganizacion');
            } else {
                $usuario_organizacionR = DB::table('usuario_organizacion as uso')
                    ->where('uso.organi_id', '=', session('sesionidorg'))
                    ->where('uso.user_id', '=', Auth::user()->id)
                    ->get()->first();
                return view('tareas.reporteTrazabilidadC', ['organizacion' => $organizacion, 'idrol' => $usuario_organizacionR->rol_id]);
            }
        }
    }

    public function capturasTrazabilidad(Request $request)
    {
        $fecha_horaI = $request->get('fecha_horaI');
        $fecha_horaF = $request->get('fecha_horaF');
        $organizacion = $request->get('organizacion');
        $empleado = $request->get('empleado');
        $respuesta = [];

        $horaI = Carbon::create($request->get('fecha_horaI'))->format('H:i:s');
        $horaF = Carbon::create($request->get('fecha_horaF'))->format('H:i:s');
        $fecha = Carbon::create($request->get('fecha_horaI'))->format('Y-m-d');
        $date1 = new DateTime($fecha_horaI);
        $date2 = new DateTime($fecha_horaF);
        $diff = $date1->diff($date2);
        // DB::enableQueryLog();
        if (is_null($empleado) === true) {
            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->join('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                ->where('e.organi_id', '=', $organizacion)
                ->whereNotNull('v.pc_mac')
                ->groupBy('e.emple_id')
                ->get();
        } else {
            $empleado = DB::table('empleado as e')
                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->join('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                ->where('e.organi_id', '=', $organizacion)
                ->whereIn('e.emple_id', $empleado)
                ->whereNotNull('v.pc_mac')
                ->groupBy('e.emple_id')
                ->get();
        }
        // dd(DB::getQueryLog());
        $horas = array();
        $cantidad = array();
        for ($i = 0; $i <= $diff->h; $i++) {
            $hora = strtotime('+' . $i . 'hours', strtotime($horaI));

            array_push($horas, date('H:i', $hora));
            array_push($cantidad, 0);
        }
        // array_push($respuesta, ["horas" => $horas]);
        foreach ($empleado as $emple) {
            array_push($respuesta, array("idEmpleado" => $emple->emple_id, "nombre_apellido" => $emple->nombre . " " . $emple->apPaterno . " " . $emple->apMaterno, "cantidad" => $cantidad));
        }
        // DB::enableQueryLog();
        $horasCapturas = DB::table('empleado as e')
            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
            ->select('e.emple_id', DB::raw('TIME(cp.hora_ini) as hora'))
            ->where('e.organi_id', '=', $organizacion)
            ->WhereRaw("DATE(cp.hora_ini) ='$fecha'")
            ->groupBy('cp.idCaptura')
            ->get();
        // dd(DB::getQueryLog());
        for ($index = 0; $index < sizeof($respuesta); $index++) {
            $arrayHoras = $horas;
            $idEmpleado = $respuesta[$index]["idEmpleado"];

            for ($j = 0; $j < sizeof($arrayHoras); $j++) {
                $contador = 0;
                foreach ($horasCapturas as $hc) {
                    if ($idEmpleado == $hc->emple_id) {
                        $formato = Carbon::create($horas[$j])->format('H:i:s');
                        $hora = strtotime('+1 hours', strtotime($formato));
                        $resultado = date('H:i:s', $hora);
                        if ($hc->hora >= $formato && $hc->hora < $resultado) {
                            $contador = $contador + 1;
                        }
                    }
                }
                $respuesta[$index]["cantidad"][$j] = $contador;
                $contador = 0;
            }
        }
        return response()->json(array("horas" => $horas, "datos" => $respuesta), 200);
    }
}
