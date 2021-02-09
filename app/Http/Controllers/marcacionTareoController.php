<?php

namespace App\Http\Controllers;

use App\marcacion_tareo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $area = DB::table('area')->where('organi_id', '=', session('sesionidorg'))
        ->select('area_id as idarea', 'area_descripcion as descripcion')
        ->get();
        $cargo = DB::table('cargo')
            ->where('organi_id', '=', session('sesionidorg'))
            ->select('cargo_id as idcargo', 'cargo_descripcion as descripcion')
            ->get();
        $local = DB::table('local')
            ->where('organi_id', '=', session('sesionidorg'))
            ->select('local_id as idlocal', 'local_descripcion as descripcion')
            ->get();
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
                return view('ReporteTareo.reporteFecha', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion,
                'areas' => $area, 'cargos' => $cargo, 'locales' => $local]);
            }
        } else {
            return view('ReporteTareo.reporteFecha', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion,
            'areas' => $area, 'cargos' => $cargo, 'locales' => $local]);
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
        $arrayeve = collect();

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
                'dist.dispoT_descripUbicacion as descripcion'
            );

            /*  --------------------*/
            function agregarDetalle($array){

                foreach($array as $marcacionesD){
                    $detalllesP= DB::table('punto_control_detalle as pcde')
                    ->where('pcde.idPuntoControl', '=',$marcacionesD->idPC)
                    ->get();
                    $marcacionesD->detalleNombres=$detalllesP;
                }
                return $array;
            }
            /* -------------------- */
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
                        ->leftJoinSub($DispositivosEoS, 'entradaD', function ($join) {
                            $join->on('mt.iddispositivos_entrada', '=', 'entradaD.iddispositivos_tareo');
                        })
                        ->leftJoinSub($DispositivosEoS, 'salidaD', function ($join) {
                            $join->on('mt.iddispositivos_salida', '=', 'salidaD.iddispositivos_tareo');
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
                            'pc.codigoControl as idpuntoControl',
                            'pc.id as idPC',
                            DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                            DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                            DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida"),
                            DB::raw("IF(entradaD.descripcion is null,'MANUAL' , entradaD.descripcion) as dispositivoEntrada"),
                            DB::raw("IF(salidaD.descripcion is null, 'MANUAL' ,salidaD.descripcion) as dispositivoSalida")
                        )
                        ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                        ->where('mt.organi_id', '=', session('sesionidorg'))
                        ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                        ->get();
                        $marcaciones=agregarDetalle($marcaciones);
                        $arrayeve->push($marcaciones);

                } else {
                    foreach($idemp as $idemps){
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
                        ->leftJoinSub($DispositivosEoS, 'entradaD', function ($join) {
                            $join->on('mt.iddispositivos_entrada', '=', 'entradaD.iddispositivos_tareo');
                        })
                        ->leftJoinSub($DispositivosEoS, 'salidaD', function ($join) {
                            $join->on('mt.iddispositivos_salida', '=', 'salidaD.iddispositivos_tareo');
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
                            'pc.codigoControl as idpuntoControl',
                            'pc.id as idPC',
                            DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                            DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                            DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida"),
                            DB::raw("IF(entradaD.descripcion is null,'MANUAL' , entradaD.descripcion) as dispositivoEntrada"),
                            DB::raw("IF(salidaD.descripcion is null, 'MANUAL' ,salidaD.descripcion) as dispositivoSalida")

                        )
                        ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                        ->where('mt.organi_id', '=', session('sesionidorg'))
                        ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                        ->where('e.emple_id', $idemps)
                        ->get();
                        $marcaciones=agregarDetalle($marcaciones);
                        $arrayeve->push($marcaciones);


                    }
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
                            ->leftJoinSub($DispositivosEoS, 'entradaD', function ($join) {
                                $join->on('mt.iddispositivos_entrada', '=', 'entradaD.iddispositivos_tareo');
                            })
                            ->leftJoinSub($DispositivosEoS, 'salidaD', function ($join) {
                                $join->on('mt.iddispositivos_salida', '=', 'salidaD.iddispositivos_tareo');
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
                                'pc.codigoControl as idpuntoControl',
                                'pc.id as idPC',
                                DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                                DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                                DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida"),
                                DB::raw("IF(entradaD.descripcion is null,'MANUAL' , entradaD.descripcion) as dispositivoEntrada"),
                                DB::raw("IF(salidaD.descripcion is null, 'MANUAL' ,salidaD.descripcion) as dispositivoSalida")

                            )
                            ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                            ->where('mt.organi_id', '=', session('sesionidorg'))
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                            ->get();
                            $marcaciones=agregarDetalle($marcaciones);
                            $arrayeve->push($marcaciones);

                    } else {
                        foreach($idemp as $idemps){
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
                            ->leftJoinSub($DispositivosEoS, 'entradaD', function ($join) {
                                $join->on('mt.iddispositivos_entrada', '=', 'entradaD.iddispositivos_tareo');
                            })
                            ->leftJoinSub($DispositivosEoS, 'salidaD', function ($join) {
                                $join->on('mt.iddispositivos_salida', '=', 'salidaD.iddispositivos_tareo');
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
                                'pc.codigoControl as idpuntoControl',
                                'pc.id as idPC',
                                DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                                DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                                DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida"),
                                DB::raw("IF(entradaD.descripcion is null,'MANUAL' , entradaD.descripcion) as dispositivoEntrada"),
                                DB::raw("IF(salidaD.descripcion is null, 'MANUAL' ,salidaD.descripcion) as dispositivoSalida")

                            )
                            ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                            ->where('mt.organi_id', '=', session('sesionidorg'))
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where('e.emple_id', $idemps)
                            ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                            ->get();
                            $marcaciones=agregarDetalle($marcaciones);
                            $arrayeve->push($marcaciones);
                        }

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
                            ->leftJoinSub($DispositivosEoS, 'entradaD', function ($join) {
                                $join->on('mt.iddispositivos_entrada', '=', 'entradaD.iddispositivos_tareo');
                            })
                            ->leftJoinSub($DispositivosEoS, 'salidaD', function ($join) {
                                $join->on('mt.iddispositivos_salida', '=', 'salidaD.iddispositivos_tareo');
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
                                'pc.codigoControl as idpuntoControl',
                                'pc.id as idPC',
                                DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                                DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                                DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida"),
                                DB::raw("IF(entradaD.descripcion is null,'MANUAL' , entradaD.descripcion) as dispositivoEntrada"),
                                DB::raw("IF(salidaD.descripcion is null, 'MANUAL' ,salidaD.descripcion) as dispositivoSalida")

                            )
                            ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                            ->where('mt.organi_id', '=', session('sesionidorg'))
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                            ->get();
                            $marcaciones=agregarDetalle($marcaciones);
                            $arrayeve->push($marcaciones);

                    } else {
                        foreach($idemp as $idemps){
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
                            ->leftJoinSub($DispositivosEoS, 'entradaD', function ($join) {
                                $join->on('mt.iddispositivos_entrada', '=', 'entradaD.iddispositivos_tareo');
                            })
                            ->leftJoinSub($DispositivosEoS, 'salidaD', function ($join) {
                                $join->on('mt.iddispositivos_salida', '=', 'salidaD.iddispositivos_tareo');
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
                                'pc.codigoControl as idpuntoControl',
                                'pc.id as idPC',
                                DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                                DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                                DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida"),
                                DB::raw("IF(entradaD.descripcion is null,'MANUAL' , entradaD.descripcion) as dispositivoEntrada"),
                                DB::raw("IF(salidaD.descripcion is null, 'MANUAL' ,salidaD.descripcion) as dispositivoSalida")

                            )
                            ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                            ->where('mt.organi_id', '=', session('sesionidorg'))
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where('e.emple_id', $idemps)
                            ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                            ->get();
                            $marcaciones=agregarDetalle($marcaciones);
                            $arrayeve->push($marcaciones);
                        }

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
                    ->leftJoinSub($DispositivosEoS, 'entradaD', function ($join) {
                        $join->on('mt.iddispositivos_entrada', '=', 'entradaD.iddispositivos_tareo');
                    })
                    ->leftJoinSub($DispositivosEoS, 'salidaD', function ($join) {
                        $join->on('mt.iddispositivos_salida', '=', 'salidaD.iddispositivos_tareo');
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
                        'pc.codigoControl as idpuntoControl',
                        'pc.id as idPC',
                        DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                        DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                        DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida"),
                        DB::raw("IF(entradaD.descripcion is null,'MANUAL' , entradaD.descripcion) as dispositivoEntrada"),
                        DB::raw("IF(salidaD.descripcion is null, 'MANUAL' ,salidaD.descripcion) as dispositivoSalida")

                    )
                    ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                    ->where('mt.organi_id', '=', session('sesionidorg'))
                    ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                    ->get();

                    $marcaciones=agregarDetalle($marcaciones);
                    $arrayeve->push($marcaciones);

            } else {
                foreach($idemp as $idemps){
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
                    ->leftJoinSub($DispositivosEoS, 'entradaD', function ($join) {
                        $join->on('mt.iddispositivos_entrada', '=', 'entradaD.iddispositivos_tareo');
                    })
                    ->leftJoinSub($DispositivosEoS, 'salidaD', function ($join) {
                        $join->on('mt.iddispositivos_salida', '=', 'salidaD.iddispositivos_tareo');
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
                        'pc.codigoControl as idpuntoControl',
                        'pc.id as idPC',
                        DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id) as idHE'),
                        DB::raw("IF(entrada.nombre is null, 0 , entrada.nombre) as controladorEntrada"),
                        DB::raw("IF(salida.nombre is null, 0 , salida.nombre) as controladorSalida"),
                        DB::raw("IF(entradaD.descripcion is null,'MANUAL' , entradaD.descripcion) as dispositivoEntrada"),
                        DB::raw("IF(salidaD.descripcion is null, 'MANUAL' ,salidaD.descripcion) as dispositivoSalida")

                    )
                    ->where(DB::raw('IF(mt.marcaTareo_entrada is null, DATE(mt.marcaTareo_salida), DATE(mt.marcaTareo_entrada))'), '=', $fecha)
                    ->where('mt.organi_id', '=', session('sesionidorg'))
                    ->orderBy(DB::raw('IF(mt.marcaTareo_entrada is null, mt.marcaTareo_salida , mt.marcaTareo_entrada)', 'ASC'))
                    ->where('e.emple_id', $idemps)
                    ->get();
                    $marcaciones=agregarDetalle($marcaciones);
                    $arrayeve->push($marcaciones);
                }

            }
        }

        return response()->json(Arr::flatten($arrayeve), 200);

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
        $idmarcacion = $request->id;
        $marcacionesT = DB::table('marcacion_tareo as mt')
            ->where('mt.idmarcaciones_tareo', '=', $idmarcacion)
            ->get()->first();

        if ($marcacionesT->marcaTareo_entrada != null) {
            $marcacionModi = marcacion_tareo::findOrFail($idmarcacion);
            $marcacionModi->marcaTareo_salida = $marcacionesT->marcaTareo_entrada;
            $marcacionModi->marcaTareo_entrada = null;
            $marcacionModi->idcontroladores_salida = $marcacionesT->idcontroladores_entrada;
            $marcacionModi->idcontroladores_entrada = null;
            $marcacionModi->iddispositivos_salida = $marcacionesT->iddispositivos_entrada;
            $marcacionModi->iddispositivos_entrada = null;
            $marcacionModi->save();
            return "Salida modificada";
        } else {
            $marcacionModi = marcacion_tareo::findOrFail($idmarcacion);
            $marcacionModi->marcaTareo_entrada = $marcacionesT->marcaTareo_salida;
            $marcacionModi->marcaTareo_salida = null;
            $marcacionModi->idcontroladores_entrada = $marcacionesT->idcontroladores_salida;
            $marcacionModi->idcontroladores_salida = null;
            $marcacionModi->iddispositivos_entrada = $marcacionesT->iddispositivos_salida;
            $marcacionModi->iddispositivos_salida = null;
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
                    DB::raw('IF(he.horaAdic is null, 0 ,he.horaAdic) as horasA'),
                    'h.horasObliga as horasO'
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
                $nuevaFecha = $entrada->copy()->addDays(1)->isoFormat('YYYY-MM-DD'); // : OBTENEMOS LA FECHA DEL DIA SIGUIENTE
                $horarioFin = Carbon::parse($nuevaFecha . " " . $horario->horaF);
                if ($tiempo > $entrada->copy()->isoFormat('HH:mm:ss')) {
                    // ? OBTENER TIEMPO DE SALIDA
                    $nuevoTiempo = $entrada->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo;
                } else {
                    // ? OBTENER TIEMPO DE SALIDA
                    $nuevoTiempo = $nuevaFecha . " " . $tiempo;
                }
            }
            $salida = Carbon::parse($nuevoTiempo); //: OBTENEMOS EL TIEMPO DE SALIDA
        }
        $salida = Carbon::parse($entrada->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo); //: OBTENEMOS EL TIEMPO DE SALIDA
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
                    // : SUMAR TIEMPOS DE MARCACIONES
                    $sumaTotalDeHoras = DB::table('marcacion_tareo as m')
                        ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaTareo_salida,m.marcaTareo_entrada)))) as totalT'))
                        ->where('m.marcaTareo_idempleado', '=', $marcacion->marcaTareo_idempleado)
                        ->whereNotNull('m.marcaTareo_entrada')
                        ->whereNotNull('m.marcaTareo_salida')
                        ->where(DB::raw('DATE(marcaTareo_entrada)'), '=', $entrada->copy()->isoFormat('YYYY-MM-DD'))
                        ->where('m.horarioEmp_id', '=', $idhorarioE)
                        ->get();
                    // : CALCULAR TIEMPO
                    $sumaTotalDeHoras[0]->totalT = $sumaTotalDeHoras[0]->totalT == null ? "00:00:00" : $sumaTotalDeHoras[0]->totalT;
                    // : TIEMPO DE ENTRADA Y SALIDA
                    $horaIParse = Carbon::parse($entrada);
                    $horaFParse = Carbon::parse($salida);
                    // : CALCULAMOS EL TIEMPO ENTRE SALIDA Y ENTRADA
                    $totalDuration = $horaFParse->diffInSeconds($horaIParse);
                    // : TIEMPO TOTAL DE MARCACIONES AGREGAMOS EL TIEMPO ENTRE SALIDA Y ENTRADA
                    $tiempoTotal = Carbon::parse($sumaTotalDeHoras[0]->totalT)->addSeconds($totalDuration);
                    // : TIEMPO DE HORAS OBLIGADAS DE HORARIO MAS LAS HORAS ADICIONALES
                    $tiempoTotalDeHorario = Carbon::parse($horario->horasO)->addMinutes($horario->horasA * 60);
                    if ($tiempoTotal->lte($tiempoTotalDeHorario)) {
                        if ($horario->fueraH == 0) {
                            // * VALIDAR SIN FUERA DE HORARIO
                            $horarioInicioT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
                            $horarioFinT = $horarioFin->copy()->addMinutes($horario->toleranciaF);
                            if ($entrada->gte($horarioInicioT) && $salida->lte($horarioFinT)) {
                                $marcacion->marcaTareo_salida = $salida;
                                $marcacion->iddispositivos_salida = null;
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
                            $marcacion->iddispositivos_salida = null;
                            $marcacion->save();
                            return response()->json($marcacion->idmarcaciones_tareo, 200);
                        }
                    } else {
                        return response()->json(
                            array("respuesta" => "Sobretiempo en la marcación."),
                            200
                        );
                    }
                } else {
                    $marcacion->marcaTareo_salida = $salida;
                    $marcacion->iddispositivos_salida = null;
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
                    DB::raw('IF(he.horaAdic is null, 0 ,he.horaAdic) as horasA'),
                    'h.horasObliga as horasO'
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
                $nuevaFecha = $salida->copy()->addDays(1)->isoFormat('YYYY-MM-DD'); // : OBTENEMOS LA FECHA DEL DIA SIGUIENTE
                $horarioFin = Carbon::parse($nuevaFecha . " " . $horario->horaF);
                if ($tiempo > $salida->copy()->isoFormat('HH:mm:ss')) {
                    // ? OBTENER TIEMPO DE SALIDA
                    $nuevoTiempo = $salida->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo;
                } else {
                    // ? OBTENER TIEMPO DE SALIDA
                    $nuevoTiempo = $nuevaFecha . " " . $tiempo;
                }
            }
            $entrada = Carbon::parse($nuevoTiempo); //: OBTENEMOS EL TIEMPO DE SALIDA
        }
        $entrada = Carbon::parse($salida->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo); //: OBTENEMOS EL TIEMPO DE SALIDA
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
                    $sumaTotalDeHoras = DB::table('marcacion_tareo as m')
                        ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaTareo_salida,m.marcaTareo_entrada)))) as totalT'))
                        ->where('m.marcaTareo_idempleado', '=', $marcacion->marcaTareo_idempleado)
                        ->whereNotNull('m.marcaTareo_entrada')
                        ->whereNotNull('m.marcaTareo_salida')
                        ->where(DB::raw('DATE(marcaTareo_entrada)'), '=', $entrada->copy()->isoFormat('YYYY-MM-DD'))
                        ->where('m.horarioEmp_id', '=', $idhorarioE)
                        ->get();
                    // * CALCULAR TIEMPO
                    $sumaTotalDeHoras[0]->totalT = $sumaTotalDeHoras[0]->totalT == null ? "00:00:00" : $sumaTotalDeHoras[0]->totalT;
                    $horaIParse = Carbon::parse($entrada);
                    $horaFParse = Carbon::parse($salida);
                    $totalDuration = $horaFParse->diffInSeconds($horaIParse);
                    $tiempoTotal = Carbon::parse($sumaTotalDeHoras[0]->totalT)->addSeconds($totalDuration);
                    $tiempoTotalDeHorario = Carbon::parse($horario->horasO)->addMinutes($horario->horasA * 60);
                    if ($tiempoTotal->lte($tiempoTotalDeHorario)) {
                        if ($horario->fueraH == 0) {
                            // * VALIDAR SIN FUERA DE HORARIO
                            $horarioInicioT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
                            $horarioFinT = $horarioFin->copy()->addMinutes($horario->toleranciaF);

                            if ($entrada->gte($horarioInicioT) && $salida->lte($horarioFinT)) {
                                $marcacion->marcaTareo_entrada = $entrada;
                                $marcacion->iddispositivos_entrada = null;
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
                            $marcacion->iddispositivos_entrada = null;
                            $marcacion->save();
                            return response()->json($marcacion->idmarcaciones_tareo, 200);
                        }
                    } else {
                        return response()->json(
                            array("respuesta" => "Sobretiempo en la marcación."),
                            200
                        );
                    }
                } else {
                    $marcacion->marcaTareo_entrada = $entrada;
                    $marcacion->iddispositivos_entrada = null;
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

    public function listPuntoControl(Request $request){

        $punto_control = DB::table('punto_control as pc')
        ->select(
            'pc.id',
            'pc.descripcion',
            'pc.codigoControl',
            'pc.verificacion',
            'pc.estado'
        )
        ->where('pc.organi_id', '=',  session('sesionidorg'))
        ->where('pc.ModoTareo', '=', 1)
        ->where('pc.estado', '=', 1)
        ->get();
        return response()->json($punto_control, 200);
    }

    public function registrarPunto(Request $request){

        $idMarcacion=$request->idMarcacion;
        $idPunto=$request->idPunto;

        $marcacion_tareo=marcacion_tareo::findOrFail($idMarcacion);
        $marcacion_tareo->puntoC_id=$idPunto;
        $marcacion_tareo->save();

    }

    public function listActividadTareo(){

        $actividades = DB::table('actividad as a')
        ->select(
            'a.Activi_id',
            'a.Activi_Nombre',
            'a.organi_id',
            'a.codigoActividad'
        )
        ->where('a.organi_id', '=', session('sesionidorg'))
        ->where('a.estado', '=', 1)
        ->where('a.modoTareo', '=', 1)
        ->get();

    foreach ($actividades as $actividadesSub) {
        $Subactividades = DB::table('actividad_subactividad as asu')
            ->join('subactividad as su', 'asu.subActividad', '=', 'su.idsubActividad')
            ->select(
                'asu.Activi_id',
                'su.idsubActividad',
                'su.subAct_nombre',
                'su.subAct_codigo',
                'su.estado',
                'su.modoTareo',
                'su.organi_id'
            )
            ->where('asu.Activi_id', '=', $actividadesSub->Activi_id)
            ->where('su.estado', '=', 1)
            ->where('asu.estado', '=', 1)
            ->where('su.modoTareo', '=', 1)
            ->groupBy('asu.idactividad_subactividad')
            ->get();

        $actividadesSub->subactividades = $Subactividades;
        if($Subactividades->isEmpty()){
            $actividadesSub->conSub = 0;
        }
        else{
            $actividadesSub->conSub = 1;
        }

    }
    return response()->json($actividades, 200);
    }

    public function registrarActiv(Request $request){

        $idMarcacion=$request->idMarcacion;
        $idActiv=$request->idActiv;
        $idSubact=$request->idSubact;

        $marcacion_tareo=marcacion_tareo::findOrFail($idMarcacion);
        $marcacion_tareo->Activi_id=$idActiv;
        $marcacion_tareo->idsubActividad=$idSubact;
        $marcacion_tareo->save();
    }

    // * ELIMINAR MARCACION
    public function eliminarMarcacion(Request $request)
    {
        $id = $request->get('id');
        $tipo = $request->get('tipo');

        // * BUSCAMOS MARCACION
        $marcacion = marcacion_tareo::findOrFail($id);
        if ($tipo == 1) {
            $marcacion->marcaTareo_entrada = NULL;
            $marcacion->iddispositivos_entrada = NULL;
            $marcacion->idcontroladores_entrada = NULL;
            $marcacion->save();
        } else {
            $marcacion->marcaTareo_salida = NULL;
            $marcacion->iddispositivos_salida = NULL;
            $marcacion->idcontroladores_salida = NULL;
            $marcacion->save();
        }

        if (is_null($marcacion->marcaTareo_entrada) && is_null($marcacion->marcaTareo_salida)) {
            $marcacion->delete();
        }

        return response()->json("Marcación eliminada.", 200);
    }

    //* LISTA DE CONTROLADORES
    public function listControladores(Request $request){

        $controlador = DB::table('controladores_tareo as ct')
        ->select(
            'idcontroladores_tareo',
            DB::raw('CONCAT(contrT_nombres," ",contrT_ApPaterno," ",contrT_ApMaterno) as nombre'),
            'contrT_estado',
            'organi_id'

        )
        ->where('organi_id', '=',  session('sesionidorg'))
        ->where('contrT_estado', '=', 1)
        ->get();
        return response()->json($controlador, 200);
    }

    //*REGISTRAR CONTROLADOR DE ENTRADA
    public function TareoregistrarContE(Request $request){
        $idMarcacion=$request->idMarcacion;
        $idControl=$request->idControl;

        $marcacion_tareo=marcacion_tareo::findOrFail($idMarcacion);
        $marcacion_tareo->idcontroladores_entrada=$idControl;
        $marcacion_tareo->save();
    }

    //*REGISTRAR CONTROLADOR DE SALIDA
    public function TareoregistrarContS(Request $request){
        $idMarcacion=$request->idMarcacion;
        $idControl=$request->idControl;

        $marcacion_tareo=marcacion_tareo::findOrFail($idMarcacion);
        $marcacion_tareo->idcontroladores_salida=$idControl;
        $marcacion_tareo->save();
    }

     // * HORARIOS DE MARCACIONES
     public function horariosxMarcacion(Request $request)
     {
         $tipo = $request->get('tipo');
         $id = $request->get('id');
         $marcacion = marcacion_tareo::findOrFail($id);
         $fechaM = $tipo == 2 ? $marcacion->marcaTareo_salida : $marcacion->marcaTareo_entrada;
         $fecha = Carbon::parse($fechaM)->isoFormat("YYYY-MM-DD");

         $respuesta = [];

         $horario = DB::table('horario_empleado as he')
             ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
             ->join('horario as h', 'h.horario_id', '=', 'horario_horario_id')
             ->select(
                 'he.horarioEmp_id as id',
                 'h.horario_descripcion',
                 'h.horaI',
                 'h.horaF',
                 'h.horario_tolerancia',
                 'h.horario_toleranciaF',
                 DB::raw("CONCAT( DATE(hd.start),' ', h.horaI) as horarioInicio"),
                 DB::raw("IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF)) as horarioFin")
             )
             ->where('he.empleado_emple_id', '=', $marcacion->marcaTareo_idempleado)
             ->where(DB::raw('DATE(hd.start)'), '=', $fecha)
             ->where('he.estado', '=', 1)
             ->get();

         foreach ($horario as $h) {
             $horarioFSuma = Carbon::parse($h->horarioFin)->addMinutes($h->horario_toleranciaF);
             $horarioIResta = Carbon::parse($h->horarioInicio)->subMinutes($h->horario_tolerancia);
             // * VALIDACION DE HORARIO CON EL TIEMPO DE MARCACION
             if (Carbon::parse($fechaM)->gte($horarioIResta) && Carbon::parse($fechaM)->lt($horarioFSuma)) {
                 $arrayHorario = (object) array(
                     "id" => $h->id,
                     "horario_descripcion" => $h->horario_descripcion,
                     "horaI" => $h->horaI,
                     "horaF" => $h->horaF
                 );

                 array_push($respuesta, $arrayHorario);
             }
         }

         return response()->json($respuesta, 200);
     }

     // * GUARDAR A NUEVA ASIGNACION
    public function asignacionMarcacion(Request $request)
    {
        $id = $request->get('id');
        $idHorarioE = $request->get('idHorario');
        $marcacionTipo = $request->get('tipoM');
        $tipo = $request->get('tipo');
        $marcacion = marcacion_tareo::findOrFail($id);
        if ($marcacionTipo == 1) {
            $fecha = Carbon::parse($marcacion->marcaTareo_entrada)->isoFormat('YYYY-MM-DD');
        } else {
            $fecha = Carbon::parse($marcacion->marcaTareo_salida)->isoFormat('YYYY-MM-DD');
        }
        // * VALIDACIONES
        $marcacionesValidar = DB::table('marcacion_tareo as m')
            ->select(
                'm.idmarcaciones_tareo',
                DB::raw('IF(m.marcaTareo_entrada is null,0,m.marcaTareo_entrada) AS entrada'),
                DB::raw('IF(m.marcaTareo_salida is null,0,m.marcaTareo_salida) AS salida')
            )
            ->where('m.marcaTareo_idempleado', '=', $marcacion->marcaTareo_idempleado)
            ->whereNotIn('m.idmarcaciones_tareo', [$marcacion->idmarcaciones_tareo])
            ->where(DB::raw('IF(m.marcaTareo_entrada is null,DATE(m.marcaTareo_salida),DATE(m.marcaTareo_entrada))'), "=", $fecha)
            ->whereNotNull('m.marcaTareo_entrada')
            ->whereNotNull('m.marcaTareo_salida')
            ->get();
        // dd(DB::getQueryLog());
        $respuesta = true;
        foreach ($marcacionesValidar as $mv) {
            if ($marcacionTipo == 1) {
                $respuestaCheck = checkHora($mv->entrada, $mv->salida, $marcacion->marcaTareo_entrada);
            } else {
                $respuestaCheck = checkHora($mv->entrada, $mv->salida, $marcacion->marcaTareo_salida);
            }
            if ($respuestaCheck) {
                $respuesta = false;
            }
        }

        if ($respuesta) {
            // * TOMAR MARCACION PARA NUEVA MARCACION
            if ($marcacionTipo == 1) {
                $nuevaMarcacion = $marcacion->marcaTareo_entrada;
                $marcacion->marcaTareo_entrada = NULL;
                $nuevoDispositivo=$marcacion->iddispositivos_entrada;
                $marcacion->iddispositivos_entrada=NULL;
                $nuevoControlador=$marcacion->idcontroladores_entrada;
                $marcacion->idcontroladores_entrada=NULL;
                $marcacion->save();
            } else {
                $nuevaMarcacion = $marcacion->marcaTareo_salida;
                $marcacion->marcaTareo_salida = NULL;
                $nuevoDispositivo=$marcacion->iddispositivos_salida;
                $marcacion->iddispositivos_salida=NULL;
                $nuevoControlador=$marcacion->idcontroladores_salida;
                $marcacion->idcontroladores_salida=NULL;
                $marcacion->save();
            }

            // * GENERAR NUEVA MARCACION
            $newMarcacion = new marcacion_tareo();
            if ($tipo ==  1) {
                $newMarcacion->marcaTareo_entrada = $nuevaMarcacion;
                // * DISPOSITIVO
                $dispositivoE = $nuevoDispositivo;
                $dispositivoS = NULL;
                // * CONTROLADOR
                $controladorE = $nuevoControlador;
                $controladorS = NULL;
            } else {
                $newMarcacion->marcaTareo_salida = $nuevaMarcacion;
                // * DISPOSITIVO
                $dispositivoS = $nuevoDispositivo;
                $dispositivoE = NULL;
                // * CONTROLADOR
                $controladorS = $nuevoControlador;
                $controladorE = NULL;
            }
            $newMarcacion->marcaTareo_idempleado = $marcacion->marcaTareo_idempleado;
            $newMarcacion->organi_id =  $marcacion->organi_id;
            $newMarcacion->horarioEmp_id = $idHorarioE == 0 ? NULL : $idHorarioE;
            $newMarcacion->marcaTareo_latitud = $marcacion->marcaTareo_latitud;
            $newMarcacion->marcaTareo_longitud = $marcacion->marcaTareo_longitud;
            $newMarcacion->Activi_id  = $marcacion->Activi_id;
            $newMarcacion->idsubActividad  = $marcacion->idsubActividad;
            $newMarcacion->puntoC_id = $marcacion->puntoC_id;
            $newMarcacion->centroC_id = $marcacion->centroC_id;
            $newMarcacion->idcontroladores_entrada = $controladorE;
            $newMarcacion->idcontroladores_salida = $controladorS;
            $newMarcacion->iddispositivos_entrada = $dispositivoE;
            $newMarcacion->iddispositivos_salida = $dispositivoS;
            $newMarcacion->save();

            return response()->json($newMarcacion->idmarcaciones_tareo, 200);
        } else {
            return response()->json(array("respuesta" => "Posibilidad de cruze de marcación."), 200);
        }
    }

       // * CONVERTIR TIEMPOS
       public function convertirTiempos(Request $request)
       {
           $idM = $request->get('id');

           $marcacion = marcacion_tareo::findOrFail($idM);
           $carbonEntrada = carbon::parse($marcacion->marcaTareo_entrada);
           $carbonSalida = carbon::parse($marcacion->marcaTareo_salida);
           $controladorEntrada=$marcacion->idcontroladores_entrada;
           $controladorSalida=$marcacion->idcontroladores_salida;
           $dispositivoEntrada=$marcacion->iddispositivos_entrada;
           $dispositivoSalida=$marcacion->iddispositivos_salida;

           if ($carbonSalida->lt($carbonEntrada)) {        // ? COMPARAMOS SI LA SALIDA ES MENOR A LA ENTRADA
               $marcacion->marcaTareo_entrada = $carbonSalida;
               $marcacion->marcaTareo_salida = $carbonEntrada;
               $marcacion->idcontroladores_entrada=$controladorSalida;
               $marcacion->idcontroladores_salida=$controladorEntrada;
               $marcacion->iddispositivos_entrada=$dispositivoSalida;
               $marcacion->iddispositivos_salida=$dispositivoEntrada;
               $marcacion->save();

               return response()->json($marcacion->idmarcaciones_tareo, 200);
           } else {
               return response()->json(array("respuesta" => "Hora final debe ser mayor a entrada."), 200);
           }
       }


     // * LISTA DE ENTRADAS CON SALIDAD NULL
     function listaDeEntradasSinS(Request $request)
     {
         $fecha = $request->get('fecha');
         $idEmpleado = $request->get('idEmpleado');

         $entradas = DB::table('marcacion_tareo as mt')
             ->leftJoin('horario_empleado as he', 'mt.horarioEmp_id', '=', 'he.horarioEmp_id')
             ->leftJoin('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
             ->select(
                 'mt.idmarcaciones_tareo as id',
                 'mt.marcaTareo_entrada as entrada',
                 DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id ) as idH'),
                 DB::raw('IF(h.horario_descripcion is null , "Sin horario",h.horario_descripcion) as horario')
             )
             ->whereNotNull('mt.marcaTareo_entrada')
             ->whereNull('mt.marcaTareo_salida')
             ->whereRaw("DATE(marcaTareo_entrada) = '$fecha'")
             ->where('mt.marcaTareo_idempleado', '=', $idEmpleado)
             ->get();

         $entradas = agruparMarcacionesEHorario($entradas);

         return response()->json($entradas, 200);
     }

      // * CAMBIAR SALIDA
    public function cambiarSalidaMarcacion(Request $request)
    {
        $idEntradaCambiar = $request->get('idCambiar');
        $idMarcacion = $request->get('idMarcacion');
        $tipo = $request->get('tipo');
        if ($idEntradaCambiar != $idMarcacion) {
            $marcacionCambiar = marcacion_tareo::findOrFail($idEntradaCambiar);     // ? MARCACION A CAMBIAR
            $marcacion = marcacion_tareo::findOrFail($idMarcacion);                 // ? MARCACION A RECIBIR ENTRADA
            // **************************************** VALIDACION DE NUEVO RANGOS **************************************
            $nuevaEntrada = $marcacion->marcaTareo_entrada;
            $nuevaSalida = $tipo == 2 ? $marcacionCambiar->marcaTareo_salida : $marcacionCambiar->marcaTareo_entrada;
            if ($tipo == 1) {
                $fecha = Carbon::parse($marcacionCambiar->marcaTareo_entrada)->isoFormat('YYYY-MM-DD');
            } else {
                $fecha = Carbon::parse($marcacionCambiar->marcaTareo_salida)->isoFormat('YYYY-MM-DD');
            }
            if (Carbon::parse($nuevaSalida)->gt(Carbon::parse($nuevaEntrada))) {
                // DB::enableQueryLog();
                $marcacionesValidar = DB::table('marcacion_tareo as m')
                    ->select(
                        'm.idmarcaciones_tareo',
                        DB::raw('IF(m.marcaTareo_entrada is null,0,m.marcaTareo_entrada) AS entrada'),
                        DB::raw('IF(m.marcaTareo_salida is null,0,m.marcaTareo_salida) AS salida')
                    )
                    ->where('m.marcaTareo_idempleado', '=', $marcacion->marcaTareo_idempleado)
                    ->where(DB::raw('IF(m.marcaTareo_entrada is null,DATE(m.marcaTareo_salida),DATE(m.marcaTareo_entrada))'), "=", $fecha)
                    ->whereNotIn('m.idmarcaciones_tareo', [$marcacion->idmarcaciones_tareo])
                    ->get();
                // dd(DB::getQueryLog());
                $respuesta = true;
                foreach ($marcacionesValidar as $mv) {
                    if ($mv->entrada != 0) {
                        $respuestaCheck = checkHora($nuevaEntrada, $nuevaSalida, $mv->entrada);
                        if ($respuestaCheck) {
                            $respuesta = false;
                        }
                    } else {
                        if ($mv->salida != 0) {
                            $respuestaCheck = checkHora($nuevaEntrada, $nuevaSalida, $mv->salida);
                            if ($respuestaCheck) {
                                $respuesta = false;
                            }
                        }
                    }
                }
                if ($respuesta) {
                    if (!empty($marcacion->horarioEmp_id)) {
                        $entrada = Carbon::parse($nuevaEntrada);
                        $salida = Carbon::parse($nuevaSalida);
                        $horario = DB::table('horario_empleado as he')
                            ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                            ->select(
                                'h.horario_descripcion as descripcion',
                                'h.horaI',
                                'h.horaF',
                                'h.horario_tolerancia as toleranciaI',
                                'h.horario_toleranciaF as toleranciaF',
                                'he.fuera_horario as fueraH',
                                DB::raw('IF(he.horaAdic is null, 0 ,he.horaAdic) as horasA'),
                                'h.horasObliga as horasO'
                            )
                            ->where('he.horarioEmp_id', '=', $marcacion->horarioEmp_id)
                            ->get()
                            ->first();
                        if ($horario->horaF > $horario->horaI) {
                            $horarioFin = Carbon::parse($entrada->copy()->isoFormat('YYYY-MM-DD') . " " . $horario->horaF);
                        } else {
                            $nuevaFecha = $entrada->copy()->addDays(1)->isoFormat('YYYY-MM-DD');  // : OBTENEMOS LA FECHA DEL DIA SIGUIENTE
                            $horarioFin = Carbon::parse($nuevaFecha . " " . $horario->horaF);
                        }
                        $horarioFinT = $horarioFin->copy()->addMinutes($horario->toleranciaF);
                        // : SUMAR TIEMPOS DE MARCACIONES
                        $sumaTotalDeHoras = DB::table('marcacion_tareo as m')
                            ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaTareo_salida,m.marcaTareo_entrada)))) as totalT'))
                            ->where('m.marcaTareo_idempleado', '=', $marcacion->marcaTareo_idempleado)
                            ->whereNotNull('m.marcaTareo_entrada')
                            ->whereNotNull('m.marcaTareo_salida')
                            ->where(DB::raw('DATE(marcaTareo_entrada)'), '=', $entrada->copy()->isoFormat('YYYY-MM-DD'))
                            ->where('m.horarioEmp_id', '=', $marcacion->horarioEmp_id)
                            ->where('m.idmarcaciones_tareo', '!=', $marcacionCambiar->idmarcaciones_tareo)
                            ->get();
                        // : CALCULAR TIEMPO
                        $sumaTotalDeHoras[0]->totalT = $sumaTotalDeHoras[0]->totalT == null ? "00:00:00" : $sumaTotalDeHoras[0]->totalT;
                        // : TIEMPO DE ENTRADA Y SALIDA
                        $horaIParse = Carbon::parse($entrada);
                        $horaFParse = Carbon::parse($salida);
                        // : CALCULAMOS EL TIEMPO ENTRE SALIDA Y ENTRADA
                        $totalDuration = $horaFParse->diffInSeconds($horaIParse);
                        // : TIEMPO TOTAL DE MARCACIONES AGREGAMOS EL TIEMPO ENTRE SALIDA Y ENTRADA
                        $tiempoTotal = Carbon::parse($sumaTotalDeHoras[0]->totalT)->addSeconds($totalDuration);
                        // : TIEMPO DE HORAS OBLIGADAS DE HORARIO MAS LAS HORAS ADICIONALES
                        $tiempoTotalDeHorario = Carbon::parse($horario->horasO)->addMinutes($horario->horasA * 60);
                        if ($tiempoTotal->lte($tiempoTotalDeHorario)) {
                            if ($horario->fueraH == 0) {
                                if ($salida->lte($horarioFinT)) {
                                    // ! MARCACION A REGISTRAR SALIDA
                                    $marcacion->marcaTareo_salida = $nuevaSalida;
                                    if ($tipo == 2) {
                                        $marcacion->idcontroladores_salida = $marcacionCambiar->idcontroladores_salida;
                                        $marcacion->iddispositivos_salida = $marcacionCambiar->iddispositivos_salida;
                                    } else {
                                        $marcacion->idcontroladores_salida  = $marcacionCambiar->idcontroladores_entrada;
                                        $marcacion->iddispositivos_salida = $marcacionCambiar->iddispositivos_entrada;
                                    }
                                    $marcacion->save();
                                    // ! MARCACION A CAMBIAR
                                    if ($tipo == 2) {
                                        $marcacionCambiar->marcaTareo_salida = NULL;
                                        $marcacionCambiar->idcontroladores_salida = NULL;
                                        $marcacionCambiar->iddispositivos_salida = NULL;
                                    } else {
                                        $marcacionCambiar->marcaTareo_entrada = NULL;
                                        $marcacionCambiar->idcontroladores_entrada  = NULL;
                                        $marcacionCambiar->iddispositivos_entrada = NULL;
                                    }
                                    $marcacionCambiar->save();
                                } else {
                                    return response()->json(
                                        array("respuesta" => "Marcación fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . $horario->horaI . " - " . $horario->horaF . " )"),
                                        200
                                    );
                                }
                            } else {
                                // ! MARCACION A REGISTRAR SALIDA
                                $marcacion->marcaTareo_salida = $nuevaSalida;
                                if ($tipo == 2) {
                                    $marcacion->idcontroladores_salida = $marcacionCambiar->idcontroladores_salida;
                                    $marcacion->iddispositivos_salida = $marcacionCambiar->iddispositivos_salida;
                                } else {
                                    $marcacion->idcontroladores_salida  = $marcacionCambiar->idcontroladores_entrada;
                                    $marcacion->iddispositivos_salida = $marcacionCambiar->iddispositivos_entrada;
                                }
                                $marcacion->save();
                                // ! MARCACION A CAMBIAR
                                if ($tipo == 2) {
                                    $marcacionCambiar->marcaTareo_salida = NULL;
                                    $marcacionCambiar->idcontroladores_salida = NULL;
                                    $marcacionCambiar->iddispositivos_salida = NULL;
                                } else {
                                    $marcacionCambiar->marcaTareo_entrada = NULL;
                                    $marcacionCambiar->idcontroladores_entrada  = NULL;
                                    $marcacionCambiar->iddispositivos_entrada = NULL;
                                }
                                $marcacionCambiar->save();
                            }
                        } else {
                            return response()->json(
                                array("respuesta" => "Sobretiempo en la marcación."),
                                200
                            );
                        }
                    } else {
                        // ! MARCACION A REGISTRAR SALIDA
                        $marcacion->marcaTareo_salida = $nuevaSalida;
                        if ($tipo == 2) {
                            $marcacion->idcontroladores_salida = $marcacionCambiar->idcontroladores_salida;
                            $marcacion->iddispositivos_salida = $marcacionCambiar->iddispositivos_salida;
                        } else {
                            $marcacion->idcontroladores_salida  = $marcacionCambiar->idcontroladores_entrada;
                            $marcacion->iddispositivos_salida = $marcacionCambiar->iddispositivos_entrada;
                        }
                        $marcacion->save();
                        // ! MARCACION A CAMBIAR
                        if ($tipo == 2) {
                            $marcacionCambiar->marcaTareo_salida = NULL;
                            $marcacionCambiar->idcontroladores_salida = NULL;
                            $marcacionCambiar->iddispositivos_salida = NULL;
                        } else {
                            $marcacionCambiar->marcaTareo_entrada = NULL;
                            $marcacionCambiar->idcontroladores_entrada  = NULL;
                            $marcacionCambiar->iddispositivos_entrada = NULL;
                        }
                        $marcacionCambiar->save();
                    }
                    // ! BUSCAR SI LA MARCACION A CAMBIAR TIENE LOS CAMPOS VACIOS DE ENTRADA Y SALIDA
                    if (is_null($marcacionCambiar->marcaTareo_entrada) && is_null($marcacionCambiar->marcaTareo_salida)) {
                        $marcacionCambiar->delete();  // ? ELIMINAR MARCACION
                    }
                    return response()->json($marcacion->idmarcaciones_tareo, 200);
                } else {
                    return response()->json(array("respuesta" => "Posibilidad de cruze de marcación."), 200);
                }
            } else {
                return response()->json(array("respuesta" => "Salida debe ser mayor entrada."), 200);
            }
            // *************************************** FINALIZACION ******************************************************
        } else {
            $marcacion = marcacion_tareo::findOrFail($idEntradaCambiar);
            $marcacion->marcaTareo_salida = $marcacion->marcaTareo_entrada;
            $marcacion->idcontroladores_salida = $marcacion->idcontroladores_entrada;
            $marcacion->idcontroladores_entrada = NULL;
            $marcacion->iddispositivos_salida = $marcacion->iddispositivos_entrada;
            $marcacion->iddispositivos_entrada = NULL;
            $marcacion->marcaTareo_entrada = NULL;
            $marcacion->save();
            return response()->json($marcacion->idmarcaciones_tareo, 200);
        }
    }

    // * LISTA DE SALIDAS CON ENTRADA NULL
    function listaDeSalidasSinE(Request $request)
    {
        $fecha = $request->get('fecha');
        $idEmpleado = $request->get('idEmpleado');

        $salidas = DB::table('marcacion_tareo as mt')
            ->leftJoin('horario_empleado as he', 'mt.horarioEmp_id', '=', 'he.horarioEmp_id')
            ->leftJoin('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->select(
                'mt.idmarcaciones_tareo as id',
                'mt.marcaTareo_salida as salida',
                DB::raw('IF(mt.horarioEmp_id is null, 0 , mt.horarioEmp_id ) as idH'),
                DB::raw('IF(h.horario_descripcion is null , "Sin horario",h.horario_descripcion) as horario')
            )
            ->whereNotNull('mt.marcaTareo_salida')
            ->whereNull('mt.marcaTareo_entrada')
            ->whereRaw("DATE(marcaTareo_salida) = '$fecha'")
            ->where('mt.marcaTareo_idempleado', '=', $idEmpleado)
            ->get();

        $salidas = agruparMarcacionesHorario($salidas);

        return response()->json($salidas, 200);
    }

     // * CAMBIAR ENTRDA
     public function cambiarEntraMarcacion(Request $request)
     {
         $idEntradaCambiar = $request->get('idCambiar');
         $idMarcacion = $request->get('idMarcacion');
         $tipo = $request->get('tipo');
         if ($idEntradaCambiar != $idMarcacion) {
             $marcacionCambiar = marcacion_tareo::findOrFail($idEntradaCambiar);     // ? MARCACION A CAMBIAR
             $marcacion = marcacion_tareo::findOrFail($idMarcacion);                 // ? MARCACION A RECIBIR ENTRADA
             // **************************************** VALIDACION DE NUEVO RANGOS **************************************
             $nuevaEntrada = $tipo  == 2 ? $marcacionCambiar->marcaTareo_salida : $marcacionCambiar->marcaTareo_entrada;
             $nuevaSalida = $marcacion->marcaTareo_salida;
             if ($tipo == 1) {
                 $fecha = Carbon::parse($marcacionCambiar->marcaTareo_entrada)->isoFormat('YYYY-MM-DD');
             } else {
                 $fecha = Carbon::parse($marcacionCambiar->marcaTareo_salida)->isoFormat('YYYY-MM-DD');
             }
             if (Carbon::parse($nuevaSalida)->gt(Carbon::parse($nuevaEntrada))) {
                 // DB::enableQueryLog();
                 $marcacionesValidar = DB::table('marcacion_tareo as m')
                     ->select(
                         'm.idmarcaciones_tareo',
                         DB::raw('IF(m.marcaTareo_entrada is null,0,m.marcaTareo_entrada) AS entrada'),
                         DB::raw('IF(m.marcaTareo_salida is null,0,m.marcaTareo_salida) AS salida')
                     )
                     ->where('m.marcaTareo_idempleado', '=', $marcacion->marcaTareo_idempleado)
                     ->where(DB::raw('IF(m.marcaTareo_entrada is null,DATE(m.marcaTareo_salida),DATE(m.marcaTareo_entrada))'), "=", $fecha)
                     ->whereNotIn('m.idmarcaciones_tareo', [$marcacion->idmarcaciones_tareo])
                     ->get();
                 // dd(DB::getQueryLog());
                 $respuesta = true;
                 foreach ($marcacionesValidar as $mv) {
                     if (!($mv->idmarcaciones_tareo == $marcacionCambiar->idmarcaciones_tareo)) {
                         if ($mv->entrada != 0) {
                             $respuestaCheck = checkHora($nuevaEntrada, $nuevaSalida, $mv->entrada);
                             if ($respuestaCheck) {
                                 $respuesta = false;
                             }
                         } else {
                             if ($mv->salida != 0) {
                                 $respuestaCheck = checkHora($nuevaEntrada, $nuevaSalida, $mv->salida);
                                 if ($respuestaCheck) {
                                     $respuesta = false;
                                 }
                             }
                         }
                     } else {
                         if ($tipo  == 2 ?  !empty($marcacionCambiar->marcaTareo_entrada) : !empty($marcacionCambiar->marcaTareo_salida)) {
                             $respuestaCheck = checkHora($nuevaEntrada, $nuevaSalida, $tipo  == 2 ?  $mv->entrada : $mv->salida);
                             if ($respuestaCheck) {
                                 $respuesta = false;
                             }
                         }
                     }
                 }
                 if ($respuesta) {
                     if (!empty($marcacion->horarioEmp_id)) {
                         $entrada = Carbon::parse($nuevaEntrada);
                         $salida = Carbon::parse($nuevaSalida);
                         $horario = DB::table('horario_empleado as he')
                             ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                             ->select(
                                 'h.horario_descripcion as descripcion',
                                 'h.horaI',
                                 'h.horaF',
                                 'h.horario_tolerancia as toleranciaI',
                                 'h.horario_toleranciaF as toleranciaF',
                                 'he.fuera_horario as fueraH',
                                 DB::raw('IF(he.horaAdic is null, 0 ,he.horaAdic) as horasA'),
                                 'h.horasObliga as horasO'
                             )
                             ->where('he.horarioEmp_id', '=', $marcacion->horarioEmp_id)
                             ->get()
                             ->first();
                         $horarioInicio = Carbon::parse($entrada->copy()->isoFormat('YYYY-MM-DD') . " " . $horario->horaI);
                         $horarioIniT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
                         // : SUMAR TIEMPOS DE MARCACIONES
                         $sumaTotalDeHoras = DB::table('marcacion_tareo as m')
                             ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaTareo_salida,m.marcaTareo_entrada)))) as totalT'))
                             ->where('m.marcaTareo_idempleado', '=', $marcacion->marcaTareo_idempleado)
                             ->whereNotNull('m.marcaTareo_entrada')
                             ->whereNotNull('m.marcaTareo_salida')
                             ->where(DB::raw('DATE(marcaTareo_entrada)'), '=', $entrada->copy()->isoFormat('YYYY-MM-DD'))
                             ->where('m.horarioEmp_id', '=', $marcacion->horarioEmp_id)
                             ->where('m.idmarcaciones_tareo', '!=', $marcacionCambiar->idmarcaciones_tareo)
                             ->get();
                         // : CALCULAR TIEMPO
                         $sumaTotalDeHoras[0]->totalT = $sumaTotalDeHoras[0]->totalT == null ? "00:00:00" : $sumaTotalDeHoras[0]->totalT;
                         // : TIEMPO DE ENTRADA Y SALIDA
                         $horaIParse = Carbon::parse($entrada);
                         $horaFParse = Carbon::parse($salida);
                         // : CALCULAMOS EL TIEMPO ENTRE SALIDA Y ENTRADA
                         $totalDuration = $horaFParse->diffInSeconds($horaIParse);
                         // : TIEMPO TOTAL DE MARCACIONES AGREGAMOS EL TIEMPO ENTRE SALIDA Y ENTRADA
                         $tiempoTotal = Carbon::parse($sumaTotalDeHoras[0]->totalT)->addSeconds($totalDuration);
                         // : TIEMPO DE HORAS OBLIGADAS DE HORARIO MAS LAS HORAS ADICIONALES
                         $tiempoTotalDeHorario = Carbon::parse($horario->horasO)->addMinutes($horario->horasA * 60);
                         if ($tiempoTotal->lte($tiempoTotalDeHorario)) {
                             if ($horario->fueraH == 0) {
                                 if ($horarioIniT->lte($entrada)) {
                                     // ! MARCACION A REGISTRAR ENTRADA
                                     $marcacion->marcaTareo_entrada = $nuevaEntrada;
                                     if ($tipo == 2) {
                                         $marcacion->idcontroladores_entrada = $marcacionCambiar->idcontroladores_salida;
                                         $marcacion->iddispositivos_entrada = $marcacionCambiar->iddispositivos_salida;
                                     } else {
                                         $marcacion->idcontroladores_entrada  = $marcacionCambiar->idcontroladores_entrada;
                                         $marcacion->iddispositivos_entrada = $marcacionCambiar->iddispositivos_entrada;
                                     }
                                     $marcacion->save();
                                     // ! MARCACION A CAMBIAR
                                     if ($tipo == 2) {
                                         $marcacionCambiar->marcaTareo_salida = NULL;
                                         $marcacionCambiar->idcontroladores_salida = NULL;
                                         $marcacionCambiar->iddispositivos_salida = NULL;
                                     } else {
                                         $marcacionCambiar->marcaTareo_entrada = NULL;
                                         $marcacionCambiar->idcontroladores_entrada  = NULL;
                                         $marcacionCambiar->iddispositivos_entrada = NULL;
                                     }
                                     $marcacionCambiar->save();
                                 } else {
                                     return response()->json(
                                         array("respuesta" => "Marcación fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . $horario->horaI . " - " . $horario->horaF . " )"),
                                         200
                                     );
                                 }
                             } else {
                                 // ! MARCACION A REGISTRAR ENTRADA
                                 $marcacion->marcaTareo_entrada = $nuevaEntrada;
                                 if ($tipo == 2) {
                                     $marcacion->idcontroladores_entrada = $marcacionCambiar->idcontroladores_salida;
                                     $marcacion->iddispositivos_entrada = $marcacionCambiar->iddispositivos_salida;
                                 } else {
                                     $marcacion->idcontroladores_entrada  = $marcacionCambiar->idcontroladores_entrada;
                                     $marcacion->iddispositivos_entrada = $marcacionCambiar->iddispositivos_entrada;
                                 }
                                 $marcacion->save();
                                 // ! MARCACION A CAMBIAR
                                 if ($tipo == 2) {
                                     $marcacionCambiar->marcaTareo_salida = NULL;
                                     $marcacionCambiar->idcontroladores_salida = NULL;
                                     $marcacionCambiar->iddispositivos_salida = NULL;
                                 } else {
                                     $marcacionCambiar->marcaTareo_entrada = NULL;
                                     $marcacionCambiar->idcontroladores_entrada  = NULL;
                                     $marcacionCambiar->iddispositivos_entrada = NULL;
                                 }
                                 $marcacionCambiar->save();
                             }
                         } else {
                             return response()->json(
                                 array("respuesta" => "Sobretiempo en la marcación."),
                                 200
                             );
                         }
                     } else {
                         // ! MARCACION A REGISTRAR ENTRADA
                         $marcacion->marcaTareo_entrada = $nuevaEntrada;
                         if ($tipo == 2) {
                             $marcacion->idcontroladores_entrada = $marcacionCambiar->idcontroladores_salida;
                             $marcacion->iddispositivos_entrada = $marcacionCambiar->iddispositivos_salida;
                         } else {
                             $marcacion->idcontroladores_entrada  = $marcacionCambiar->idcontroladores_entrada;
                             $marcacion->iddispositivos_entrada = $marcacionCambiar->iddispositivos_entrada;
                         }
                         $marcacion->save();
                         // ! MARCACION A CAMBIAR
                         if ($tipo == 2) {
                             $marcacionCambiar->marcaTareo_salida = NULL;
                             $marcacionCambiar->idcontroladores_salida = NULL;
                             $marcacionCambiar->iddispositivos_salida = NULL;
                         } else {
                             $marcacionCambiar->marcaTareo_entrada = NULL;
                             $marcacionCambiar->idcontroladores_entrada  = NULL;
                             $marcacionCambiar->iddispositivos_entrada = NULL;
                         }
                         $marcacionCambiar->save();
                     }
                     // ! BUSCAR SI LA MARCACION A CAMBIAR TIENE LOS CAMPOS VACIOS DE ENTRADA Y SALIDA
                     if (is_null($marcacionCambiar->marcaTareo_entrada) && is_null($marcacionCambiar->marcaTareo_salida)) {
                         $marcacionCambiar->delete();  // ? ELIMINAR MARCACION
                     }
                     return response()->json($marcacion->idmarcaciones_tareo, 200);
                 } else {
                     return response()->json(array("respuesta" => "Posibilidad de cruze de marcación."), 200);
                 }
             } else {
                 return response()->json(array("respuesta" => "Entrada debe ser menor a salida."), 200);
             }
             // *************************************** FINALIZACION ******************************************************
         } else {
             $marcacion = marcacion_tareo::findOrFail($idEntradaCambiar);
             $marcacion->marcaTareo_entrada = $marcacion->marcaTareo_salida;
             $marcacion->idcontroladores_entrada = $marcacion->idcontroladores_salida;
             $marcacion->iddispositivos_entrada = $marcacion->iddispositivos_salida;
             $marcacion->marcaTareo_salida = NULL;
             $marcacion->idcontroladores_salida= NULL;
             $marcacion->iddispositivos_salida= NULL;
             $marcacion->save();

             return response()->json($marcacion->idmarcaciones_tareo, 200);
         }
     }

     //*CAMBIAR SELECT
     public function selectFiltro(Request $request)
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

}
