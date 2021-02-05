<?php

namespace App\Http\Controllers;

use App\marcacion_tareo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        //*RELACION CON CONTROLADORES
        $ControladorEoS = DB::table('controladores_tareo as cont')
        ->select(
            'cont.idcontroladores_tareo',
            DB::raw('CONCAT(cont.contrT_nombres," ",cont.contrT_ApPaterno," ",cont.contrT_ApMaterno) as nombre')
        );

        //*RELACION CON DISPOSITIVOS

        $DispositivosEoS = DB::table('dispositivos_tareo as dist')
        ->select(
            'dist.iddispositivos_tareo',
            'dis.dispoT_descripUbicacion'
        );

        $invitadod = DB::table('invitado')
            ->where('user_Invitado', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('rol_id', '=', 3)
            ->get()->first();
        if ($invitadod) {
            if ($invitadod->verTodosEmps == 1) {
                if ($idemp == 0 || $idemp == ' ') {
                    $marcaciones = DB::table('marcacion_tareo as mt')
                        ->join('empleado as e', 'mt.marcaTareo_idempleado', '=', 'e.emple_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoinSub($ControladorEoS, 'entrada', function ($join) {
                            $join->on('mt.idcontroladores_entrada', '=', 'entrada.idcontroladores_tareo');
                        })
                        ->leftJoinSub($ControladorEoS, 'salida', function ($join) {
                            $join->on('mt.idcontroladores_salida', '=', 'salida.idcontroladores_tareo');
                        })
                        ->leftJoin('actividad as act', 'mt.Activi_id', '=', 'act.Activi_id')
                        ->leftJoin('punto_control as pc', 'mt.puntoC_id', '=', 'pc.id')
                        ->leftJoin('centro_costo as centC', 'mt.centroC_id', '=', 'centC.centroC_id')
                        ->leftJoin('subactividad as sub', 'mt.idsubActividad', '=', 'sub.idsubActividad')

                        ->select(
                            'e.emple_id',
                            'mt.idmarcaciones_tareo',
                            'e.emple_nDoc',
                            DB::raw('IF(e.emple_codigo is null, 0 ,e.emple_codigo) as emple_codigo'),
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'p.perso_sexo',
                            DB::raw('IF(c.cargo_descripcion is null, 0 ,c.cargo_descripcion) as cargo_descripcion'),
                            DB::raw('IF(act.codigoActividad is null, 0 , act.codigoActividad) as codigoActividad'),
                            'act.Activi_Nombre',
                            DB::raw('IF(sub.subAct_codigo is null, 0 ,sub.subAct_codigo) as codigoSubactiv'),
                            'sub.subAct_nombre',
                            DB::raw('IF(mt.marcaTareo_entrada is null, 0 , mt.marcaTareo_entrada) as entrada'),
                            DB::raw('IF(mt.marcaTareo_salida is null, 0 , mt.marcaTareo_salida) as salida'),
                            'mt.idmarcaciones_tareo as idMarcacion',

                            'pc.descripcion as puntoControl',
                            DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                            DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                            DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida")
                        )
                        ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                        ->where('mt.organi_id', '=', session('sesionidorg'))
                        ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                        ->get();

                } else {
                    $marcaciones = DB::table('marcacion_tareo as mt')
                        ->join('empleado as e', 'mt.marcaTareo_idempleado', '=', 'e.emple_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoinSub($ControladorEoS, 'entrada', function ($join) {
                            $join->on('mt.idcontroladores_entrada', '=', 'entrada.idcontroladores_tareo');
                        })
                        ->leftJoinSub($ControladorEoS, 'salida', function ($join) {
                            $join->on('mt.idcontroladores_salida', '=', 'salida.idcontroladores_tareo');
                        })
                        ->leftJoin('actividad as act', 'mt.Activi_id', '=', 'act.Activi_id')
                        ->leftJoin('punto_control as pc', 'mt.puntoC_id', '=', 'pc.id')
                        ->leftJoin('centro_costo as centC', 'mt.centroC_id', '=', 'centC.centroC_id')
                        ->leftJoin('subactividad as sub', 'mt.idsubActividad', '=', 'sub.idsubActividad')

                        ->select(
                            'e.emple_id',
                            'mt.idmarcaciones_tareo',
                            'e.emple_nDoc',
                            DB::raw('IF(e.emple_codigo is null, 0 ,e.emple_codigo) as emple_codigo'),
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'p.perso_sexo',
                            DB::raw('IF(c.cargo_descripcion is null, 0 ,c.cargo_descripcion) as cargo_descripcion'),
                            DB::raw('IF(act.codigoActividad is null, 0 , act.codigoActividad) as codigoActividad'),
                            'act.Activi_Nombre',
                            DB::raw('IF(sub.subAct_codigo is null, 0 ,sub.subAct_codigo) as codigoSubactiv'),
                            'sub.subAct_nombre',
                            DB::raw('IF(mt.marcaTareo_entrada is null, 0 , mt.marcaTareo_entrada) as entrada'),
                            DB::raw('IF(mt.marcaTareo_salida is null, 0 , mt.marcaTareo_salida) as salida'),
                            'mt.idmarcaciones_tareo as idMarcacion',

                            'pc.descripcion as puntoControl',
                            DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                            DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                            DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida")

                        )
                        ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                        ->where('mt.organi_id', '=', session('sesionidorg'))
                        ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                        ->where('e.emple_id', $idemp)
                        ->get();

                }
            } else {
                /* CUANDO ES POR EMPLEADOS PERSONALIZADOS */
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();
                if ($invitado_empleadoIn != null) {
                    if ($idemp == 0 || $idemp == ' ') {
                        $marcaciones = DB::table('marcacion_tareo as mt')
                            ->join('empleado as e', 'mt.marcaTareo_idempleado', '=', 'e.emple_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->leftJoinSub($ControladorEoS, 'entrada', function ($join) {
                                $join->on('mt.idcontroladores_entrada', '=', 'entrada.idcontroladores_tareo');
                            })
                            ->leftJoinSub($ControladorEoS, 'salida', function ($join) {
                                $join->on('mt.idcontroladores_salida', '=', 'salida.idcontroladores_tareo');
                            })
                            ->leftJoin('actividad as act', 'mt.Activi_id', '=', 'act.Activi_id')
                            ->leftJoin('punto_control as pc', 'mt.puntoC_id', '=', 'pc.id')
                            ->leftJoin('centro_costo as centC', 'mt.centroC_id', '=', 'centC.centroC_id')
                            ->leftJoin('subactividad as sub', 'mt.idsubActividad', '=', 'sub.idsubActividad')

                            ->select(
                                'e.emple_id',
                                'mt.idmarcaciones_tareo',
                                'e.emple_nDoc',
                                DB::raw('IF(e.emple_codigo is null, 0 ,e.emple_codigo) as emple_codigo'),
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'p.perso_sexo',
                                DB::raw('IF(c.cargo_descripcion is null, 0 ,c.cargo_descripcion) as cargo_descripcion'),
                                DB::raw('IF(act.codigoActividad is null, 0 , act.codigoActividad) as codigoActividad'),
                                'act.Activi_Nombre',
                                DB::raw('IF(sub.subAct_codigo is null, 0 ,sub.subAct_codigo) as codigoSubactiv'),
                                'sub.subAct_nombre',
                                DB::raw('IF(mt.marcaTareo_entrada is null, 0 , mt.marcaTareo_entrada) as entrada'),
                                DB::raw('IF(mt.marcaTareo_salida is null, 0 , mt.marcaTareo_salida) as salida'),
                                'mt.idmarcaciones_tareo as idMarcacion',

                                'pc.descripcion as puntoControl',
                                DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                                DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                                DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida")

                            )
                            ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                            ->where('mt.organi_id', '=', session('sesionidorg'))
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                            ->get();


                    } else {
                        $marcaciones = DB::table('marcacion_tareo as mt')
                            ->join('empleado as e', 'mt.marcaTareo_idempleado', '=', 'e.emple_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->leftJoinSub($ControladorEoS, 'entrada', function ($join) {
                                $join->on('mt.idcontroladores_entrada', '=', 'entrada.idcontroladores_tareo');
                            })
                            ->leftJoinSub($ControladorEoS, 'salida', function ($join) {
                                $join->on('mt.idcontroladores_salida', '=', 'salida.idcontroladores_tareo');
                            })
                            ->leftJoin('actividad as act', 'mt.Activi_id', '=', 'act.Activi_id')
                            ->leftJoin('punto_control as pc', 'mt.puntoC_id', '=', 'pc.id')
                            ->leftJoin('centro_costo as centC', 'mt.centroC_id', '=', 'centC.centroC_id')
                            ->leftJoin('subactividad as sub', 'mt.idsubActividad', '=', 'sub.idsubActividad')

                            ->select(
                                'e.emple_id',
                                'mt.idmarcaciones_tareo',
                                'e.emple_nDoc',
                                DB::raw('IF(e.emple_codigo is null, 0 ,e.emple_codigo) as emple_codigo'),
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'p.perso_sexo',
                                DB::raw('IF(c.cargo_descripcion is null, 0 ,c.cargo_descripcion) as cargo_descripcion'),
                                DB::raw('IF(act.codigoActividad is null, 0 , act.codigoActividad) as codigoActividad'),
                                'act.Activi_Nombre',
                                DB::raw('IF(sub.subAct_codigo is null, 0 ,sub.subAct_codigo) as codigoSubactiv'),
                                'sub.subAct_nombre',
                                DB::raw('IF(mt.marcaTareo_entrada is null, 0 , mt.marcaTareo_entrada) as entrada'),
                                DB::raw('IF(mt.marcaTareo_salida is null, 0 , mt.marcaTareo_salida) as salida'),
                                'mt.idmarcaciones_tareo as idMarcacion',

                                'pc.descripcion as puntoControl',
                                DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                                DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                                DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida")

                            )
                            ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                            ->where('mt.organi_id', '=', session('sesionidorg'))
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where('e.emple_id', $idemp)
                            ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                            ->get();


                    }
                } else {
                    if ($idemp == 0 || $idemp == ' ') {
                        $marcaciones = DB::table('marcacion_tareo as mt')
                            ->join('empleado as e', 'mt.marcaTareo_idempleado', '=', 'e.emple_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->leftJoinSub($ControladorEoS, 'entrada', function ($join) {
                                $join->on('mt.idcontroladores_entrada', '=', 'entrada.idcontroladores_tareo');
                            })
                            ->leftJoinSub($ControladorEoS, 'salida', function ($join) {
                                $join->on('mt.idcontroladores_salida', '=', 'salida.idcontroladores_tareo');
                            })
                            ->leftJoin('actividad as act', 'mt.Activi_id', '=', 'act.Activi_id')
                            ->leftJoin('punto_control as pc', 'mt.puntoC_id', '=', 'pc.id')
                            ->leftJoin('centro_costo as centC', 'mt.centroC_id', '=', 'centC.centroC_id')
                            ->leftJoin('subactividad as sub', 'mt.idsubActividad', '=', 'sub.idsubActividad')

                            ->select(
                                'e.emple_id',
                                'mt.idmarcaciones_tareo',
                                'e.emple_nDoc',
                                DB::raw('IF(e.emple_codigo is null, 0 ,e.emple_codigo) as emple_codigo'),
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'p.perso_sexo',
                                DB::raw('IF(c.cargo_descripcion is null, 0 ,c.cargo_descripcion) as cargo_descripcion'),
                                DB::raw('IF(act.codigoActividad is null, 0 , act.codigoActividad) as codigoActividad'),
                                'act.Activi_Nombre',
                                DB::raw('IF(sub.subAct_codigo is null, 0 ,sub.subAct_codigo) as codigoSubactiv'),
                                'sub.subAct_nombre',
                                DB::raw('IF(mt.marcaTareo_entrada is null, 0 , mt.marcaTareo_entrada) as entrada'),
                                DB::raw('IF(mt.marcaTareo_salida is null, 0 , mt.marcaTareo_salida) as salida'),
                                'mt.idmarcaciones_tareo as idMarcacion',

                                'pc.descripcion as puntoControl',
                                DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                                DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                                DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida")

                            )
                            ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                            ->where('mt.organi_id', '=', session('sesionidorg'))
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                            ->get();


                    } else {
                        $marcaciones = DB::table('marcacion_tareo as mt')
                            ->join('empleado as e', 'mt.marcaTareo_idempleado', '=', 'e.emple_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->leftJoinSub($ControladorEoS, 'entrada', function ($join) {
                                $join->on('mt.idcontroladores_entrada', '=', 'entrada.idcontroladores_tareo');
                            })
                            ->leftJoinSub($ControladorEoS, 'salida', function ($join) {
                                $join->on('mt.idcontroladores_salida', '=', 'salida.idcontroladores_tareo');
                            })
                            ->leftJoin('actividad as act', 'mt.Activi_id', '=', 'act.Activi_id')
                            ->leftJoin('punto_control as pc', 'mt.puntoC_id', '=', 'pc.id')
                            ->leftJoin('centro_costo as centC', 'mt.centroC_id', '=', 'centC.centroC_id')
                            ->leftJoin('subactividad as sub', 'mt.idsubActividad', '=', 'sub.idsubActividad')

                            ->select(
                                'e.emple_id',
                                'mt.idmarcaciones_tareo',
                                'e.emple_nDoc',
                                DB::raw('IF(e.emple_codigo is null, 0 ,e.emple_codigo) as emple_codigo'),
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'p.perso_sexo',
                                DB::raw('IF(c.cargo_descripcion is null, 0 ,c.cargo_descripcion) as cargo_descripcion'),
                                DB::raw('IF(act.codigoActividad is null, 0 , act.codigoActividad) as codigoActividad'),
                                'act.Activi_Nombre',
                                DB::raw('IF(sub.subAct_codigo is null, 0 ,sub.subAct_codigo) as codigoSubactiv'),
                                'sub.subAct_nombre',
                                DB::raw('IF(mt.marcaTareo_entrada is null, 0 , mt.marcaTareo_entrada) as entrada'),
                                DB::raw('IF(mt.marcaTareo_salida is null, 0 , mt.marcaTareo_salida) as salida'),
                                'mt.idmarcaciones_tareo as idMarcacion',

                                DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                                DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                                DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida")

                            )
                            ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                            ->where('mt.organi_id', '=', session('sesionidorg'))
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where('e.emple_id', $idemp)
                            ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                            ->get();


                    }
                }
            }
        } else {
            if ($idemp == 0 || $idemp == ' ') {

                $marcaciones = DB::table('marcacion_tareo as mt')
                    ->join('empleado as e', 'mt.marcaTareo_idempleado', '=', 'e.emple_id')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoinSub($ControladorEoS, 'entrada', function ($join) {
                        $join->on('mt.idcontroladores_entrada', '=', 'entrada.idcontroladores_tareo');
                    })
                    ->leftJoinSub($ControladorEoS, 'salida', function ($join) {
                        $join->on('mt.idcontroladores_salida', '=', 'salida.idcontroladores_tareo');
                    })
                    ->leftJoin('actividad as act', 'mt.Activi_id', '=', 'act.Activi_id')
                    ->leftJoin('punto_control as pc', 'mt.puntoC_id', '=', 'pc.id')
                    ->leftJoin('centro_costo as centC', 'mt.centroC_id', '=', 'centC.centroC_id')
                    ->leftJoin('subactividad as sub', 'mt.idsubActividad', '=', 'sub.idsubActividad')

                    ->select(
                        'e.emple_id',
                        'mt.idmarcaciones_tareo',
                        'e.emple_nDoc',
                        DB::raw('IF(e.emple_codigo is null, 0 ,e.emple_codigo) as emple_codigo'),
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'p.perso_sexo',
                        DB::raw('IF(c.cargo_descripcion is null, 0 ,c.cargo_descripcion) as cargo_descripcion'),
                        DB::raw('IF(act.codigoActividad is null, 0 , act.codigoActividad) as codigoActividad'),
                        'act.Activi_Nombre',
                        DB::raw('IF(sub.subAct_codigo is null, 0 ,sub.subAct_codigo) as codigoSubactiv'),
                        'sub.subAct_nombre',
                        DB::raw('IF(mt.marcaTareo_entrada is null, 0 , mt.marcaTareo_entrada) as entrada'),
                        DB::raw('IF(mt.marcaTareo_salida is null, 0 , mt.marcaTareo_salida) as salida'),
                        'mt.idmarcaciones_tareo as idMarcacion',
                        'pc.descripcion as puntoControl',
                        DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                        DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                        DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida")

                    )
                    ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                    ->where('mt.organi_id', '=', session('sesionidorg'))
                    ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                    ->get();


            } else {
                $marcaciones = DB::table('marcacion_tareo as mt')
                    ->join('empleado as e', 'mt.marcaTareo_idempleado', '=', 'e.emple_id')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoinSub($ControladorEoS, 'entrada', function ($join) {
                        $join->on('mt.idcontroladores_entrada', '=', 'entrada.idcontroladores_tareo');
                    })
                    ->leftJoinSub($ControladorEoS, 'salida', function ($join) {
                        $join->on('mt.idcontroladores_salida', '=', 'salida.idcontroladores_tareo');
                    })
                    ->leftJoin('actividad as act', 'mt.Activi_id', '=', 'act.Activi_id')
                    ->leftJoin('punto_control as pc', 'mt.puntoC_id', '=', 'pc.id')
                    ->leftJoin('centro_costo as centC', 'mt.centroC_id', '=', 'centC.centroC_id')
                    ->leftJoin('subactividad as sub', 'mt.idsubActividad', '=', 'sub.idsubActividad')

                    ->select(
                        'e.emple_id',
                        'mt.idmarcaciones_tareo',
                        'e.emple_nDoc',
                        DB::raw('IF(e.emple_codigo is null, 0 ,e.emple_codigo) as emple_codigo'),
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'p.perso_sexo',
                        DB::raw('IF(c.cargo_descripcion is null, 0 ,c.cargo_descripcion) as cargo_descripcion'),
                        DB::raw('IF(act.codigoActividad is null, 0 , act.codigoActividad) as codigoActividad'),
                        'act.Activi_Nombre',
                        DB::raw('IF(sub.subAct_codigo is null, 0 ,sub.subAct_codigo) as codigoSubactiv'),
                        'sub.subAct_nombre',
                        DB::raw('IF(mt.marcaTareo_entrada is null, 0 , mt.marcaTareo_entrada) as entrada'),
                        DB::raw('IF(mt.marcaTareo_salida is null, 0 , mt.marcaTareo_salida) as salida'),
                        'mt.idmarcaciones_tareo as idMarcacion',

                        'pc.descripcion as puntoControl',
                        DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                        DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                        DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida")

                    )
                    ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                    ->where('mt.organi_id', '=', session('sesionidorg'))
                    ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                    ->where('e.emple_id', $idemp)
                    ->get();


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

    public function intercambiarTareo(Request $request)
    {
        $idmarcacion=$request->id;
        $marcacionesT = DB::table('marcacion_tareo as mt')
            ->where('mt.idmarcaciones_tareo', '=',$idmarcacion)
            ->get()->first();

        if($marcacionesT->marcaTareo_entrada!=null){
            $marcacionModi=marcacion_tareo::findOrFail($idmarcacion);
            $marcacionModi->marcaTareo_salida=$marcacionesT->marcaTareo_entrada;
            $marcacionModi->marcaTareo_entrada=null;
            $marcacionModi->save();
            return "Salida modificada";
        }
        else{
            $marcacionModi=marcacion_tareo::findOrFail($idmarcacion);
            $marcacionModi->marcaTareo_entrada=$marcacionesT->marcaTareo_salida;
            $marcacionModi->marcaTareo_salida=null;
            $marcacionModi->save();
            return "Entrada modificada";
        }
    }

     // * NUEVA SALIDA
     public function registrarNSalida(Request $request)
     {
         $id = $request->get('id');
         $tiempo = $request->get('salida');
         $idhorarioE = $request->get('horario');

         $marcacion = marcacion_tareo::findOrFail($id);
         $entrada = Carbon::parse($marcacion->marcaTareo_entrada);
         // * COMPROBAR SI TIENE HORARIO EMPLEADO
         if ($idhorarioE != 0) {
             $horario = DB::table('horario_empleado as he')
                 ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                 ->select(
                     'h.horario_descripcion as descripcion',
                     'h.horaI',
                     'h.horaF',
                     'h.horario_tolerancia as toleranciaI',
                     'h.horario_toleranciaF as toleranciaF',
                     'he.fuera_horario as fueraH',
                     'he.nHoraAdic as horasA'
                 )
                 ->where('he.horarioEmp_id', '=', $idhorarioE)
                 ->get()
                 ->first();
             // * OBTENER TIEMPO DE HORARIOS
             $horarioInicio = Carbon::parse($entrada->copy()->isoFormat('YYYY-MM-DD') . " " . $horario->horaI);
             if ($horario->horaF > $horario->horaI) {
                 $horarioFin = Carbon::parse($entrada->copy()->isoFormat('YYYY-MM-DD') . " " . $horario->horaF);
                 // ? OBTENER TIEMPO DE SALIDA
                 $nuevoTiempo = $entrada->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo;
             } else {
                 $nuevaFecha = $entrada->copy()->addDays(1)->isoFormat('YYYY-MM-DD');  // : OBTENEMOS LA FECHA DEL DIA SIGUIENTE
                 $horarioFin = Carbon::parse($nuevaFecha . " " . $horario->horaF);
                 if ($tiempo > $entrada->copy()->isoFormat('HH:mm:ss')) {
                     // ? OBTENER TIEMPO DE SALIDA
                     $nuevoTiempo = $entrada->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo;
                 } else {
                     // ? OBTENER TIEMPO DE SALIDA
                     $nuevoTiempo = $nuevaFecha . " " . $tiempo;
                 }
             }
             $salida = Carbon::parse($nuevoTiempo);  //: OBTENEMOS EL TIEMPO DE SALIDA
         }
         $salida = Carbon::parse($entrada->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo);   //: OBTENEMOS EL TIEMPO DE SALIDA
         // * VALIDAR QUE SALIDA DEBE SER MAYOR A ENTRADA
         if ($salida->gt($entrada)) {
             // * VALIDACION ENTRE CRUCES DE HORAS
             $marcacionesValidar = DB::table('marcacion_tareo as m')
                 ->select(
                     'm.idmarcaciones_tareo',
                     DB::raw('IF(m.marcaTareo_entrada is null,0,m.marcaTareo_entrada) AS entrada'),
                     DB::raw('IF(m.marcaTareo_salida is null,0,m.marcaTareo_salida) AS salida')
                 )
                 ->where('m.marcaTareo_idempleado', '=', $marcacion->marcaTareo_idempleado)
                 ->where(DB::raw('IF(m.marcaTareo_entrada is null,DATE(m.marcaTareo_salida),DATE(m.marcaTareo_entrada))'), "=", $entrada->copy()->isoFormat('YYYY-MM-DD'))
                 ->whereNotIn('m.idmarcaciones_tareo', [$marcacion->idmarcaciones_tareo])
                 ->get();
             $respuesta = true;
             foreach ($marcacionesValidar as $mv) {
                 if ($mv->entrada != 0) {
                     $respuestaCheck = checkHora($entrada, $salida, $mv->entrada);
                     if ($respuestaCheck) {
                         $respuesta = false;
                     }
                 }
                 if ($mv->salida != 0) {
                     $respuestaCheck = checkHora($entrada, $salida, $mv->salida);
                     if ($respuestaCheck) {
                         $respuesta = false;
                     }
                 }
             }
             // ! SI NO ENCUENTRA CRUCES
             if ($respuesta) {
                 // * VALIDAR CON EL HORARIO
                 if ($idhorarioE != 0) {
                     if ($horario->fueraH == 0) {
                         // * VALIDAR SIN FUERA DE HORARIO
                         $horarioInicioT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
                         $horarioFinT = $horarioFin->copy()->addMinutes($horario->toleranciaF)->addHours($horario->horasA);

                         if ($entrada->gte($horarioInicioT) && $salida->lte($horarioFinT)) {
                             $marcacion->marcaTareo_salida = $salida;
                             $marcacion->save();
                             return response()->json($marcacion->idmarcaciones_tareo, 200);
                         } else {
                             return response()->json(
                                 array("respuesta" => "Marcación fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . $horario->horaI . " - " . $horario->horaF . " )"),
                                 200
                             );
                         }
                     } else {
                         $marcacion->marcaTareo_salida = $salida;
                         $marcacion->save();
                         return response()->json($marcacion->idmarcaciones_tareo, 200);
                     }
                 } else {
                     $marcacion->marcaTareo_salida = $salida;
                     $marcacion->save();
                     return response()->json($marcacion->idmarcaciones_tareo, 200);
                 }
             } else {
                 return response()->json(array("respuesta" => "Posibilidad de cruce de hora"), 200);
             }
         } else {
             return response()->json(array("respuesta" => "Salida debe ser mayor a entrada."), 200);
         }
     }


    // * NUEVA ENTRADA
    public function registrarNEntrada(Request $request)
    {
        $id = $request->get('id');
        $tiempo = $request->get('entrada');
        $idhorarioE = $request->get('horario');

        $marcacion = marcacion_tareo::findOrFail($id);
        $salida = Carbon::parse($marcacion->marcaTareo_salida);
        // * COMPROBAR SI TIENE HORARIO EMPLEADO
        if ($idhorarioE != 0) {
            $horario = DB::table('horario_empleado as he')
                ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                ->select(
                    'h.horario_descripcion as descripcion',
                    'h.horaI',
                    'h.horaF',
                    'h.horario_tolerancia as toleranciaI',
                    'h.horario_toleranciaF as toleranciaF',
                    'he.fuera_horario as fueraH',
                    'he.nHoraAdic as horasA'
                )
                ->where('he.horarioEmp_id', '=', $idhorarioE)
                ->get()
                ->first();
            // * OBTENER TIEMPO DE HORARIOS
            $horarioInicio = Carbon::parse($salida->copy()->isoFormat('YYYY-MM-DD') . " " . $horario->horaI);
            if ($horario->horaF > $horario->horaI) {
                $horarioFin = Carbon::parse($salida->copy()->isoFormat('YYYY-MM-DD') . " " . $horario->horaF);
                // ? OBTENER TIEMPO DE SALIDA
                $nuevoTiempo = $salida->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo;
            } else {
                $nuevaFecha = $salida->copy()->addDays(1)->isoFormat('YYYY-MM-DD');  // : OBTENEMOS LA FECHA DEL DIA SIGUIENTE
                $horarioFin = Carbon::parse($nuevaFecha . " " . $horario->horaF);
                if ($tiempo > $salida->copy()->isoFormat('HH:mm:ss')) {
                    // ? OBTENER TIEMPO DE SALIDA
                    $nuevoTiempo = $salida->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo;
                } else {
                    // ? OBTENER TIEMPO DE SALIDA
                    $nuevoTiempo = $nuevaFecha . " " . $tiempo;
                }
            }
            $entrada = Carbon::parse($nuevoTiempo);  //: OBTENEMOS EL TIEMPO DE SALIDA
        }
        $entrada = Carbon::parse($salida->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo);   //: OBTENEMOS EL TIEMPO DE SALIDA
        // * VALIDAR QUE SALIDA DEBE SER MAYOR A ENTRADA
        if ($salida->gt($entrada)) {
            // * VALIDACION ENTRE CRUCES DE HORAS
            $marcacionesValidar = DB::table('marcacion_tareo as m')
                ->select(
                    'm.idmarcaciones_tareo',
                    DB::raw('IF(m.marcaTareo_entrada is null,0,m.marcaTareo_entrada) AS entrada'),
                    DB::raw('IF(m.marcaTareo_salida is null,0,m.marcaTareo_salida) AS salida')
                )
                ->where('m.marcaTareo_idempleado', '=', $marcacion->marcaTareo_idempleado)
                ->where(DB::raw('IF(m.marcaTareo_entrada is null,DATE(m.marcaTareo_salida),DATE(m.marcaTareo_entrada))'), "=", $salida->copy()->isoFormat('YYYY-MM-DD'))
                ->whereNotIn('m.idmarcaciones_tareo', [$marcacion->idmarcaciones_tareo])
                ->get();
            $respuesta = true;
            foreach ($marcacionesValidar as $mv) {
                if ($mv->entrada != 0) {
                    $respuestaCheck = checkHora($entrada, $salida, $mv->entrada);
                    if ($respuestaCheck) {
                        $respuesta = false;
                    }
                }
                if ($mv->salida != 0) {
                    $respuestaCheck = checkHora($entrada, $salida, $mv->salida);
                    if ($respuestaCheck) {
                        $respuesta = false;
                    }
                }
            }
            // ! SI NO ENCUENTRA CRUCES
            if ($respuesta) {
                // * VALIDAR CON EL HORARIO
                if ($idhorarioE != 0) {
                    if ($horario->fueraH == 0) {
                        // * VALIDAR SIN FUERA DE HORARIO
                        $horarioInicioT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
                        $horarioFinT = $horarioFin->copy()->addMinutes($horario->toleranciaF)->addHours($horario->horasA);

                        if ($entrada->gte($horarioInicioT) && $salida->lte($horarioFinT)) {
                            $marcacion->marcaTareo_entrada = $entrada;
                            $marcacion->save();
                            return response()->json($marcacion->idmarcaciones_tareo, 200);
                        } else {
                            return response()->json(
                                array("respuesta" => "Marcación fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . $horario->horaI . " - " . $horario->horaF . " )"),
                                200
                            );
                        }
                    } else {
                        $marcacion->marcaTareo_entrada = $entrada;
                        $marcacion->save();
                        return response()->json($marcacion->idmarcaciones_tareo, 200);
                    }
                } else {
                    $marcacion->marcaTareo_entrada = $entrada;
                    $marcacion->save();
                    return response()->json($marcacion->idmarcaciones_tareo, 200);
                }
            } else {
                return response()->json(array("respuesta" => "Posibilidad de cruce de hora"), 200);
            }
        } else {
            return response()->json(array("respuesta" => "Entrada debe ser menor a salida."), 200);
        }
    }
}
