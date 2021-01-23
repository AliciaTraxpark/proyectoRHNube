<?php

namespace App\Http\Controllers;

use App\marcacion_tareo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;
class marcacionTareoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function index()
    {
        //
        /* OBTENEMOS DATOS DE ORGANIZACION */
        $organizacion = DB::table('organizacion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();
        $nombreOrga = $organizacion->organi_razonSocial;
        $ruc = $organizacion->organi_ruc;
        $direccion = $organizacion->organi_direccion;
        /* ------------------------------------------- */

        $invitadod = DB::table('invitado')
            ->where('user_Invitado', '=', Auth::user()->id)
            ->where('rol_id', '=', 3)
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();
        if ($invitadod) {
            if ($invitadod->verTodosEmps == 1) {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->get();
            } else {
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();
                if ($invitado_empleadoIn != null) {

                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->get();
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                        ->get();
                }
            }
        } else {
            $empleados = DB::table('empleado as e')
                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->get();
        }

        if ($invitadod) {
            if ($invitadod->rol_id != 1) {
                /*  if ($invitadod->reporteAsisten == 1) {

                return view('Dispositivos.reporteDis', [
                'organizacion' => $nombreOrga, 'empleado' => $empleados, 'modifReporte' => $invitadod->ModificarReportePuerta,
                'ruc' => $ruc, 'direccion' => $direccion,
                ]);
                } else {
                return redirect('/dashboard');
                } */
                /*   */
            } else {
                return view('ReporteTareo.reporteFecha', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion]);
            }
        } else {
            return view('ReporteTareo.reporteFecha', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion]);
        }
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
     * @param  \App\marcacion_tareo  $marcacion_tareo
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        $fechaR = $request->fecha;
        $idemp = $request->idemp;
        $fecha = Carbon::create($fechaR);
        $año = $fecha->year;
        $mes = $fecha->month;
        $dia = $fecha->day;
        $ndia = $dia + 1;

        function agruparEmpleadosMarcaciones($array)
        {
            $resultado = array();

            foreach ($array as $empleado) {
                if (!isset($resultado[$empleado->emple_id])) {
                    $resultado[$empleado->emple_id] = $empleado;
                }
                if (!isset($resultado[$empleado->emple_id]->marcaciones)) {
                    $resultado[$empleado->emple_id]->marcaciones = array();
                }
                $arrayMarcacion = array("idMarcacion" => $empleado->idMarcacion, "entrada" => $empleado->entrada, "salida" => $empleado->salida,
                "actividad" => $empleado->actividad,  "horario" => $empleado->horario, "horarioIni" => $empleado->horarioIni, "horarioFin" => $empleado->horarioFin);
                array_push($resultado[$empleado->emple_id]->marcaciones, $arrayMarcacion);
            }
            return array_values($resultado);
        }

        $invitadod = DB::table('invitado')
            ->where('user_Invitado', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('rol_id', '=', 3)
            ->get()->first();
        if ($invitadod) {
            if ($invitadod->verTodosEmps == 1) {
                if ($idemp == 0 || $idemp == ' ') {
                    $marcaciones = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                        ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')

                        ->select(
                            'e.emple_id',

                            'mp.marcaMov_id',
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'mp.organi_id',

                            DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horario_descripcion) as horario'),
                            DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaI) as horarioIni'),
                            DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaF) as horarioFin'),
                            DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                            DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                            'mp.marcaMov_id as idMarcacion'
                        )
                        ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)

                        ->where('mp.organi_id', '=', session('sesionidorg'))
                        ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)', 'ASC'))
                        ->get();
                    $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
                } else {
                    $marcaciones = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                        ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')

                        ->select(
                            'e.emple_id',

                            'mp.marcaMov_id',
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'mp.organi_id',

                            DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horario_descripcion) as horario'),
                            DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaI) as horarioIni'),
                            DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaF) as horarioFin'),
                            DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                            DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                            'mp.marcaMov_id as idMarcacion'
                        )
                        ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)
                        ->where('e.emple_id', $idemp)

                        ->where('mp.organi_id', '=', session('sesionidorg'))
                        ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)', 'ASC'))
                        ->get();
                    $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
                }
            } else {
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();
                if ($invitado_empleadoIn != null) {
                    if ($idemp == 0 || $idemp == ' ') {
                        $marcaciones = DB::table('empleado as e')
                            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                            ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')

                            ->select(
                                'e.emple_id',

                                'mp.marcaMov_id',
                                'e.emple_nDoc',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'mp.organi_id',

                                DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horario_descripcion) as horario'),
                                DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaI) as horarioIni'),
                                DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaF) as horarioFin'),
                                DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                                DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                                'mp.marcaMov_id as idMarcacion'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)

                            ->where('mp.organi_id', '=', session('sesionidorg'))
                            ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)', 'ASC'))
                            ->get();
                        $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
                    } else {
                        $marcaciones = DB::table('empleado as e')
                            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                            ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')

                            ->select(
                                'e.emple_id',

                                'mp.marcaMov_id',
                                'e.emple_nDoc',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'mp.organi_id',

                                DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horario_descripcion) as horario'),
                                DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaI) as horarioIni'),
                                DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaF) as horarioFin'),
                                DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                                DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                                'mp.marcaMov_id as idMarcacion'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)
                            ->where('e.emple_id', $idemp)

                            ->where('mp.organi_id', '=', session('sesionidorg'))
                            ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)', 'ASC'))
                            ->get();
                        $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
                    }
                } else {
                    if ($idemp == 0 || $idemp == ' ') {
                        $marcaciones = DB::table('empleado as e')
                            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                            ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')

                            ->select(
                                'e.emple_id',

                                'mp.marcaMov_id',
                                'e.emple_nDoc',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'mp.organi_id',

                                DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horario_descripcion) as horario'),
                                DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaI) as horarioIni'),
                                DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaF) as horarioFin'),
                                DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                                DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                                'mp.marcaMov_id as idMarcacion'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)

                            ->where('mp.organi_id', '=', session('sesionidorg'))
                            ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)', 'ASC'))
                            ->get();
                        $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
                    } else {
                        $marcaciones = DB::table('empleado as e')
                            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                            ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')

                            ->select(
                                'e.emple_id',

                                'mp.marcaMov_id',
                                'e.emple_nDoc',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'mp.organi_id',

                                DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horario_descripcion) as horario'),
                                DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaI) as horarioIni'),
                                DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaF) as horarioFin'),
                                DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                                DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                                'mp.marcaMov_id as idMarcacion'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)
                            ->where('e.emple_id', $idemp)

                            ->where('mp.organi_id', '=', session('sesionidorg'))
                            ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)', 'ASC'))
                            ->get();
                        $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
                    }
                }
            }
        } else {
            if ($idemp == 0 || $idemp == ' ') {
                $marcaciones = DB::table('empleado as e')
                    ->join('marcacion_tareo as mt', 'mt.marcaTareo_idempleado', '=', 'e.emple_id')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoin('horario_empleado as hoe', 'mt.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                    ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                    ->leftJoin('actividad as act','mt.Activi_id','=','act.Activi_id')

                    ->select(
                        'e.emple_id',

                        'mt.idmarcaciones_tareo',
                        'e.emple_nDoc',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion',
                        'mt.organi_id',
                        DB::raw('IF(act.Activi_Nombre is null, 0 , act.Activi_Nombre) as actividad'),
                        DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horario_descripcion) as horario'),
                        DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaI) as horarioIni'),
                        DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaF) as horarioFin'),
                        DB::raw('IF(mt.marcaTareo_entrada is null, 0 , mt.marcaTareo_entrada) as entrada'),
                        DB::raw('IF(mt.marcaTareo_salida is null, 0 , mt.marcaTareo_salida) as salida'),
                        'mt.idmarcaciones_tareo as idMarcacion'
                    )
                    ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)

                    ->where('mt.organi_id', '=', session('sesionidorg'))
                    ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                    ->get();
                $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
            } else {
                $marcaciones = DB::table('empleado as e')
                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                    ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')

                    ->select(
                        'e.emple_id',

                        'mp.marcaMov_id',
                        'e.emple_nDoc',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion',
                        'mp.organi_id',

                        DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horario_descripcion) as horario'),
                        DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaI) as horarioIni'),
                        DB::raw('IF(hor.horario_descripcion is null, 0 , hor.horaF) as horarioFin'),
                        DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                        DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                        'mp.marcaMov_id as idMarcacion'
                    )
                    ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)
                    ->where('e.emple_id', $idemp)

                    ->where('mp.organi_id', '=', session('sesionidorg'))
                    ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)', 'ASC'))
                    ->get();
                $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
            }
        }
        return response()->json($marcaciones, 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\marcacion_tareo  $marcacion_tareo
     * @return \Illuminate\Http\Response
     */
    public function edit(marcacion_tareo $marcacion_tareo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\marcacion_tareo  $marcacion_tareo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, marcacion_tareo $marcacion_tareo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\marcacion_tareo  $marcacion_tareo
     * @return \Illuminate\Http\Response
     */
    public function destroy(marcacion_tareo $marcacion_tareo)
    {
        //
    }
}
