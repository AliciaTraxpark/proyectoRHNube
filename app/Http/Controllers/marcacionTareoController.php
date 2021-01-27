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
                        ->leftJoin('controladores_tareo as cont', 'mt.idcontroladores_tareo', '=', 'cont.idcontroladores_tareo')
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
                            'cont.contrT_nombres',
                            'cont.contrT_ApPaterno',
                            'cont.contrT_ApMaterno',
                            'pc.descripcion as puntoControl'

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
                        ->leftJoin('controladores_tareo as cont', 'mt.idcontroladores_tareo', '=', 'cont.idcontroladores_tareo')
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
                            'cont.contrT_nombres',
                            'cont.contrT_ApPaterno',
                            'cont.contrT_ApMaterno',
                            'pc.descripcion as puntoControl'

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
                            ->leftJoin('controladores_tareo as cont', 'mt.idcontroladores_tareo', '=', 'cont.idcontroladores_tareo')
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
                                'cont.contrT_nombres',
                                'cont.contrT_ApPaterno',
                                'cont.contrT_ApMaterno',
                                'pc.descripcion as puntoControl'

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
                            ->leftJoin('controladores_tareo as cont', 'mt.idcontroladores_tareo', '=', 'cont.idcontroladores_tareo')
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
                                'cont.contrT_nombres',
                                'cont.contrT_ApPaterno',
                                'cont.contrT_ApMaterno',
                                'pc.descripcion as puntoControl'

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
                            ->leftJoin('controladores_tareo as cont', 'mt.idcontroladores_tareo', '=', 'cont.idcontroladores_tareo')
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
                                'cont.contrT_nombres',
                                'cont.contrT_ApPaterno',
                                'cont.contrT_ApMaterno',
                                'pc.descripcion as puntoControl'

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
                            ->leftJoin('controladores_tareo as cont', 'mt.idcontroladores_tareo', '=', 'cont.idcontroladores_tareo')
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
                                'cont.contrT_nombres',
                                'cont.contrT_ApPaterno',
                                'cont.contrT_ApMaterno',
                                'pc.descripcion as puntoControl'

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
                    ->leftJoin('controladores_tareo as cont', 'mt.idcontroladores_tareo', '=', 'cont.idcontroladores_tareo')
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
                        'cont.contrT_nombres',
                        'cont.contrT_ApPaterno',
                        'cont.contrT_ApMaterno',
                        'pc.descripcion as puntoControl'

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
                    ->leftJoin('controladores_tareo as cont', 'mt.idcontroladores_tareo', '=', 'cont.idcontroladores_tareo')
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
                        'cont.contrT_nombres',
                        'cont.contrT_ApPaterno',
                        'cont.contrT_ApMaterno',
                        'pc.descripcion as puntoControl'

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
}
