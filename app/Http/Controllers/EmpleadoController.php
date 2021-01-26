<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use App\actividad;
use App\actividad_area;
use App\actividad_empleado;
use Illuminate\Support\Facades\Hash;
use App\empleado;
use App\area;
use App\cargo;
use App\centro_costo;
use App\condicion_pago;
use App\contrato;
use App\doc_empleado;
use Illuminate\Http\Request;
use App\ubigeo_peru_departments;
use App\ubigeo_peru_provinces;
use App\ubigeo_peru_districts;
use App\tipo_documento;
use App\tipo_contrato;
use App\nivel;
use App\local;
use App\persona;
use App\tipo_dispositivo;
use App\horario_empleado;
use App\incidencias;
use App\licencia_empleado;
use App\eventos_usuario;
use App\eventos_empleado_temp;
use App\horario;
use App\eventos_empleado;
use App\historial_empleado;
use App\incidencia_dias;
use App\horario_dias;
use App\pausas_horario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\invitado_empleado;
use App\historial_horarioempleado;
use App\Notifications\NuevaNotification;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except('provincias', 'distritos', 'fechas');
    }
    public function fechas($id)
    {
        return tipo_contrato::where('contrato_id', $id)->get();
    }
    public function provincias($id)
    {
        return ubigeo_peru_provinces::where('departamento_id', $id)->get();
    }
    public function distritos($id)
    {
        return ubigeo_peru_districts::where('province_id', $id)->get();
    }
    public function index()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
            $departamento = ubigeo_peru_departments::all();
            $provincia = ubigeo_peru_provinces::all();
            $distrito = ubigeo_peru_districts::all();
            $tipo_doc = tipo_documento::all();
            $tipo_cont = tipo_contrato::where('organi_id', '=', session('sesionidorg'))->get();
            $area = area::where('organi_id', '=', session('sesionidorg'))->get();
            $cargo = cargo::where('organi_id', '=', session('sesionidorg'))->get();
            $centro_costo = centro_costo::where('organi_id', '=', session('sesionidorg'))->where('estado', '=', 1)->get();
            $nivel = nivel::where('organi_id', '=', session('sesionidorg'))->get();
            $local = local::where('organi_id', '=', session('sesionidorg'))->get();
            $empleado = empleado::where('emple_estado', '=', 1)->where('organi_id', '=', session('sesionidorg'))->get();
            $dispositivo = tipo_dispositivo::all();
            $tabla_empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                ->join('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                ->select(
                    'p.perso_nombre',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    'c.cargo_descripcion',
                    'a.area_descripcion',
                    'cc.centroC_descripcion',
                    'e.emple_id'
                )
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();
            $calendario = DB::table('calendario as ca')
                ->where('ca.organi_id', '=', session('sesionidorg'))
                ->get();
            $horario = horario::where('organi_id', '=', session('sesionidorg'))->get();
            $condicionPago = condicion_pago::where('organi_id', '=', session('sesionidorg'))->get();

            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    return redirect('/dashboard');
                } else {
                    return view('empleado.empleado', [
                        'departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito,
                        'tipo_doc' => $tipo_doc, 'tipo_cont' => $tipo_cont, 'area' => $area, 'cargo' => $cargo, 'centro_costo' => $centro_costo,
                        'nivel' => $nivel, 'local' => $local, 'empleado' => $empleado, 'tabla_empleado' => $tabla_empleado, 'dispositivo' => $dispositivo,
                        'calendario' => $calendario, 'horario' => $horario, 'condicionP' => $condicionPago
                    ]);
                }
            } else {
                return view('empleado.empleado', [
                    'departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito,
                    'tipo_doc' => $tipo_doc, 'tipo_cont' => $tipo_cont, 'area' => $area, 'cargo' => $cargo, 'centro_costo' => $centro_costo,
                    'nivel' => $nivel, 'local' => $local, 'empleado' => $empleado, 'tabla_empleado' => $tabla_empleado, 'dispositivo' => $dispositivo,
                    'calendario' => $calendario, 'horario' => $horario, 'condicionP' => $condicionPago
                ]);
            }
        }
    }
    public function cargarDatos()
    {   //DATOS DE TABLA PARA CARGAR EXCEL
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
            $empleado = DB::table('empleado as e')
                ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->leftJoin('tipo_documento as tipoD', 'e.emple_tipoDoc', '=', 'tipoD.tipoDoc_id')
                ->leftJoin('ubigeo_peru_departments as depar', 'e.emple_departamento', '=', 'depar.id')
                ->leftJoin('ubigeo_peru_provinces as provi', 'e.emple_provincia', '=', 'provi.id')
                ->leftJoin('ubigeo_peru_districts as dist', 'e.emple_distrito', '=', 'dist.id')
                ->where('e.emple_estado', '=', 1)
                ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                ->leftJoin('ubigeo_peru_departments as para', 'e.emple_departamentoN', '=', 'para.id')
                ->leftJoin('ubigeo_peru_provinces as proviN', 'e.emple_provinciaN', '=', 'proviN.id')
                ->leftJoin('ubigeo_peru_districts as distN', 'e.emple_distritoN', '=', 'distN.id')
                ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')


                ->select(
                    'e.emple_id',
                    'p.perso_id',
                    'p.perso_nombre',
                    'tipoD.tipoDoc_descripcion',
                    'e.emple_nDoc',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    'p.perso_fechaNacimiento',
                    'p.perso_direccion',
                    'p.perso_sexo',
                    'depar.id as depar',
                    'depar.name as deparNom',
                    'provi.id as proviId',
                    'provi.name as provi',
                    'dist.id as distId',
                    'dist.name as distNo',
                    'c.cargo_descripcion',
                    'a.area_descripcion',
                    'cc.centroC_descripcion',
                    'para.id as iddepaN',
                    'para.name as depaN',
                    'proviN.id as idproviN',
                    'proviN.name as proviN',
                    'distN.id as iddistN',
                    'distN.name as distN',
                    'e.emple_id',
                    'c.cargo_id',
                    'a.area_id',
                    'cc.centroC_id',

                    'e.emple_local',
                    'e.emple_nivel',
                    'e.emple_departamento',
                    'e.emple_provincia',
                    'e.emple_distrito',
                    'e.emple_foto as foto',
                    'e.emple_celular',
                    'e.emple_telefono',


                    'e.emple_Correo'
                )
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->get();

            $usuario = DB::table('users')
                ->where('id', '=', Auth::user()->id)->get();

            return view('empleado.cargarEmpleado', ['empleado' => $empleado, 'usuario' => $usuario[0]->user_estado]);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tabla()
    {
        function agruparEmpleados($array)
        {
            $resultado = array();

            foreach ($array as $empleado) {
                if (!isset($resultado[$empleado->emple_id])) {
                    $resultado[$empleado->emple_id] = $empleado;
                }
                if (!isset($resultado[$empleado->emple_id]->dispositivos)) {
                    $resultado[$empleado->emple_id]->dispositivos = array();
                }
                array_push($resultado[$empleado->emple_id]->dispositivos, $empleado->dispositivo);
            }
            return $resultado;
        }
        $invitadod = DB::table('invitado')
            ->where('user_Invitado', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('rol_id', '=', 3)
            ->get()->first();
        if ($invitadod) {
            if ($invitadod->verTodosEmps == 1) {
                DB::enableQueryLog();
                $tabla_empleado1 = DB::table('empleado as e')
                    ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                    ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                    ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                    ->leftJoin('vinculacion_ruta as vr', 'vr.idEmpleado', '=', 'e.emple_id')
                    ->leftJoin('modo as md', function ($join) {
                        $join->on('md.id', '=', 'v.idModo');
                        $join->orOn('md.id', '=', 'vr.idModo');
                    })
                    ->leftJoin('modo as md', 'md.id', '=', 'v.idModo')

                    ->select(
                        'e.emple_nDoc',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion',
                        'a.area_descripcion',
                        'cc.centroC_descripcion',
                        'e.emple_id',
                        'md.idTipoModo as dispositivo',
                        'e.emple_foto',
                        'e.asistencia_puerta',
                        'e.modoTareo'
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->get();
                /*  dd(DB::getQueryLog()); */
            } else {
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();
                if ($invitado_empleadoIn != null) {
                    $tabla_empleado1 = DB::table('empleado as e')
                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                        ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                        ->leftJoin('vinculacion_ruta as vr', 'vr.idEmpleado', '=', 'e.emple_id')
                        ->leftJoin('modo as md', function ($join) {
                            $join->on('md.id', '=', 'v.idModo');
                            $join->orOn('md.id', '=', 'vr.idModo');
                        })
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->select(
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'a.area_descripcion',
                            'cc.centroC_descripcion',
                            'e.emple_id',
                            'md.idTipoModo as dispositivo',
                            'e.emple_foto',
                            'e.asistencia_puerta',
                            'e.modoTareo'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->get();
                } else {
                    $tabla_empleado1 = DB::table('empleado as e')
                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                        ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                        ->leftJoin('vinculacion_ruta as vr', 'vr.idEmpleado', '=', 'e.emple_id')
                        ->leftJoin('modo as md', function ($join) {
                            $join->on('md.id', '=', 'v.idModo');
                            $join->orOn('md.id', '=', 'vr.idModo');
                        })
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->select(
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'a.area_descripcion',
                            'cc.centroC_descripcion',
                            'e.emple_id',
                            'md.idTipoModo as dispositivo',
                            'e.emple_foto',
                            'e.asistencia_puerta',
                            'e.modoTareo'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->get();
                }
            }
        } else {
            $tabla_empleado1 = DB::table('empleado as e')
                ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                ->leftJoin('vinculacion_ruta as vr', 'vr.idEmpleado', '=', 'e.emple_id')
                ->leftJoin('modo as md', function ($join) {
                    $join->on('md.id', '=', 'v.idModo');
                    $join->orOn('md.id', '=', 'vr.idModo');
                })
                ->select(
                    'e.emple_nDoc',
                    'p.perso_nombre',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    'c.cargo_descripcion',
                    'a.area_descripcion',
                    'cc.centroC_descripcion',
                    'e.emple_id',
                    'md.idTipoModo as dispositivo',
                    'e.emple_foto',
                    'e.asistencia_puerta',
                    'e.modoTareo'
                )
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();
        }

        $vinculacionD = [];
        $vinculacionRD = [];
        //* FOREACH PARA OBTENER DISPOSITIVOS
        foreach ($tabla_empleado1 as $tab) {
            //* VINCULACION DE CONTROL REMOTO
            $vinculacion = DB::table('vinculacion as v')
                ->join('modo as m', 'm.id', '=', 'v.idModo')
                ->join('tipo_dispositivo as td', 'td.id', 'm.idTipoDispositivo')
                ->join('licencia_empleado as le', 'le.id', '=', 'v.idLicencia')
                ->select('v.id as idV', 'v.pc_mac as pc', 'v.envio as envio', 'v.hash as codigo', 'le.idEmpleado', 'le.licencia', 'le.id as idL', 'le.disponible', 'td.dispositivo_descripcion')
                ->where('v.idEmpleado', '=', $tab->emple_id)
                ->get();
            foreach ($vinculacion as $vinc) {
                array_push($vinculacionD, array("idVinculacion" => $vinc->idV, "pc" => $vinc->pc, "idLicencia" => $vinc->idL, "licencia" => $vinc->licencia, "disponible" => $vinc->disponible, "dispositivoD" => $vinc->dispositivo_descripcion, "codigo" => $vinc->codigo, "envio" => $vinc->envio));
            }
            $tab->vinculacion = $vinculacionD;
            unset($vinculacionD);
            $vinculacionD = array();
            $modoCR = DB::table('vinculacion as v')
                ->join('modo as m', 'm.id', '=', 'v.idModo')
                ->join('tipo_dispositivo as td', 'td.id', 'm.idTipoDispositivo')
                ->join('licencia_empleado as le', 'le.id', '=', 'v.idLicencia')
                ->select('v.id as idV', 'v.envio as envio', 'v.hash as codigo', 'le.idEmpleado', 'le.licencia', 'le.id as idL', 'le.disponible', 'td.dispositivo_descripcion')
                ->where('v.idEmpleado', '=', $tab->emple_id)
                ->where('m.idTipoModo', '=', 1)
                ->get();
            $estadoCR = false;
            foreach ($modoCR as $md) {
                if ($md->disponible == 'c' || $md->disponible == 'e' || $md->disponible == 'a') {
                    $estadoCR = true;
                }
            }
            $tab->estadoCR = $estadoCR;
            //* *****************FINALIZACION DE CONTROL REMOTO****************
            //* VINCULACION DE CONTROL RUTA
            $vinculacion_ruta = DB::table('vinculacion_ruta as vr')
                ->join('modo as m', 'm.id', '=', 'vr.idModo')
                ->join('tipo_dispositivo as td', 'td.id', '=', 'm.idTipoDispositivo')
                ->select('vr.id as idV', 'vr.envio as envio', 'vr.hash as codigo', 'vr.idEmpleado', 'vr.disponible', 'td.dispositivo_descripcion', 'vr.modelo')
                ->where('vr.idEmpleado', '=', $tab->emple_id)
                ->where('m.idTipoModo', '=', 2)
                ->get();
            $estadoCRT = false;
            foreach ($vinculacion_ruta as $vr) {
                array_push($vinculacionRD, array("idVinculacion" => $vr->idV, "modelo" => $vr->modelo, "disponible" => $vr->disponible, "dispositivoD" => $vr->dispositivo_descripcion, "codigo" => $vr->codigo, "envio" => $vr->envio));
                if ($vr->disponible == 'c' || $vr->disponible == 'e' || $vr->disponible == 'a') {
                    $estadoCRT = true;
                }
            }
            $tab->vinculacionRuta = $vinculacionRD;
            $tab->estadoCRT = $estadoCRT;
            unset($vinculacionRD);
            $vinculacionRD = array();
        }
        //* ********************************
        $result = agruparEmpleados($tabla_empleado1);
        $area = DB::table('area as a')
            ->select('a.area_id', 'a.area_descripcion')
            ->where('a.organi_id', '=', session('sesionidorg'))
            ->get();
        // dd($result);
        return view('empleado.tablaEmpleado', ['tabla_empleado' => $result, 'areas' => $area]);
    }

    public function refresTabla()
    {
        function agruparEmpleadosRefresh($array)
        {
            $resultado = array();

            foreach ($array as $empleado) {
                if (!isset($resultado[$empleado->emple_id])) {
                    $resultado[$empleado->emple_id] = $empleado;
                }
                if (!isset($resultado[$empleado->emple_id]->dispositivos)) {
                    $resultado[$empleado->emple_id]->dispositivos = array();
                }
                array_push($resultado[$empleado->emple_id]->dispositivos, $empleado->dispositivo);
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
                $tabla_empleado1 = DB::table('empleado as e')
                    ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                    ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                    ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                    ->leftJoin('vinculacion_ruta as vr', 'vr.idEmpleado', '=', 'e.emple_id')
                    ->leftJoin('modo as md', function ($join) {
                        $join->on('md.id', '=', 'v.idModo');
                        $join->orOn('md.id', '=', 'vr.idModo');
                    })

                    ->select(
                        'e.emple_nDoc',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion',
                        'a.area_descripcion',
                        'cc.centroC_descripcion',
                        'e.emple_id',
                        'md.idTipoModo as dispositivo',
                        'e.emple_foto',
                        'e.asistencia_puerta',
                        'e.modoTareo'
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)
                    ->get();
            } else {
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();
                if ($invitado_empleadoIn != null) {
                    $tabla_empleado1 = DB::table('empleado as e')
                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                        ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                        ->leftJoin('vinculacion_ruta as vr', 'vr.idEmpleado', '=', 'e.emple_id')
                        ->leftJoin('modo as md', function ($join) {
                            $join->on('md.id', '=', 'v.idModo');
                            $join->orOn('md.id', '=', 'vr.idModo');
                        })
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->select(
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'a.area_descripcion',
                            'cc.centroC_descripcion',
                            'e.emple_id',
                            'md.idTipoModo as dispositivo',
                            'e.emple_foto',
                            'e.asistencia_puerta',
                            'e.modoTareo'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->get();
                } else {
                    $tabla_empleado1 = DB::table('empleado as e')
                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                        ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                        ->leftJoin('vinculacion_ruta as vr', 'vr.idEmpleado', '=', 'e.emple_id')
                        ->leftJoin('modo as md', function ($join) {
                            $join->on('md.id', '=', 'v.idModo');
                            $join->orOn('md.id', '=', 'vr.idModo');
                        })
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->select(
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'a.area_descripcion',
                            'cc.centroC_descripcion',
                            'e.emple_id',
                            'md.idTipoModo as dispositivo',
                            'e.emple_foto',
                            'e.asistencia_puerta',
                            'e.modoTareo'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->get();
                }
            }
        } else {
            $tabla_empleado1 = DB::table('empleado as e')
                ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                ->leftJoin('vinculacion_ruta as vr', 'vr.idEmpleado', '=', 'e.emple_id')
                ->leftJoin('modo as md', function ($join) {
                    $join->on('md.id', '=', 'v.idModo');
                    $join->orOn('md.id', '=', 'vr.idModo');
                })

                ->select(
                    'e.emple_nDoc',
                    'p.perso_nombre',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    'c.cargo_descripcion',
                    'a.area_descripcion',
                    'cc.centroC_descripcion',
                    'e.emple_id',
                    'md.idTipoModo as dispositivo',
                    'e.emple_foto',
                    'e.asistencia_puerta',
                    'e.modoTareo'
                )
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();
        }
        $vinculacionD = [];
        $vinculacionRD = [];
        foreach ($tabla_empleado1 as $tab) {
            $vinculacion = DB::table('vinculacion as v')
                ->join('modo as m', 'm.id', '=', 'v.idModo')
                ->join('tipo_dispositivo as td', 'td.id', 'm.idTipoDispositivo')
                ->join('licencia_empleado as le', 'le.id', '=', 'v.idLicencia')
                ->select('v.id as idV', 'v.pc_mac as pc', 'v.envio as envio', 'v.hash as codigo', 'le.idEmpleado', 'le.licencia', 'le.id as idL', 'le.disponible', 'td.dispositivo_descripcion')
                ->where('v.idEmpleado', '=', $tab->emple_id)
                ->get();
            foreach ($vinculacion as $vinc) {
                array_push($vinculacionD, array("idVinculacion" => $vinc->idV, "pc" => $vinc->pc, "idLicencia" => $vinc->idL, "licencia" => $vinc->licencia, "disponible" => $vinc->disponible, "dispositivoD" => $vinc->dispositivo_descripcion, "codigo" => $vinc->codigo, "envio" => $vinc->envio));
            }
            $tab->vinculacion = $vinculacionD;
            unset($vinculacionD);
            $vinculacionD = array();
            $modoCR = DB::table('vinculacion as v')
                ->join('modo as m', 'm.id', '=', 'v.idModo')
                ->join('tipo_dispositivo as td', 'td.id', 'm.idTipoDispositivo')
                ->join('licencia_empleado as le', 'le.id', '=', 'v.idLicencia')
                ->select('v.id as idV', 'v.envio as envio', 'v.hash as codigo', 'le.idEmpleado', 'le.licencia', 'le.id as idL', 'le.disponible', 'td.dispositivo_descripcion')
                ->where('v.idEmpleado', '=', $tab->emple_id)
                ->where('m.idTipoModo', '=', 1)
                ->get();
            $estadoCR = false;
            foreach ($modoCR as $md) {
                if ($md->disponible == 'c' || $md->disponible == 'e' || $md->disponible == 'a') {
                    $estadoCR = true;
                }
            }
            $tab->estadoCR = $estadoCR;
            //* VINCULACION DE CONTROL RUTA
            $vinculacion_ruta = DB::table('vinculacion_ruta as vr')
                ->join('modo as m', 'm.id', '=', 'vr.idModo')
                ->join('tipo_dispositivo as td', 'td.id', '=', 'm.idTipoDispositivo')
                ->select('vr.id as idV', 'vr.envio as envio', 'vr.hash as codigo', 'vr.idEmpleado', 'vr.disponible', 'td.dispositivo_descripcion', 'vr.modelo')
                ->where('vr.idEmpleado', '=', $tab->emple_id)
                ->where('m.idTipoModo', '=', 2)
                ->get();
            $estadoCRT = false;
            foreach ($vinculacion_ruta as $vr) {
                array_push($vinculacionRD, array("idVinculacion" => $vr->idV, "modelo" => $vr->modelo, "disponible" => $vr->disponible, "dispositivoD" => $vr->dispositivo_descripcion, "codigo" => $vr->codigo, "envio" => $vr->envio));
                if ($vr->disponible == 'c' || $vr->disponible == 'e' || $vr->disponible == 'a') {
                    $estadoCRT = true;
                }
            }
            $tab->vinculacionRuta = $vinculacionRD;
            $tab->estadoCRT = $estadoCRT;
            unset($vinculacionRD);
            $vinculacionRD = array();
        }
        $result = agruparEmpleadosRefresh($tabla_empleado1);

        return response()->json($result, 200);
    }

    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $objEmpleado = json_decode($request->get('objEmpleado'), true);

        $persona = new persona();
        $persona->perso_nombre = $objEmpleado['nombres'];
        $persona->perso_apPaterno = $objEmpleado['apPaterno'];
        $persona->perso_apMaterno = $objEmpleado['apMaterno'];
        $persona->perso_direccion = $objEmpleado['direccion'];
        $persona->perso_fechaNacimiento = $objEmpleado['fechaN'];
        $persona->perso_sexo = $objEmpleado['tipo'];
        $persona->save();
        $emple_persona = $persona->perso_id;

        $empleado = new empleado();
        $empleado->emple_tipoDoc = $objEmpleado['documento'];
        $empleado->emple_nDoc = $objEmpleado['numDocumento'];
        $empleado->emple_persona = $emple_persona;
        if ($objEmpleado['departamento'] != '') {
            $empleado->emple_departamentoN = $objEmpleado['departamento'];
        }
        if ($objEmpleado['provincia'] != '') {
            $empleado->emple_provinciaN = $objEmpleado['provincia'];
        }
        if ($objEmpleado['distrito'] != '') {
            $empleado->emple_distritoN = $objEmpleado['distrito'];
        }
        if ($objEmpleado['dep'] != '') {
            $empleado->emple_departamento = $objEmpleado['dep'];
        }
        if ($objEmpleado['prov'] != '') {
            $empleado->emple_provincia = $objEmpleado['prov'];
        }
        if ($objEmpleado['dist'] != '') {
            $empleado->emple_distrito = $objEmpleado['dist'];
        }
        $empleado->emple_celular = '';
        if ($objEmpleado['celular'] != '') {
            $empleado->emple_celular = $objEmpleado['celular'];
        }
        $empleado->emple_telefono = '';
        if ($objEmpleado['telefono'] != '') {
            $empleado->emple_telefono = $objEmpleado['telefono'];
        }
        if ($objEmpleado['correo'] != '') {
            $empleado->emple_Correo = $objEmpleado['correo'];
        }
        $empleado->emple_pasword = Hash::make($objEmpleado['numDocumento']);
        $empleado->emple_estado = '1';
        $empleado->users_id = Auth::user()->id;
        $empleado->organi_id = session('sesionidorg');
        $empleado->emple_foto = '';
        $empleado->save();
        $idempleado = $empleado->emple_id;

        ///////////////// SI USUARIO ES INVITADO
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->where('uso.organi_id', '=', session('sesionidorg'))
            ->where('uso.user_id', '=', Auth::user()->id)
            ->get()->first();
        /* if ($usuario_organizacion->rol_id == 3) {
            $invitado = DB::table('invitado as in')
                ->where('organi_id', '=', session('sesionidorg'))
                ->where('rol_id', '=', 3)
                ->where('in.user_Invitado', '=', Auth::user()->id)
                ->get()->first();
            $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                ->where('invem.idinvitado', '=',  $invitado->idinvitado)
                ->get()->first();
            if ($invitado_empleadoIn != null) {
                $invitado_empleado = new invitado_empleado();
                $invitado_empleado->idinvitado = $invitado->idinvitado;
                $invitado_empleado->emple_id = $empleado->emple_id;
                $invitado_empleado->save();
            }
        } */
        ///////////////////////////////////
        //* BUSCAR ACTIVIDAD DE CONTROL REMOTO EN ORGANIZACION
        $actividad = actividad::where('organi_id', '=', session('sesionidorg'))->where('controlRemoto', '=', 1)->where('controlRuta', '=', 1)->where('eliminacion', '=', 0)->get()->first();

        if ($actividad) {
            //VINCULAR ACTIVIDAD DE CONTROL REMOTO PARA EMPLEADO
            $actividad_empleado =  new actividad_empleado();
            $actividad_empleado->idActividad = $actividad->Activi_id;
            $actividad_empleado->idEmpleado = $idempleado;
            $actividad_empleado->eliminacion = 0;
            $actividad_empleado->save();
        } else {
            // CREAR ACTIVIDAD DE CONTROL REMOTO PARA ORGANIZACION DEFAULT
            $actividad = new actividad();
            $actividad->Activi_Nombre = "Tarea 01";
            $actividad->controlRemoto = 1;
            $actividad->controlRuta = 1;
            $actividad->asistenciaPuerta = 0;
            $actividad->eliminacion = 0;
            $actividad->organi_id = session('sesionidorg');
            $actividad->save();

            $idActividad = $actividad->Activi_id;

            //VINCULAR ACTIVIDAD DE CONTROL REMOTO PARA EMPLEADO
            $actividad_empleado =  new actividad_empleado();
            $actividad_empleado->idActividad = $idActividad;
            $actividad_empleado->idEmpleado = $idempleado;
            $actividad_empleado->eliminacion = 0;
            $actividad_empleado->save();
        }

        //* BUSCAR ACTIVIDADES GLOBALES POR EMPLEADO
        $actividadGlobalEmpleado = actividad::where('organi_id', '=', session('sesionidorg'))->where('globalEmpleado', '=', 1)->where('estado', '=', 1)->get();
        if (sizeof($actividadGlobalEmpleado) != 0) {
            foreach ($actividadGlobalEmpleado as $actividadG) {
                //*VINCULAR ACTIVIDAD DE GLOBAL POR EMPLEADO
                $actividad_empleado =  new actividad_empleado();
                $actividad_empleado->idActividad = $actividadG->Activi_id;
                $actividad_empleado->idEmpleado = $idempleado;
                $actividad_empleado->eliminacion = 1;
                $actividad_empleado->save();
            }
        }
        $asistencia = actividad::where('organi_id', '=', session('sesionidorg'))->where('asistenciaPuerta', '=', 1)->get()->first();
        if (!$asistencia) {
            // CREAR ACTIVIDAD DE ASISTENCIA EN PUERTA PARA ORGANIZACION DEFAULT
            $actividad = new actividad();
            $actividad->Activi_Nombre = "Asistencia";
            $actividad->controlRemoto = 0;
            $actividad->controlRuta = 0;
            $actividad->asistenciaPuerta = 1;
            $actividad->eliminacion = 0;
            $actividad->organi_id = session('sesionidorg');
            $actividad->save();
        }

        return response()->json($idempleado, 200);
    }

    public function storeEmpleado(Request $request, $idE)
    {
        $objEmpleado = json_decode($request->get('objEmpleado'), true);
        $empleado = Empleado::findOrFail($idE);
        $persona = persona::findOrFail($empleado->emple_persona);
        $persona->perso_nombre = $objEmpleado['nombres'];
        $persona->perso_apPaterno = $objEmpleado['apPaterno'];
        $persona->perso_apMaterno = $objEmpleado['apMaterno'];
        $persona->perso_direccion = $objEmpleado['direccion'];
        $persona->perso_fechaNacimiento = $objEmpleado['fechaN'];
        $persona->perso_sexo = $objEmpleado['tipo'];
        $persona->save();
        $empleado->emple_tipoDoc = $objEmpleado['documento'];
        $empleado->emple_nDoc = $objEmpleado['numDocumento'];
        if ($objEmpleado['departamento'] != '') {
            $empleado->emple_departamentoN = $objEmpleado['departamento'];
        }
        if ($objEmpleado['provincia'] != '') {
            $empleado->emple_provinciaN = $objEmpleado['provincia'];
        }
        if ($objEmpleado['distrito'] != '') {
            $empleado->emple_distritoN = $objEmpleado['distrito'];
        }
        if ($objEmpleado['dep'] != '') {
            $empleado->emple_departamento = $objEmpleado['dep'];
        }
        if ($objEmpleado['prov'] != '') {
            $empleado->emple_provincia = $objEmpleado['prov'];
        }
        if ($objEmpleado['dist'] != '') {
            $empleado->emple_distrito = $objEmpleado['dist'];
        }
        $empleado->emple_celular = '';
        if ($objEmpleado['celular'] != '') {
            $empleado->emple_celular = $objEmpleado['celular'];
        }
        $empleado->emple_telefono = '';
        if ($objEmpleado['telefono'] != '') {
            $empleado->emple_telefono = $objEmpleado['telefono'];
        }
        if ($objEmpleado['correo'] != '') {
            $empleado->emple_Correo = $objEmpleado['correo'];
        }
        $empleado->emple_pasword = Hash::make($objEmpleado['numDocumento']);
        $empleado->emple_foto = '';
        $empleado->save();
        return json_encode(array('status' => true));
    }

    public function storeEmpresarial(Request $request, $idE)
    {
        $objEmpleado = json_decode($request->get('objEmpleado'), true);
        $empleado = Empleado::findOrFail($idE);
        $empleado->emple_codigo = $objEmpleado['codigoEmpleado'];
        $idContrato = '';
        if ($objEmpleado['cargo'] != '') {
            $empleado->emple_cargo = $objEmpleado['cargo'];
        }
        if ($objEmpleado['area'] != '') {
            $empleado->emple_area = $objEmpleado['area'];
        }
        if ($objEmpleado['centroc'] != '') {
            $empleado->emple_centCosto = $objEmpleado['centroc'];
        }
        if ($objEmpleado['nivel'] != '') {
            $empleado->emple_nivel = $objEmpleado['nivel'];
        }
        if ($objEmpleado['local'] != '') {
            $empleado->emple_local = $objEmpleado['local'];
        }
        $empleado->save();
        //* BUSCAR ACTIVIDADES GLOBALES POR AREAS
        if ($objEmpleado['area'] != '') {
            $actividadGlobalArea = actividad::where('organi_id', '=', session('sesionidorg'))->where('globalArea', '=', 1)->where('estado', '=', 1)->get();
            if (sizeof($actividadGlobalArea) != 0) {
                foreach ($actividadGlobalArea as $actividadG) {
                    $busqueda = false;
                    //* BUSCAR AREAS
                    $actividad_area = actividad_area::where('idActividad', '=', $actividadG->Activi_id)->where('idArea', '=', $objEmpleado['area'])->where('estado', '=', 1)->get();
                    foreach ($actividad_area as $a) {
                        $busquedaActividadE = actividad_empleado::where('idEmpleado', '=', $empleado->emple_id)->where('idActividad', '=', $a->idActividad)->get()->first();
                        if (!$busquedaActividadE) {
                            //*VINCULAR ACTIVIDAD DE GLOBAL POR EMPLEADO
                            $actividad_empleado =  new actividad_empleado();
                            $actividad_empleado->idActividad = $a->idActividad;
                            $actividad_empleado->idEmpleado = $empleado->emple_id;
                            $actividad_empleado->eliminacion = 1;
                            $actividad_empleado->save();
                        }
                    }
                }
            }
        }
        return response()->json($idContrato, 200);
    }

    public function storeFoto(Request $request, $idE)
    {
        $empleado = Empleado::findOrFail($idE);
        $empleado->emple_foto = '';

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = public_path() . '/fotosEmpleado';
            $fileName = uniqid() . $file->getClientOriginalName();
            $file->move($path, $fileName);
            $empleado->emple_foto = $fileName;
        }
        $empleado->save();
        return json_encode(array('status' => true));
    }


    public function storeCalendario(Request $request, $idE)
    {
        $empleado = Empleado::findOrFail($idE);
        $idempleado = $empleado->emple_id;
        $objEmpleado = json_decode($request->get('objEmpleado'), true);
        ///CALENDARIO

        $eventos_empleado_tempEU = eventos_empleado_temp::where('users_id', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('id_horario', '=', null)->where('color', '!=', '#9E9E9E')
            ->where('calendario_calen_id', '=', $objEmpleado['idca'])->get();

        $eventos_empleadoVerif = eventos_empleado::where('id_empleado', '=', $idempleado)
            ->get();
        if ($eventos_empleadoVerif->isEmpty()) {
            foreach ($eventos_empleado_tempEU as $eventos_empleado_tempEUs) {
                $eventos_empleado = new eventos_empleado();
                $eventos_empleado->title = $eventos_empleado_tempEUs->title;
                $eventos_empleado->color = $eventos_empleado_tempEUs->color;
                $eventos_empleado->textColor = $eventos_empleado_tempEUs->textColor;
                $eventos_empleado->start = $eventos_empleado_tempEUs->start;
                $eventos_empleado->end = $eventos_empleado_tempEUs->end;
                $eventos_empleado->id_empleado = $idempleado;
                $eventos_empleado->tipo_ev = $eventos_empleado_tempEUs->tipo_ev;
                $eventos_empleado->id_calendario = $eventos_empleado_tempEUs->calendario_calen_id;
                $eventos_empleado->laborable = 0;
                $eventos_empleado->save();
            }
        }
        //INIDENC
        $incidenciasborrar = incidencia_dias::where('id_empleado', '=', $idempleado)
            ->delete();
        $eventos_empleado_tempInc = eventos_empleado_temp::where('users_id', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))->where('id_horario', '=', null)->where('color', '=', '#9E9E9E')->where('textColor', '=', '#313131')
            ->where('calendario_calen_id', '=', $objEmpleado['idca'])->get();

        foreach ($eventos_empleado_tempInc as $eventos_empleado_tempIncs) {
            $incidenciadias_dias = new incidencia_dias();
            $incidenciadias_dias->inciden_dias_fechaI = $eventos_empleado_tempIncs->start;
            $incidenciadias_dias->inciden_dias_fechaF = $eventos_empleado_tempIncs->end;
            $incidenciadias_dias->id_incidencia = $eventos_empleado_tempIncs->tipo_ev;
            $incidenciadias_dias->id_empleado = $idempleado;
            $incidenciadias_dias->save();
        }
        return json_encode(array('status' => true));
    }

    public function storeHorario(Request $request, $idE)
    {
        $empleado = Empleado::findOrFail($idE);
        $idempleado = $empleado->emple_id;
        $objEmpleado = json_decode($request->get('objEmpleado'), true);
        //HORARIO

        $horario_empleadoBor = DB::table('horario_empleado')
        ->where('empleado_emple_id', $idempleado)
        ->update(['estado' => 0]);
        $eventos_empleado_tempHor = eventos_empleado_temp::where('users_id', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))->where('id_horario', '!=', null)->where('color', '=', '#ffffff')->where('textColor', '=', '111111')
            ->where('calendario_calen_id', '=', $objEmpleado['idca'])->get();


        foreach ($eventos_empleado_tempHor as $eventos_empleado_tempHors) {
            $horario_dias = new horario_dias();
            $horario_dias->title = $eventos_empleado_tempHors->title;
            $horario_dias->color = $eventos_empleado_tempHors->color;
            $horario_dias->textColor = $eventos_empleado_tempHors->textColor;
            $horario_dias->start = $eventos_empleado_tempHors->start;
            $horario_dias->end = $eventos_empleado_tempHors->end;
            $horario_dias->users_id = Auth::user()->id;
            $horario_dias->organi_id = session('sesionidorg');
            $horario_dias->save();

            $horario_empleados = new horario_empleado();
            $horario_empleados->horario_horario_id = $eventos_empleado_tempHors->id_horario;
            $horario_empleados->empleado_emple_id = $idempleado;
            $horario_empleados->horario_dias_id = $horario_dias->id;
            $horario_empleados->fuera_horario =  $eventos_empleado_tempHors->fuera_horario;
            $horario_empleados->horarioComp =  $eventos_empleado_tempHors->horarioComp;
            $horario_empleados->horaAdic =  $eventos_empleado_tempHors->horaAdic;
            $horario_empleados->nHoraAdic =  $eventos_empleado_tempHors->nHoraAdic;
            $horario_empleados->estado =  1;
            if ($eventos_empleado_tempHors->fuera_horario == 1) {
                $horario_empleados->borderColor = '#5369f8';
            }
            $horario_empleados->save();
        }
        ///////////FIN CALENDARIO
        return json_encode(array('status' => true));
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $idempleado = $request->get('value');
        function agruparEmpleadosShow($array)
        {
            $resultado = array();

            foreach ($array as $empleado) {
                if (!isset($resultado[$empleado->emple_id])) {
                    $resultado[$empleado->emple_id] = $empleado;
                }
            }
            return $resultado;
        }
        $empleados = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('tipo_documento as tipoD', 'e.emple_tipoDoc', '=', 'tipoD.tipoDoc_id')
            ->leftJoin('ubigeo_peru_departments as depar', 'e.emple_departamento', '=', 'depar.id')
            ->leftJoin('ubigeo_peru_provinces as provi', 'e.emple_provincia', '=', 'provi.id')
            ->leftJoin('ubigeo_peru_districts as dist', 'e.emple_distrito', '=', 'dist.id')

            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('ubigeo_peru_departments as para', 'e.emple_departamentoN', '=', 'para.id')
            ->leftJoin('ubigeo_peru_provinces as proviN', 'e.emple_provinciaN', '=', 'proviN.id')
            ->leftJoin('ubigeo_peru_districts as distN', 'e.emple_distritoN', '=', 'distN.id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
            ->leftJoin('nivel as n', 'e.emple_nivel', '=', 'n.nivel_id')
            ->leftJoin('local as l', 'e.emple_local', '=', 'l.local_id')
            ->leftJoin('eventos_empleado as eve', 'e.emple_id', '=', 'eve.id_empleado')


            ->select(
                'e.emple_id',
                'p.perso_id',
                'p.perso_nombre',
                'tipoD.tipoDoc_descripcion',
                'e.emple_nDoc',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'p.perso_fechaNacimiento',
                'p.perso_direccion',
                'p.perso_sexo',
                'depar.id as depar',
                'depar.name as deparNo',
                'provi.id as proviId',
                'provi.name as provi',
                'dist.id as distId',
                'dist.name as distNo',
                'c.cargo_descripcion',
                'a.area_descripcion',
                'cc.centroC_descripcion',
                'para.id as iddepaN',
                'para.name as depaN',
                'proviN.id as idproviN',
                'proviN.name as proviN',
                'distN.id as iddistN',
                'distN.name as distN',
                'e.emple_id',
                'c.cargo_id',
                'a.area_id',
                'cc.centroC_id',
                'e.emple_local',
                'e.emple_nivel',
                'e.emple_departamento',
                'e.emple_provincia',
                'e.emple_distrito',
                'e.emple_foto as foto',
                'e.emple_celular',
                'e.emple_telefono',
                'e.emple_Correo',
                'e.emple_codigo',
                'n.nivel_descripcion',
                'l.local_descripcion',
                'eve.id_calendario as idcalendar'
            )
            ->where('e.emple_id', '=', $idempleado)
            /*  ->where('e.emple_estado', '=', 1) */
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->groupBy('e.organi_id')
            ->get();
        $contrato = DB::table('contrato as c')
            ->join('tipo_contrato as tc', 'tc.contrato_id', '=', 'c.id_tipoContrato')
            ->leftJoin('condicion_pago as cp', 'cp.id', '=', 'c.id_condicionPago')
            ->select('c.id as idC', 'c.fechaInicio', 'c.fechaFinal', 'c.monto', 'tc.contrato_id as idTipoC', 'tc.contrato_descripcion', 'cp.id as idCond', 'cp.condicion')
            ->where('c.idEmpleado', '=', $idempleado)
            ->where('c.estado', '=', 1)
            ->get();
        $empleados[0]->contrato = $contrato;
        $empleado = agruparEmpleadosShow($empleados);
        return array_values($empleado);
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function edit(empleado $empleado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idE)
    {

        $objEmpleado = json_decode($request->get('objEmpleadoA'), true);
        if ($request == null) return false;
        $empleado = Empleado::findOrFail($idE);

        if ($objEmpleado['departamento_v'] != '') {
            $empleado->emple_departamentoN = $objEmpleado['departamento_v'];
        }
        if ($objEmpleado['provincia_v'] != '') {
            $empleado->emple_provinciaN = $objEmpleado['provincia_v'];
        }
        if ($objEmpleado['distrito_v'] != '') {
            $empleado->emple_distritoN = $objEmpleado['distrito_v'];
        }
        if ($objEmpleado['dep_v'] != '') {
            $empleado->emple_departamento = $objEmpleado['dep_v'];
        }
        if ($objEmpleado['prov_v'] != '') {
            $empleado->emple_provincia = $objEmpleado['prov_v'];
        }
        if ($objEmpleado['dist_v'] != '') {
            $empleado->emple_distrito = $objEmpleado['dist_v'];
        }

        $empleado->emple_celular = $objEmpleado['celular_v'];

        $empleado->emple_telefono = $objEmpleado['telefono_v'];
        $empleado->emple_Correo = $objEmpleado['correo_v'];
        $empleado->save();

        $idpersona = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_id')
            ->where('emple_id', '=', $idE)
            ->get();

        $persona = Persona::findOrFail($idpersona[0]->perso_id);
        $persona->perso_nombre = $objEmpleado['nombres_v'];
        $persona->perso_apPaterno = $objEmpleado['apPaterno_v'];
        $persona->perso_apMaterno = $objEmpleado['apMaterno_v'];
        $persona->perso_direccion = $objEmpleado['direccion_v'];
        $persona->perso_fechaNacimiento = $objEmpleado['fechaN_v'];
        $persona->perso_sexo = $objEmpleado['tipo_v'];
        $persona->save();
        return response()->json($empleado, 200);
    }

    public function updateEmpresarial(Request $request, $idE)
    {
        $objEmpleado = json_decode($request->get('objEmpleadoA'), true);
        if ($request == null) return false;
        $empleado = Empleado::findOrFail($idE);
        if ($objEmpleado['area_v'] != $empleado->emple_area) {
            //* BUSCAR ACTIVIDADES DEL EMPLEADO
            $buscarActividadesAreas = actividad_area::where('idArea', '=', $empleado->emple_area)->where('estado', '=', 1)->get();
            if (sizeof($buscarActividadesAreas) != 0) {
                foreach ($buscarActividadesAreas as $actividadB) {
                    //* BUSCAR AREAS
                    $actividad = actividad::where('Activi_id', '=', $actividadB->idActividad)->where('estado', '=', 1)->get()->first();
                    $actividad_empleado = actividad_empleado::where('idActividad', '=', $actividad->Activi_id)
                        ->where('idEmpleado', '=', $empleado->emple_id)->where('eliminacion', '=', 1)->get()->first();
                    $actividad_empleado->estado = 0;
                    $actividad_empleado->save();
                }
            }
            //* BUSCAR ACTIVIDADES DE AREAS
            if ($objEmpleado['area_v'] != '') {
                $actividadGlobalArea = actividad::where('organi_id', '=', session('sesionidorg'))->where('globalArea', '=', 1)->where('estado', '=', 1)->get();
                if (sizeof($actividadGlobalArea) != 0) {
                    foreach ($actividadGlobalArea as $actividadG) {
                        //* BUSCAR AREAS
                        $actividad_area = actividad_area::where('idActividad', '=', $actividadG->Activi_id)->where('idArea', '=', $objEmpleado['area_v'])->where('estado', '=', 1)->get();
                        foreach ($actividad_area as $a) {
                            $busquedaActividadE = actividad_empleado::where('idEmpleado', '=', $empleado->emple_id)->where('idActividad', '=', $a->idActividad)->get()->first();
                            if (!$busquedaActividadE) {
                                //*VINCULAR ACTIVIDAD DE GLOBAL POR EMPLEADO
                                $actividad_empleado =  new actividad_empleado();
                                $actividad_empleado->idActividad = $a->idActividad;
                                $actividad_empleado->idEmpleado = $empleado->emple_id;
                                $actividad_empleado->estado = 1;
                                $actividad_empleado->eliminacion = 1;
                                $actividad_empleado->save();
                            } else {
                                $busquedaActividadE->estado = 1;
                                $busquedaActividadE->save();
                            }
                        }
                    }
                }
            }
        }
        $empleado->emple_codigo = $objEmpleado['codigoEmpleado_v'];
        if ($objEmpleado['cargo_v'] != '') {
            $empleado->emple_cargo = $objEmpleado['cargo_v'];
        }
        if ($objEmpleado['area_v'] != '') {
            $empleado->emple_area = $objEmpleado['area_v'];
        }
        if ($objEmpleado['centroc_v'] != '') {
            $empleado->emple_centCosto = $objEmpleado['centroc_v'];
        }
        if ($objEmpleado['nivel_v'] != '') {
            $empleado->emple_nivel = $objEmpleado['nivel_v'];
        }
        if ($objEmpleado['local_v'] != '') {
            $empleado->emple_local = $objEmpleado['local_v'];
        }
        $empleado->save();
        return response()->json($objEmpleado, 200);
    }

    public function updateFoto(Request $request, $idE)
    {
        $objEmpleado = json_decode($request->get('objEmpleadoA'), true);
        if ($request == null) return false;
        $empleado = Empleado::findOrFail($idE);
        if ($request->hasfile('file')) {
            $file = $request->file('file');
            $path = public_path() . '/fotosEmpleado';
            $fileName = uniqid() . $file->getClientOriginalName();
            $file->move($path, $fileName);
            $empleado->emple_foto = $fileName;
        }
        $empleado->save();
        return response()->json($empleado, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {



        $empleado = empleado::find($request->get('id'));


        $empleado->delete();

        $persona = persona::where('perso_id', '=', $empleado->emple_persona)->delete();
    }

    public function eliminarFoto(Request $request, $v_id)
    {
        $empleado = Empleado::findOrFail($v_id);
        $idFoto = DB::table('empleado as e')
            ->select('e.emple_foto')
            ->where('emple_id', '=', $v_id)
            ->get();
        unlink(public_path() . '/fotosEmpleado/' . $idFoto[0]->emple_foto);
        $empleado->emple_foto = "";
        $empleado->save();
        return json_encode(array("result" => true));
    }

    public function bajaEmpleado(Request $request)
    {
        $idE = $request->get('idEmpleado');
        $fechaBaja = $request->get('fechaBaja');
        $historial_empleado = historial_empleado::where('emple_id', '=', $idE)->whereNotNull('idContrato')->whereNull('fecha_baja')->get()->first();
        if ($historial_empleado) {
            if (Carbon::parse($fechaBaja)->gt(Carbon::parse($historial_empleado->fecha_alta))) {
                //* HISTORIAL DE EMPLEADO
                $historial_empleado->fecha_baja = $fechaBaja;
                $historial_empleado->save();
                //* CONTRATO
                $contrato = contrato::where('id', '=', $historial_empleado->idContrato)->get()->first();
                $contrato->fechaFinal = $fechaBaja;
                $contrato->estado = 0;
                $contrato->save();
                //* EMPLEADO
                $empleado = empleado::where('emple_id', '=', $historial_empleado->emple_id)->get()->first();
                $empleado->emple_estado = 0;
                $empleado->save();
                return $historial_empleado->idhistorial_empleado;
            } else {
                return response()->json(array("respuesta" => false, "fecha" => $historial_empleado->fecha_alta), 200);
            }
        } else {
            return 0;
        }
    }

    public function storeDocumentoBaja(Request $request, $id)
    {
        //* VALIDAR SI ES VACIO O O ACTUALIZAR
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $filesC) {
                $file = $filesC;
                $path = public_path() . '/documEmpleado';
                $fileName = uniqid() . $file->getClientOriginalName();
                $file->move($path, $fileName);

                $doc_empleado = new doc_empleado();
                $doc_empleado->idhistorial_empleado = $id;
                $doc_empleado->rutaDocumento = $fileName;
                $doc_empleado->save();
            }
        }

        return json_encode(array('status' => true));
    }

    public function indexMenu()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
            $departamento = ubigeo_peru_departments::all();
            $provincia = ubigeo_peru_provinces::all();
            $distrito = ubigeo_peru_districts::all();
            $tipo_doc = tipo_documento::all();
            $tipo_cont = tipo_contrato::where('organi_id', '=', session('sesionidorg'))->get();
            $area = area::where('organi_id', '=', session('sesionidorg'))->get();
            $cargo = cargo::where('organi_id', '=', session('sesionidorg'))->get();
            $centro_costo = centro_costo::where('organi_id', '=', session('sesionidorg'))->where('estado', '=', 1)->get();
            $nivel = nivel::where('organi_id', '=', session('sesionidorg'))->get();
            $local = local::where('organi_id', '=', session('sesionidorg'))->get();
            $empleado = empleado::all();
            $dispositivo = tipo_dispositivo::all();
            $tabla_empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                ->join('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                ->select(
                    'p.perso_nombre',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    'c.cargo_descripcion',
                    'a.area_descripcion',
                    'cc.centroC_descripcion',
                    'e.emple_id'
                )
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 1)
                ->get();
            $calendario = DB::table('calendario as ca')
                ->where('ca.organi_id', '=', session('sesionidorg'))
                ->get();
            $horario = horario::where('organi_id', '=', session('sesionidorg'))->get();
            $condicionPago = condicion_pago::where('organi_id', '=', session('sesionidorg'))->get();
            //dd($tabla_empleado);

            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('rol_id', '=', 3)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    if ($invitadod->permiso_Emp == 1) {
                        $permiso_invitado = DB::table('permiso_invitado')
                            ->where('idinvitado', '=', $invitadod->idinvitado)
                            ->get()->first();
                        return view('empleado.empleadoMenu', [
                            'departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito,
                            'tipo_doc' => $tipo_doc, 'tipo_cont' => $tipo_cont, 'area' => $area, 'cargo' => $cargo, 'centro_costo' => $centro_costo,
                            'nivel' => $nivel, 'local' => $local, 'empleado' => $empleado, 'tabla_empleado' => $tabla_empleado, 'dispositivo' => $dispositivo,
                            'calendario' => $calendario, 'horario' => $horario, 'condicionP' => $condicionPago, 'agregarEmp' => $permiso_invitado->agregarEmp,
                            'modifEmp' => $permiso_invitado->modifEmp, 'bajaEmp' => $permiso_invitado->bajaEmp, 'GestActEmp' => $permiso_invitado->GestActEmp
                        ]);
                    } else {
                        return redirect('/dashboard');
                    }
                    /*   */
                } else {
                    return view('empleado.empleadoMenu', [
                        'departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito,
                        'tipo_doc' => $tipo_doc, 'tipo_cont' => $tipo_cont, 'area' => $area, 'cargo' => $cargo, 'centro_costo' => $centro_costo,
                        'nivel' => $nivel, 'local' => $local, 'empleado' => $empleado, 'tabla_empleado' => $tabla_empleado, 'dispositivo' => $dispositivo,
                        'calendario' => $calendario, 'horario' => $horario, 'condicionP' => $condicionPago
                    ]);
                }
            } else {
                return view('empleado.empleadoMenu', [
                    'departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito,
                    'tipo_doc' => $tipo_doc, 'tipo_cont' => $tipo_cont, 'area' => $area, 'cargo' => $cargo, 'centro_costo' => $centro_costo,
                    'nivel' => $nivel, 'local' => $local, 'empleado' => $empleado, 'tabla_empleado' => $tabla_empleado, 'dispositivo' => $dispositivo,
                    'calendario' => $calendario, 'horario' => $horario, 'condicionP' => $condicionPago
                ]);
            }
        }
    }

    public function comprobarNumD(Request $request)
    {
        $numeroD = $request->get('numeroD');
        $empleado = DB::table('empleado as e')
            ->where('e.emple_nDoc', '=', $numeroD)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_estado', '=', 1)
            ->get()->first();
        $empleadoEli = DB::table('empleado as e')
            ->where('e.emple_nDoc', '=', $numeroD)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_estado', '=', 0)
            ->get()->first();
        if ($empleado != null) {
            return 1;
        }
        if ($empleadoEli != null) {
            return 2;
        }
        if ($empleado == null && $empleadoEli == null) {
            return 3;
        }
    }

    public function comprobarNumDocumentoStore(Request $request)
    {
        $numDoc = $request->get('numDoc');
        $empleado = $request->get('idE');
        $empleado1 = DB::table('empleado as e')
            ->where('e.emple_nDoc', '=', $numDoc)
            ->where('e.emple_id', '!=', $empleado)
            ->where('e.emple_estado', '=', 1)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get()->first();

        $empleado2 = DB::table('empleado as e')
            ->where('e.emple_nDoc', '=', $numDoc)
            ->where('e.emple_id', '!=', $empleado)
            ->where('e.emple_estado', '=', 0)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->get()->first();



        if ($empleado1 != null) {
            return 1;
        }
        if ($empleado2 != null) {
            return 2;
        }
        if ($empleado1 == null && $empleado2 == null) {
            return 3;
        }
    }

    public function comprobarCorreo(Request $request)
    {
        $email = $request->get('email');
        $empleado = DB::table('empleado as e')
            ->where('emple_Correo', '=', $email)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_estado', '=', 1)
            ->get()->first();
        if ($empleado != null) {
            return 1;
        }
    }

    public function comprobarCorreoEditar(Request $request)
    {
        $email = $request->get('email');
        $empleado = $request->get('idE');
        $empleado = DB::table('empleado as e')
            ->where('emple_Correo', '=', $email)
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_id', '!=', $empleado)
            ->where('e.emple_estado', '=', 1)
            ->get()->first();
        if ($empleado != null) {
            return 1;
        }
    }

    public function calendarioEmpTemp(Request $request)
    {
        $idcalendario = $request->idcalendario;

        $eventos_empleado_tempCop = eventos_empleado_temp::where('users_id', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))->where('calendario_calen_id', '=', $idcalendario)->get();
        if ($eventos_empleado_tempCop->isEmpty()) {
            $eventos_usuario = eventos_usuario::where('organi_id', '=', session('sesionidorg'))
                ->where('id_calendario', '=', $idcalendario)->get();
            if ($eventos_usuario) {
                foreach ($eventos_usuario as $eventos_usuarios) {
                    $eventos_empleado_tempSave = new eventos_empleado_temp();
                    $eventos_empleado_tempSave->users_id = Auth::user()->id;
                    $eventos_empleado_tempSave->title = $eventos_usuarios->title;
                    $eventos_empleado_tempSave->color = $eventos_usuarios->color;
                    $eventos_empleado_tempSave->textColor = $eventos_usuarios->textColor;
                    $eventos_empleado_tempSave->start = $eventos_usuarios->start;
                    $eventos_empleado_tempSave->end = $eventos_usuarios->end;
                    $eventos_empleado_tempSave->tipo_ev = $eventos_usuarios->tipo;
                    $eventos_empleado_tempSave->calendario_calen_id = $idcalendario;
                    $eventos_empleado_tempSave->organi_id = session('sesionidorg');
                    $eventos_empleado_tempSave->save();
                }
            }
        }


        $eventos_empleado_temp = DB::table('eventos_empleado_temp as evt')
            ->select(['evEmpleadoT_id as id', 'title', 'color', 'textColor', 'start', 'end', 'tipo_ev', 'users_id', 'calendario_calen_id',
             'horaI', 'horaF', 'borderColor', 'horaAdic','nHoraAdic','h.horasObliga'])
            ->leftJoin('horario as h', 'evt.id_horario', '=', 'h.horario_id')
            ->where('evt.users_id', '=', Auth::user()->id)
            ->where('evt.calendario_calen_id', '=', $idcalendario)
            ->where('evt.organi_id', '=', session('sesionidorg'))
            ->get();

        return $eventos_empleado_temp;
    }
    public function storeCalendarioTem(Request $request)
    {
        $eventos_empleado_tempSave = new eventos_empleado_temp();
        $eventos_empleado_tempSave->users_id = Auth::user()->id;
        $eventos_empleado_tempSave->title = $request->get('title');
        $eventos_empleado_tempSave->color = $request->get('color');
        $eventos_empleado_tempSave->textColor = $request->get('textColor');
        $eventos_empleado_tempSave->start = $request->get('start');
        $eventos_empleado_tempSave->end = $request->get('end');
        $eventos_empleado_tempSave->tipo_ev = $request->get('tipo');
        $eventos_empleado_tempSave->calendario_calen_id = $request->get('id_calendario');
        $eventos_empleado_tempSave->organi_id = session('sesionidorg');
        $eventos_empleado_tempSave->save();



        $eventos_empleado_temp = DB::table('eventos_empleado_temp as evt')
            ->select(['evEmpleadoT_id as id', 'title', 'color', 'textColor', 'start', 'end', 'tipo_ev', 'users_id', 'calendario_calen_id'])
            ->where('evt.users_id', '=', Auth::user()->id)
            ->where('evt.calendario_calen_id', '=', $request->get('id_calendario'))
            ->where('evt.organi_id', '=', session('sesionidorg'))
            ->get();

        return $eventos_empleado_temp;
    }

    public function storeIncidTem(Request $request)
    {

        $incidencia = new incidencias();
        $incidencia->inciden_descripcion =  $request->get('title');
        $incidencia->inciden_descuento = $request->get('descuentoI');
        $incidencia->inciden_hora =  $request->get('horaIn');
        $incidencia->users_id = Auth::user()->id;
        $incidencia->organi_id = session('sesionidorg');
        $incidencia->save();

        $eventos_empleado_tempSave = new eventos_empleado_temp();
        $eventos_empleado_tempSave->users_id = Auth::user()->id;
        $eventos_empleado_tempSave->title = $request->get('title');
        $eventos_empleado_tempSave->color = '#9E9E9E';
        $eventos_empleado_tempSave->textColor = '#313131';
        $eventos_empleado_tempSave->start = $request->get('start');
        $eventos_empleado_tempSave->end = $request->get('end');
        $eventos_empleado_tempSave->tipo_ev = $incidencia->inciden_id;
        $eventos_empleado_tempSave->calendario_calen_id = $request->get('id_calendario');
        $eventos_empleado_tempSave->organi_id = session('sesionidorg');
        $eventos_empleado_tempSave->save();



        $eventos_empleado_temp = DB::table('eventos_empleado_temp as evt')
            ->select(['evEmpleadoT_id as id', 'title', 'color', 'textColor', 'start', 'end', 'tipo_ev', 'users_id', 'calendario_calen_id'])
            ->where('evt.users_id', '=', Auth::user()->id)
            ->where('evt.calendario_calen_id', '=', $request->get('id_calendario'))
            ->where('evt.organi_id', '=', session('sesionidorg'))
            ->get();

        return $eventos_empleado_temp;
    }
    public function vaciarcalend()
    {
        DB::table('eventos_empleado_temp')->where('users_id', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))
            ->delete();
    }
    public function vaciarcalendId(Request $request)
    {
        DB::table('eventos_empleado_temp')->where('users_id', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))->where('calendario_calen_id', '=', $request->get('idca'))

            ->delete();
    }
    public function registrarHorario(Request $request)
    {
        $tardanza = $request->tardanza;
        $descripcion = $request->descripcion;
        $toleranciaH = $request->toleranciaH;
        $inicio = $request->inicio;
        $fin = $request->fin;
        $toleranciaF = $request->toleranciaF;
        $horaOblig = $request->horaOblig;
        $horario = new horario();

        $horario->organi_id = session('sesionidorg');
        $horario->horario_descripcion = $descripcion;
        $horario->horario_tolerancia = $toleranciaH;
        $horario->horaI = $inicio;
        $horario->horaF = $fin;
        $horario->user_id = Auth::user()->id;
        $horario->horario_toleranciaF = $toleranciaF;
        $horario->horasObliga = $horaOblig;
        $horario->hora_contTardanza = $tardanza;
        $horario->save();

        $descPausa = $request->get('descPausa');
        $IniPausa = $request->get('pausaInicio');
        $FinPausa = $request->get('finPausa');

        if ($descPausa) {

            if ($descPausa != null || $descPausa != '') {
                for ($i = 0; $i < sizeof($descPausa); $i++) {
                    if ($descPausa[$i] != null) {
                        $pausas_horario = new pausas_horario();
                        $pausas_horario->pausH_descripcion = $descPausa[$i];
                        $pausas_horario->pausH_Inicio = $IniPausa[$i];
                        $pausas_horario->pausH_Fin = $FinPausa[$i];
                        $pausas_horario->horario_id = $horario->horario_id;
                        $pausas_horario->save();
                    }
                }
            }
        }

        return $horario;
    }

    public function guardarhorarioTem(Request $request)
    {
        $datafecha = $request->fechasArray;
        $horas = $request->hora;
        $idhorar = $request->idhorar;
        $idca = $request->idca;
        $fueraHora = $request->fueraHora;
        $horaC = $request->horarioC;
        $horaA = $request->horarioA;
        $arrayeve = collect();
        $arrayrep = collect();
        $nHoraAdic = $request->nHoraAdic;
        foreach ($datafecha as $datafechas) {
            $tempre = eventos_empleado_temp::where('users_id', '=', Auth::user()->id)
                ->where('organi_id', '=', session('sesionidorg'))
                ->where('start', '=', $datafechas)
                ->where('id_horario', '=', $idhorar)
                ->get()->first();
            if ($tempre) {
                $startArre = carbon::create($tempre->start);
                $arrayrep->push($startArre->format('Y-m-d'));
            }
        }

        $datos = Arr::flatten($arrayrep);


        //DIFERENCIA ARRAYS
        $datafecha2 = array_values(array_diff($datafecha, $datos));


        foreach ($datafecha2 as $datafechas) {
            $eventos_empleado_tempSave = new eventos_empleado_temp();
            $eventos_empleado_tempSave->title = $horas;
            $eventos_empleado_tempSave->start = $datafechas;
            $eventos_empleado_tempSave->color = '#ffffff';
            $eventos_empleado_tempSave->textColor = '111111';
            $eventos_empleado_tempSave->users_id = Auth::user()->id;
            $eventos_empleado_tempSave->tipo_ev = 5;
            $eventos_empleado_tempSave->id_horario = $idhorar;
            $eventos_empleado_tempSave->calendario_calen_id = $idca;
            $eventos_empleado_tempSave->fuera_horario = $fueraHora;
            $eventos_empleado_tempSave->horarioComp = $horaC;
            $eventos_empleado_tempSave->horaAdic = $horaA;
            $eventos_empleado_tempSave->nHoraAdic =  $nHoraAdic;
            $eventos_empleado_tempSave->organi_id = session('sesionidorg');
            if ($fueraHora == 1) {
                $eventos_empleado_tempSave->borderColor = '#5369f8';
            }
            $eventos_empleado_tempSave->save();
            $arrayeve->push($eventos_empleado_tempSave);
        }
    }

    public function vercalendarioEmpl(Request $request)
    {
        $idempleado = $request->idempleado;
        $incidencias = DB::table('incidencias as i')
            ->select([
                'idi.inciden_dias_id as id', 'i.inciden_descripcion as title', 'i.inciden_descuento as color', 'i.inciden_descuento as textColor',
                'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end', 'i.inciden_descripcion as horaI', 'i.inciden_descripcion as horaF', 'i.inciden_descripcion as borderColor', 'laborable',
                'i.inciden_descripcion as horaAdic', 'i.inciden_descripcion as idhorario','i.inciden_descripcion as horasObliga','i.inciden_descripcion as nHoraAdic'
            ])
            ->join('incidencia_dias as idi', 'i.inciden_id', '=', 'idi.id_incidencia')
            ->where('idi.id_empleado', '=', $idempleado);

        $eventos_empleado = DB::table('eventos_empleado')
            ->select([
                'evEmpleado_id as id', 'title', 'color', 'textColor', 'start', 'end', 'title as horaI', 'title as horaF', 'title as borderColor', 'laborable',
                'title as horaAdic', 'start as idhorario','start as horasObliga','start as nHoraAdic'
            ])
            ->where('id_empleado', '=', $idempleado)
            ->union($incidencias);

        $horario_empleado = DB::table('horario_empleado as he')
            ->select(['he.horarioEmp_id as id', 'title', 'color', 'textColor', 'start', 'end', 'horaI', 'horaF', 'borderColor', 'laborable', 'horaAdic', 'h.horario_id as idhorario'
            ,'horasObliga','nHoraAdic'])
            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.empleado_emple_id', '=', $idempleado)
            ->where('he.estado', '=', 1)
            ->union($eventos_empleado)
            ->get();


        foreach ($horario_empleado as $tab) {
            $pausas_horario = DB::table('pausas_horario as pauh')
                ->select('idpausas_horario', 'pausH_descripcion', 'pausH_Inicio', 'pausH_Fin', 'pauh.horario_id')
                ->where('pauh.horario_id', '=', $tab->idhorario)
                ->distinct('pauh.idpausas_horario')
                ->get();

            $tab->pausas = $pausas_horario;

        }
        return $horario_empleado;
    }

    public function calendarioEditar(Request $request)
    {

        $horario_empleado = DB::table('horario_empleado as he')
            ->select(['he.horarioEmp_id as id', 'title', 'color', 'textColor', 'start', 'end'])
            /*  ->where('users_id', '=', Auth::user()->id) */
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.estado', '=', 1)
            ->where('he.empleado_emple_id', '=', $request->get('idempleado'));

        $incidencias = DB::table('incidencias as i')
            ->select(['idi.inciden_dias_id as id', 'i.inciden_descripcion as title', 'i.inciden_descuento as color', 'i.inciden_descuento as textColor', 'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end'])
            ->join('incidencia_dias as idi', 'i.inciden_id', '=', 'idi.id_incidencia')
            ->where('idi.id_empleado', '=', $request->get('idempleado'))
            ->union($horario_empleado);


        $eventos_empleado = DB::table('eventos_empleado')
            ->select(['evEmpleado_id as id', 'title', 'color', 'textColor', 'start', 'end'])
            ->where('id_empleado', '=', $request->get('idempleado'))
            ->union($incidencias)
            ->get();

        if ($eventos_empleado->isEmpty()) {
            return 1;
        } else {
            return $eventos_empleado;
        }
    }

    public function eliminarEte(Request $request)
    {
        $ideve = $request->ideve;
        $eventos_empleado_temp = eventos_empleado_temp::where('evEmpleadoT_id', '=', $ideve)->delete();
    }

    //////////////////77
    public function calendarioEmp(Request $request)
    {
        $idcalendario = $request->idcalendario;
        $idempleado = $request->idempleado;

        $eventos_empleado = eventos_empleado::where('id_empleado', '=', $idempleado)
            ->get();

        if ($eventos_empleado->isEmpty()) {
            $eventos_usuario = eventos_usuario::where('organi_id', '=', session('sesionidorg'))
                ->where('id_calendario', '=', $idcalendario)->get();
            if ($eventos_usuario) {
                foreach ($eventos_usuario as $eventos_usuarios) {
                    $eventos_empleado_r = new eventos_empleado();
                    $eventos_empleado_r->id_empleado = $idempleado;
                    $eventos_empleado_r->title = $eventos_usuarios->title;
                    $eventos_empleado_r->color = $eventos_usuarios->color;
                    $eventos_empleado_r->textColor = $eventos_usuarios->textColor;
                    $eventos_empleado_r->start = $eventos_usuarios->start;
                    $eventos_empleado_r->end = $eventos_usuarios->end;
                    $eventos_empleado_r->tipo_ev = $eventos_usuarios->tipo;
                    $eventos_empleado_r->id_calendario = $idcalendario;
                    $eventos_empleado_r->laborable = 0;
                    $eventos_empleado_r->save();
                }
            }
        }




        /*  dd($horario_empleado); */

        $incidencias = DB::table('incidencias as i')
            ->select([
                'idi.inciden_dias_id as id', 'i.inciden_descripcion as title', 'i.inciden_descuento as color', 'i.inciden_descuento as textColor',
                'idi.inciden_dias_fechaI as start', 'idi.inciden_dias_fechaF as end', 'i.inciden_descripcion as horaI', 'i.inciden_descripcion as horaF', 'i.inciden_descripcion as borderColor', 'laborable',
                'i.inciden_descripcion as horaAdic', 'i.inciden_descripcion as idhorario','i.inciden_descripcion as horasObliga','i.inciden_descripcion as nHoraAdic'
            ])
            ->join('incidencia_dias as idi', 'i.inciden_id', '=', 'idi.id_incidencia')
            ->where('idi.id_empleado', '=', $idempleado);
        /*   ->union($horario_empleado); */


        $eventos_empleado = DB::table('eventos_empleado')
            ->select([
                'evEmpleado_id as id', 'title', 'color', 'textColor', 'start', 'end', 'title as horaI', 'title as horaF', 'title as borderColor', 'laborable',
                'title as horaAdic', 'start as idhorario','start as horasObliga','start as nHoraAdic'
            ])
            ->where('id_empleado', '=', $idempleado)
            ->union($incidencias);

        /*   $horario_empleado ->union($eventos_empleado); */


        $horario_empleado = DB::table('horario_empleado as he')
            ->select(['he.horarioEmp_id as id', 'title', 'color', 'textColor', 'start', 'end', 'horaI', 'horaF', 'borderColor', 'laborable', 'horaAdic', 'h.horario_id as idhorario','horasObliga','nHoraAdic'])
            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('he.estado', '=', 1)
            ->where('he.empleado_emple_id', '=', $idempleado)
            ->union($eventos_empleado)
            ->get();



        foreach ($horario_empleado as $tab) {
            $pausas_horario = DB::table('pausas_horario as pauh')
                ->select('idpausas_horario', 'pausH_descripcion', 'pausH_Inicio', 'pausH_Fin', 'pauh.horario_id')
                ->where('pauh.horario_id', '=', $tab->idhorario)
                ->distinct('pauh.idpausas_horario')
                ->get();


            $tab->pausas = $pausas_horario;

        }
        return $horario_empleado;
    }
    public function vaciarcalendempleado(Request $request)
    {
        $idempleado = $request->idempleado;
        DB::table('eventos_empleado')->where('id_empleado', '=', $idempleado)
            ->delete();
    }

    public function storeCalendarioempleado(Request $request)
    {
        $ev1 = eventos_empleado::where('id_empleado', '=', $request->get('idempleado'))
            ->first();

        $eventos_empleado = new eventos_empleado();

        $eventos_empleado->title = $request->get('title');
        $eventos_empleado->color = $request->get('color');
        $eventos_empleado->textColor = $request->get('textColor');
        $eventos_empleado->start = $request->get('start');
        $eventos_empleado->end = $request->get('end');
        $eventos_empleado->tipo_ev = $request->get('tipo');
        $eventos_empleado->id_empleado = $request->get('idempleado');
        $eventos_empleado->id_calendario = $ev1->id_calendario;
        $eventos_empleado->laborable = 0;
        $eventos_empleado->save();
    }
    public function storeIncidempleado(Request $request)
    {

        $incidencia = new incidencias();
        $incidencia->inciden_descripcion =  $request->get('title');
        $incidencia->inciden_descuento = $request->get('descuentoI');
        $incidencia->inciden_hora =  $request->get('horaIn');
        $incidencia->users_id = Auth::user()->id;
        $incidencia->organi_id = session('sesionidorg');
        $incidencia->save();

        $incidencia_dias = new incidencia_dias();
        $incidencia_dias->id_incidencia = $incidencia->inciden_id;
        $incidencia_dias->inciden_dias_fechaI = $request->get('start');
        $incidencia_dias->inciden_dias_fechaF = $request->get('end');
        $incidencia_dias->id_empleado = $request->get('idempleado');

        $incidencia_dias->save();
    }

    public function guardarhorarioempleado(Request $request)
    {
        $datafecha = $request->fechasArray;
        $horas = $request->hora;
        $idhorar = $request->idhorar;
        $idempleado = $request->idempleado;
        $fueraHora = $request->fueraHora;
        $horaC = $request->horarioC;
        $horaA = $request->horarioA;
        $nHoraAdic = $request->nHoraAdic;
        $arrayeve = collect();
        $arrayrep = collect();
        //COMPRARA SI ES EL MISMO
        foreach ($datafecha as $datafechas) {
            $tempre = horario_empleado::select(['horario_empleado.horarioEmp_id as id', 'title', 'color', 'textColor', 'start', 'end', 'horaI', 'horaF', 'borderColor'])
                ->join('horario as h', 'horario_empleado.horario_horario_id', '=', 'h.horario_id')
                ->join('horario_dias as hd', 'horario_empleado.horario_dias_id', '=', 'hd.id')
                ->where('start', '=', $datafechas)
                ->where('h.horario_id', '=', $idhorar)
                ->where('horario_empleado.estado', '=', 1)
                ->where('horario_empleado.empleado_emple_id', '=', $idempleado)
                ->get()->first();
            if ($tempre) {
                $startArre = carbon::create($tempre->start);
                $arrayrep->push($startArre->format('Y-m-d'));
            }
        }
        $datos = Arr::flatten($arrayrep);
        //DIFERENCIA ARRAYS
        $datafecha2 = array_values(array_diff($datafecha, $datos));
        /////////////////////////////////////COMPARAR SI ESTA DENTRO DE RANGO
        $horarioEmpleado = horario::where('horario_id', $idhorar)->first();
        $horaInicialF = Carbon::parse($horarioEmpleado->horaI);
        $horaFinalF = Carbon::parse($horarioEmpleado->horaF);
        $arrayHDentro = collect();
        /*  dd($horarioDentro); */
        foreach ($datafecha as $datafechas) {
            $horarioDentro = horario_empleado::select(['horario_empleado.horarioEmp_id as id', 'title', 'color', 'textColor', 'start', 'end', 'horaI', 'horaF', 'borderColor'])
                ->join('horario as h', 'horario_empleado.horario_horario_id', '=', 'h.horario_id')
                ->join('horario_dias as hd', 'horario_empleado.horario_dias_id', '=', 'hd.id')
                ->where('horario_empleado.estado', '=', 1)
                ->where('start', '=', $datafechas)
                /* ->where('h.horaI', '=', $idhorar)
                     ->where('h.horaF', '=', $idhorar) */
                ->where('horario_empleado.empleado_emple_id', '=', $idempleado)
                ->get();
            if ($horarioDentro) {
                foreach ($horarioDentro as $horarioDentros) {
                    $horaIDentro = Carbon::parse($horarioDentros->horaI);
                    $horaFDentro = Carbon::parse($horarioDentros->horaF);
                    if ($horaIDentro->gte($horaInicialF) && $horaIDentro->lt($horaFinalF)) {
                        $startArreD = carbon::create($horarioDentros->start);
                        $arrayHDentro->push($startArreD->format('Y-m-d'));
                    } else {
                        if ($horaFDentro->gte($horaFinalF) && $horaFDentro->gte($horaInicialF)) {
                            $startArreD = carbon::create($horarioDentros->start);
                            $arrayHDentro->push($startArreD->format('Y-m-d'));
                        } else {
                            if ($horaFDentro->lt($horaFinalF) && $horaFDentro->gte($horaInicialF)) {
                                $startArreD = carbon::create($horarioDentros->start);
                                $arrayHDentro->push($startArreD->format('Y-m-d'));
                            }
                        }
                    }
                }
            }
        }
        $datosDentroN = Arr::flatten($arrayHDentro);
        $datafecha3 = array_values(array_diff($datafecha2, $datosDentroN));
        /*  dd($datafecha3); */
        /////////////////////////////////////
        foreach ($datafecha3 as $datafechas) {
            $horario_dias = new horario_dias();
            $horario_dias->title = $horas;
            $horario_dias->start = $datafechas;
            $horario_dias->color = '#ffffff';
            $horario_dias->textColor = '111111';
            $horario_dias->users_id = Auth::user()->id;
            $horario_dias->organi_id = session('sesionidorg');
            $horario_dias->save();
            $arrayeve->push($horario_dias);

            $horario_empleados = new horario_empleado();
            $horario_empleados->horario_horario_id = $idhorar;
            $horario_empleados->empleado_emple_id = $idempleado;
            $horario_empleados->horario_dias_id = $horario_dias->id;
            $horario_empleados->fuera_horario = $fueraHora;
            $horario_empleados->horarioComp = $horaC;
            $horario_empleados->horaAdic = $horaA;
            $horario_empleados->nHoraAdic = $nHoraAdic;
            $horario_empleados->estado = 1;
            if ($fueraHora == 1) {
                $horario_empleados->borderColor = '#5369f8';
            }
            $horario_empleados->save();

            /*---- REGISTRAR HISTORIAL DE CAMBIO -------------------*/
            /*------ SE REGISTRA SI EL CAMBIO O REGISTRO EN EL HORARIO ES EL DIA ACTUAL--- */
            /* OBTENEMOS DIA ACTUAL */
            $fechaHoy = Carbon::now('America/Lima');
            $diaActual = $fechaHoy->isoFormat('YYYY-MM-DD');
            /* --------------------------------------------- */
            /* OBTENEMOS DIA DE HORARIO */
            $fechaHoy1 = Carbon::create($datafechas);
            $diaHorario = $fechaHoy1->isoFormat('YYYY-MM-DD');
            /* --------------------------------------------- */
            if($diaHorario==$diaActual){
               /* SI LAS FECHAS SON IGUALES */
               $historial_horarioE = new historial_horarioempleado();
               $historial_horarioE->horarioEmp_id =$horario_empleados->horarioEmp_id;
               $historial_horarioE->fechaCambio = $fechaHoy;
               $historial_horarioE->estadohorarioEmp=1;
               $historial_horarioE->save();
            }
            else{

            }

            /* ------------------------------- */

        }
        $datafechaValida = array_values(array_diff($datafecha, $datafecha3));
        /* dd($datafechaValida); */
        if ($datafechaValida != null || $datafechaValida != []) {
            return 'Ya existe un horario asignado en este rango de horas, revise y vuelva a intentar.';
        } else {
            return 'Cambios guardados';
        }
    }

    public function vaciardfTem(Request $request)
    {
        DB::table('eventos_empleado_temp')->where('users_id', '=', Auth::user()->id)
            ->where('color', '=', '#e6bdbd')
            ->whereYear('start', $request->get('aniocalen'))
            ->whereMonth('start', $request->get('mescale'))
            ->where('organi_id', '=', session('sesionidorg'))
            ->delete();
    }

    public function vaciardlabTem(Request $request)
    {
        DB::table('eventos_empleado_temp')->where('users_id', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('color', '=', '#dfe6f2')
            ->where('textColor', '=', '#0b1b29')
            ->whereYear('start', $request->get('aniocalen'))
            ->whereMonth('start', $request->get('mescale'))
            ->delete();
    }
    public function vaciardNlabTem(Request $request)
    {
        DB::table('eventos_empleado_temp')->where('users_id', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('color', '=', '#a34141')
            ->where('textColor', '=', '#ffffff')
            ->whereYear('start', $request->get('aniocalen'))
            ->whereMonth('start', $request->get('mescale'))
            ->delete();
    }
    public function vaciardIncidTem(Request $request)
    {
        DB::table('eventos_empleado_temp')->where('users_id', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('color', '=', '#9E9E9E')
            ->where('textColor', '=', '#313131')
            ->whereYear('start', $request->get('aniocalen'))
            ->whereMonth('start', $request->get('mescale'))
            ->delete();
    }
    public function eliminareventBD(Request $request)
    {
        $ideve = $request->ideve;
        /*  $eventos_empleado = eventos_empleado::where('evEmpleado_id', '=', $ideve)->delete(); */
        $eventos_empleado = eventos_empleado::findOrFail($ideve);
        eventos_empleado::destroy($ideve);
        return response()->json($eventos_empleado);
    }

    public function eliminarHorariosEdit(Request $request)
    {
        $ideve = $request->ideve;
        $horario_empleado1 = DB::table('horario_empleado')
        ->where('horarioEmp_id', '=', $ideve)
        ->update(['estado' => 0]);
        /*---- REGISTRAR HISTORIAL DE CAMBIO -------------------*/
            /*------ SE REGISTRA SI la eliminacion EN EL HORARIO ES EL DIA ACTUAL--- */
            /* OBTENEMOS DIA ACTUAL */
            $fechaHoy = Carbon::now('America/Lima');
            $diaActual = $fechaHoy->isoFormat('YYYY-MM-DD');
            /* --------------------------------------------- */
            /* OBTENEMOS DIA DE HORARIO */
            $horario_empleadoEl = DB::table('horario_empleado as he')
            ->select(['he.horarioEmp_id as id', 'hd.start as fechaEli'])
            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('horarioEmp_id', '=', $ideve)
            ->get()->first();
            $fechaHorario= $horario_empleadoEl->fechaEli;
            $fechaHoy1 = Carbon::create($fechaHorario);
            $diaHorario = $fechaHoy1->isoFormat('YYYY-MM-DD');
            /* --------------------------------------------- */
            if($diaHorario==$diaActual){
               /* SI LAS FECHAS SON IGUALES */
               $historial_horarioE = new historial_horarioempleado();
               $historial_horarioE->horarioEmp_id =$ideve;
               $historial_horarioE->fechaCambio = $fechaHoy;
               $historial_horarioE->estadohorarioEmp=0;
               $historial_horarioE->save();
            }
            else{

            }

            /* ------------------------------- */
        return response()->json($horario_empleado1);

    }
    public function eliminarInciEdit(Request $request)
    {
        $ideve = $request->ideve;

        $incidencia_dias = incidencia_dias::findOrFail($ideve);
        incidencia_dias::destroy($ideve);
        return response()->json($incidencia_dias);
    }

    public function vaciardescansoTem(Request $request)
    {
        DB::table('eventos_empleado_temp')->where('users_id', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('color', '=', '#4673a0')
            ->where('textColor', '=', '#ffffff')
            ->whereYear('start', $request->get('aniocalen'))
            ->whereMonth('start', $request->get('mescale'))
            ->delete();
    }
    public function vaciarFerBD(Request $request)
    {
        DB::table('eventos_empleado')
            ->where('id_empleado', '=', $request->get('idempleado'))
            ->where('color', '=', '#e6bdbd')
            ->whereYear('start', $request->get('aniocalen'))
            ->whereMonth('start', $request->get('mescale'))
            ->delete();
    }
    public function vaciarFdescansoBD(Request $request)
    {
        $eliDescaso = eventos_empleado::where('id_empleado', '=', $request->get('idempleado'))
            /*  ->where('color', '=', '#4673a0')
            ->where('textColor', '=', '#ffffff') */
            ->whereIn('tipo_ev', [1, 3])

            ->whereYear('start', $request->get('aniocalen'))
            ->whereMonth('start', $request->get('mescale'))->get();
        foreach ($eliDescaso as $eliDescasos) {
            $eliDescasos->delete();
        }


        /*  $eliDescaso->delete(); */
        return response()->json($eliDescaso);
    }
    public function vaciardnlaBD(Request $request)
    {
        DB::table('eventos_empleado')
            ->where('id_empleado', '=', $request->get('idempleado'))
            ->where('color', '=', '#a34141')
            ->where('textColor', '=', '#ffffff')
            ->whereYear('start', $request->get('aniocalen'))
            ->whereMonth('start', $request->get('mescale'))
            ->delete();
    }
    public function vaciarincidelaBD(Request $request)
    {
        DB::table('incidencia_dias')
            ->where('id_empleado', '=', $request->get('idempleado'))
            ->whereYear('inciden_dias_fechaI', $request->get('aniocalen'))
            ->whereMonth('inciden_dias_fechaI', $request->get('mescale'))
            ->delete();
    }
    public function vaciarhorarioTem(Request $request)
    {
        DB::table('eventos_empleado_temp')->where('users_id', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('color', '=', '#ffffff')
            ->where('textColor', '=', '111111')
            ->whereYear('start', $request->get('aniocalen'))
            ->whereMonth('start', $request->get('mescale'))
            ->delete();
    }

    public function eliminarhorariosBD(Request $request)
    {
        DB::table('horario_empleado as he')
            ->where('he.empleado_emple_id', '=', $request->get('idempleado'))
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->whereYear('hd.start', $request->get('aniocalen'))
            ->whereMonth('hd.start', $request->get('mescale'))
            ->update(['he.estado' => 0]);
    }
    public function vaciarbdempleado(Request $request)
    {
        DB::table('eventos_empleado')
            ->where('id_empleado', '=', $request->get('idempleado'))
            ->delete();
    }

    public function vaciarhorariosBD(Request $request)
    {

        DB::table('horario_empleado as he')
            ->where('he.empleado_emple_id', '=', $request->get('idempleado'))
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->update(['he.estado' => 0]);
    }

    public function cambiarEstadoEmp(Request $request)
    {
        $ids = $request->ids;
        $empleado = DB::table('empleado')
            ->where('emple_nDoc', $ids)
            ->where('organi_id', '=', session('sesionidorg'))
            ->update(['emple_estado' => 1]);
    }

    public function agregarCorreoE(Request $request)
    {
        $empleado = empleado::find($request->get('idEmpleado'));
        if ($empleado) {
            $empleado->emple_Correo = $request->get('correo');
            $empleado->save();
        }

        return response()->json($empleado, 200);
    }

    public function agregarCelularE(Request $request)
    {
        $empleado = empleado::find($request->get('idEmpleado'));
        if ($empleado) {
            $empleado->emple_celular = "+51" . $request->get('celular');
            $empleado->save();
        }

        return response()->json($empleado, 200);
    }

    public function asisPuerta(Request $request)
    {
        $empleado = empleado::find($request->get('idPuerta'));
        if ($empleado) {
            $empleado->asistencia_puerta = $request->get('estadoP');
            $empleado->save();
        }
    }
    public function refresTablaAre(Request $request)
    {
        function agruparEmpleadosRefreshArea($array)
        {
            $resultado = array();

            foreach ($array as $empleado) {
                if (!isset($resultado[$empleado->emple_id])) {
                    $resultado[$empleado->emple_id] = $empleado;
                }
                if (!isset($resultado[$empleado->emple_id]->dispositivos)) {
                    $resultado[$empleado->emple_id]->dispositivos = array();
                }
                array_push($resultado[$empleado->emple_id]->dispositivos, $empleado->dispositivo);
            }
            return array_values($resultado);
        }
        $idarea = $request->idarea;
        $arrayem = collect();
        $invitadod = DB::table('invitado')
            ->where('user_Invitado', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('rol_id', '=', 3)
            ->get()->first();
        if ($idarea != null) {
            foreach ($idarea as $idareas) {
                if ($invitadod) {
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    if ($invitado_empleadoIn != null) {
                        $tabla_empleado1 = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                            ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                            ->leftJoin('modo as md', 'md.id', '=', 'v.idModo')
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->select(
                                'e.emple_nDoc',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'a.area_descripcion',
                                'cc.centroC_descripcion',
                                'e.emple_id',
                                'md.idTipoModo as dispositivo',
                                'e.emple_foto',
                                'e.asistencia_puerta',
                                'e.modoTareo'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.emple_area', '=', $idareas)
                            ->get();
                    } else {
                        $tabla_empleado1 = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                            ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                            ->leftJoin('modo as md', 'md.id', '=', 'v.idModo')
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->select(
                                'e.emple_nDoc',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'a.area_descripcion',
                                'cc.centroC_descripcion',
                                'e.emple_id',
                                'md.idTipoModo as dispositivo',
                                'e.emple_foto',
                                'e.asistencia_puerta',
                                'e.modoTareo'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.emple_area', '=', $idareas)
                            ->get();
                    }
                } else {
                    $tabla_empleado1 = DB::table('empleado as e')
                        ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                        ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                        ->leftJoin('modo as md', 'md.id', '=', 'v.idModo')

                        ->select(
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'a.area_descripcion',
                            'cc.centroC_descripcion',
                            'e.emple_id',
                            'md.idTipoModo as dispositivo',
                            'e.emple_foto',
                            'e.asistencia_puerta',
                            'e.modoTareo'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->where('e.emple_area', '=', $idareas)
                        ->get();
                }

                $arrayem->push($tabla_empleado1);
            }

            $vinculacionD = [];
            foreach (array_flatten($arrayem) as $tab) {
                $vinculacion = DB::table('vinculacion as v')
                    ->join('modo as m', 'm.id', '=', 'v.idModo')
                    ->join('tipo_dispositivo as td', 'td.id', 'm.idTipoDispositivo')
                    ->join('licencia_empleado as le', 'le.id', '=', 'v.idLicencia')
                    ->select('v.id as idV', 'v.pc_mac as pc', 'v.envio as envio', 'v.hash as codigo', 'le.idEmpleado', 'le.licencia', 'le.id as idL', 'le.disponible', 'td.dispositivo_descripcion')
                    ->where('v.idEmpleado', '=', $tab->emple_id)
                    ->get();
                foreach ($vinculacion as $vinc) {
                    array_push($vinculacionD, array("idVinculacion" => $vinc->idV, "pc" => $vinc->pc, "idLicencia" => $vinc->idL, "licencia" => $vinc->licencia, "disponible" => $vinc->disponible, "dispositivoD" => $vinc->dispositivo_descripcion, "codigo" => $vinc->codigo, "envio" => $vinc->envio));
                }
                $tab->vinculacion = $vinculacionD;
                unset($vinculacionD);
                $vinculacionD = array();
                $modoCR = DB::table('vinculacion as v')
                    ->join('modo as m', 'm.id', '=', 'v.idModo')
                    ->join('tipo_dispositivo as td', 'td.id', 'm.idTipoDispositivo')
                    ->join('licencia_empleado as le', 'le.id', '=', 'v.idLicencia')
                    ->select('v.id as idV', 'v.envio as envio', 'v.hash as codigo', 'le.idEmpleado', 'le.licencia', 'le.id as idL', 'le.disponible', 'td.dispositivo_descripcion')
                    ->where('v.idEmpleado', '=', $tab->emple_id)
                    ->where('m.idTipoModo', '=', 1)
                    ->get();
                $estadoCR = false;
                foreach ($modoCR as $md) {
                    if ($md->disponible == 'c' || $md->disponible == 'e' || $md->disponible == 'a') {
                        $estadoCR = true;
                    }
                }
                $tab->estadoCR = $estadoCR;
            }
            $result = agruparEmpleadosRefreshArea(array_flatten($arrayem));
        } else {
            if ($invitadod) {
                if ($invitadod->verTodosEmps == 1) {
                    $arrayem = DB::table('empleado as e')
                        ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                        ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                        ->leftJoin('modo as md', 'md.id', '=', 'v.idModo')

                        ->select(
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'a.area_descripcion',
                            'cc.centroC_descripcion',
                            'e.emple_id',
                            'md.idTipoModo as dispositivo',
                            'e.emple_foto',
                            'e.asistencia_puerta',
                            'e.modoTareo'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.emple_estado', '=', 1)
                        ->get();
                } else {
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    if ($invitado_empleadoIn != null) {
                        $arrayem = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                            ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                            ->leftJoin('modo as md', 'md.id', '=', 'v.idModo')
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->select(
                                'e.emple_nDoc',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'a.area_descripcion',
                                'cc.centroC_descripcion',
                                'e.emple_id',
                                'md.idTipoModo as dispositivo',
                                'e.emple_foto',
                                'e.asistencia_puerta',
                                'e.modoTareo'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->get();
                    } else {
                        $arrayem = DB::table('empleado as e')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                            ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                            ->leftJoin('modo as md', 'md.id', '=', 'v.idModo')
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->select(
                                'e.emple_nDoc',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'a.area_descripcion',
                                'cc.centroC_descripcion',
                                'e.emple_id',
                                'md.idTipoModo as dispositivo',
                                'e.emple_foto',
                                'e.asistencia_puerta',
                                'e.modoTareo'
                            )
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->where('e.emple_estado', '=', 1)
                            ->get();
                    }
                }
            } else {
                $arrayem = DB::table('empleado as e')
                    ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                    ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                    ->leftJoin('vinculacion as v', 'v.idEmpleado', '=', 'e.emple_id')
                    ->leftJoin('modo as md', 'md.id', '=', 'v.idModo')

                    ->select(
                        'e.emple_nDoc',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion',
                        'a.area_descripcion',
                        'cc.centroC_descripcion',
                        'e.emple_id',
                        'md.idTipoModo as dispositivo',
                        'e.emple_foto',
                        'e.asistencia_puerta',
                        'e.modoTareo'
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 1)

                    ->get();
            }

            $vinculacionD = [];
            foreach ($arrayem as $tab) {
                $vinculacion = DB::table('vinculacion as v')
                    ->join('modo as m', 'm.id', '=', 'v.idModo')
                    ->join('tipo_dispositivo as td', 'td.id', 'm.idTipoDispositivo')
                    ->join('licencia_empleado as le', 'le.id', '=', 'v.idLicencia')
                    ->select('v.id as idV', 'v.pc_mac as pc', 'v.envio as envio', 'v.hash as codigo', 'le.idEmpleado', 'le.licencia', 'le.id as idL', 'le.disponible', 'td.dispositivo_descripcion')
                    ->where('v.idEmpleado', '=', $tab->emple_id)
                    ->get();
                foreach ($vinculacion as $vinc) {
                    array_push($vinculacionD, array("idVinculacion" => $vinc->idV, "pc" => $vinc->pc, "idLicencia" => $vinc->idL, "licencia" => $vinc->licencia, "disponible" => $vinc->disponible, "dispositivoD" => $vinc->dispositivo_descripcion, "codigo" => $vinc->codigo, "envio" => $vinc->envio));
                }
                $tab->vinculacion = $vinculacionD;
                unset($vinculacionD);
                $vinculacionD = array();
                $modoCR = DB::table('vinculacion as v')
                    ->join('modo as m', 'm.id', '=', 'v.idModo')
                    ->join('tipo_dispositivo as td', 'td.id', 'm.idTipoDispositivo')
                    ->join('licencia_empleado as le', 'le.id', '=', 'v.idLicencia')
                    ->select('v.id as idV', 'v.envio as envio', 'v.hash as codigo', 'le.idEmpleado', 'le.licencia', 'le.id as idL', 'le.disponible', 'td.dispositivo_descripcion')
                    ->where('v.idEmpleado', '=', $tab->emple_id)
                    ->where('m.idTipoModo', '=', 1)
                    ->get();
                $estadoCR = false;
                foreach ($modoCR as $md) {
                    if ($md->disponible == 'c' || $md->disponible == 'e' || $md->disponible == 'a') {
                        $estadoCR = true;
                    }
                }
                $tab->estadoCR = $estadoCR;
            }
            $result = agruparEmpleadosRefreshArea($arrayem);
        }





        return response()->json($result, 200);
    }

    public function empleadosBaja()
    {
        if (session('sesionidorg') == null || session('sesionidorg') == 'null') {
            return redirect('/elegirorganizacion');
        } else {
            $departamento = ubigeo_peru_departments::all();
            $provincia = ubigeo_peru_provinces::all();
            $distrito = ubigeo_peru_districts::all();
            $tipo_doc = tipo_documento::all();
            $tipo_cont = tipo_contrato::where('organi_id', '=', session('sesionidorg'))->get();
            $area = area::where('organi_id', '=', session('sesionidorg'))->get();
            $cargo = cargo::where('organi_id', '=', session('sesionidorg'))->get();
            $centro_costo = centro_costo::where('organi_id', '=', session('sesionidorg'))->get();
            $nivel = nivel::where('organi_id', '=', session('sesionidorg'))->get();
            $local = local::where('organi_id', '=', session('sesionidorg'))->get();
            $empleado = empleado::all();
            $dispositivo = tipo_dispositivo::all();
            $tabla_empleado = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                ->join('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                ->select(
                    'p.perso_nombre',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    'c.cargo_descripcion',
                    'a.area_descripcion',
                    'cc.centroC_descripcion',
                    'e.emple_id'
                )
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 0)
                ->get();
            $calendario = DB::table('calendario as ca')
                ->where('ca.organi_id', '=', session('sesionidorg'))
                ->get();
            $horario = horario::where('organi_id', '=', session('sesionidorg'))->get();
            $condicionPago = condicion_pago::where('organi_id', '=', session('sesionidorg'))->get();
            //dd($tabla_empleado);

            $invitadod = DB::table('invitado')
                ->where('user_Invitado', '=', Auth::user()->id)
                ->where('organi_id', '=', session('sesionidorg'))
                ->get()->first();

            if ($invitadod) {
                if ($invitadod->rol_id != 1) {
                    return redirect('/dashboard');
                } else {
                    return view('empleado.empleadoMenuBaja', [
                        'departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito,
                        'tipo_doc' => $tipo_doc, 'tipo_cont' => $tipo_cont, 'area' => $area, 'cargo' => $cargo, 'centro_costo' => $centro_costo,
                        'nivel' => $nivel, 'local' => $local, 'empleado' => $empleado, 'tabla_empleado' => $tabla_empleado, 'dispositivo' => $dispositivo,
                        'calendario' => $calendario, 'horario' => $horario, 'condicionP' => $condicionPago
                    ]);
                }
            } else {
                return view('empleado.empleadoMenuBaja', [
                    'departamento' => $departamento, 'provincia' => $provincia, 'distrito' => $distrito,
                    'tipo_doc' => $tipo_doc, 'tipo_cont' => $tipo_cont, 'area' => $area, 'cargo' => $cargo, 'centro_costo' => $centro_costo,
                    'nivel' => $nivel, 'local' => $local, 'empleado' => $empleado, 'tabla_empleado' => $tabla_empleado, 'dispositivo' => $dispositivo,
                    'calendario' => $calendario, 'horario' => $horario, 'condicionP' => $condicionPago
                ]);
            }
        }
    }
    public function refresTablaEmpBaja()
    {

        $tabla_empleado1 = DB::table('empleado as e')
            ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
            ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')


            ->select(
                'e.emple_nDoc',
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'c.cargo_descripcion',
                'a.area_descripcion',
                'cc.centroC_descripcion',
                'e.emple_id'

            )
            ->where('e.organi_id', '=', session('sesionidorg'))
            ->where('e.emple_estado', '=', 0)
            ->get();


        return response()->json($tabla_empleado1, 200);
    }

    public function refresTablaAreBaja(Request $request)
    {


        $idarea = $request->idarea;
        $arrayem = collect();
        if ($idarea != null) {
            foreach ($idarea as $idareas) {
                $tabla_empleado1 = DB::table('empleado as e')
                    ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                    ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')


                    ->select(
                        'e.emple_nDoc',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion',
                        'a.area_descripcion',
                        'cc.centroC_descripcion',
                        'e.emple_id'
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.emple_estado', '=', 0)
                    ->where('e.emple_area', '=', $idareas)
                    ->get();
                if ($tabla_empleado1->isNotEmpty()) {
                    $arrayem->push($tabla_empleado1);
                }
            }
            $arrayem = array_flatten($arrayem);
        } else {
            $arrayem = DB::table('empleado as e')
                ->leftJoin('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                ->leftJoin('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')


                ->select(
                    'e.emple_nDoc',
                    'p.perso_nombre',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    'c.cargo_descripcion',
                    'a.area_descripcion',
                    'cc.centroC_descripcion',
                    'e.emple_id'
                )
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.emple_estado', '=', 0)

                ->get();
        }
        return response()->json($arrayem, 200);
    }

    public function darAltaEmpleado(Request $request)
    {
        $ids = $request->ids;
        $fechaAlta = $request->fechaAlta;
        $empleado = empleado::whereIn('emple_id', explode(",", $ids))->get();
        $array = array();
        foreach ($empleado as $t) {
            $t->emple_estado = 1;
            $t->save();
            $array[] = $t->emple_persona;
        }
        $arrayEmp = array();
        $arrayEmp[] = explode(",", $ids);
        foreach ($arrayEmp[0] as $emp) {

            $historial_empleadoN = new historial_empleado();
            $historial_empleadoN->emple_id =  $emp;
            $historial_empleadoN->fecha_alta = $fechaAlta;
            $historial_empleadoN->save();
        }
        return $historial_empleadoN->idhistorial_empleado;
    }

    public function storeDocumentoAlta(Request $request, $data)
    {


        //VALIDAR SI ES VACIO O O ACTUALIZAR
        if ($request->hasFile('AltaFile')) {
            foreach ($request->file('AltaFile') as $filesC) {
                $file = $filesC;
                $path = public_path() . '/documEmpleado';
                $fileName = uniqid() . $file->getClientOriginalName();
                $file->move($path, $fileName);

                $doc_empleado = new doc_empleado();
                $doc_empleado->idhistorial_empleado = $data;
                $doc_empleado->rutaDocumento = $fileName;
                $doc_empleado->save();
            }
        }

        return json_encode(array('status' => true));
    }

    /* MODO TAREO CAMBIAR ESTADO */
    public function modoTareo(Request $request)
    {
        $empleado = empleado::find($request->get('idTareo'));
        if ($empleado) {
            $empleado->modoTareo = $request->get('estadoP');
            $empleado->save();
        }
    }
}
