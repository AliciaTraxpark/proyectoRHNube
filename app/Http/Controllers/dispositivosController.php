<?php

namespace App\Http\Controllers;

use App\dispositivo_area;
use App\dispositivo_controlador;
use App\dispositivo_empleado;
use App\dispositivos;
use App\eventos_empleado;
use App\horario_empleado;
use App\marcacion_puerta;
use App\pausas_horario;
use App\tardanza;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class dispositivosController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function index()
    {
        $invitadod = DB::table('invitado')
            ->where('user_Invitado', '=', Auth::user()->id)
            ->where('rol_id', '=', 3)
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();


        $controladores = DB::table('controladores')
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('cont_estado', '=', 1)
            ->get();

        $area = DB::table('area as ar')
            ->where('ar.organi_id', '=', session('sesionidorg'))
            ->select(
                'ar.area_id as idarea',
                'area_descripcion as descripcion'
            )
            ->groupBy('ar.area_id')
            ->get();

        /* FILTRAMOS EMPLEADOS */
        if ($invitadod) {

            /* SI EL INVITADO TIENE PERMISO A VER TODOS LOS EMPLEADOS */
            if ($invitadod->verTodosEmps == 1) {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                    ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                    ->where('e.emple_estado', '=', 1)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->where('e.asistencia_puerta', '=', 1)
                    ->get();
            } else {
                /* SI TIENE PERMISO  POR EMPLEADO PERSONALIZADO O POR AREAS */
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();

                /* SI ES PERMISO POR EMPLEADO PERSONALIZADO */
                if ($invitado_empleadoIn != null) {

                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                        ->where('e.emple_estado', '=', 1)
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.asistencia_puerta', '=', 1)
                        ->get();
                } else {
                    /* SI EL PERMISO ES POR AREA */
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                        ->where('e.emple_estado', '=', 1)
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->where('e.asistencia_puerta', '=', 1)
                        ->get();
                }
            }
            /*  */
            if ($invitadod->rol_id != 1) {
                if ($invitadod->asistePuerta == 1) {
                    $permiso_invitado = DB::table('permiso_invitado')
                        ->where('idinvitado', '=', $invitadod->idinvitado)
                        ->get()->first();
                    return view('Dispositivos.dispositivos', [
                        'verPuerta' => $permiso_invitado->verPuerta, 'agregarPuerta' => $permiso_invitado->agregarPuerta,
                        'modifPuerta' => $permiso_invitado->modifPuerta, 'controladores' => $controladores, 'area' => $area, 'empleado' => $empleados
                    ]);
                } else {
                    return redirect('/dashboard');
                }
                /*   */
            } else {
                return view('Dispositivos.dispositivos', ['controladores' => $controladores, 'area' => $area, 'empleado' => $empleados]);
            }
        } else {

            $empleados = DB::table('empleado as e')
                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.emple_estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.asistencia_puerta', '=', 1)
                ->get();

            return view('Dispositivos.dispositivos', ['controladores' => $controladores, 'area' => $area, 'empleado' => $empleados]);
        }
    }
    public function store(Request $request)
    {

        /*   dd($request->tData,$request->lectura); */
        $codigo = STR::random(4);

        $dispositivos = new dispositivos();
        $dispositivos->tipoDispositivo = 2;
        $dispositivos->dispo_descripUbicacion = $request->descripccionUb;
        $dispositivos->dispo_movil = $request->numeroM;
        $dispositivos->dispo_tSincro = $request->tSincron;
        $dispositivos->dispo_tMarca = $request->tMarcac;
        $dispositivos->dispo_estadoActivo = 1;
        $dispositivos->dispo_estado = 0;
        $dispositivos->organi_id = session('sesionidorg');
        $dispositivos->dispo_Data = $request->tData;
        foreach ($request->lectura as $lectura) {
            if ($lectura == 1) {
                $dispositivos->dispo_Manu = 1;
            }
            if ($lectura == 2) {
                $dispositivos->dispo_Scan = 1;
            }

            if ($lectura == 3) {
                $dispositivos->dispo_Cam = 1;
            }
        }

        $dispositivos->save();

        $contro = $request->idContro;
        if ($contro != null) {
            foreach ($contro as $contros) {
                $dispositivo_controlador = new dispositivo_controlador();
                $dispositivo_controlador->idDispositivos = $dispositivos->idDispositivos;
                $dispositivo_controlador->idControladores = $contros;
                $dispositivo_controlador->organi_id = session('sesionidorg');
                $dispositivo_controlador->save();
            }
        }



        if ($request->smsCh == 1) {
            $dispositivosAc = dispositivos::findOrFail($dispositivos->idDispositivos);
            $dispositivosAc->dispo_estado = 1;
            $dispositivosAc->dispo_codigo = $codigo;
            $dispositivosAc->save();


            $nroCel = substr($dispositivosAc->dispo_movil, 2);

            $mensaje = "Dispositivo " . $nroCel . " registrado en RH nube - Modo Asistencia en puerta, tu codigo es " . $codigo . " - Descargalo en https://play.google.com/store/apps/details?id=com.pe.rhnube";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.broadcastermobile.com/brdcstr-endpoint-web/services/messaging/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_FOLLOWLOCATION => TRUE,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => '{
                   "apiKey":2308,
                   "country":"PE",
                   "dial":38383,
                   "message":"' . $mensaje . '",
                   "msisdns":[' .  $dispositivosAc->dispo_movil . '],
                   "tag":"tag-prueba"
                }',
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization:67p7e5ONkalvrKLDQh3RaONgSFs=",
                    "Cache-Control: no-cache"
                ),
            ));
            $err = curl_error($curl);
            $response = curl_exec($curl);
        }
    }
    public function enviarmensaje(Request $request)
    {
        $codigo = STR::random(4);
        $dispositivosAc = dispositivos::findOrFail($request->idDis);
        $dispositivosAc->dispo_estado = 1;
        $dispositivosAc->dispo_codigo = $codigo;
        $dispositivosAc->save();
        $nroCel = substr($dispositivosAc->dispo_movil, 2);

        $mensaje = "Dispositivo " . $nroCel . " registrado en RH nube - Modo Asistencia en puerta, tu codigo es " . $codigo . " - Descargalo en https://play.google.com/store/apps/details?id=com.pe.rhnube";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.broadcastermobile.com/brdcstr-endpoint-web/services/messaging/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{
               "apiKey":2308,
               "country":"PE",
               "dial":38383,
               "message":"' . $mensaje . '",
               "msisdns":[' .  $dispositivosAc->dispo_movil . '],
               "tag":"tag-prueba"
            }',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization:67p7e5ONkalvrKLDQh3RaONgSFs=",
                "Cache-Control: no-cache"
            ),
        ));
        $err = curl_error($curl);
        $response = curl_exec($curl);
    }

    public function reenviarmensaje(Request $request)
    {

        $dispositivosAc = dispositivos::findOrFail($request->idDis);
        $codigo = $dispositivosAc->dispo_codigo;
        $nroCel = substr($dispositivosAc->dispo_movil, 2);

        $mensaje = "Dispositivo " . $nroCel . " registrado en RH nube - Modo Asistencia en puerta, tu codigo es " . $codigo . " - Descargalo en https://play.google.com/store/apps/details?id=com.pe.rhnube";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.broadcastermobile.com/brdcstr-endpoint-web/services/messaging/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{
               "apiKey":2308,
               "country":"PE",
               "dial":38383,
               "message":"' . $mensaje . '",
               "msisdns":[' .  $dispositivosAc->dispo_movil . '],
               "tag":"tag-prueba"
            }',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization:67p7e5ONkalvrKLDQh3RaONgSFs=",
                "Cache-Control: no-cache"
            ),
        ));
        $err = curl_error($curl);
        $response = curl_exec($curl);
    }

    public function tablaDisposit()
    {
        $dispositivos = dispositivos::where('organi_id', '=', session('sesionidorg'))->get();
        return json_encode($dispositivos);
    }

    public function comprobarMovil(Request $request)
    {

        $dispositivos = dispositivos::where('dispo_movil', '=', $request->numeroM)->get()->first();

        if ($dispositivos != null) {
            return 1;
        } else {
            return 0;
        }
    }

    public function reporteMarcaciones()
    {
        $organizacion = DB::table('organizacion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();
        $nombreOrga = $organizacion->organi_razonSocial;
        $ruc = $organizacion->organi_ruc;
        $direccion = $organizacion->organi_direccion;

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
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
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
                if ($invitadod->reporteAsisten == 1) {

                    return view('Dispositivos.reporteDis', [
                        'organizacion' => $nombreOrga, 'empleado' => $empleados, 'modifReporte' => $invitadod->ModificarReportePuerta,
                        'ruc' => $ruc, 'direccion' => $direccion
                    ]);
                } else {
                    return redirect('/dashboard');
                }
                /*   */
            } else {
                return view('Dispositivos.reporteDis', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion]);
            }
        } else {
            return view('Dispositivos.reporteDis', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion]);
        }
    }

    public function reporteTabla(Request $request)
    {
        $fechaR = $request->fecha;
        $idemp = $request->idemp;
        $fecha = Carbon::create($fechaR)->format('Y-m-d');

        function agruparEmpleadosMarcaciones($array)
        {
            $resultado = array();

            foreach ($array as $empleado) {
                if (!isset($resultado[$empleado->emple_id])) {
                    $resultado[$empleado->emple_id] = (object) array(
                        "emple_id" => $empleado->emple_id,
                        "organi_id" => $empleado->organi_id,
                    );
                }
                if (!isset($resultado[$empleado->emple_id]->data)) {
                    $resultado[$empleado->emple_id]->data = array();
                }
                if (!isset($resultado[$empleado->emple_id]->data[$empleado->idHorario]["horario"])) {
                    $resultado[$empleado->emple_id]->data[$empleado->idHorario]["horario"] =  (object) array(
                        "horario" => $empleado->detalleHorario,
                        "horarioIni" => $empleado->horarioIni,
                        "horarioFin" => $empleado->horarioFin,
                        "idHorario" => $empleado->idHorario,
                        "toleranciaI" => $empleado->toleranciaI,
                        "toleranciaF" => $empleado->toleranciaF,
                        "idHorarioE" => $empleado->idHorarioE,
                        "estado" => $empleado->estado,
                        "horasObligadas" => $empleado->horasObligadas
                    );
                }
                if (!isset($resultado[$empleado->emple_id]->data[$empleado->idHorario]["pausas"])) {
                    $resultado[$empleado->emple_id]->data[$empleado->idHorario]["pausas"] = array();
                }
                if (!isset($resultado[$empleado->emple_id]->incidencias)) {
                    $resultado[$empleado->emple_id]->incidencias = array();
                }
                if (!isset($resultado[$empleado->emple_id]->data[$empleado->idHorario]["marcaciones"])) {
                    $resultado[$empleado->emple_id]->data[$empleado->idHorario]["marcaciones"] = array();
                }
                $arrayMarcacion = (object) array(
                    "idMarcacion" => $empleado->idMarcacion,
                    "entrada" => $empleado->entrada,
                    "salida" => $empleado->salida,
                    "idH" => $empleado->idHorario,
                    "idHE" => $empleado->idHorarioE,
                    "dispositivoEntrada" => ucfirst(strtolower($empleado->dispositivoEntrada)),
                    "dispositivoSalida" => ucfirst(strtolower($empleado->dispositivoSalida))
                );
                array_push($resultado[$empleado->emple_id]->data[$empleado->idHorario]["marcaciones"], $arrayMarcacion);
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
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                        ->select(
                            'e.emple_id',
                            'e.emple_nDoc',
                            'e.emple_codigo',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'o.organi_razonSocial',
                            'o.organi_direccion',
                            'o.organi_ruc',
                            'e.emple_estado',
                            'e.organi_id'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->orderBy('p.perso_nombre', 'ASC')
                        ->get();
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->select(
                            'e.emple_id',
                            'e.emple_nDoc',
                            'e.emple_codigo',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'o.organi_razonSocial',
                            'o.organi_direccion',
                            'o.organi_ruc',
                            'e.emple_estado',
                            'e.organi_id'
                        )
                        ->where('e.emple_id', $idemp)
                        ->get();
                }
            } else {
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();
                if ($invitado_empleadoIn != null) {
                    if ($idemp == 0 || $idemp == ' ') {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->select(
                                'e.emple_id',
                                'e.emple_nDoc',
                                'e.emple_codigo',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'o.organi_razonSocial',
                                'o.organi_direccion',
                                'o.organi_ruc',
                                'e.emple_estado',
                                'e.organi_id'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->orderBy('p.perso_nombre', 'ASC')
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->select(
                                'e.emple_id',
                                'e.emple_nDoc',
                                'e.emple_codigo',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'o.organi_razonSocial',
                                'o.organi_direccion',
                                'o.organi_ruc',
                                'e.emple_estado',
                                'e.organi_id'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where('e.emple_id', $idemp)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();
                    }
                } else {
                    if ($idemp == 0 || $idemp == ' ') {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->select(
                                'e.emple_id',
                                'e.emple_nDoc',
                                'e.emple_codigo',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'o.organi_razonSocial',
                                'o.organi_direccion',
                                'o.organi_ruc',
                                'e.emple_estado',
                                'e.organi_id'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->orderBy('p.perso_nombre', 'ASC')
                            ->get();
                    } else {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                            ->select(
                                'e.emple_id',
                                'e.emple_nDoc',
                                'e.emple_codigo',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'o.organi_razonSocial',
                                'o.organi_direccion',
                                'o.organi_ruc',
                                'e.emple_estado',
                                'e.organi_id'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where('e.emple_id', $idemp)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->get();
                    }
                }
            }
        } else {
            if ($idemp == 0 || $idemp == ' ') {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_nDoc',
                        'e.emple_codigo',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion',
                        'o.organi_razonSocial',
                        'o.organi_direccion',
                        'o.organi_ruc',
                        'e.emple_estado',
                        'e.organi_id'
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->orderBy('p.perso_nombre', 'ASC')
                    ->get();
            } else {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_nDoc',
                        'e.emple_codigo',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion',
                        'o.organi_razonSocial',
                        'o.organi_direccion',
                        'o.organi_ruc',
                        'e.emple_estado',
                        'e.organi_id'
                    )
                    ->where('e.emple_id', $idemp)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->get();
            }
        }
        $marcaciones = [];
        // DB::enableQueryLog();
        $tipoDispositivo = DB::table('dispositivos as d')
            ->leftJoin('tipo_dispositivo as td', 'td.id', '=', 'd.tipoDispositivo')
            ->select(
                'd.idDispositivos',
                'td.dispositivo_descripcion as dispositivo'
            );
        $data =  DB::table('empleado as e')
            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
            ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
            ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
            ->leftJoinSub($tipoDispositivo, 'entrada', function ($join) {
                $join->on('mp.dispositivoEntrada', '=', 'entrada.idDispositivos');
            })
            ->leftJoinSub($tipoDispositivo, 'salida', function ($join) {
                $join->on('mp.dispositivoSalida', '=', 'salida.idDispositivos');
            })
            ->select(
                'e.emple_id',
                'mp.marcaMov_id',
                'mp.organi_id',
                DB::raw("IF(entrada.dispositivo is null, 'MANUAL' , entrada.dispositivo) as dispositivoEntrada"),
                DB::raw("IF(salida.dispositivo is null, 'MANUAL' , salida.dispositivo) as dispositivoSalida"),
                DB::raw('IF(hor.horario_id is null, 0 , horario_id) as idHorario'),
                DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                DB::raw("IF(hor.horaF is null , 0 , IF(hor.horaF > hor.horaI,CONCAT( DATE(hd.start),' ', hor.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', hor.horaF))) as horarioFin"),
                DB::raw("IF(hor.horaI is null , null , horario_descripcion) as detalleHorario"),
                DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                DB::raw('IF(hoe.horarioEmp_id is null, 0 , hoe.horarioEmp_id) as idHorarioE'),
                'hor.horario_tolerancia as toleranciaI',
                'hor.horario_toleranciaF as toleranciaF',
                'mp.marcaMov_id as idMarcacion',
                'hor.horasObliga as horasObligadas',
                'hoe.estado'
            )
            ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)
            ->where('mp.organi_id', '=', session('sesionidorg'))
            ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC', 'p.perso_nombre', 'ASC')
            ->get();
        // dd(DB::getQueryLog());
        // dd($data);
        $data = agruparEmpleadosMarcaciones($data);  //: CONVERTIR UN SOLO EMPLEADO CON VARIOS MARCACIONES

        // * UNIR EMPLEADOS CON MARCACIONES

        for ($index = 0; $index < sizeof($empleados); $index++) {
            $ingreso = true;
            for ($element = 0; $element < sizeof($data); $element++) {
                if ($empleados[$index]->emple_id == $data[$element]->emple_id) {    //: BUSCAMOS EL ID EMPLEADO IGUAL
                    $ingreso = false;
                    $arrayNuevo = (object) array(
                        "emple_id" => $empleados[$index]->emple_id,
                        "emple_nDoc" => $empleados[$index]->emple_nDoc,
                        "emple_codigo" => empty($empleados[$index]->emple_codigo) == true ? "---" : $empleados[$index]->emple_codigo,
                        "perso_nombre" => $empleados[$index]->perso_nombre,
                        "perso_apPaterno" => $empleados[$index]->perso_apPaterno,
                        "perso_apMaterno" => $empleados[$index]->perso_apMaterno,
                        "cargo_descripcion" => empty($empleados[$index]->cargo_descripcion) == true ? "---" : $empleados[$index]->cargo_descripcion,
                        "organi_id" => $data[$element]->organi_id,
                        "organi_razonSocial" => $empleados[$index]->organi_razonSocial,
                        "organi_direccion" =>  $empleados[$index]->organi_direccion,
                        "organi_ruc" => $empleados[$index]->organi_ruc,
                        "emple_estado" => $empleados[$index]->emple_estado,
                        "data" => $data[$element]->data,
                        "incidencias" => array()
                    );
                    array_push($marcaciones, $arrayNuevo);
                }
            }
            if ($ingreso && $empleados[$index]->emple_estado == 1) {         //: VALIDAMOS PARA EMPLEADOS QUE NO TIENEN DATA DE ESA FECHA
                $arrayNuevo = (object) array(
                    "emple_id" => $empleados[$index]->emple_id,
                    "emple_nDoc" => $empleados[$index]->emple_nDoc,
                    "emple_codigo" => empty($empleados[$index]->emple_codigo) == true ? "---" : $empleados[$index]->emple_codigo,
                    "perso_nombre" => $empleados[$index]->perso_nombre,
                    "perso_apPaterno" => $empleados[$index]->perso_apPaterno,
                    "perso_apMaterno" => $empleados[$index]->perso_apMaterno,
                    "cargo_descripcion" => empty($empleados[$index]->cargo_descripcion) == true ? "---" : $empleados[$index]->cargo_descripcion,
                    "organi_id" => $empleados[$index]->organi_id,
                    "organi_razonSocial" => $empleados[$index]->organi_razonSocial,
                    "organi_direccion" =>  $empleados[$index]->organi_direccion,
                    "organi_ruc" => $empleados[$index]->organi_ruc,
                    "emple_estado" => $empleados[$index]->emple_estado,
                    "data" => array(),
                    "incidencias" => array()
                );
                array_push($marcaciones, $arrayNuevo);
            }
        }
        // * AGREGAR HORARIOS EMPLEADO ASIGNADO POR DIA PARA FALTAS
        foreach ($marcaciones as $m) {
            // * HORARIO EMPLEADO
            $m->data = array_values($m->data);
            $arrayHorarioE = [];
            foreach ($m->data as $key => $horario) {
                if ($horario["horario"]->idHorarioE != 0) {
                    array_push($arrayHorarioE, $horario["horario"]->idHorarioE);
                }
            }
            $horarioEmpleado = DB::table('horario_empleado as he')
                ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->select(
                    'h.horario_descripcion as horario',
                    DB::raw('CONCAT("' . $fecha . '" , " ",h.horaI) as horarioIni'),
                    DB::raw('CONCAT("' . $fecha . '" , " ",h.horaF) as horarioFin'),
                    'h.horario_id  as idHorario',
                    'h.horario_tolerancia as toleranciaI',
                    'h.horario_toleranciaF as toleranciaF',
                    'he.estado as estado'
                )
                ->whereNotIn('he.horarioEmp_id', $arrayHorarioE)
                ->where('empleado_emple_id', '=', $m->emple_id)
                ->where(DB::raw('DATE(hd.start)'), '=', $fecha)
                ->where('he.estado', '=', 1)
                ->get();

            foreach ($horarioEmpleado as $he) {
                array_push($m->data, array("horario" => $he, "pausas" => array(), "marcaciones" => array()));
            }
        }
        // * AGREGAR ATRIBUTOS DE HORARIO Y PAUSAS EN CADA HORARIO
        foreach ($marcaciones as  $m) {
            $m->data = array_values($m->data);
            // * ********************* PAUSAS ********************
            foreach ($m->data as $key => $d) {
                if ($d["horario"]->idHorario != 0) {
                    // * AÑADIR PAUSAS DEL HORARIO
                    $pausas = DB::table('pausas_horario')->select(
                        'idpausas_horario as id',
                        'pausH_descripcion as descripcion',
                        'pausH_Inicio as inicio',
                        'pausH_Fin as fin',
                        'tolerancia_inicio',
                        'tolerancia_fin',
                        'horario_id'
                    )
                        ->where('horario_id', '=', $d["horario"]->idHorario)->get();
                    foreach ($pausas as $p) {
                        array_push($m->data[$key]["pausas"], $p);
                    }
                }
            }
            // * ******************* INCIDENCIAS *********************
            $idEmpleado = $m->emple_id;
            // * TABLA EVENTOS EMPLEADO
            $eventos = eventos_empleado::select('title as descripcion')
                ->where(DB::raw('DATE(start)'), '=', $fecha)
                ->where('id_empleado', '=', $idEmpleado)
                ->get();
            foreach ($eventos as $e) {
                array_push($m->incidencias, $e);
            }
            // * TABLA INCIDENCIAS DIA
            $incidencias = DB::table('incidencia_dias as id')
                ->join('incidencias as i', 'i.inciden_id', '=', 'id.id_incidencia')
                ->select('i.inciden_descripcion as descripcion')
                ->where(DB::raw('DATE(id.inciden_dias_fechaI)'), '=', $fecha)
                ->where('id.id_empleado', '=', $idEmpleado)
                ->get();
            foreach ($incidencias as $i) {
                array_push($m->incidencias, $i);
            }
        }

        return response()->json($marcaciones, 200);
    }

    public function datosDispoEditar(Request $request)
    {
        $idDispo = $request->id;
        $dispositivo = dispositivos::where('dispositivos.organi_id', '=', session('sesionidorg'))

            ->where('dispositivos.idDispositivos', $idDispo)
            ->select(
                'dispositivos.idDispositivos',
                'tipoDispositivo',
                'dispo_descripUbicacion',
                'dispo_movil',
                'dispo_tSincro',
                'dispo_tMarca',
                'dispo_Data',
                'dispo_Manu',
                'dispo_Scan',
                'dispo_Cam',
                /*        'idControladores', */
                'version_firmware',
                'dispo_codigo',
                'dispo_todosEmp',
                'dispo_porEmp',
                'dispo_porArea'
            )->get();
        foreach ($dispositivo as  $dispositivos) {
            $disposit_controlador = DB::table('dispositivo_controlador as dc')
                ->select('idControladores')
                ->where('idDispositivos', '=', $dispositivos->idDispositivos)
                ->get();

            $dispositivos->idControladores =  $disposit_controlador;
        }
        $dispositivo_empleado = dispositivo_empleado::where('idDispositivos', '=', $idDispo)
            ->where('estado', '=', 1)->get();
        /*  if($dispositivo_empleado->isNotEmpty()){

            else{

            } */
        $dispositivo_area = dispositivo_area::where('idDispositivos', '=', $idDispo)
            ->where('estado', '=', 1)->get();
        return [$dispositivo[0], $dispositivo_empleado, $dispositivo_area];
    }

    public function actualizarDispos(Request $request)
    {
        $dispositivos = dispositivos::findOrFail($request->idDisposEd_ed);
        $dispositivos->dispo_descripUbicacion = $request->descripccionUb_ed;
        $dispositivos->dispo_movil = $request->numeroM_ed;
        $dispositivos->dispo_tSincro = $request->tSincron_ed;
        $dispositivos->dispo_tMarca = $request->tMarca_ed;
        $dispositivos->dispo_Data = $request->tData_ed;
        foreach ($request->lectura_ed as $lectura) {
            if ($lectura == 1) {
                $dispositivos->dispo_Manu = 1;
            }
            if ($lectura == 2) {
                $dispositivos->dispo_Scan = 1;
            }

            if ($lectura == 3) {
                $dispositivos->dispo_Cam = 1;
            }
        }
        $dispositivos->save();

        $idcont_id = $request->idcont_id;
        $borrarDispo = dispositivo_controlador::where('idDispositivos', '=', $request->idDisposEd_ed)
            ->where('organi_id', '=', session('sesionidorg'))->get();
        if ($borrarDispo) {
            $borrarDispo->each->delete();
        }
        if ($idcont_id != null) {
            foreach ($idcont_id as $contros) {
                $dispositivo_controlador = new dispositivo_controlador();
                $dispositivo_controlador->idDispositivos = $request->idDisposEd_ed;
                $dispositivo_controlador->idControladores = $contros;
                $dispositivo_controlador->organi_id = session('sesionidorg');
                $dispositivo_controlador->save();
            }
        }
    }

    public function desactivarDisposi(Request $request)
    {

        $dispositivos = dispositivos::findOrFail($request->idDisDesac);
        $dispositivos->dispo_estadoActivo = 0;
        $dispositivos->save();
    }

    public function activarDisposi(Request $request)
    {

        $dispositivos = dispositivos::findOrFail($request->idDisAct);
        $dispositivos->dispo_estadoActivo = 1;
        $dispositivos->save();
    }

    public function reporteMarcacionesEmp()
    {
        $organizacion = DB::table('organizacion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();
        $nombreOrga = $organizacion->organi_razonSocial;
        $ruc = $organizacion->organi_ruc;
        $direccion = $organizacion->organi_direccion;

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
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
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
                if ($invitadod->reporteAsisten == 1) {

                    return view('Dispositivos.reporteEmpleado', [
                        'organizacion' => $nombreOrga, 'empleado' => $empleados, 'modifReporte' => $invitadod->ModificarReportePuerta,
                        'ruc' => $ruc, 'direccion' => $direccion
                    ]);
                } else {
                    return redirect('/dashboard');
                }
                /*   */
            } else {
                return view('Dispositivos.reporteEmpleado', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion]);
            }
        } else {
            return view('Dispositivos.reporteEmpleado', ['organizacion' => $nombreOrga, 'empleado' => $empleados, 'ruc' => $ruc, 'direccion' => $direccion]);
        }
    }

    public function ReporteFecha()
    {
        $organizacion = DB::table('organizacion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();
        $nombreOrga = $organizacion->organi_razonSocial;
        $ruc = $organizacion->organi_ruc;
        $direccion = $organizacion->organi_direccion;

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
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
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
                if ($invitadod->reporteAsisten == 1) {

                    return view('Dispositivos.reporteFecha', [
                        'organizacion' => $nombreOrga, 'empleado' => $empleados, 'modifReporte' => $invitadod->ModificarReportePuerta,
                        'ruc' => $ruc, 'direccion' => $direccion
                    ]);
                } else {
                    return redirect('/dashboard');
                }
                /*   */
            } else {
                return view('Dispositivos.reporteFecha', ['organizacion' => $nombreOrga, 'empleado' => $empleados,  'ruc' => $ruc, 'direccion' => $direccion]);
            }
        } else {
            return view('Dispositivos.reporteFecha', ['organizacion' => $nombreOrga, 'empleado' => $empleados,  'ruc' => $ruc, 'direccion' => $direccion]);
        }
    }

    public function reporteTablaEmp(Request $request)
    {
        $fechaR = $request->fecha1;
        $idemp = $request->idemp;
        $fecha = Carbon::create($fechaR);

        $fecha2 = $request->fecha2;
        $fechaF = Carbon::create($fecha2);

        function agruparPorFechayHorario($array)
        {
            $resultado = array();

            foreach ($array as $empleado) {
                if (!isset($resultado[$empleado->emple_id])) {
                    $resultado[$empleado->emple_id] = (object) array(
                        "organi_id" => $empleado->organi_id,
                        "organi_razonSocial" => $empleado->organi_razonSocial,
                        "organi_direccion" => $empleado->organi_direccion,
                        "organi_ruc" => $empleado->organi_ruc,
                        "emple_id" => $empleado->emple_id,
                        "area" => $empleado->area_descripcion,
                        "nDoc" => $empleado->emple_nDoc,
                        "nombre" => $empleado->perso_nombre,
                        "apPaterno" => $empleado->perso_apPaterno,
                        "apMaterno" => $empleado->perso_apMaterno,
                        "cargo_descripcion" => $empleado->cargo_descripcion,
                        "area_descripcion" => $empleado->area_descripcion
                    );
                }
                if (!isset($resultado[$empleado->emple_id]->datos)) {
                    $resultado[$empleado->emple_id]->datos = array();
                }
                if (!isset($resultado[$empleado->emple_id]->datos[$empleado->entradaModif])) {
                    $resultado[$empleado->emple_id]->datos[$empleado->entradaModif] =  array();
                }
                if (!isset($resultado[$empleado->emple_id]->datos[$empleado->entradaModif][$empleado->idhorario])) {
                    $resultado[$empleado->emple_id]->datos[$empleado->entradaModif][$empleado->idhorario] = (object)array(
                        "idHorario" => $empleado->idhorario,
                        "horario" => $empleado->horario,
                        "fecha" => $empleado->entradaModif,
                        "tolerancia" => $empleado->tolerancia,
                        "horarioIni" => $empleado->horarioIni,
                        "horarioFin" => $empleado->horarioFin
                    );
                }
                if (!isset($resultado[$empleado->emple_id]->datos[$empleado->entradaModif][$empleado->idhorario]->marcaciones)) {
                    $resultado[$empleado->emple_id]->datos[$empleado->entradaModif][$empleado->idhorario]->marcaciones = array();
                }
                $arrayMarcaciones = (object) array(
                    "idMarcacion" => $empleado->idMarcacion,
                    "entrada" => $empleado->entrada,
                    "salida" => $empleado->salida,
                    "idHorario" => $empleado->idhorario,
                );
                array_push($resultado[$empleado->emple_id]->datos[$empleado->entradaModif][$empleado->idhorario]->marcaciones, $arrayMarcaciones);
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

                $marcaciones = DB::table('empleado as e')
                    ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                    ->leftJoin('actividad as acti', 'mp.marcaIdActivi', '=', 'acti.Activi_id')
                    ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                    ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                    ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                    ->select(
                        'e.emple_id',
                        'o.organi_razonSocial',
                        'o.organi_direccion',
                        'o.organi_ruc',
                        DB::raw('IF(mp.marcaMov_fecha is null,DATE(mp.marcaMov_salida) ,DATE(mp.marcaMov_fecha)) as entradaModif'),
                        DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id) as idhorario'),
                        'ar.area_descripcion',
                        'e.emple_nDoc',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion',
                        'mp.organi_id',
                        DB::raw('IF(hor.horario_id is null, 0 , hor.horario_descripcion) as horario'),
                        DB::raw("IF(hor.horaF is null , 0 , IF(hor.horaF > hor.horaI,CONCAT( DATE(hd.start),' ', hor.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', hor.horaF))) as horarioFin"),
                        DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                        'mp.marcaMov_id as idMarcacion',
                        'hor.horario_tolerancia as tolerancia',
                        DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                        DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida')
                    )
                    ->whereBetween(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), [$fecha, $fechaF])
                    ->where('e.emple_id', $idemp)
                    ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC')
                    ->where('mp.organi_id', '=', session('sesionidorg'))
                    ->get();
                $marcaciones = agruparPorFechayHorario($marcaciones);
            } else {
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();
                if ($invitado_empleadoIn != null) {

                    $marcaciones = DB::table('empleado as e')
                        ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                        ->leftJoin('actividad as acti', 'mp.marcaIdActivi', '=', 'acti.Activi_id')
                        ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                        ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                        ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                        ->select(
                            'e.emple_id',
                            'o.organi_razonSocial',
                            'o.organi_direccion',
                            'o.organi_ruc',
                            DB::raw('IF(mp.marcaMov_fecha is null,DATE(mp.marcaMov_salida) ,DATE(mp.marcaMov_fecha)) as entradaModif'),
                            DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id) as idhorario'),
                            'ar.area_descripcion',
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'mp.organi_id',
                            DB::raw('IF(hor.horario_id is null, 0 , hor.horario_descripcion) as horario'),
                            DB::raw("IF(hor.horaF is null , 0 , IF(hor.horaF > hor.horaI,CONCAT( DATE(hd.start),' ', hor.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', hor.horaF))) as horarioFin"),
                            DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                            'mp.marcaMov_id as idMarcacion',
                            'hor.horario_tolerancia as tolerancia',
                            DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                            DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida')
                        )
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->whereBetween(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), [$fecha, $fechaF])
                        ->where('e.emple_id', $idemp)
                        ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC')
                        ->where('mp.organi_id', '=', session('sesionidorg'))
                        ->get();
                    $marcaciones = agruparPorFechayHorario($marcaciones);
                } else {

                    $marcaciones = DB::table('empleado as e')
                        ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('actividad as acti', 'mp.marcaIdActivi', '=', 'acti.Activi_id')
                        ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                        ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                        ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                        ->select(
                            'e.emple_id',
                            'o.organi_razonSocial',
                            'o.organi_direccion',
                            'o.organi_ruc',
                            DB::raw('IF(mp.marcaMov_fecha is null,DATE(mp.marcaMov_salida) ,DATE(mp.marcaMov_fecha)) as entradaModif'),
                            DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id) as idhorario'),
                            'ar.area_descripcion',
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'mp.organi_id',
                            DB::raw('IF(hor.horario_id is null, 0 , hor.horario_descripcion) as horario'),
                            DB::raw("IF(hor.horaF is null , 0 , IF(hor.horaF > hor.horaI,CONCAT( DATE(hd.start),' ', hor.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', hor.horaF))) as horarioFin"),
                            DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                            'mp.marcaMov_id as idMarcacion',
                            'hor.horario_tolerancia as tolerancia',
                            DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                            DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida')
                        )
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->whereBetween(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), [$fecha, $fechaF])
                        ->where('e.emple_id', $idemp)
                        ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC')
                        ->where('mp.organi_id', '=', session('sesionidorg'))
                        ->get();
                    $marcaciones = agruparPorFechayHorario($marcaciones);
                }
            }
        } else {
            $marcaciones = DB::table('empleado as e')
                ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                ->leftJoin('actividad as acti', 'mp.marcaIdActivi', '=', 'acti.Activi_id')
                ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                ->select(
                    'e.emple_id',
                    'o.organi_razonSocial',
                    'o.organi_direccion',
                    'o.organi_ruc',
                    DB::raw('IF(mp.marcaMov_fecha is null,DATE(mp.marcaMov_salida) ,DATE(mp.marcaMov_fecha)) as entradaModif'),
                    DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id) as idhorario'),
                    'ar.area_descripcion',
                    'e.emple_nDoc',
                    'p.perso_nombre',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    'c.cargo_descripcion',
                    'mp.organi_id',
                    DB::raw('IF(hor.horario_id is null, 0 , hor.horario_descripcion) as horario'),
                    DB::raw("IF(hor.horaF is null , 0 , IF(hor.horaF > hor.horaI,CONCAT( DATE(hd.start),' ', hor.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', hor.horaF))) as horarioFin"),
                    DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                    'mp.marcaMov_id as idMarcacion',
                    'hor.horario_tolerancia as tolerancia',
                    DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                    DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida')
                )
                ->whereBetween(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), [$fecha, $fechaF])
                ->where('e.emple_id', $idemp)
                ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC')
                ->where('mp.organi_id', '=', session('sesionidorg'))
                ->get();
            $marcaciones = agruparPorFechayHorario($marcaciones);
        }
        // * ***************** TODAS LAS FECHAS **********************
        $period = Carbon::parse($fecha)->toPeriod($fechaF);
        $dates = [];
        foreach ($period as $key => $date) {
            array_push($dates, $date->format('Y-m-d'));
        }
        // : RECORREMOS FECHAS
        foreach ($dates as $d) {
            // : RECORREMOS MARCACIONES
            foreach ($marcaciones as $key => $m) {
                // : BUSCAMOS SI YA EXISTE LA FECHA EN EL ARRAY
                if (array_key_exists($d, $m->datos)) {
                    $horarios = array_keys($m->datos[$d]);   // : OBTENEMOS TODOS LOS HORARIOS DE ESA FECHA
                    $clave = array_search(0, $horarios);     // : BUSCAMOS HORARIOS CON ID 0
                    if (!is_bool($clave)) {
                        unset($horarios[$clave]);            // : DESCARTAMOS LOS HORARIOS CON ID 0
                    }
                    $horarioEmpleado = DB::table('horario_empleado as he')
                        ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                        ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                        ->select(
                            'h.horario_id as idHorario',
                            'h.horario_descripcion as horario',
                            DB::raw('DATE(hd.start) as fecha'),
                            'h.horario_tolerancia as tolerancia',
                            DB::raw("IF(h.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', h.horaI)) as horarioIni"),
                            DB::raw("IF(h.horaF is null , 0 , IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF))) as horarioFin")
                        )
                        ->where(DB::raw('DATE(hd.start)'), '=', $d)
                        ->where('he.empleado_emple_id', '=', $idemp)
                        ->whereNotIn('h.horario_id', $horarios)
                        ->where('he.estado', '=', 1)
                        ->get();
                    foreach ($horarioEmpleado as $he) {
                        // : AGREGAMOS LOS HORARIOS QUE FALTA EN ESA FECHA
                        $he->marcaciones = array();
                        $marcaciones[$key]->datos[$d][$he->idHorario] = $he;
                    }
                } else {
                    $horarioEmpleado = DB::table('horario_empleado as he')
                        ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                        ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                        ->select(
                            'h.horario_id as idHorario',
                            'h.horario_descripcion as horario',
                            DB::raw('DATE(hd.start) as fecha'),
                            'h.horario_tolerancia as tolerancia',
                            DB::raw("IF(h.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', h.horaI)) as horarioIni"),
                            DB::raw("IF(h.horaF is null , 0 , IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF))) as horarioFin")
                        )
                        ->where(DB::raw('DATE(hd.start)'), '=', $d)
                        ->where('he.empleado_emple_id', '=', $idemp)
                        ->where('he.estado', '=', 1)
                        ->get();
                    if (sizeof($horarioEmpleado) != 0) {
                        $marcaciones[$key]->datos[$d] = array();
                        foreach ($horarioEmpleado as $he) {
                            // : AGREGAMOS LOS HORARIOS QUE FALTA EN ESA FECHA
                            $he->marcaciones = array();
                            $marcaciones[$key]->datos[$d][$he->idHorario] = $he;
                        }
                    }
                }
            }
            // : RECORREMOS PARA INCIDENCIA
            foreach ($marcaciones as $key => $m) {
                // * *********************** INCIDENCIAS ***********************
                $idEmpleado = $m->emple_id;
                // * TABLA EVENTOS EMPLEADO
                $eventos = eventos_empleado::select('title as descripcion')
                    ->where(DB::raw('DATE(start)'), '=', $d)
                    ->where('id_empleado', '=', $idEmpleado)
                    ->get();
                // * TABLA INCIDENCIAS DIA
                $incidencias = DB::table('incidencia_dias as id')
                    ->join('incidencias as i', 'i.inciden_id', '=', 'id.id_incidencia')
                    ->select('i.inciden_descripcion as descripcion')
                    ->where(DB::raw('DATE(id.inciden_dias_fechaI)'), '=', $d)
                    ->where('id.id_empleado', '=', $idEmpleado)
                    ->get();
                if (array_key_exists($d, $m->datos)) {
                    $horarios = array_keys($m->datos[$d]);
                    foreach ($horarios as $h) {
                        $marcaciones[$key]->datos[$d][$h]->incidencias = array();
                        foreach ($eventos as $e) {
                            array_push($marcaciones[$key]->datos[$d][$h]->incidencias, $e);
                        }
                        foreach ($incidencias as $i) {
                            array_push($marcaciones[$key]->datos[$d][$h]->incidencias, $i);
                        }
                    }
                } else {
                    // : REGISTRAMOS LA FECHA SI SOLO TIENE EVENTOS O INCIDENCIAS
                    if (sizeof($eventos) != 0 || sizeof($incidencias) != 0) {
                        $marcaciones[$key]->datos[$d][0] = (object)array(
                            "idHorario" => 0,
                            "horario" => 0,
                            "fecha" => $d,
                            "tolerancia" => NULL,
                            "horarioIni" => 0,
                            "horarioFin" => 0,
                            "marcaciones" => array(),
                            "incidencias" => array(),
                            "pausas" => array()
                        );
                        foreach ($eventos as $e) {
                            array_push($marcaciones[$key]->datos[$d][0]->incidencias, $e);
                        }
                        foreach ($incidencias as $i) {
                            array_push($marcaciones[$key]->datos[$d][0]->incidencias, $i);
                        }
                    }
                }
            }
        }
        foreach ($marcaciones as $m) {
            ksort($m->datos);
            $m->datos = array_values($m->datos);
            foreach ($m->datos as $key => $datos) {
                $m->datos[$key] = array_values($m->datos[$key]);
                foreach ($m->datos[$key] as $item => $valor) {
                    $idHorario = $valor->idHorario;
                    // * PAUSAS
                    $pausas = pausas_horario::select(
                        'idpausas_horario  as id',
                        'pausH_descripcion as descripcion',
                        'pausH_Inicio as inicio',
                        'pausH_Fin as fin',
                        'tolerancia_inicio as toleranciaI',
                        'tolerancia_fin as toleranciaF',
                        'horario_id as idH'
                    )
                        ->where('horario_id', '=', $idHorario)
                        ->get();
                    $arrayP = [];
                    foreach ($pausas as $p) {
                        array_push($arrayP, $p);
                    }
                    $m->datos[$key][$item]->pausas = $arrayP;
                }
            }
            $m->datos = Arr::flatten($m->datos);
        }
        return response()->json((array)Arr::first($marcaciones), 200);
    }

    public function registrarNTardanza(Request $request)
    {
        $idtardanza = $request->idtardanza;
        $hora = $request->hora;
        /*  $fecha = $request->fecha;
        $fecha1 = Carbon::create($fecha); */

        $tardanza = tardanza::findOrFail($idtardanza);
        $tardanza->tiempoTardanza = $hora;
        $tardanza->save();
        return 1;
    }

    public function editarRowEntrada(Request $request)
    {
        $idmarcacion = $request->idmarcacion;
        $hora = $request->hora;
        $fecha = $request->fecha;
        $fecha1 = Carbon::create($fecha);

        $marcacion_puerta = marcacion_puerta::findOrFail($idmarcacion);
        $fechaSalida = $marcacion_puerta->marcaMov_salida;

        /* MARCACION ANTERIOR  */
        $fecha111 = Carbon::create($marcacion_puerta->marcaMov_fecha)->toDateString();

        $marcacion_puerta001 = DB::table('marcacion_puerta as mv')
            ->where('mv.marcaMov_emple_id', '=', $marcacion_puerta->marcaMov_emple_id)
            ->where('mv.marcaMov_id', '!=', $idmarcacion)
            ->where(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '<', $marcacion_puerta->marcaMov_fecha)
            ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha111)
            ->orderBy(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)', 'ASC'))
            ->get()->last();
        $marcacion_puerta00 = DB::table('marcacion_puerta as mv')
            ->where('mv.marcaMov_emple_id', '=', $marcacion_puerta->marcaMov_emple_id)
            ->where('mv.marcaMov_id', '!=', $idmarcacion)
            ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha111)
            ->orderBy(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)', 'ASC'))
            ->get()->last();
        /*  */
        if ($fechaSalida != null) {

            /*   IF HORA DE ENTRADA ES MAYOR O IGUAL QUE HORA DE SALIDA */
            if ($fecha1->gte($fechaSalida)) {
                return [0, 'Hora de entrada debe ser menor a la hora de salida.'];
            }
            /* AQUI VALIDAMOS PPUES */ else {

                /* IF EXISTE MARCACION ANTERIOR */
                if ($marcacion_puerta001) {
                    $fechaEAnte = Carbon::create($marcacion_puerta001->marcaMov_fecha);
                    $fechaSAnte = Carbon::create($marcacion_puerta001->marcaMov_salida);

                    /* IF LAS MACACIONES SON DIF DE NULL O SI NO  */
                    if ($marcacion_puerta001->marcaMov_fecha != null && $marcacion_puerta001->marcaMov_salida != null) {

                        /* IF TENGO LAS 2 MARCACIONES MAYORES A LA HORA ACTUAL O SI NO TENGO  */
                        if ($fecha1->gt($fechaEAnte) && $fecha1->gt($fechaSAnte)) {
                            if ($marcacion_puerta00) {
                                $fechaEAnte1 = Carbon::create($marcacion_puerta00->marcaMov_fecha);
                                $fechaSAnte1 = Carbon::create($marcacion_puerta00->marcaMov_salida);

                                /* IF LAS MACACIONES SON DIF DE NULL O SI NO  */
                                if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida != null) {

                                    /* IF TENGO LAS 2 MARCACIONES MAYORES A LA HORA ACTUAL O SI NO TENGO  */
                                    if ($fecha1->gt($fechaEAnte1) && $fecha1->gt($fechaSAnte1)) {
                                        $marcacion_puerta->marcaMov_fecha = $fecha1;
                                        $marcacion_puerta->save();
                                        return 1;
                                    } else {
                                        /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                        return [0, 'Tienes registrado la ultima  marcacion marcacion, la hora de entrada debe ser mayor a las de la anterior marcacion'];
                                    }
                                } else {  /* IF MARCACION ENTRADA es dif de null */
                                    /*  dd($marcacion_puerta001); */
                                    if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida == null) {
                                        if ($fecha1->gt($fechaEAnte1)) {
                                            $marcacion_puerta->marcaMov_fecha = $fecha1;
                                            $marcacion_puerta->save();
                                            return 1;
                                        } else {
                                            /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                            return [0, 'Tienes registrado la ultima  marcacion, la hora de entrada debe ser mayor a la entrada de la ant marcacion'];
                                        }
                                    } else {
                                        if ($fecha1->gt($fechaSAnte1)) {
                                            $marcacion_puerta->marcaMov_fecha = $fecha1;
                                            $marcacion_puerta->save();
                                            return 1;
                                        } else {
                                            /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                            return [0, 'Tienes registrado la ultima marcacion, la hora de entrada debe ser mayor a la salida de la ant marcacion'];
                                        }
                                    }
                                }
                            } else {
                                $marcacion_puerta->marcaMov_fecha = $fecha1;
                                $marcacion_puerta->save();
                                return 1;
                            }
                        } else {
                            /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                            return [0, 'Tienes registrado otra marcacion, la hora de entrada debe ser mayor a las de la anterior marcacion'];
                        }
                    } else {  /* IF MARCACION ENTRADA es dif de null */
                        /*  dd($marcacion_puerta001); */
                        if ($marcacion_puerta001->marcaMov_fecha != null && $marcacion_puerta001->marcaMov_salida == null) {
                            if ($fecha1->gt($fechaEAnte)) {
                                if ($marcacion_puerta00) {
                                    $fechaEAnte1 = Carbon::create($marcacion_puerta00->marcaMov_fecha);
                                    $fechaSAnte1 = Carbon::create($marcacion_puerta00->marcaMov_salida);

                                    /* IF LAS MACACIONES SON DIF DE NULL O SI NO  */
                                    if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida != null) {

                                        /* IF TENGO LAS 2 MARCACIONES MAYORES A LA HORA ACTUAL O SI NO TENGO  */
                                        if ($fecha1->gt($fechaEAnte1) && $fecha1->gt($fechaSAnte1)) {
                                            $marcacion_puerta->marcaMov_fecha = $fecha1;
                                            $marcacion_puerta->save();
                                            return 1;
                                        } else {
                                            /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                            return [0, 'Tienes registrado la ultima  marcacion marcacion, la hora de entrada debe ser mayor a las de la anterior marcacion'];
                                        }
                                    } else {  /* IF MARCACION ENTRADA es dif de null */
                                        /*  dd($marcacion_puerta001); */
                                        if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida == null) {
                                            if ($fecha1->gt($fechaEAnte1)) {
                                                $marcacion_puerta->marcaMov_fecha = $fecha1;
                                                $marcacion_puerta->save();
                                                return 1;
                                            } else {
                                                /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                                return [0, 'Tienes registrado la ultima  marcacion, la hora de entrada debe ser mayor a la entrada de la ant marcacion'];
                                            }
                                        } else {
                                            if ($fecha1->gt($fechaSAnte1)) {
                                                $marcacion_puerta->marcaMov_fecha = $fecha1;
                                                $marcacion_puerta->save();
                                                return 1;
                                            } else {
                                                /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                                return [0, 'Tienes registrado la ultima marcacion, la hora de entrada debe ser mayor a la salida de la ant marcacion'];
                                            }
                                        }
                                    }
                                } else {
                                    $marcacion_puerta->marcaMov_fecha = $fecha1;
                                    $marcacion_puerta->save();
                                    return 1;
                                }
                            } else {
                                /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                return [0, 'Tienes registrado otra marcacion, la hora de entrada debe ser mayor a la entrada de la ant marcacion'];
                            }
                        } else {
                            if ($fecha1->gt($fechaSAnte)) {
                                if ($marcacion_puerta00) {
                                    $fechaEAnte1 = Carbon::create($marcacion_puerta00->marcaMov_fecha);
                                    $fechaSAnte1 = Carbon::create($marcacion_puerta00->marcaMov_salida);

                                    /* IF LAS MACACIONES SON DIF DE NULL O SI NO  */
                                    if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida != null) {

                                        /* IF TENGO LAS 2 MARCACIONES MAYORES A LA HORA ACTUAL O SI NO TENGO  */
                                        if ($fecha1->gt($fechaEAnte1) && $fecha1->gt($fechaSAnte1)) {
                                            $marcacion_puerta->marcaMov_fecha = $fecha1;
                                            $marcacion_puerta->save();
                                            return 1;
                                        } else {
                                            /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                            return [0, 'Tienes registrado la ultima  marcacion marcacion, la hora de entrada debe ser mayor a las de la anterior marcacion'];
                                        }
                                    } else {  /* IF MARCACION ENTRADA es dif de null */
                                        /*  dd($marcacion_puerta001); */
                                        if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida == null) {
                                            if ($fecha1->gt($fechaEAnte1)) {
                                                $marcacion_puerta->marcaMov_fecha = $fecha1;
                                                $marcacion_puerta->save();
                                                return 1;
                                            } else {
                                                /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                                return [0, 'Tienes registrado la ultima  marcacion, la hora de entrada debe ser mayor a la entrada de la ant marcacion'];
                                            }
                                        } else {
                                            if ($fecha1->gt($fechaSAnte1)) {
                                                $marcacion_puerta->marcaMov_fecha = $fecha1;
                                                $marcacion_puerta->save();
                                                return 1;
                                            } else {
                                                /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                                return [0, 'Tienes registrado la ultima marcacion, la hora de entrada debe ser mayor a la salida de la ant marcacion'];
                                            }
                                        }
                                    }
                                } else {
                                    $marcacion_puerta->marcaMov_fecha = $fecha1;
                                    $marcacion_puerta->save();
                                    return 1;
                                }
                            } else {
                                /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                return [0, 'Tienes registrado otra marcacion, la hora de entrada debe ser mayor a la salida de la ant marcacion'];
                            }
                        }
                    }
                } else {
                    if ($marcacion_puerta00) {
                        $fechaEAnte1 = Carbon::create($marcacion_puerta00->marcaMov_fecha);
                        $fechaSAnte1 = Carbon::create($marcacion_puerta00->marcaMov_salida);

                        /* IF LAS MACACIONES SON DIF DE NULL O SI NO  */
                        if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida != null) {

                            /* IF TENGO LAS 2 MARCACIONES MAYORES A LA HORA ACTUAL O SI NO TENGO  */
                            if ($fecha1->gt($fechaEAnte1) && $fecha1->gt($fechaSAnte1)) {
                                $marcacion_puerta->marcaMov_fecha = $fecha1;
                                $marcacion_puerta->save();
                                return 1;
                            } else {
                                /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                return [0, 'Tienes registrado la ultima  marcacion marcacion, la hora de entrada debe ser mayor a las de la anterior marcacion'];
                            }
                        } else {  /* IF MARCACION ENTRADA es dif de null */
                            /*  dd($marcacion_puerta001); */
                            if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida == null) {
                                if ($fecha1->gt($fechaEAnte1)) {
                                    $marcacion_puerta->marcaMov_fecha = $fecha1;
                                    $marcacion_puerta->save();
                                    return 1;
                                } else {
                                    /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                    return [0, 'Tienes registrado la ultima  marcacion, la hora de entrada debe ser mayor a la entrada de la ant marcacion'];
                                }
                            } else {
                                if ($fecha1->gt($fechaSAnte1)) {
                                    $marcacion_puerta->marcaMov_fecha = $fecha1;
                                    $marcacion_puerta->save();
                                    return 1;
                                } else {
                                    /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                    return [0, 'Tienes registrado la ultima marcacion, la hora de entrada debe ser mayor a la salida de la ant marcacion'];
                                }
                            }
                        }
                    } else {
                        if ($marcacion_puerta00) {
                            $fechaEAnte1 = Carbon::create($marcacion_puerta00->marcaMov_fecha);
                            $fechaSAnte1 = Carbon::create($marcacion_puerta00->marcaMov_salida);

                            /* IF LAS MACACIONES SON DIF DE NULL O SI NO  */
                            if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida != null) {

                                /* IF TENGO LAS 2 MARCACIONES MAYORES A LA HORA ACTUAL O SI NO TENGO  */
                                if ($fecha1->gt($fechaEAnte1) && $fecha1->gt($fechaSAnte1)) {
                                    $marcacion_puerta->marcaMov_fecha = $fecha1;
                                    $marcacion_puerta->save();
                                    return 1;
                                } else {
                                    /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                    return [0, 'Tienes registrado la ultima  marcacion marcacion, la hora de entrada debe ser mayor a las de la anterior marcacion'];
                                }
                            } else {  /* IF MARCACION ENTRADA es dif de null */
                                /*  dd($marcacion_puerta001); */
                                if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida == null) {
                                    if ($fecha1->gt($fechaEAnte1)) {
                                        $marcacion_puerta->marcaMov_fecha = $fecha1;
                                        $marcacion_puerta->save();
                                        return 1;
                                    } else {
                                        /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                        return [0, 'Tienes registrado la ultima  marcacion, la hora de entrada debe ser mayor a la entrada de la ant marcacion'];
                                    }
                                } else {
                                    if ($fecha1->gt($fechaSAnte1)) {
                                        $marcacion_puerta->marcaMov_fecha = $fecha1;
                                        $marcacion_puerta->save();
                                        return 1;
                                    } else {
                                        /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                        return [0, 'Tienes registrado la ultima marcacion, la hora de entrada debe ser mayor a la salida de la ant marcacion'];
                                    }
                                }
                            }
                        } else {
                            /* SI NO EXISTE MARCACION ANTERIOR  */
                            $marcacion_puerta->marcaMov_fecha = $fecha1;
                            $marcacion_puerta->save();
                            return 1;
                        }
                    }
                }
            }
        } else {

            /* IF EXISTE MARCACION ANTERIOR */
            if ($marcacion_puerta001) {
                $fechaEAnte = Carbon::create($marcacion_puerta001->marcaMov_fecha);
                $fechaSAnte = Carbon::create($marcacion_puerta001->marcaMov_salida);

                /* IF LAS MACACIONES SON DIF DE NULL O SI NO  */
                if ($marcacion_puerta001->marcaMov_fecha != null && $marcacion_puerta001->marcaMov_salida != null) {

                    /* IF TENGO LAS 2 MARCACIONES MAYORES A LA HORA ACTUAL O SI NO TENGO  */
                    if ($fecha1->gt($fechaEAnte) && $fecha1->gt($fechaSAnte)) {
                        if ($marcacion_puerta00) {
                            $fechaEAnte1 = Carbon::create($marcacion_puerta00->marcaMov_fecha);
                            $fechaSAnte1 = Carbon::create($marcacion_puerta00->marcaMov_salida);

                            /* IF LAS MACACIONES SON DIF DE NULL O SI NO  */
                            if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida != null) {

                                /* IF TENGO LAS 2 MARCACIONES MAYORES A LA HORA ACTUAL O SI NO TENGO  */
                                if ($fecha1->gt($fechaEAnte1) && $fecha1->gt($fechaSAnte1)) {
                                    $marcacion_puerta->marcaMov_fecha = $fecha1;
                                    $marcacion_puerta->save();
                                    return 1;
                                } else {
                                    /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                    return [0, 'Tienes registrado la ultima  marcacion marcacion, la hora de entrada debe ser mayor a las de la anterior marcacion'];
                                }
                            } else {  /* IF MARCACION ENTRADA es dif de null */
                                /*  dd($marcacion_puerta001); */
                                if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida == null) {
                                    if ($fecha1->gt($fechaEAnte1)) {
                                        $marcacion_puerta->marcaMov_fecha = $fecha1;
                                        $marcacion_puerta->save();
                                        return 1;
                                    } else {
                                        /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                        return [0, 'Tienes registrado la ultima  marcacion, la hora de entrada debe ser mayor a la entrada de la ant marcacion'];
                                    }
                                } else {
                                    if ($fecha1->gt($fechaSAnte1)) {
                                        $marcacion_puerta->marcaMov_fecha = $fecha1;
                                        $marcacion_puerta->save();
                                        return 1;
                                    } else {
                                        /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                        return [0, 'Tienes registrado la ultima marcacion, la hora de entrada debe ser mayor a la salida de la ant marcacion'];
                                    }
                                }
                            }
                        } else {
                            $marcacion_puerta->marcaMov_fecha = $fecha1;
                            $marcacion_puerta->save();
                            return 1;
                        }
                    } else {
                        /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                        return [0, 'Tienes registrado otra marcacion, la hora de entrada debe ser mayor a las de la anterior marcacion'];
                    }
                } else {  /* IF MARCACION ENTRADA es dif de null */
                    /*  dd($marcacion_puerta001); */
                    if ($marcacion_puerta001->marcaMov_fecha != null && $marcacion_puerta001->marcaMov_salida == null) {
                        if ($fecha1->gt($fechaEAnte)) {
                            if ($marcacion_puerta00) {
                                $fechaEAnte1 = Carbon::create($marcacion_puerta00->marcaMov_fecha);
                                $fechaSAnte1 = Carbon::create($marcacion_puerta00->marcaMov_salida);

                                /* IF LAS MACACIONES SON DIF DE NULL O SI NO  */
                                if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida != null) {

                                    /* IF TENGO LAS 2 MARCACIONES MAYORES A LA HORA ACTUAL O SI NO TENGO  */
                                    if ($fecha1->gt($fechaEAnte1) && $fecha1->gt($fechaSAnte1)) {
                                        $marcacion_puerta->marcaMov_fecha = $fecha1;
                                        $marcacion_puerta->save();
                                        return 1;
                                    } else {
                                        /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                        return [0, 'Tienes registrado la ultima  marcacion marcacion, la hora de entrada debe ser mayor a las de la anterior marcacion'];
                                    }
                                } else {  /* IF MARCACION ENTRADA es dif de null */
                                    /*  dd($marcacion_puerta001); */
                                    if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida == null) {
                                        if ($fecha1->gt($fechaEAnte1)) {
                                            $marcacion_puerta->marcaMov_fecha = $fecha1;
                                            $marcacion_puerta->save();
                                            return 1;
                                        } else {
                                            /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                            return [0, 'Tienes registrado la ultima  marcacion, la hora de entrada debe ser mayor a la entrada de la ant marcacion'];
                                        }
                                    } else {
                                        if ($fecha1->gt($fechaSAnte1)) {
                                            $marcacion_puerta->marcaMov_fecha = $fecha1;
                                            $marcacion_puerta->save();
                                            return 1;
                                        } else {
                                            /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                            return [0, 'Tienes registrado la ultima marcacion, la hora de entrada debe ser mayor a la salida de la ant marcacion'];
                                        }
                                    }
                                }
                            } else {
                                $marcacion_puerta->marcaMov_fecha = $fecha1;
                                $marcacion_puerta->save();
                                return 1;
                            }
                        } else {
                            /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                            return [0, 'Tienes registrado otra marcacion, la hora de entrada debe ser mayor a la entrada de la ant marcacion'];
                        }
                    } else {
                        if ($fecha1->gt($fechaSAnte)) {
                            if ($marcacion_puerta00) {
                                $fechaEAnte1 = Carbon::create($marcacion_puerta00->marcaMov_fecha);
                                $fechaSAnte1 = Carbon::create($marcacion_puerta00->marcaMov_salida);

                                /* IF LAS MACACIONES SON DIF DE NULL O SI NO  */
                                if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida != null) {

                                    /* IF TENGO LAS 2 MARCACIONES MAYORES A LA HORA ACTUAL O SI NO TENGO  */
                                    if ($fecha1->gt($fechaEAnte1) && $fecha1->gt($fechaSAnte1)) {
                                        $marcacion_puerta->marcaMov_fecha = $fecha1;
                                        $marcacion_puerta->save();
                                        return 1;
                                    } else {
                                        /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                        return [0, 'Tienes registrado la ultima  marcacion marcacion, la hora de entrada debe ser mayor a las de la anterior marcacion'];
                                    }
                                } else {  /* IF MARCACION ENTRADA es dif de null */
                                    /*  dd($marcacion_puerta001); */
                                    if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida == null) {
                                        if ($fecha1->gt($fechaEAnte1)) {
                                            $marcacion_puerta->marcaMov_fecha = $fecha1;
                                            $marcacion_puerta->save();
                                            return 1;
                                        } else {
                                            /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                            return [0, 'Tienes registrado la ultima  marcacion, la hora de entrada debe ser mayor a la entrada de la ant marcacion'];
                                        }
                                    } else {
                                        if ($fecha1->gt($fechaSAnte1)) {
                                            $marcacion_puerta->marcaMov_fecha = $fecha1;
                                            $marcacion_puerta->save();
                                            return 1;
                                        } else {
                                            /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                            return [0, 'Tienes registrado la ultima marcacion, la hora de entrada debe ser mayor a la salida de la ant marcacion'];
                                        }
                                    }
                                }
                            } else {
                                $marcacion_puerta->marcaMov_fecha = $fecha1;
                                $marcacion_puerta->save();
                                return 1;
                            }
                        } else {
                            /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                            return [0, 'Tienes registrado otra marcacion, la hora de entrada debe ser mayor a la salida de la ant marcacion'];
                        }
                    }
                }
            } else {
                /* SI NO EXISTE MARCACION ANTERIOR  */
                /* SI EXISTE OTRA MARCACION */
                if ($marcacion_puerta00) {
                    $fechaEAnte1 = Carbon::create($marcacion_puerta00->marcaMov_fecha);
                    $fechaSAnte1 = Carbon::create($marcacion_puerta00->marcaMov_salida);

                    /* IF LAS MACACIONES SON DIF DE NULL O SI NO  */
                    if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida != null) {

                        /* IF TENGO LAS 2 MARCACIONES MAYORES A LA HORA ACTUAL O SI NO TENGO  */
                        if ($fecha1->gt($fechaEAnte1) && $fecha1->gt($fechaSAnte1)) {
                            $marcacion_puerta->marcaMov_fecha = $fecha1;
                            $marcacion_puerta->save();
                            return 1;
                        } else {
                            /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                            return [0, 'Tienes registrado la ultima  marcacion marcacion, la hora de entrada debe ser mayor a las de la anterior marcacion'];
                        }
                    } else {  /* IF MARCACION ENTRADA es dif de null */
                        /*  dd($marcacion_puerta001); */
                        if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida == null) {
                            if ($fecha1->gt($fechaEAnte1)) {
                                $marcacion_puerta->marcaMov_fecha = $fecha1;
                                $marcacion_puerta->save();
                                return 1;
                            } else {
                                /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                return [0, 'Tienes registrado la ultima  marcacion, la hora de entrada debe ser mayor a la entrada de la ant marcacion'];
                            }
                        } else {
                            if ($fecha1->gt($fechaSAnte1)) {
                                $marcacion_puerta->marcaMov_fecha = $fecha1;
                                $marcacion_puerta->save();
                                return 1;
                            } else {
                                /* MENSAJE DEBE SER MAYOR QUE ANTERIOR MARCACION */
                                return [0, 'Tienes registrado la ultima marcacion, la hora de entrada debe ser mayor a la salida de la ant marcacion'];
                            }
                        }
                    }
                } else {
                    $marcacion_puerta->marcaMov_fecha = $fecha1;
                    $marcacion_puerta->save();
                    return 1;
                }
            }
        }
    }

    // * BUSCAR MARCACIONES SIN PAREJA X EMPLEADO
    function buscarMarcacionPorEmpleado(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $fecha = $request->get('fecha');
        $marcacion = marcacion_puerta::select('marcaMov_id')
            ->whereRaw("IF(marcaMov_fecha is null, DATE(marcaMov_salida), DATE(marcaMov_fecha)) = '$fecha'")
            ->where(function ($query) {
                $query->whereNull('marcaMov_fecha')
                    ->orWhereNull('marcaMov_salida');
            })
            ->where('marcaMov_emple_id', '=', $idEmpleado)
            ->get()
            ->first();
        if ($marcacion) {
            return response()->json($marcacion->marcaMov_id, 200);
        }
        return response()->json(array("respuesta" => "ok"), 200);
    }

    // * LISTA DE SALIDAS CON ENTRADA NULL
    function listaDeSalidasSinE(Request $request)
    {
        $fecha = $request->get('fecha');
        $idEmpleado = $request->get('idEmpleado');

        $salidas = DB::table('marcacion_puerta as mp')
            ->leftJoin('horario_empleado as he', 'mp.horarioEmp_id', '=', 'he.horarioEmp_id')
            ->leftJoin('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->select(
                'mp.marcaMov_id as id',
                'mp.marcaMov_salida as salida',
                DB::raw('IF(mp.horarioEmp_id is null, 0 , mp.horarioEmp_id ) as idH'),
                'h.horario_descripcion as horario'
            )
            ->whereNotNull('mp.marcaMov_salida')
            ->whereNull('mp.marcaMov_fecha')
            ->whereRaw("DATE(marcaMov_salida) = '$fecha'")
            ->where('mp.marcaMov_emple_id', '=', $idEmpleado)
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
            $marcacionCambiar = marcacion_puerta::findOrFail($idEntradaCambiar);     // ? MARCACION A CAMBIAR
            $marcacion = marcacion_puerta::findOrFail($idMarcacion);                 // ? MARCACION A RECIBIR ENTRADA
            // **************************************** VALIDACION DE NUEVO RANGOS **************************************
            $nuevaEntrada = $tipo  == 2 ? $marcacionCambiar->marcaMov_salida : $marcacionCambiar->marcaMov_fecha;
            $nuevaSalida = $marcacion->marcaMov_salida;
            if ($tipo == 1) {
                $fecha = Carbon::parse($marcacionCambiar->marcaMov_fecha)->isoFormat('YYYY-MM-DD');
            } else {
                $fecha = Carbon::parse($marcacionCambiar->marcaMov_salida)->isoFormat('YYYY-MM-DD');
            }
            if (Carbon::parse($nuevaSalida)->gt(Carbon::parse($nuevaEntrada))) {
                // DB::enableQueryLog();
                $marcacionesValidar = DB::table('marcacion_puerta as m')
                    ->select(
                        'm.marcaMov_id',
                        DB::raw('IF(m.marcaMov_fecha is null,0,m.marcaMov_fecha) AS entrada'),
                        DB::raw('IF(m.marcaMov_salida is null,0,m.marcaMov_salida) AS salida')
                    )
                    ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                    ->where(DB::raw('IF(m.marcaMov_fecha is null,DATE(m.marcaMov_salida),DATE(m.marcaMov_fecha))'), "=", $fecha)
                    ->whereNotIn('m.marcaMov_id', [$marcacion->marcaMov_id])
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
                    // ! MARCACION A CAMBIAR
                    if ($tipo == 1) {
                        $marcacionCambiar->marcaMov_fecha = NULL;
                    } else {
                        $marcacionCambiar->marcaMov_salida = NULL;
                    }
                    $marcacionCambiar->save();
                    // ! MARCACION A REGISTRAR ENTRADA
                    $marcacion->marcaMov_fecha = $nuevaEntrada;
                    $marcacion->save();

                    // ! BUSCAR SI LA MARCACION A CAMBIAR TIENE LOS CAMPOS VACIOS DE ENTRADA Y SALIDA
                    if (is_null($marcacionCambiar->marcaMov_fecha) && is_null($marcacionCambiar->marcaMov_salida)) {
                        $marcacionCambiar->delete();  // ? ELIMINAR MARCACION
                    }
                    return response()->json($marcacion->marcaMov_id, 200);
                } else {
                    return response()->json(array("respuesta" => "Posibilidad de cruze de marcación."), 200);
                }
            } else {
                return response()->json(array("respuesta" => "Entrada debe ser menor a salida."), 200);
            }
            // *************************************** FINALIZACION ******************************************************
        } else {
            $marcacion = marcacion_puerta::findOrFail($idEntradaCambiar);
            $marcacion->marcaMov_fecha = $marcacion->marcaMov_salida;
            $marcacion->marcaMov_salida = NULL;
            $marcacion->save();

            return response()->json($marcacion->marcaMov_id, 200);
        }
    }

    // * LISTA DE ENTRADAS CON SALIDAD NULL
    function listaDeEntradasSinS(Request $request)
    {
        $fecha = $request->get('fecha');
        $idEmpleado = $request->get('idEmpleado');

        $entradas = DB::table('marcacion_puerta as mp')
            ->leftJoin('horario_empleado as he', 'mp.horarioEmp_id', '=', 'he.horarioEmp_id')
            ->leftJoin('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->select(
                'mp.marcaMov_id as id',
                'mp.marcaMov_fecha as entrada',
                DB::raw('IF(mp.horarioEmp_id is null, 0 , mp.horarioEmp_id ) as idH'),
                'h.horario_descripcion as horario'
            )
            ->whereNotNull('mp.marcaMov_fecha')
            ->whereNull('mp.marcaMov_salida')
            ->whereRaw("DATE(marcaMov_fecha) = '$fecha'")
            ->where('mp.marcaMov_emple_id', '=', $idEmpleado)
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
            $marcacionCambiar = marcacion_puerta::findOrFail($idEntradaCambiar);     // ? MARCACION A CAMBIAR
            $marcacion = marcacion_puerta::findOrFail($idMarcacion);                 // ? MARCACION A RECIBIR ENTRADA
            // **************************************** VALIDACION DE NUEVO RANGOS **************************************
            $nuevaEntrada = $marcacion->marcaMov_fecha;
            $nuevaSalida = $tipo == 2 ? $marcacionCambiar->marcaMov_salida : $marcacionCambiar->marcaMov_fecha;
            if ($tipo == 1) {
                $fecha = Carbon::parse($marcacionCambiar->marcaMov_fecha)->isoFormat('YYYY-MM-DD');
            } else {
                $fecha = Carbon::parse($marcacionCambiar->marcaMov_salida)->isoFormat('YYYY-MM-DD');
            }
            if (Carbon::parse($nuevaSalida)->gt(Carbon::parse($nuevaEntrada))) {
                // DB::enableQueryLog();
                $marcacionesValidar = DB::table('marcacion_puerta as m')
                    ->select(
                        'm.marcaMov_id',
                        DB::raw('IF(m.marcaMov_fecha is null,0,m.marcaMov_fecha) AS entrada'),
                        DB::raw('IF(m.marcaMov_salida is null,0,m.marcaMov_salida) AS salida')
                    )
                    ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                    ->where(DB::raw('IF(m.marcaMov_fecha is null,DATE(m.marcaMov_salida),DATE(m.marcaMov_fecha))'), "=", $fecha)
                    ->whereNotIn('m.marcaMov_id', [$marcacion->marcaMov_id])
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
                    // ! MARCACION A CAMBIAR
                    if ($tipo == 2) {
                        $marcacionCambiar->marcaMov_salida = NULL;
                    } else {
                        $marcacionCambiar->marcaMov_fecha = NULL;
                    }
                    $marcacionCambiar->save();

                    // ! MARCACION A REGISTRAR ENTRADA
                    $marcacion->marcaMov_salida = $nuevaSalida;
                    $marcacion->save();

                    // ! BUSCAR SI LA MARCACION A CAMBIAR TIENE LOS CAMPOS VACIOS DE ENTRADA Y SALIDA
                    if (is_null($marcacionCambiar->marcaMov_fecha) && is_null($marcacionCambiar->marcaMov_salida)) {
                        $marcacionCambiar->delete();  // ? ELIMINAR MARCACION
                    }
                    return response()->json($marcacion->marcaMov_id, 200);
                } else {
                    return response()->json(array("respuesta" => "Posibilidad de cruze de marcación."), 200);
                }
            } else {
                return response()->json(array("respuesta" => "Salida debe ser mayor entrada."), 200);
            }
            // *************************************** FINALIZACION ******************************************************
        } else {
            $marcacion = marcacion_puerta::findOrFail($idEntradaCambiar);
            $marcacion->marcaMov_salida = $marcacion->marcaMov_fecha;
            $marcacion->marcaMov_fecha = NULL;
            $marcacion->save();
            return response()->json($marcacion->marcaMov_id, 200);
        }
    }

    // * CONVERTIR TIEMPOS
    public function convertirTiempos(Request $request)
    {
        $idM = $request->get('id');

        $marcacion = marcacion_puerta::findOrFail($idM);
        $carbonEntrada = carbon::parse($marcacion->marcaMov_fecha);
        $carbonSalida = carbon::parse($marcacion->marcaMov_salida);

        if ($carbonSalida->lt($carbonEntrada)) {        // ? COMPARAMOS SI LA SALIDA ES MENOR A LA ENTRADA
            $marcacion->marcaMov_fecha = $carbonSalida;
            $marcacion->marcaMov_salida = $carbonEntrada;
            $marcacion->save();

            return response()->json($marcacion->marcaMov_id, 200);
        } else {
            return response()->json(array("respuesta" => "Hora final debe ser mayor a entrada."), 200);
        }
    }

    // * HORARIOS DE MARCACIONES
    public function horariosxMarcacion(Request $request)
    {
        $tipo = $request->get('tipo');
        $id = $request->get('id');
        $marcacion = marcacion_puerta::findOrFail($id);
        $fechaM = $tipo == 2 ? $marcacion->marcaMov_salida : $marcacion->marcaMov_fecha;
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
            ->where('he.empleado_emple_id', '=', $marcacion->marcaMov_emple_id)
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
        $marcacion = marcacion_puerta::findOrFail($id);
        if ($marcacionTipo == 1) {
            $fecha = Carbon::parse($marcacion->marcaMov_fecha)->isoFormat('YYYY-MM-DD');
        } else {
            $fecha = Carbon::parse($marcacion->marcaMov_salida)->isoFormat('YYYY-MM-DD');
        }
        // * VALIDACIONES
        $marcacionesValidar = DB::table('marcacion_puerta as m')
            ->select(
                'm.marcaMov_id',
                DB::raw('IF(m.marcaMov_fecha is null,0,m.marcaMov_fecha) AS entrada'),
                DB::raw('IF(m.marcaMov_salida is null,0,m.marcaMov_salida) AS salida')
            )
            ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
            ->whereNotIn('m.marcaMov_id', [$marcacion->marcaMov_id])
            ->where(DB::raw('IF(m.marcaMov_fecha is null,DATE(m.marcaMov_salida),DATE(m.marcaMov_fecha))'), "=", $fecha)
            ->whereNotNull('m.marcaMov_fecha')
            ->whereNotNull('m.marcaMov_salida')
            ->get();
        // dd(DB::getQueryLog());
        $respuesta = true;
        foreach ($marcacionesValidar as $mv) {
            if ($marcacionTipo == 1) {
                $respuestaCheck = checkHora($mv->entrada, $mv->salida, $marcacion->marcaMov_fecha);
            } else {
                $respuestaCheck = checkHora($mv->entrada, $mv->salida, $marcacion->marcaMov_salida);
            }
            if ($respuestaCheck) {
                $respuesta = false;
            }
        }

        if ($respuesta) {
            // * TOMAR MARCACION PARA NUEVA MARCACION
            if ($marcacionTipo == 1) {
                $nuevaMarcacion = $marcacion->marcaMov_fecha;
                $marcacion->marcaMov_fecha = NULL;
                $marcacion->save();
            } else {
                $nuevaMarcacion = $marcacion->marcaMov_salida;
                $marcacion->marcaMov_salida = NULL;
                $marcacion->save();
            }

            // * GENERAR NUEVA MARCACION
            $newMarcacion = new marcacion_puerta();
            if ($tipo ==  1) {
                $newMarcacion->marcaMov_fecha = $nuevaMarcacion;
                $dispositivoE = $marcacion->dispositivoEntrada;
                $dispositivoS = NULL;
            } else {
                $newMarcacion->marcaMov_salida = $nuevaMarcacion;
                $dispositivoS = $marcacion->dispositivoSalida;
                $dispositivoE = NULL;
            }
            $newMarcacion->marcaMov_emple_id = $marcacion->marcaMov_emple_id;
            $newMarcacion->organi_id =  $marcacion->organi_id;
            $newMarcacion->horarioEmp_id = $idHorarioE == 0 ? NULL : $idHorarioE;
            $newMarcacion->marca_latitud = $marcacion->marca_latitud;
            $newMarcacion->marca_longitud = $marcacion->marca_longitud;
            $newMarcacion->marcaIdActivi  = $marcacion->marcaIdActivi;
            $newMarcacion->puntoC_id = $marcacion->puntoC_id;
            $newMarcacion->centC_id = $marcacion->centC_id;
            $newMarcacion->controladores_idControladores = $marcacion->controladores_idControladores;
            $newMarcacion->dispositivoEntrada = $dispositivoE;
            $newMarcacion->dispositivoSalida = $dispositivoS;
            $newMarcacion->save();

            return response()->json($newMarcacion->marcaMov_id, 200);
        } else {
            return response()->json(array("respuesta" => "Posibilidad de cruze de marcación."), 200);
        }
    }

    // * ELIMINAR MARCACION
    public function eliminarMarcacion(Request $request)
    {
        $id = $request->get('id');
        $tipo = $request->get('tipo');

        // * BUSCAMOS MARCACION
        $marcacion = marcacion_puerta::findOrFail($id);
        if ($tipo == 1) {
            $marcacion->marcaMov_fecha = NULL;
            $marcacion->dispositivoEntrada = NULL;
            $marcacion->controladores_idControladores = NULL;
            $marcacion->save();
        } else {
            $marcacion->marcaMov_salida = NULL;
            $marcacion->dispositivoSalida = NULL;
            $marcacion->controladores_salida = NULL;
            $marcacion->save();
        }

        if (is_null($marcacion->marcaMov_fecha) && is_null($marcacion->marcaMov_salida)) {
            $marcacion->delete();
        }

        return response()->json("Marcación eliminada.", 200);
    }

    // * NUEVA SALIDA
    public function registrarNSalida(Request $request)
    {
        $id = $request->get('id');
        $tiempo = $request->get('salida');
        $idhorarioE = $request->get('horario');

        $marcacion = marcacion_puerta::findOrFail($id);
        $entrada = Carbon::parse($marcacion->marcaMov_fecha);
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
                    'he.nHoraAdic as horasA',
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
            $marcacionesValidar = DB::table('marcacion_puerta as m')
                ->select(
                    'm.marcaMov_id',
                    DB::raw('IF(m.marcaMov_fecha is null,0,m.marcaMov_fecha) AS entrada'),
                    DB::raw('IF(m.marcaMov_salida is null,0,m.marcaMov_salida) AS salida')
                )
                ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                ->where(DB::raw('IF(m.marcaMov_fecha is null,DATE(m.marcaMov_salida),DATE(m.marcaMov_fecha))'), "=", $entrada->copy()->isoFormat('YYYY-MM-DD'))
                ->whereNotIn('m.marcaMov_id', [$marcacion->marcaMov_id])
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
                    $sumaTotalDeHoras = DB::table('marcacion_puerta as m')
                        ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaMov_salida,m.marcaMov_fecha)))) as totalT'))
                        ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                        ->whereNotNull('m.marcaMov_fecha')
                        ->whereNotNull('m.marcaMov_salida')
                        ->where(DB::raw('DATE(marcaMov_fecha)'), '=', $entrada->copy()->isoFormat('YYYY-MM-DD'))
                        ->where('m.horarioEmp_id', '=', $idhorarioE)
                        ->get();
                    // * CALCULAR TIEMPO
                    $sumaTotalDeHoras[0]->totalT = $sumaTotalDeHoras[0]->totalT == null ? "00:00:00" : $sumaTotalDeHoras[0]->totalT;
                    $horaIParse = Carbon::parse($entrada);
                    $horaFParse = Carbon::parse($salida);
                    $totalDuration = $horaFParse->diffInSeconds($horaIParse);
                    $tiempoTotal = Carbon::parse($sumaTotalDeHoras[0]->totalT)->addSeconds($totalDuration);
                    $tiempoTotalDeHorario = Carbon::parse($horario->horasO)->addHours($horario->horasA);
                    if ($tiempoTotal->lte($tiempoTotalDeHorario)) {
                        if ($horario->fueraH == 0) {
                            // * VALIDAR SIN FUERA DE HORARIO
                            $horarioInicioT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
                            $horarioFinT = $horarioFin->copy()->addMinutes($horario->toleranciaF);
                            if ($entrada->gte($horarioInicioT) && $salida->lte($horarioFinT)) {
                                $marcacion->marcaMov_salida = $salida;
                                $marcacion->dispositivoSalida = NULL;
                                $marcacion->save();
                                return response()->json($marcacion->marcaMov_id, 200);
                            } else {
                                return response()->json(
                                    array("respuesta" => "Marcación fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . $horario->horaI . " - " . $horario->horaF . " )"),
                                    200
                                );
                            }
                        } else {
                            $marcacion->marcaMov_salida = $salida;
                            $marcacion->dispositivoSalida = NULL;
                            $marcacion->save();
                            return response()->json($marcacion->marcaMov_id, 200);
                        }
                    } else {
                        return response()->json(
                            array("respuesta" => "Sobretiempo en la marcación."),
                            200
                        );
                    }
                } else {
                    $marcacion->marcaMov_salida = $salida;
                    $marcacion->dispositivoSalida = NULL;
                    $marcacion->save();
                    return response()->json($marcacion->marcaMov_id, 200);
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

        $marcacion = marcacion_puerta::findOrFail($id);
        $salida = Carbon::parse($marcacion->marcaMov_salida);
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
                    'he.nHoraAdic as horasA',
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
            $marcacionesValidar = DB::table('marcacion_puerta as m')
                ->select(
                    'm.marcaMov_id',
                    DB::raw('IF(m.marcaMov_fecha is null,0,m.marcaMov_fecha) AS entrada'),
                    DB::raw('IF(m.marcaMov_salida is null,0,m.marcaMov_salida) AS salida')
                )
                ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                ->where(DB::raw('IF(m.marcaMov_fecha is null,DATE(m.marcaMov_salida),DATE(m.marcaMov_fecha))'), "=", $salida->copy()->isoFormat('YYYY-MM-DD'))
                ->whereNotIn('m.marcaMov_id', [$marcacion->marcaMov_id])
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
                    $sumaTotalDeHoras = DB::table('marcacion_puerta as m')
                        ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaMov_salida,m.marcaMov_fecha)))) as totalT'))
                        ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                        ->whereNotNull('m.marcaMov_fecha')
                        ->whereNotNull('m.marcaMov_salida')
                        ->where(DB::raw('DATE(marcaMov_fecha)'), '=', $entrada->copy()->isoFormat('YYYY-MM-DD'))
                        ->where('m.horarioEmp_id', '=', $idhorarioE)
                        ->get();
                    // * CALCULAR TIEMPO
                    $sumaTotalDeHoras[0]->totalT = $sumaTotalDeHoras[0]->totalT == null ? "00:00:00" : $sumaTotalDeHoras[0]->totalT;
                    $horaIParse = Carbon::parse($entrada);
                    $horaFParse = Carbon::parse($salida);
                    $totalDuration = $horaFParse->diffInSeconds($horaIParse);
                    $tiempoTotal = Carbon::parse($sumaTotalDeHoras[0]->totalT)->addSeconds($totalDuration);
                    $tiempoTotalDeHorario = Carbon::parse($horario->horasO)->addHours($horario->horasA);
                    if ($tiempoTotal->lte($tiempoTotalDeHorario)) {
                        if ($horario->fueraH == 0) {
                            // * VALIDAR SIN FUERA DE HORARIO
                            $horarioInicioT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
                            $horarioFinT = $horarioFin->copy()->addMinutes($horario->toleranciaF)->addHours($horario->horasA);

                            if ($entrada->gte($horarioInicioT) && $salida->lte($horarioFinT)) {
                                $marcacion->marcaMov_fecha = $entrada;
                                $marcacion->dispositivoEntrada = NULL;
                                $marcacion->save();
                                return response()->json($marcacion->marcaMov_id, 200);
                            } else {
                                return response()->json(
                                    array("respuesta" => "Marcación fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . $horario->horaI . " - " . $horario->horaF . " )"),
                                    200
                                );
                            }
                        } else {
                            $marcacion->marcaMov_fecha = $entrada;
                            $marcacion->dispositivoEntrada = NULL;
                            $marcacion->save();
                            return response()->json($marcacion->marcaMov_id, 200);
                        }
                    } else {
                        return response()->json(
                            array("respuesta" => "Sobretiempo en la marcación."),
                            200
                        );
                    }
                } else {
                    $marcacion->marcaMov_fecha = $entrada;
                    $marcacion->dispositivoEntrada = NULL;
                    $marcacion->save();
                    return response()->json($marcacion->marcaMov_id, 200);
                }
            } else {
                return response()->json(array("respuesta" => "Posibilidad de cruce de hora"), 200);
            }
        } else {
            return response()->json(array("respuesta" => "Entrada debe ser menor a salida."), 200);
        }
    }

    // * LISTAS DE HORARIOS POR EMPLEADO
    public function horarioEmpleado(Request $request)
    {
        $fecha = $request->get('fecha');
        $idHorarioE = $request->get('idHE');
        $idEmpleado = $request->get('idEmpleado');
        // DB::enableQueryLog();
        $horario = DB::table('horario_empleado as he')
            ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
            ->select(
                'h.horario_descripcion as descripcion',
                'h.horaI',
                'h.horaF',
                'h.horario_tolerancia as toleranciaI',
                'h.horario_toleranciaF as toleranciaF',
                'he.fuera_horario as fueraH',
                'he.horarioEmp_id as idHorarioE',
                'h.horasObliga as horasObligadas',
                DB::raw('IF(he.horaAdic is null, 0 ,he.horaAdic) as horasAdicionales')
            )
            ->where(DB::raw('DATE(hd.start)'), '=', $fecha)
            ->where('he.empleado_emple_id', '=', $idEmpleado)
            ->where('he.horarioEmp_id', '!=', $idHorarioE)
            ->where('he.estado', '=', 1)
            ->get();
        // dd(DB::getQueryLog());

        return response()->json($horario, 200);
    }

    // * CAMBIAR HORARIO DE MARCACIONES
    public function cambiarHorario(Request $request)
    {
        $idHorarioE = $request->get('idHE') == 0 ? null : $request->get('idHE');
        $nuevoHorarioE = $request->get('idNuevo');
        $fecha = $request->get('fecha');
        $idEmpleado = $request->get('idEmpleado');

        $marcacion = marcacion_puerta::where('horarioEmp_id', '=', $idHorarioE)
            ->where(DB::raw("IF(marcaMov_fecha is null,DATE(marcaMov_salida),DATE(marcaMov_fecha))"), '=', $fecha)
            ->where('marcaMov_emple_id', '=', $idEmpleado)
            ->get();

        if ($nuevoHorarioE != 0) {
            $horarioEmpleado = horario_empleado::findOrFail($nuevoHorarioE);
            if ($horarioEmpleado->fuera_horario == 1) {
                foreach ($marcacion as $m) {
                    $actualizarM = marcacion_puerta::findOrFail($m->marcaMov_id);
                    $actualizarM->horarioEmp_id = $horarioEmpleado->horarioEmp_id;
                    $actualizarM->save();
                }
                return response()->json($nuevoHorarioE, 200);
            } else {
                $horario = DB::table('horario_empleado as he')
                    ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->select(
                        'h.horaI',
                        'h.horaF',
                        'h.horario_tolerancia as toleranciaI',
                        'h.horario_toleranciaF as toleranciaF',
                        DB::raw('DATE(hd.start) as fecha'),
                        'he.nHoraAdic as horasA'
                    )
                    ->where('he.horarioEmp_id', '=', $horarioEmpleado->horarioEmp_id)
                    ->get()
                    ->first();
                $horarioInicio = Carbon::parse($horario->fecha . " " . $horario->horaI);
                if ($horario->horaF > $horario->horaI) {
                    $horarioFin = Carbon::parse($horario->fecha . " " . $horario->horaF);
                } else {
                    $fechaSiguiente = Carbon::parse($horario->fecha)->addDays(1)->isoFormat('YYYY-MM-DD');
                    $horarioFin = Carbon::parse($fechaSiguiente . " " . $horario->horaF);
                }
                // * VALIDAR SIN FUERA DE HORARIO
                $horarioInicioT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
                $horarioFinT = $horarioFin->copy()->addMinutes($horario->toleranciaF)->addHours($horario->horasA);
                $respuesta = true;
                foreach ($marcacion as $m) {
                    if (!is_null($m->marcaMov_fecha)) {
                        $respuestaCheck = checkHora($horarioInicioT, $horarioFinT, $m->marcaMov_fecha);
                        if (!$respuestaCheck) {
                            $respuesta  = false;
                        }
                    }
                    if (!is_null($m->marcaMov_salida)) {
                        $respuestaCheck = checkHora($horarioInicioT, $horarioFinT, $m->marcaMov_salida);
                        if (!$respuestaCheck) {
                            $respuesta  = false;
                        }
                    }
                }
                if ($respuesta) {
                    foreach ($marcacion as $m) {
                        $actualizarM = marcacion_puerta::findOrFail($m->marcaMov_id);
                        $actualizarM->horarioEmp_id = $horarioEmpleado->horarioEmp_id;
                        $actualizarM->save();
                    }
                    return response()->json($nuevoHorarioE, 200);
                } else {
                    return response()->json(array("respuesta" => "Algunas marcaciones fuera de rango"), 200);
                }
            }
        } else {
            foreach ($marcacion as $m) {
                $actualizarM = marcacion_puerta::findOrFail($m->marcaMov_id);
                $actualizarM->horarioEmp_id = NULL;
                $actualizarM->save();
            }
            return response()->json($nuevoHorarioE, 200);
        }
    }

    // * REGISTRAR NUEVA MARCACION
    public function nuevaMarcacion(Request $request)
    {
        $idHorarioE = $request->get('idHE');
        $inicio = $request->get('horaI');
        $fin = $request->get('horaF');
        $idEmpleado = $request->get('idEmpleado');
        $fechaM = $request->get('fecha');

        // ? ******************************** VALIDACION ENTRE CRUCES DE HORAS ***************************
        $marcacionesValidar = DB::table('marcacion_puerta as m')
            ->select(
                'm.marcaMov_id',
                DB::raw('IF(m.marcaMov_fecha is null,0,m.marcaMov_fecha) AS entrada'),
                DB::raw('IF(m.marcaMov_salida is null,0,m.marcaMov_salida) AS salida')
            )
            ->where('m.marcaMov_emple_id', '=', $idEmpleado)
            ->where(DB::raw('IF(m.marcaMov_fecha is null,DATE(m.marcaMov_salida),DATE(m.marcaMov_fecha))'), "=", $fechaM)
            ->get();
        // ? ******************************* VALIDACION DE SIN HORARIO Y CON HORARIO ************************
        if ($idHorarioE != 0) {
            // * HORARIO EMPLEADO, HORARIO Y HORA DIAS
            $horario = DB::table('horario_empleado as he')
                ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->select(
                    'h.horaI',
                    'h.horaF',
                    'h.horario_tolerancia as toleranciaI',
                    'h.horario_toleranciaF as toleranciaF',
                    'he.fuera_horario as fueraH',
                    DB::raw('IF(he.horaAdic is null, 0 ,he.horaAdic) as horasA'),
                    'he.empleado_emple_id as idEmpleado',
                    DB::raw('DATE(hd.start) as fecha'),
                    'h.organi_id'
                )
                ->where('he.horarioEmp_id', '=', $idHorarioE)
                ->get()
                ->first();

            $fecha = Carbon::parse($horario->fecha);                            // : FECHA
            $fechaNext = $fecha->copy()->addDays(1)->isoFormat("YYYY-MM-DD");   // :FECHA SIGUIENTE
            //: HORA DE INICIO DE HORARIO
            $horarioInicio = Carbon::parse($fecha->isoFormat("YYYY-MM-DD") . " " . $horario->horaI)->subMinutes($horario->toleranciaI);
            // : HORA DE INICIO DE MARCACION
            $entrada = $inicio == null ? NULL : Carbon::parse($fecha->isoFormat("YYYY-MM-DD") . " " . $inicio);
            if ($horario->horaF > $horario->horaI) {
                // : HORA DE FIN DE HORARIO
                $horarioFin = Carbon::parse($fecha->isoFormat("YYYY-MM-DD") . " " . $horario->horaF)->addMinutes($horario->toleranciaF)->addHours($horario->horasA);
                // : HORA DE FIN DE MARCACION
                $salida = $fin == null ? NULL : Carbon::parse($fecha->isoFormat("YYYY-MM-DD") . " " . $fin);
            } else {
                // : HORA DE FIN DE HORARIO
                $horarioFin = Carbon::parse($fechaNext . " " . $horario->horaF)->addMinutes($horario->toleranciaF)->addHours($horario->horasA);
                // : HORA DE FIN DE MARCACION
                if ($fin  > $inicio) {
                    $salida = $fin == null ? NULL : Carbon::parse($fecha->isoFormat("YYYY-MM-DD") . " " . $fin);
                } else {
                    $salida = $fin == null ? NULL : Carbon::parse($fechaNext . " " . $fin);
                }
            }
            // * QUE NO SE ENCUENTRE VACIOS NINGUNO DE LOS DOS
            if (!is_null($fin) && !is_null($inicio)) {
                if ($salida->gt($entrada)) {
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
                        // * VALIDACION CON HORARIO
                        if ($horario->fueraH == 1) {
                            $marcacion = new marcacion_puerta();
                            $marcacion->marcaMov_fecha = $entrada;
                            $marcacion->marcaMov_emple_id = $horario->idEmpleado;
                            $marcacion->organi_id = session('sesionidorg');
                            $marcacion->horarioEmp_id = $idHorarioE;
                            $marcacion->marcaMov_salida = $salida;
                            $marcacion->save();
                            return response()->json($marcacion->marcaMov_id, 200);
                        } else {
                            if ($entrada->gte($horarioInicio) && $salida->lte($horarioFin)) {
                                $marcacion = new marcacion_puerta();
                                $marcacion->marcaMov_fecha = $entrada;
                                $marcacion->marcaMov_emple_id = $horario->idEmpleado;
                                $marcacion->organi_id = session('sesionidorg');
                                $marcacion->horarioEmp_id = $idHorarioE;
                                $marcacion->marcaMov_salida = $salida;
                                $marcacion->save();
                                return response()->json($marcacion->marcaMov_id, 200);
                            } else {
                                return response()->json(array("respuesta" => "Marcación fuera de horario."), 200);
                            }
                        }
                    } else {
                        return response()->json(array("respuesta" => "Posibilidad de cruce de hora."), 200);
                    }
                } else {
                    return response()->json(array("respuesta" => "Hora salida debe ser menor a la hora de entrada."), 200);
                }
            } else {
                $respuesta = true;
                foreach ($marcacionesValidar as $mv) {
                    if (!is_null($entrada)) {
                        $entrada = Carbon::parse($entrada)->isoFormat("YYYY-MM-DD H:mm:ss");
                        if ($mv->entrada != 0 && $mv->salida != 0) {
                            $respuestaCheck = checkHora($mv->entrada, $mv->salida, $entrada);
                            if ($respuestaCheck) {
                                $respuesta = false;
                            }
                        }
                        if ($mv->entrada != 0) {
                            $horaMinutoV = Carbon::create($mv->entrada)->format('H:i');
                            $horaMinutoE = Carbon::create($entrada)->format('H:i');
                            if ($horaMinutoV == $horaMinutoE) {
                                $respuesta = false;
                            }
                        }
                        if ($mv->salida != 0) {
                            $horaMinutoV = Carbon::create($mv->salida)->format('H:i');
                            $horaMinutoE = Carbon::create($entrada)->format('H:i');
                            if ($horaMinutoV == $horaMinutoE) {
                                $respuesta = false;
                            }
                        }
                    }
                    if (!is_null($salida)) {
                        $salida = Carbon::parse($salida)->isoFormat("YYYY-MM-DD H:mm:ss");
                        if ($mv->entrada != 0 && $mv->salida != 0) {
                            $respuestaCheck = checkHora($mv->entrada, $mv->salida, $salida);
                            if ($respuestaCheck) {
                                $respuesta = false;
                            }
                        }
                        if ($mv->entrada != 0) {
                            $horaMinutoV = Carbon::create($mv->entrada)->format('H:i');
                            $horaMinutoE = Carbon::create($salida)->format('H:i');
                            if ($horaMinutoV == $horaMinutoE) {
                                $respuesta = false;
                            }
                        }
                        if ($mv->salida != 0) {
                            $horaMinutoV = Carbon::create($mv->salida)->format('H:i');
                            $horaMinutoE = Carbon::create($salida)->format('H:i');
                            if ($horaMinutoV == $horaMinutoE) {
                                $respuesta = false;
                            }
                        }
                    }
                }
                // ! SI NO ENCUENTRA CRUCES
                if ($respuesta) {
                    $estado = true;
                    if (!is_null($entrada)) {
                        $respuestaCheck = checkHora($horarioInicio, $horarioFin, $entrada);
                        if (!$respuestaCheck) $estado = false;
                    }
                    if (!is_null($salida)) {
                        $respuestaCheck = checkHora($horarioInicio, $horarioFin, $salida);
                        if (!$respuestaCheck) $estado = false;
                    }
                    if ($estado) {
                        $marcacion = new marcacion_puerta();
                        $marcacion->marcaMov_fecha = $entrada;
                        $marcacion->marcaMov_emple_id = $idEmpleado;
                        $marcacion->organi_id = session('sesionidorg');
                        $marcacion->horarioEmp_id = $idHorarioE;
                        $marcacion->marcaMov_salida = $salida;
                        $marcacion->save();
                        return response()->json($marcacion->marcaMov_id, 200);
                    } else {
                        return response()->json(array("respuesta" => "Marcación fuera de horario."), 200);
                    }
                } else {
                    return response()->json(array("respuesta" => "Posibilidad de cruce de hora."), 200);
                }
            }
        } else {
            $fecha = Carbon::parse($fechaM);
            $fechaNext = $fecha->copy()->addDays(1)->isoFormat("YYYY-MM-DD");
            $entrada = $inicio == null ? NULL : Carbon::parse($fecha->isoFormat("YYYY-MM-DD") . " " . $inicio);
            $salida = $fin == null ? NULL : Carbon::parse($fecha->isoFormat("YYYY-MM-DD") . " " . $fin);
            // * QUE NO SE ENCUENTRE VACIOS NINGUNO DE LOS DOS
            if (!is_null($fin) && !is_null($inicio)) {
                if ($fin > $inicio) {
                    $marcacion = new marcacion_puerta();
                    $marcacion->marcaMov_fecha = $entrada;
                    $marcacion->marcaMov_emple_id = $idEmpleado;
                    $marcacion->organi_id = session('sesionidorg');
                    $marcacion->horarioEmp_id = NULL;
                    $marcacion->marcaMov_salida = $salida;
                    $marcacion->save();
                    return response()->json($marcacion->marcaMov_id, 200);
                } else {
                    return response()->json(array("respuesta" => "Hora salida debe ser menor a la hora de entrada."), 200);
                }
            } else {
                $respuesta = true;
                foreach ($marcacionesValidar as $mv) {
                    if (!is_null($entrada)) {
                        $entrada = Carbon::parse($entrada)->isoFormat("YYYY-MM-DD H:mm:ss");
                        if ($mv->entrada != 0 && $mv->salida != 0) {
                            $respuestaCheck = checkHora($mv->entrada, $mv->salida, $entrada);
                            if ($respuestaCheck) {
                                $respuesta = false;
                            }
                        }
                        if ($mv->entrada != 0) {
                            $horaMinutoV = Carbon::create($mv->entrada)->format('H:i');
                            $horaMinutoE = Carbon::create($entrada)->format('H:i');
                            if ($horaMinutoV == $horaMinutoE) {
                                $respuesta = false;
                            }
                        }
                        if ($mv->salida != 0) {
                            $horaMinutoV = Carbon::create($mv->salida)->format('H:i');
                            $horaMinutoE = Carbon::create($entrada)->format('H:i');
                            if ($horaMinutoV == $horaMinutoE) {
                                $respuesta = false;
                            }
                        }
                    }
                    if (!is_null($salida)) {
                        $salida = Carbon::parse($salida)->isoFormat("YYYY-MM-DD H:mm:ss");
                        if ($mv->entrada != 0 && $mv->salida != 0) {
                            $respuestaCheck = checkHora($mv->entrada, $mv->salida, $salida);
                            if ($respuestaCheck) {
                                $respuesta = false;
                            }
                        }
                        if ($mv->entrada != 0) {
                            $horaMinutoV = Carbon::create($mv->entrada)->format('H:i');
                            $horaMinutoE = Carbon::create($salida)->format('H:i');
                            if ($horaMinutoV == $horaMinutoE) {
                                $respuesta = false;
                            }
                        }
                        if ($mv->salida != 0) {
                            $horaMinutoV = Carbon::create($mv->salida)->format('H:i');
                            $horaMinutoE = Carbon::create($salida)->format('H:i');
                            if ($horaMinutoV == $horaMinutoE) {
                                $respuesta = false;
                            }
                        }
                    }
                }
                // ! SI NO ENCUENTRA CRUCES
                if ($respuesta) {
                    $marcacion = new marcacion_puerta();
                    $marcacion->marcaMov_fecha = $entrada;
                    $marcacion->marcaMov_emple_id = $idEmpleado;
                    $marcacion->organi_id = session('sesionidorg');
                    $marcacion->horarioEmp_id = NULL;
                    $marcacion->marcaMov_salida = $salida;
                    $marcacion->save();
                    return response()->json($marcacion->marcaMov_id, 200);
                } else {
                    return response()->json(array("respuesta" => "Posibilidad de cruce de hora."), 200);
                }
            }
        }
    }
}
