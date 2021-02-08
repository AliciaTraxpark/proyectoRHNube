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

    public function mostrarReporteTardanza()
    {
        if(session('sesionidorg')==NULL){
            return redirect('/');
        }

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->leftjoin('organizacion as o', 'o.organi_id', '=', 'uso.organi_id')
            ->select('uso.*', 'o.organi_ruc as ruc', 'o.organi_razonSocial as razonSocial', 'o.organi_direccion as direccion')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $organizacion = DB::table('organizacion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();
        $nombreOrga = $organizacion->organi_razonSocial;
        $ruc = $organizacion->organi_ruc;
        $direccion = $organizacion->organi_direccion;

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
                    $empleados = DB::table('empleado as e')
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
                    $empleados = DB::table('empleado as e')
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
            $empleados = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        }

        return view('tareas.reporteTardanzas', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion]);
    }
    // CORREGIDO
    public function cargarReporteTardanzas(Request $request)
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->leftjoin('organizacion as o', 'o.organi_id', '=', 'uso.organi_id')
            ->select('uso.*', 'o.organi_ruc as ruc', 'o.organi_razonSocial as razonSocial', 'o.organi_direccion as direccion')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $empleadoL = $request->idemp;

        /*      OBTENEMOS TODOS LOS EMPLEADOS SEGÚN EL USUARIO (ADMIN O INVITADO)        */
        if ($empleadoL == 0) {
            // INVITADO CON ROL DE INVITADO = 3
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                // INVITADO CON PERMISO DE VER TODOS LOS EMPLEADOS
                if ($invitado->verTodosEmps == 1) {
                    $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                    ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                    ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_codigo as codigo',
                        'e.emple_nDoc as documento',
                        'p.perso_nombre as nombre',
                        'p.perso_apPaterno as apPaterno',
                        'p.perso_apMaterno as apMaterno',
                        'c.cargo_descripcion as cargo',
                        'a.area_descripcion as area',
                        'ho.horario_tolerancia',
                        'ho.horario_descripcion',
                        'ho.horario_id',
                        'cp.hora_ini as marcacion',
                        DB::raw('TIME(cp.hora_ini) as horaM'),
                        DB::raw('DATE(cp.hora_ini) as diaM'),
                        DB::raw('DATE(hd.start) as diaH'),
                        DB::raw('TIME(ho.horaI) as horaH'),
                        DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                    )
                    ->where('he.estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                    ->get();

                } else {
                    // INVITADOS CON EMPLEADOS ASIGNADOS
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    // INVITADO CON PERMISO DE VER SOLAMENTE LOS EMPLEADOS ASIGNADOS
                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                            ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                            ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select(
                                'e.emple_id',
                                'e.emple_codigo as codigo',
                                'e.emple_nDoc as documento',
                                'p.perso_nombre as nombre',
                                'p.perso_apPaterno as apPaterno',
                                'p.perso_apMaterno as apMaterno',
                                'c.cargo_descripcion as cargo',
                                'a.area_descripcion as area',
                                'ho.horario_tolerancia',
                                'ho.horario_descripcion',
                                'ho.horario_id',
                                'cp.hora_ini as marcacion',
                                DB::raw('TIME(cp.hora_ini) as horaM'),
                                DB::raw('DATE(cp.hora_ini) as diaM'),
                                DB::raw('DATE(hd.start) as diaH'),
                                DB::raw('TIME(ho.horaI) as horaH'),
                                DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                            )
                            ->where('he.estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                            ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                            ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                            ->join('invitado_empleado as inve', 'a.area_id', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select(
                                'e.emple_id',
                                'e.emple_codigo as codigo',
                                'e.emple_nDoc as documento',
                                'p.perso_nombre as nombre',
                                'p.perso_apPaterno as apPaterno',
                                'p.perso_apMaterno as apMaterno',
                                'c.cargo_descripcion as cargo',
                                'a.area_descripcion as area',
                                'ho.horario_tolerancia',
                                'ho.horario_descripcion',
                                'ho.horario_id',
                                'cp.hora_ini as marcacion',
                                DB::raw('TIME(cp.hora_ini) as horaM'),
                                DB::raw('DATE(cp.hora_ini) as diaM'),
                                DB::raw('DATE(hd.start) as diaH'),
                                DB::raw('TIME(ho.horaI) as horaH'),
                                DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                            )
                            ->where('he.estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                            ->get();
                    }
                }
            } else {
                // INVITADO QUE NO ES INVITADO (ES ADMINISTRADOR)
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                    ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                    ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_codigo as codigo',
                        'e.emple_nDoc as documento',
                        'p.perso_nombre as nombre',
                        'p.perso_apPaterno as apPaterno',
                        'p.perso_apMaterno as apMaterno',
                        'c.cargo_descripcion as cargo',
                        'a.area_descripcion as area',
                        'ho.horario_tolerancia',
                        'ho.horario_descripcion',
                        'ho.horario_id',
                        'cp.hora_ini as marcacion',
                        DB::raw('TIME(cp.hora_ini) as horaM'),
                        DB::raw('DATE(cp.hora_ini) as diaM'),
                        DB::raw('DATE(hd.start) as diaH'),
                        DB::raw('TIME(ho.horaI) as horaH'),
                        DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                    )
                    ->where('he.estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                    ->get();
            }
        } else {
            // EMPLEADO > 0
            // INVITADO CON ROL DE INVITADO = 3
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                // INVITADO CON PERMISO DE VER TODOS LOS EMPLEADOS
                if ($invitado->verTodosEmps == 1) {
                    $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                    ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                    ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_codigo as codigo',
                        'e.emple_nDoc as documento',
                        'p.perso_nombre as nombre',
                        'p.perso_apPaterno as apPaterno',
                        'p.perso_apMaterno as apMaterno',
                        'c.cargo_descripcion as cargo',
                        'a.area_descripcion as area',
                        'ho.horario_tolerancia',
                        'ho.horario_descripcion',
                        'ho.horario_id',
                        'cp.hora_ini as marcacion',
                        DB::raw('TIME(cp.hora_ini) as horaM'),
                        DB::raw('DATE(cp.hora_ini) as diaM'),
                        DB::raw('DATE(hd.start) as diaH'),
                        DB::raw('TIME(ho.horaI) as horaH'),
                        DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                    )
                    ->where('he.estado', '=', 1)
                    ->where('e.emple_id', '=', $empleadoL)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                    ->get();

                } else {
                    // INVITADOS CON EMPLEADOS ASIGNADOS
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    // INVITADO CON PERMISO DE VER SOLAMENTE LOS EMPLEADOS ASIGNADOS
                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                            ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                            ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select(
                                'e.emple_id',
                                'e.emple_codigo as codigo',
                                'e.emple_nDoc as documento',
                                'p.perso_nombre as nombre',
                                'p.perso_apPaterno as apPaterno',
                                'p.perso_apMaterno as apMaterno',
                                'c.cargo_descripcion as cargo',
                                'a.area_descripcion as area',
                                'ho.horario_tolerancia',
                                'ho.horario_descripcion',
                                'ho.horario_id',
                                'cp.hora_ini as marcacion',
                                DB::raw('TIME(cp.hora_ini) as horaM'),
                                DB::raw('DATE(cp.hora_ini) as diaM'),
                                DB::raw('DATE(hd.start) as diaH'),
                                DB::raw('TIME(ho.horaI) as horaH'),
                                DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                            )
                            ->where('he.estado', '=', 1)
                            ->where('e.emple_id', '=', $empleadoL)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                            ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                            ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                            ->join('invitado_empleado as inve', 'a.area_id', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select(
                                'e.emple_id',
                                'e.emple_codigo as codigo',
                                'e.emple_nDoc as documento',
                                'p.perso_nombre as nombre',
                                'p.perso_apPaterno as apPaterno',
                                'p.perso_apMaterno as apMaterno',
                                'c.cargo_descripcion as cargo',
                                'a.area_descripcion as area',
                                'ho.horario_tolerancia',
                                'ho.horario_descripcion',
                                'ho.horario_id',
                                'cp.hora_ini as marcacion',
                                DB::raw('TIME(cp.hora_ini) as horaM'),
                                DB::raw('DATE(cp.hora_ini) as diaM'),
                                DB::raw('DATE(hd.start) as diaH'),
                                DB::raw('TIME(ho.horaI) as horaH'),
                                DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                            )
                            ->where('he.estado', '=', 1)
                            ->where('e.emple_id', '=', $empleadoL)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                            ->get();
                    }
                }
            } else {
                // INVITADO QUE NO ES INVITADO (ES ADMINISTRADOR)
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                    ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                    ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_codigo as codigo',
                        'e.emple_nDoc as documento',
                        'p.perso_nombre as nombre',
                        'p.perso_apPaterno as apPaterno',
                        'p.perso_apMaterno as apMaterno',
                        'c.cargo_descripcion as cargo',
                        'a.area_descripcion as area',
                        'ho.horario_tolerancia',
                        'ho.horario_descripcion',
                        'ho.horario_id',
                        'cp.hora_ini as marcacion',
                        DB::raw('TIME(cp.hora_ini) as horaM'),
                        DB::raw('DATE(cp.hora_ini) as diaM'),
                        DB::raw('DATE(hd.start) as diaH'),
                        DB::raw('TIME(ho.horaI) as horaH'),
                        DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                    )
                    ->where('he.estado', '=', 1)
                    ->where('e.emple_id', '=', $empleadoL)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                    ->get();
            }
        }

        /*      FIN DE OBETENER EMPLEADOS    */

        /* RANGO DE FECHA */
        $emple_id = $request->idemp;
        $fecha1 = $request->fecha1;
        $fechaR = Carbon::create($fecha1);
        $fecha2 = $request->fecha2;
        $fechaF = Carbon::create($fecha2);

        /* COLECCIÓN QUE SE ENVIA AL JS */
        $datos = new Collection();
        /* VARIABLES PARA LA COMPARACIÓN */
        $cantTardanzas = 0;
        $tiempoTardanza = 0;
        $len = count($empleados);
        $i = 0;
        $employee = 0;

        foreach($empleados as $key => $empleado){
            $marcacion = Carbon::parse($empleado->marcacion);
            $diaHorario = Carbon::create($empleado->diaH);
            $horaHorario = Carbon::parse($empleado->horaH);
            $horario = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second);
            $horario_tolerancia = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second)->addMinutes($empleado->horario_tolerancia);
            /*  CAPTURA DENTRO DEL RANGO DE FECHAS  */
            if($fechaF->greaterThanOrEqualTo($diaHorario) && $diaHorario->greaterThanOrEqualTo($fechaR)){
                if($i == 0){
                    //$datos->push("-------------1-------------");
                    $employee = $empleado->emple_id;
                }

                if($employee != $empleado->emple_id && $cantTardanzas > 0){
                    //$datos->push("-------------2-------------");
                    $datos->push($obj);
                    $cantTardanzas = 0;
                    $tiempoTardanza = 0;
                    $employee = $empleado->emple_id;
                }
                /*  COMPRUEBA SI HAY TARDANZA    */
                if ($marcacion->greaterThan($horario_tolerancia) == TRUE) {
                    //$datos->push("-------------3-------------");
                    $diffS = $marcacion->DiffInSeconds($horario);
                    $tiempoTardanza += $diffS;
                    $cantTardanzas += 1;
                    $area = strlen($empleado->area) > 0 ? $empleado->area : "--";
                    $cargo = strlen($empleado->cargo) > 0 ? $empleado->cargo : "--";
                    $codigo = strlen($empleado->codigo) > 0 ? $empleado->codigo : $empleado->documento;
                    $documento = $empleado->documento;
                    $marcacion = $empleado->marcacion;
                    $horario_descripcion = $empleado->horario_descripcion;
                    $horario_id = $empleado->horario_id;
                    $emple_id = $empleado->emple_id;

                    $obj = (object) array(
                        'area_descripcion' => $area,
                        'cargo_descripcion' => $cargo,
                        'emple_id' => $emple_id,
                        'emple_code' => $codigo,
                        'emple_nDoc' => $documento,
                        'entradaModif' => $marcacion,
                        'horario' => $horario_descripcion,
                        'idhorario' => $horario_id,
                        'cantTardanzas' => $cantTardanzas,
                        'tiempoTardanzas' => gmdate('H:i:s', $tiempoTardanza),
                        'organi_id' => session('sesionidorg'),
                        'organi_razonSocial' => $usuario_organizacion->razonSocial,
                        'organi_direccion' => $usuario_organizacion->direccion,
                        'organi_ruc' => $usuario_organizacion->ruc,
                        'fecha' => now()->format('d-m-Y H:i:s'),
                        'fechaD' => $fecha1,
                        'fechaH' => $fecha2,
                        'perso_apMaterno' => $empleado->apMaterno,
                        'perso_apPaterno' => $empleado->apPaterno,
                        'perso_nombre' => $empleado->nombre
                    );
                }

                $i++;
            }

            if($key == $len - 1 && $cantTardanzas > 0){
                //$datos->push("-------------4-------------");
                $datos->push($obj);
                $cantTardanzas = 0;
                $tiempoTardanza = 0;
            }
        }

        return response()->json($datos, 200);
    }

    public function mostrarMatrizTardanzas()
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

                    $cargos =  DB::table('empleado as e')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->join('cargo as a', 'e.emple_cargo', '=', 'a.cargo_id')
                        ->select(
                            'a.cargo_id as idcargo',
                            'a.cargo_descripcion as descripcion'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->groupBy('e.emple_cargo')
                        ->get();

                    $locales =  DB::table('empleado as e')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->join('local as a', 'e.emple_local', '=', 'a.local_id')
                        ->select(
                            'a.local_id as idlocal',
                            'a.local_descripcion as descripcion'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->groupBy('e.emple_cargo')
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

                        $cargos =  DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('cargo as a', 'e.emple_cargo', '=', 'a.cargo_id')
                            ->select(
                                'a.cargo_id as idcargo',
                                'a.cargo_descripcion as descripcion'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->groupBy('e.emple_cargo')
                            ->get();

                        $locales =  DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('local as a', 'e.emple_local', '=', 'a.local_id')
                            ->select(
                                'a.local_id as idlocal',
                                'a.local_descripcion as descripcion'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->groupBy('e.emple_cargo')
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

                        $cargos =  DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('cargo as a', 'e.emple_cargo', '=', 'a.cargo_id')
                            ->select(
                                'a.cargo_id as idcargo',
                                'a.cargo_descripcion as descripcion'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->groupBy('e.emple_cargo')
                            ->get();

                        $locales =  DB::table('empleado as e')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->join('local as a', 'e.emple_local', '=', 'a.local_id')
                            ->select(
                                'a.local_id as idlocal',
                                'a.local_descripcion as descripcion'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->groupBy('e.emple_cargo')
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

                $cargos =  DB::table('empleado as e')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->join('cargo as a', 'e.emple_cargo', '=', 'a.cargo_id')
                    ->select(
                        'a.cargo_id as idcargo',
                        'a.cargo_descripcion as descripcion'
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_cargo')
                    ->get();

                $locales =  DB::table('empleado as e')
                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                    ->join('local as a', 'e.emple_local', '=', 'a.local_id')
                    ->select(
                        'a.local_id as idlocal',
                        'a.local_descripcion as descripcion'
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_cargo')
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

                        return view('tareas.reporteMatrizTardanzas', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
                    } else {
                        return redirect('/dashboard');
                    }
                    /*   */
                } else {
                    return view('tareas.reporteMatrizTardanzas', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
                }
            } else {
                return view('tareas.reporteMatrizTardanzas', ['empleado' => $empleado, 'areas' => $areas, 'cargos' => $cargos, 'locales' => $locales]);
            }
        }
    }

    public function cargarMatrizTardanzas(Request $request)
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
        $selector = $request->selector;

        // AREA -> NULL && EMPLEADO -> NULL
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
            if (is_null($area) === false && is_null($empleadoL) === true) {
                if ($usuario_organizacion->rol_id == 3) {
                    $invitado = DB::table('invitado as in')
                        ->where('organi_id', '=', session('sesionidorg'))
                        ->where('rol_id', '=', 3)
                        ->where('in.user_Invitado', '=', Auth::user()->id)
                        ->get()->first();

                    if ($invitado->verTodosEmps == 1) {
                        if($selector == "Área"){
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
                            if($selector == "Cargo"){
                                $empleados = DB::table('empleado as e')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_cargo', $area)
                                ->groupBy('e.emple_id')
                                ->get();
                            } else {
                                if($selector == "Local"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->whereIn('e.emple_local', $area)
                                    ->groupBy('e.emple_id')
                                    ->get();
                                }
                            }
                        }
                    } else {
                        $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                            ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                            ->where('invem.area_id', '=', null)
                            ->where('invem.emple_id', '!=', null)
                            ->get()->first();
                        if ($invitado_empleadoIn != null) {
                            if($selector == "Área"){
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
                                if($selector == "Cargo"){
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
                                    ->whereIn('e.emple_cargo', $area)
                                    ->groupBy('e.emple_id')
                                    ->get();
                                } else {
                                    if($selector == "Local"){
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
                                        ->whereIn('e.emple_local', $area)
                                        ->groupBy('e.emple_id')
                                        ->get();
                                    }
                                }
                            }
                        } else {
                            if($selector == "Área"){
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
                            } else {
                                if($selector == "Cargo"){
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
                                    ->whereIn('e.emple_cargo', $area)
                                    ->groupBy('e.emple_id')
                                    ->get();
                                } else {
                                    if($selector == "Local"){
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
                                        ->whereIn('e.emple_local', $area)
                                        ->groupBy('e.emple_id')
                                        ->get();
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if ($selector == "Cargo") {
                       $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_cargo', $area)
                            ->groupBy('e.emple_id')
                            ->get();
                    } else {
                        if ($selector == "Área") {
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
                            if ($selector == "Local") {
                                $empleados = DB::table('empleado as e')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->whereIn('e.emple_local', $area)
                                    ->groupBy('e.emple_id')
                                    ->get();
                            }
                        }
                    }
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
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
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
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
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
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
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
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
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
                        if($selector == "Área"){
                            $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_area', $area)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->groupBy('e.emple_id')
                            ->get();
                        } else {
                            if($selector == "Cargo"){
                                $empleados = DB::table('empleado as e')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_cargo', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->groupBy('e.emple_id')
                                ->get();
                            } else {
                                if($selector == "Local"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->whereIn('e.emple_local', $area)
                                    ->whereIn('e.emple_id', $empleadoL)
                                    ->groupBy('e.emple_id')
                                    ->get();
                                }
                            }
                        }
                        
                    } else {
                        $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                            ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                            ->where('invem.area_id', '=', null)
                            ->where('invem.emple_id', '!=', null)
                            ->get()->first();
                        if ($invitado_empleadoIn != null) {
                            if($selector == "Área"){
                                $empleados = DB::table('empleado as e')
                                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_area', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->groupBy('e.emple_id')
                                ->get();
                            } else {
                                if($selector == "Cargo"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->whereIn('e.emple_cargo', $area)
                                    ->whereIn('e.emple_id', $empleadoL)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->groupBy('e.emple_id')
                                    ->get();
                                } else {
                                    if($selector == "Local"){
                                        $empleados = DB::table('empleado as e')
                                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                        ->where('e.organi_id', '=', session('sesionidorg'))
                                        ->where('e.emple_estado', '=', 1)
                                        ->whereIn('e.emple_local', $area)
                                        ->whereIn('e.emple_id', $empleadoL)
                                        ->where('invi.estado', '=', 1)
                                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                        ->groupBy('e.emple_id')
                                        ->get();
                                    }
                                }
                            }
                        } else {
                            if($selector == "Área"){
                                $empleados = DB::table('empleado as e')
                                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_area', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->where('invi.estado', '=', 1)
                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                ->groupBy('e.emple_id')
                                ->get();
                            } else {
                                if($selector == "Cargo"){
                                    $empleados = DB::table('empleado as e')
                                    ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                    ->where('e.organi_id', '=', session('sesionidorg'))
                                    ->where('e.emple_estado', '=', 1)
                                    ->whereIn('e.emple_cargo', $area)
                                    ->whereIn('e.emple_id', $empleadoL)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                    ->groupBy('e.emple_id')
                                    ->get();
                                } else {
                                    if($selector == "Local"){
                                        $empleados = DB::table('empleado as e')
                                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                        ->where('e.organi_id', '=', session('sesionidorg'))
                                        ->where('e.emple_estado', '=', 1)
                                        ->whereIn('e.emple_local', $area)
                                        ->whereIn('e.emple_id', $empleadoL)
                                        ->where('invi.estado', '=', 1)
                                        ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                        ->groupBy('e.emple_id')
                                        ->get();
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if($selector == "Área"){
                        $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_area', $area)
                        ->whereIn('e.emple_id', $empleadoL)
                        ->groupBy('e.emple_id')
                        ->get();
                    } else {
                        if($selector == "Cargo"){
                            $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_cargo', $area)
                            ->whereIn('e.emple_id', $empleadoL)
                            ->groupBy('e.emple_id')
                            ->get();
                        } else {
                            if($selector == "Local"){
                                $empleados = DB::table('empleado as e')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_local', $area)
                                ->whereIn('e.emple_id', $empleadoL)
                                ->groupBy('e.emple_id')
                                ->get();
                            }
                        }
                    }
                    
                }
            }
        }

        $respuesta = [];
        $datos = new Collection();

        if (isset($empleados)) {
            $sql = "IF(h.id is null,if(DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)) >= 0 , DATEDIFF('" . $fechaF[1] . "',DATE(cp.hora_ini)), DAY(DATE(cp.hora_ini)) ), if(DATEDIFF('" . $fechaF[1] . "',DATE(h.start)) >= 0,DATEDIFF('" . $fechaF[1] . "',DATE(h.start)), DAY(DATE(h.start)) )) as dia";
            // DB::enableQueryLog();

            $capturas = DB::table('empleado as e')
                ->leftjoin('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_cargo')
                ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                ->select(
                    'e.emple_id',
                    'e.emple_codigo as codigo',
                    'e.emple_nDoc as documento',
                    'p.perso_nombre',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    'c.cargo_descripcion as cargo',
                    'a.area_descripcion as area',
                    'ho.horario_tolerancia',
                    'ho.horario_descripcion',
                    'ho.horario_id',
                    'cp.hora_ini as marcacion',
                    DB::raw('TIME(cp.hora_ini) as horaM'),
                    DB::raw('DATE(cp.hora_ini) as diaM'),
                    DB::raw('DATE(hd.start) as diaH'),
                    DB::raw('TIME(ho.horaI) as horaH'),
                    DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                )
                ->where('he.estado', '=', 1)
                ->orderBy('e.emple_id')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                ->get();

            $date1 = new DateTime($fechaF[0]);
            $date2 = new DateTime($fechaF[1]);
            $diff = $date1->diff($date2);
            //Array
            $horas = array();
            $dias = array();

            for ($i = 0; $i <= $diff->days; $i++) {
                array_push($horas, 0);
                $dia = strtotime('+' . $i . 'day', strtotime($fechaF[0]));
                array_push($dias, date('Y-m-j', $dia));
            }

            $sumTardanza = 0;
            $tiempoTardanza = 0;
            $employee = "";
            $len = count($capturas);
            $lenEmple = count($empleados);
            $i = 0;
            $fechaF1 = Carbon::parse($date2);
            $fechaR2 = Carbon::parse($date1);


            foreach ($empleados as $keyE => $empleado) {
                foreach ($capturas as $key => $captura) {
                    if ($captura->emple_id == $empleado->emple_id) {
                        $marcacion = Carbon::parse($captura->marcacion);
                        $diaHorario = Carbon::create($captura->diaH);
                        $horaHorario = Carbon::parse($captura->horaH);
                        $horario = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second);
                        $horario_tolerancia = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second)->addMinutes($captura->horario_tolerancia);
                        /*  CAPTURA DENTRO DEL RANGO DE FECHAS  */
                        if($fechaF1->greaterThanOrEqualTo($diaHorario) && $diaHorario->greaterThanOrEqualTo($fechaR2)){ 
                            if ($marcacion->greaterThan($horario_tolerancia) == TRUE) {
                                $diffS = $marcacion->DiffInSeconds($horario);
                                $tiempoTardanza += $diffS;
                                $sumTardanza += 1;
                                $horas[$diaHorario->day-1] += 1;
                                $codigo = strlen($captura->codigo) > 0 ? $captura->codigo : $captura->documento;
                                $documento = $captura->documento;
                            }
                        }
                    }
                }

                if($sumTardanza > 0){
                    $obj = (object) array(
                        "emple_id" => $empleado->emple_id, 
                        "nombre" => $empleado->nombre, 
                        "apPaterno" => $empleado->apPaterno,
                        "apMaterno" => $empleado->apMaterno, 
                        "horas" => $horas, 
                        "fechaF" => $dias, 
                        "totalTardanza" => gmdate('H:i:s', $tiempoTardanza),
                        "cantidadTardanza" => $sumTardanza,
                        "ruc" => $usuario_organizacion->ruc, 
                        "razonSocial" => $usuario_organizacion->razonSocial, 
                        "direccion" => $usuario_organizacion->direccion,
                        "codigo" => $codigo, 
                        "documento" => $documento , 
                        "fecha" => now()->format('d-m-Y H:i:s'), 
                        "fechaD" => $fechaF[0], 
                        "fechaH" => $fechaF[1]
                    );
                    $datos->push($obj);
                    $sumTardanza = 0;
                    $tiempoTardanza = 0;
                    for ($i = 0; $i <= $diff->days; $i++) {
                        $horas[$i] = 0;
                    }
                }
            }
        }

        return response()->json($datos, 200);
    }

    public function selectMatrizTardanzas(Request $request)
    {
        $area = $request->get('area');
        $selector = $request->selector;
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        if (is_null($area) === true) {
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
            return response()->json($empleados, 200);
        } else {
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();

                if ($invitado->verTodosEmps == 1) {
                    if($selector == "Área"){
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
                        if($selector == "Cargo"){
                            $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_cargo', $area)
                            ->groupBy('e.emple_id')
                            ->get(); 
                        } else {
                            if ($selector == "Local") {
                                $empleados = DB::table('empleado as e')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                                ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                                ->where('e.organi_id', '=', session('sesionidorg'))
                                ->where('e.emple_estado', '=', 1)
                                ->whereIn('e.emple_local', $area)
                                ->groupBy('e.emple_id')
                                ->get();
                            }
                        }
                    }
                } else {
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    if ($invitado_empleadoIn != null) {
                        if($selector == "Área"){
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
                            if($selector == "Cargo"){
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
                                ->whereIn('e.emple_cargo', $area)
                                ->groupBy('e.emple_id')
                                ->get();
                            } else {
                                if($selector == "Local"){
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
                                    ->whereIn('e.emple_local', $area)
                                    ->groupBy('e.emple_id')
                                    ->get();
                                }
                            }
                        }
                    } else {
                        if($selector == "Área"){
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
                        } else {
                            if($selector == "Cargo"){
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
                                ->whereIn('e.emple_cargo', $area)
                                ->groupBy('e.emple_id')
                                ->get();
                            } else {
                                if($selector == "Local"){
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
                                    ->whereIn('e.emple_local', $area)
                                    ->groupBy('e.emple_id')
                                    ->get();
                                }
                            }
                        }
                        
                    }
                }
            } else {
                if($selector == "Área"){
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
                    if($selector == "Cargo"){
                        $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                        ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->whereIn('e.emple_cargo', $area)
                        ->groupBy('e.emple_id')
                        ->get();
                    } else {
                        if($selector == "Local"){
                            $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                            ->select('e.emple_id', 'p.perso_nombre as nombre', 'p.perso_apPaterno as apPaterno', 'p.perso_apMaterno as apMaterno')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->whereIn('e.emple_local', $area)
                            ->groupBy('e.emple_id')
                            ->get();
                        }
                    }
                }
            }
            return response()->json($empleados, 200);
        }
    }

    public function mostrarReporteTardanzaRuta()
    {
        if(session('sesionidorg')==NULL){
            return redirect('/');
        }

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->leftjoin('organizacion as o', 'o.organi_id', '=', 'uso.organi_id')
            ->select('uso.*', 'o.organi_ruc as ruc', 'o.organi_razonSocial as razonSocial', 'o.organi_direccion as direccion')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $organizacion = DB::table('organizacion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();
        $nombreOrga = $organizacion->organi_razonSocial;
        $ruc = $organizacion->organi_ruc;
        $direccion = $organizacion->organi_direccion;

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
                    $empleados = DB::table('empleado as e')
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
                    $empleados = DB::table('empleado as e')
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
            $empleados = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('actividad_empleado as ae', 'ae.idEmpleado', '=', 'e.emple_id')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
        }

        return view('tareas.reporteTardanzasRuta', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion]);
    }

    public function cargarReporteTardanzasRuta(Request $request)
    {
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->leftjoin('organizacion as o', 'o.organi_id', '=', 'uso.organi_id')
            ->select('uso.*', 'o.organi_ruc as ruc', 'o.organi_razonSocial as razonSocial', 'o.organi_direccion as direccion')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();

        $empleadoL = $request->idemp;

        /*      OBTENEMOS TODOS LOS EMPLEADOS SEGÚN EL USUARIO (ADMIN O INVITADO)        */
        if ($empleadoL == 0) {
            // INVITADO CON ROL DE INVITADO = 3
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                // INVITADO CON PERMISO DE VER TODOS LOS EMPLEADOS
                if ($invitado->verTodosEmps == 1) {
                    $empleados_temp = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->join('ubicacion as u', 'u.idEmpleado', '=', 'e.emple_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'u.idHorario_dias')
                    ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                    ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_codigo as codigo',
                        'e.emple_nDoc as documento',
                        'p.perso_nombre as nombre',
                        'p.perso_apPaterno as apPaterno',
                        'p.perso_apMaterno as apMaterno',
                        'c.cargo_descripcion as cargo',
                        'a.area_descripcion as area',
                        'ho.horario_tolerancia',
                        'ho.horario_descripcion',
                        'ho.horario_id',
                        'u.hora_ini as marcacion',
                        DB::raw('TIME(u.hora_ini) as horaM'),
                        DB::raw('DATE(u.hora_ini) as diaM'),
                        DB::raw('DATE(hd.start) as diaH'),
                        DB::raw('TIME(ho.horaI) as horaH'),
                        DB::raw('MIN(TIME(u.hora_ini)) as minMarcacionU')
                    )
                    ->where('he.estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title');

                    $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                    ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                    ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_codigo as codigo',
                        'e.emple_nDoc as documento',
                        'p.perso_nombre as nombre',
                        'p.perso_apPaterno as apPaterno',
                        'p.perso_apMaterno as apMaterno',
                        'c.cargo_descripcion as cargo',
                        'a.area_descripcion as area',
                        'ho.horario_tolerancia',
                        'ho.horario_descripcion',
                        'ho.horario_id',
                        'cp.hora_ini as marcacion',
                        DB::raw('TIME(cp.hora_ini) as horaM'),
                        DB::raw('DATE(cp.hora_ini) as diaM'),
                        DB::raw('DATE(hd.start) as diaH'),
                        DB::raw('TIME(ho.horaI) as horaH'),
                        DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacionC')
                    )
                    ->where('he.estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                    ->union($empleados_temp)
                    ->get();

                } else {
                    // INVITADOS CON EMPLEADOS ASIGNADOS
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    // INVITADO CON PERMISO DE VER SOLAMENTE LOS EMPLEADOS ASIGNADOS
                    if ($invitado_empleadoIn != null) {
                        $empleados_temp = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('ubicacion as u', 'u.idEmpleado', '=', 'e.emple_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'u.idHorario_dias')
                            ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                            ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select(
                                'e.emple_id',
                                'e.emple_codigo as codigo',
                                'e.emple_nDoc as documento',
                                'p.perso_nombre as nombre',
                                'p.perso_apPaterno as apPaterno',
                                'p.perso_apMaterno as apMaterno',
                                'c.cargo_descripcion as cargo',
                                'a.area_descripcion as area',
                                'ho.horario_tolerancia',
                                'ho.horario_descripcion',
                                'ho.horario_id',
                                'u.hora_ini as marcacion',
                                DB::raw('TIME(u.hora_ini) as horaM'),
                                DB::raw('DATE(u.hora_ini) as diaM'),
                                DB::raw('DATE(hd.start) as diaH'),
                                DB::raw('TIME(ho.horaI) as horaH'),
                                DB::raw('MIN(TIME(u.hora_ini)) as minMarcacionU')
                            )
                            ->where('he.estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title');

                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                            ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                            ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select(
                                'e.emple_id',
                                'e.emple_codigo as codigo',
                                'e.emple_nDoc as documento',
                                'p.perso_nombre as nombre',
                                'p.perso_apPaterno as apPaterno',
                                'p.perso_apMaterno as apMaterno',
                                'c.cargo_descripcion as cargo',
                                'a.area_descripcion as area',
                                'ho.horario_tolerancia',
                                'ho.horario_descripcion',
                                'ho.horario_id',
                                'cp.hora_ini as marcacion',
                                DB::raw('TIME(cp.hora_ini) as horaM'),
                                DB::raw('DATE(cp.hora_ini) as diaM'),
                                DB::raw('DATE(hd.start) as diaH'),
                                DB::raw('TIME(ho.horaI) as horaH'),
                                DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacionC')
                            )
                            ->where('he.estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                            ->union($empleados_temp)
                            ->get();
                    } else {
                        $empleados_temp = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('ubicacion as u', 'u.idEmpleado', '=', 'e.emple_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'u.idHorario_dias')
                            ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                            ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                            ->join('invitado_empleado as inve', 'a.area_id', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select(
                                'e.emple_id',
                                'e.emple_codigo as codigo',
                                'e.emple_nDoc as documento',
                                'p.perso_nombre as nombre',
                                'p.perso_apPaterno as apPaterno',
                                'p.perso_apMaterno as apMaterno',
                                'c.cargo_descripcion as cargo',
                                'a.area_descripcion as area',
                                'ho.horario_tolerancia',
                                'ho.horario_descripcion',
                                'ho.horario_id',
                                'u.hora_ini as marcacion',
                                DB::raw('TIME(u.hora_ini) as horaM'),
                                DB::raw('DATE(u.hora_ini) as diaM'),
                                DB::raw('DATE(hd.start) as diaH'),
                                DB::raw('TIME(ho.horaI) as horaH'),
                                DB::raw('MIN(TIME(u.hora_ini)) as minMarcacionU')
                            )
                            ->where('he.estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title');

                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                            ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                            ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                            ->join('invitado_empleado as inve', 'a.area_id', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select(
                                'e.emple_id',
                                'e.emple_codigo as codigo',
                                'e.emple_nDoc as documento',
                                'p.perso_nombre as nombre',
                                'p.perso_apPaterno as apPaterno',
                                'p.perso_apMaterno as apMaterno',
                                'c.cargo_descripcion as cargo',
                                'a.area_descripcion as area',
                                'ho.horario_tolerancia',
                                'ho.horario_descripcion',
                                'ho.horario_id',
                                'cp.hora_ini as marcacion',
                                DB::raw('TIME(cp.hora_ini) as horaM'),
                                DB::raw('DATE(cp.hora_ini) as diaM'),
                                DB::raw('DATE(hd.start) as diaH'),
                                DB::raw('TIME(ho.horaI) as horaH'),
                                DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacionC')
                            )
                            ->where('he.estado', '=', 1)
                            ->orderBy('e.emple_id')
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                            ->union($empleados_temp)
                            ->get();
                    }
                }
            } else {
                // INVITADO QUE NO ES INVITADO (ES ADMINISTRADOR)
                $empleados_temp = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->join('ubicacion as u', 'u.idEmpleado', '=', 'e.emple_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'u.idHorario_dias')
                    ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                    ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_codigo as codigo',
                        'e.emple_nDoc as documento',
                        'p.perso_nombre as nombre',
                        'p.perso_apPaterno as apPaterno',
                        'p.perso_apMaterno as apMaterno',
                        'c.cargo_descripcion as cargo',
                        'a.area_descripcion as area',
                        'ho.horario_tolerancia',
                        'ho.horario_descripcion',
                        'ho.horario_id',
                        'u.hora_ini as marcacion',
                        DB::raw('TIME(u.hora_ini) as horaM'),
                        DB::raw('DATE(u.hora_ini) as diaM'),
                        DB::raw('DATE(hd.start) as diaH'),
                        DB::raw('TIME(ho.horaI) as horaH'),
                        DB::raw('MIN(TIME(u.hora_ini)) as minMarcacionU'),
                    )
                    ->where('he.estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                    ->get();

                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                    ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                    ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_codigo as codigo',
                        'e.emple_nDoc as documento',
                        'p.perso_nombre as nombre',
                        'p.perso_apPaterno as apPaterno',
                        'p.perso_apMaterno as apMaterno',
                        'c.cargo_descripcion as cargo',
                        'a.area_descripcion as area',
                        'ho.horario_tolerancia',
                        'ho.horario_descripcion',
                        'ho.horario_id',
                        'cp.hora_ini as marcacion',
                        DB::raw('TIME(cp.hora_ini) as horaM'),
                        DB::raw('DATE(cp.hora_ini) as diaM'),
                        DB::raw('DATE(hd.start) as diaH'),
                        DB::raw('TIME(ho.horaI) as horaH'),
                        DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacionC'),
                    )
                    ->where('he.estado', '=', 1)
                    ->orderBy('e.emple_id')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                    ->get();
            }
        } else {
            // EMPLEADO > 0
            // INVITADO CON ROL DE INVITADO = 3
            if ($usuario_organizacion->rol_id == 3) {
                $invitado = DB::table('invitado as in')
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->where('rol_id', '=', 3)
                    ->where('in.user_Invitado', '=', Auth::user()->id)
                    ->get()->first();
                // INVITADO CON PERMISO DE VER TODOS LOS EMPLEADOS
                if ($invitado->verTodosEmps == 1) {
                    $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                    ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                    ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_codigo as codigo',
                        'e.emple_nDoc as documento',
                        'p.perso_nombre as nombre',
                        'p.perso_apPaterno as apPaterno',
                        'p.perso_apMaterno as apMaterno',
                        'c.cargo_descripcion as cargo',
                        'a.area_descripcion as area',
                        'ho.horario_tolerancia',
                        'ho.horario_descripcion',
                        'ho.horario_id',
                        'cp.hora_ini as marcacion',
                        DB::raw('TIME(cp.hora_ini) as horaM'),
                        DB::raw('DATE(cp.hora_ini) as diaM'),
                        DB::raw('DATE(hd.start) as diaH'),
                        DB::raw('TIME(ho.horaI) as horaH'),
                        DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                    )
                    ->where('he.estado', '=', 1)
                    ->where('e.emple_id', '=', $empleadoL)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                    ->get();

                } else {
                    // INVITADOS CON EMPLEADOS ASIGNADOS
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    // INVITADO CON PERMISO DE VER SOLAMENTE LOS EMPLEADOS ASIGNADOS
                    if ($invitado_empleadoIn != null) {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                            ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                            ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select(
                                'e.emple_id',
                                'e.emple_codigo as codigo',
                                'e.emple_nDoc as documento',
                                'p.perso_nombre as nombre',
                                'p.perso_apPaterno as apPaterno',
                                'p.perso_apMaterno as apMaterno',
                                'c.cargo_descripcion as cargo',
                                'a.area_descripcion as area',
                                'ho.horario_tolerancia',
                                'ho.horario_descripcion',
                                'ho.horario_id',
                                'cp.hora_ini as marcacion',
                                DB::raw('TIME(cp.hora_ini) as horaM'),
                                DB::raw('DATE(cp.hora_ini) as diaM'),
                                DB::raw('DATE(hd.start) as diaH'),
                                DB::raw('TIME(ho.horaI) as horaH'),
                                DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                            )
                            ->where('he.estado', '=', 1)
                            ->where('e.emple_id', '=', $empleadoL)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('inve.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                            ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                            ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                            ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                            ->join('invitado_empleado as inve', 'a.area_id', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select(
                                'e.emple_id',
                                'e.emple_codigo as codigo',
                                'e.emple_nDoc as documento',
                                'p.perso_nombre as nombre',
                                'p.perso_apPaterno as apPaterno',
                                'p.perso_apMaterno as apMaterno',
                                'c.cargo_descripcion as cargo',
                                'a.area_descripcion as area',
                                'ho.horario_tolerancia',
                                'ho.horario_descripcion',
                                'ho.horario_id',
                                'cp.hora_ini as marcacion',
                                DB::raw('TIME(cp.hora_ini) as horaM'),
                                DB::raw('DATE(cp.hora_ini) as diaM'),
                                DB::raw('DATE(hd.start) as diaH'),
                                DB::raw('TIME(ho.horaI) as horaH'),
                                DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                            )
                            ->where('he.estado', '=', 1)
                            ->where('e.emple_id', '=', $empleadoL)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitado->idinvitado)
                            ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                            ->get();
                    }
                }
            } else {
                // INVITADO QUE NO ES INVITADO (ES ADMINISTRADOR)
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftjoin('cargo as c', 'c.cargo_id', '=', 'e.emple_id')
                    ->leftjoin('area as a', 'a.area_id', '=', 'e.emple_area')
                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'cp.idHorario_dias')
                    ->leftjoin('horario_empleado as he', 'he.horario_dias_id', '=', 'hd.id')
                    ->leftjoin('horario as ho', 'he.horario_horario_id', '=', 'ho.horario_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_codigo as codigo',
                        'e.emple_nDoc as documento',
                        'p.perso_nombre as nombre',
                        'p.perso_apPaterno as apPaterno',
                        'p.perso_apMaterno as apMaterno',
                        'c.cargo_descripcion as cargo',
                        'a.area_descripcion as area',
                        'ho.horario_tolerancia',
                        'ho.horario_descripcion',
                        'ho.horario_id',
                        'cp.hora_ini as marcacion',
                        DB::raw('TIME(cp.hora_ini) as horaM'),
                        DB::raw('DATE(cp.hora_ini) as diaM'),
                        DB::raw('DATE(hd.start) as diaH'),
                        DB::raw('TIME(ho.horaI) as horaH'),
                        DB::raw('MIN(TIME(cp.hora_ini)) as minMarcacion')
                    )
                    ->where('he.estado', '=', 1)
                    ->where('e.emple_id', '=', $empleadoL)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy(DB::raw('DATE(hd.start)'), 'e.emple_id', 'hd.title')
                    ->get();
            }
        }

        /*      FIN DE OBETENER EMPLEADOS    */

        /* RANGO DE FECHA */
        $emple_id = $request->idemp;
        $fecha1 = $request->fecha1;
        $fechaR = Carbon::create($fecha1);
        $fecha2 = $request->fecha2;
        $fechaF = Carbon::create($fecha2);

        /* COLECCIÓN QUE SE ENVIA AL JS */
        $datos = new Collection();
        /* VARIABLES PARA LA COMPARACIÓN */
        $cantTardanzas = 0;
        $tiempoTardanza = 0;
        $len = $empleados->count();
        $i = 0;
        $employee = 0;
        $cont = 0;
        $horaMin = Carbon::tomorrow();
        $temp = 0;

        //$empleados = $empleados->groupBy('diaH');

        //$empleados = $empleados_temp->sortBy('emple_id')->groupBy('diaH', 'emple_id', 'hd.title');

        //$empleados = $empleados->sortBy('emple_id');
        //dd($empleados_temp);

        foreach($empleados as $key => $empleado){
            $marcacion = Carbon::parse($empleado->marcacion);
            $diaHorario = Carbon::create($empleado->diaH);
            $horaHorario = Carbon::parse($empleado->horaH);
            $horario = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second);
            $horario_tolerancia = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second)->addMinutes($empleado->horario_tolerancia);
            /*  CAPTURA DENTRO DEL RANGO DE FECHAS  */
            if($fechaF->greaterThanOrEqualTo($diaHorario) && $diaHorario->greaterThanOrEqualTo($fechaR)){
                if($i == 0){
                    //$datos->push("-------------1-------------");
                    $employee = $empleado->emple_id;
                }

                if($employee != $empleado->emple_id && $cantTardanzas > 0){
                    //$datos->push("-------------2-------------");
                    $datos->push($obj);
                    $cantTardanzas = 0;
                    $tiempoTardanza = 0;
                    $employee = $empleado->emple_id;
                    $horaMin = Carbon::tomorrow();
                }
                /*  COMPRUEBA SI HAY TARDANZA    */
                if ($marcacion->greaterThan($horario_tolerancia) == TRUE) {
                    //$datos->push("-------------3-------------");
                        $horaMin = Carbon::parse($marcacion);
                        $diffS = $marcacion->DiffInSeconds($horario);
                        $tiempoTardanza += $diffS;
                        $cantTardanzas += 1;
                        $area = strlen($empleado->area) > 0 ? $empleado->area : "--";
                        $cargo = strlen($empleado->cargo) > 0 ? $empleado->cargo : "--";
                        $codigo = strlen($empleado->codigo) > 0 ? $empleado->codigo : $empleado->documento;
                        $documento = $empleado->documento;
                        $marcacion = $empleado->marcacion;
                        $horario_descripcion = $empleado->horario_descripcion;
                        $horario_id = $empleado->horario_id;
                        $emple_id = $empleado->emple_id;

                        $obj = (object) array(
                            'area_descripcion' => $area,
                            'cargo_descripcion' => $cargo,
                            'emple_id' => $emple_id,
                            'emple_code' => $codigo,
                            'emple_nDoc' => $documento,
                            'entradaModif' => $marcacion,
                            'horario' => $horario_descripcion,
                            'idhorario' => $horario_id,
                            'cantTardanzas' => $cantTardanzas,
                            'tiempoTardanzas' => gmdate('H:i:s', $tiempoTardanza),
                            'organi_id' => session('sesionidorg'),
                            'organi_razonSocial' => $usuario_organizacion->razonSocial,
                            'organi_direccion' => $usuario_organizacion->direccion,
                            'organi_ruc' => $usuario_organizacion->ruc,
                            'fecha' => now()->format('d-m-Y H:i:s'),
                            'fechaD' => $fecha1,
                            'fechaH' => $fecha2,
                            'perso_apMaterno' => $empleado->apMaterno,
                            'perso_apPaterno' => $empleado->apPaterno,
                            'perso_nombre' => $empleado->nombre
                        );
                    
                }
                $i++;
            }

            foreach($empleados_temp as $empleado_temp){
                if($empleado->emple_id == $empleado_temp->emple_id && $temp != $empleado_temp->emple_id){
                    $marcacion = Carbon::parse($empleado_temp->marcacion);
                    $diaHorario = Carbon::create($empleado_temp->diaH);
                    $horaHorario = Carbon::parse($empleado_temp->horaH);
                    $horario = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second);
                    $horario_tolerancia = Carbon::create($diaHorario->year, $diaHorario->month, $diaHorario->day, $horaHorario->hour, $horaHorario->minute, $horaHorario->second)->addMinutes($empleado_temp->horario_tolerancia);
                    /*  CAPTURA DENTRO DEL RANGO DE FECHAS  */
                    if($fechaF->greaterThanOrEqualTo($diaHorario) && $diaHorario->greaterThanOrEqualTo($fechaR)){
                        /*  COMPRUEBA SI HAY TARDANZA    */
                        if ($marcacion->greaterThan($horario_tolerancia) == TRUE) {
                            //$datos->push("-------------3-------------");
                                $horaMin = Carbon::parse($marcacion);
                                $diffS = $marcacion->DiffInSeconds($horario);
                                $tiempoTardanza += $diffS;
                                $cantTardanzas += 1;
                                $area = strlen($empleado_temp->area) > 0 ? $empleado_temp->area : "--";
                                $cargo = strlen($empleado_temp->cargo) > 0 ? $empleado_temp->cargo : "--";
                                $codigo = strlen($empleado_temp->codigo) > 0 ? $empleado_temp->codigo : $empleado_temp->documento;
                                $documento = $empleado_temp->documento;
                                $marcacion = $empleado_temp->marcacion;
                                $horario_descripcion = $empleado_temp->horario_descripcion;
                                $horario_id = $empleado_temp->horario_id;
                                $emple_id = $empleado_temp->emple_id;

                                $obj = (object) array(
                                    'area_descripcion' => $area,
                                    'cargo_descripcion' => $cargo,
                                    'emple_id' => $emple_id,
                                    'emple_code' => $codigo,
                                    'emple_nDoc' => $documento,
                                    'entradaModif' => $marcacion,
                                    'horario' => $horario_descripcion,
                                    'idhorario' => $horario_id,
                                    'cantTardanzas' => $cantTardanzas,
                                    'tiempoTardanzas' => gmdate('H:i:s', $tiempoTardanza),
                                    'organi_id' => session('sesionidorg'),
                                    'organi_razonSocial' => $usuario_organizacion->razonSocial,
                                    'organi_direccion' => $usuario_organizacion->direccion,
                                    'organi_ruc' => $usuario_organizacion->ruc,
                                    'fecha' => now()->format('d-m-Y H:i:s'),
                                    'fechaD' => $fecha1,
                                    'fechaH' => $fecha2,
                                    'perso_apMaterno' => $empleado_temp->apMaterno,
                                    'perso_apPaterno' => $empleado_temp->apPaterno,
                                    'perso_nombre' => $empleado_temp->nombre
                                );
                                $temp = $empleado_temp->emple_id;
                        }
                    }
                }
            }
            
            if($cont == $len - 1 && $cantTardanzas > 0){
                //$datos->push("-------------4-------------");
                $datos->push($obj);
                $cantTardanzas = 0;
                $tiempoTardanza = 0;
            }
            $cont++;
        }

        //dd($datos);

        return response()->json($datos, 200);
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
