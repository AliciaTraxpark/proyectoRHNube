<?php

namespace App\Http\Controllers;

use App\dispositivo_area;
use App\dispositivo_controlador;
use App\dispositivo_empleado;
use App\dispositivos;
use App\eventos_empleado;
use App\horario_empleado;
use App\incidencias;
use App\marcacion_puerta;
use App\pausas_horario;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;

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

        $tipo_biometrico = DB::table('tipo_biometrico')
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
                        'modifPuerta' => $permiso_invitado->modifPuerta, 'controladores' => $controladores, 'area' => $area, 'empleado' => $empleados,
                        'tipo_biometrico'=>$tipo_biometrico
                    ]);
                } else {
                    return redirect('/dashboard');
                }
                /*   */
            } else {
                return view('Dispositivos.dispositivos', ['controladores' => $controladores, 'area' => $area, 'empleado' => $empleados,
                'tipo_biometrico'=>$tipo_biometrico]);
            }
        } else {

            $empleados = DB::table('empleado as e')
                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.emple_estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('e.asistencia_puerta', '=', 1)
                ->get();

            return view('Dispositivos.dispositivos', ['controladores' => $controladores, 'area' => $area, 'empleado' => $empleados,
            'tipo_biometrico'=>$tipo_biometrico]);
        }
    }
    public function store(Request $request)
    {

        /*   dd($request->tData,$request->lectura); */

        //*ENCRIPTAMOS ID DE ORG
        $idorEncrip = base64_encode(session('sesionidorg'));
        $codigo = STR::random(4).$idorEncrip;

        /* dd($idorEncrip,base64_decode($idorEncrip)); */
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
                    "Authorization: zAS+nYnqJ+zX8KBr05ojMufSWuo=",
                    "Cache-Control: no-cache"
                ),
            ));
            $err = curl_error($curl);
            $response = curl_exec($curl);
            if($response==false){
                return 'Hubo un error en el servidor de mensajeria, no se pudo enviar el SMS';
            }
        }
    }
    public function enviarmensaje(Request $request)
    {
        $idorEncrip = base64_encode(session('sesionidorg'));
        $codigo = STR::random(4).$idorEncrip;
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
                "Authorization: zAS+nYnqJ+zX8KBr05ojMufSWuo=",
                "Cache-Control: no-cache"
            ),
        ));
        $err = curl_error($curl);
        $response = curl_exec($curl);
        if($response==false){
            return 'Hubo un error en el servidor de mensajeria, no se pudo enviar el SMS';
        }
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
                "Authorization: zAS+nYnqJ+zX8KBr05ojMufSWuo=",
                "Cache-Control: no-cache"
            ),
        ));
        $err = curl_error($curl);
        $response = curl_exec($curl);
      
        if($response==false){
            return 'Hubo un error en el servidor de mensajeria, no se pudo enviar el SMS';
        }
    }

    public function tablaDisposit()
    {
        $dispositivos = dispositivos::where('organi_id', '=', session('sesionidorg'))->get();
        return json_encode($dispositivos);
    }

    public function comprobarMovil(Request $request)
    {

        $dispositivos = dispositivos::where('dispo_movil', '=', $request->numeroM)
        ->where('organi_id','=',session('sesionidorg'))->get()->first();

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

    // * DETALLE DE ASISTENCIA Y POR FECHA
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
                if (!isset($resultado[$empleado->emple_id]->data[$empleado->idHorarioE]["horario"])) {
                    $resultado[$empleado->emple_id]->data[$empleado->idHorarioE]["horario"] =  (object) array(
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
                if (!isset($resultado[$empleado->emple_id]->data[$empleado->idHorarioE]["pausas"])) {
                    $resultado[$empleado->emple_id]->data[$empleado->idHorarioE]["pausas"] = array();
                }
                if (!isset($resultado[$empleado->emple_id]->incidencias)) {
                    $resultado[$empleado->emple_id]->incidencias = array();
                }
                if (!isset($resultado[$empleado->emple_id]->data[$empleado->idHorarioE]["marcaciones"])) {
                    $resultado[$empleado->emple_id]->data[$empleado->idHorarioE]["marcaciones"] = array();
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
                array_push($resultado[$empleado->emple_id]->data[$empleado->idHorarioE]["marcaciones"], $arrayMarcacion);
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
            ->where(DB::raw('IF(hoe.horarioEmp_id is null, IF(mp.marcaMov_fecha is null,DATE(mp.marcaMov_salida) , DATE(mp.marcaMov_fecha)) , DATE(hd.start))'), '=', $fecha)
            ->where('mp.organi_id', '=', session('sesionidorg'))
            ->orderBy(
                DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida) , DATE(mp.marcaMov_fecha))'),
                'ASC'
            )
            ->orderBy(
                DB::raw('IF(mp.marcaMov_fecha is null, TIME(mp.marcaMov_salida) , TIME(mp.marcaMov_fecha))'),
                'ASC'
            )
            ->get();
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
                    'he.estado as estado',
                    'he.horarioEmp_id as idHorarioE',
                    'h.horasObliga as horasObligadas'
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
                        'horario_id',
                        'descontar'
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
                ->where('id_empleado', '=', $idEmpleado)
                ->whereBetween(DB::raw('DATE(eventos_empleado.start)'), [$fecha, $fecha])
                ->orWhere(function ($query) use ($fecha, $idEmpleado) {
                    $query->where('id_empleado', '=', $idEmpleado);
                    $query->whereNotNull('eventos_empleado.end');
                    $query->where(DB::raw('DATE(eventos_empleado.start)'), '<=', $fecha);
                    $query->where(DB::raw('DATE(eventos_empleado.end)'), '>', $fecha);
                })
                ->get();
            foreach ($eventos as $e) {
                array_push($m->incidencias, $e);
            }
            // * TABLA INCIDENCIAS DIA
            $incidencias = DB::table('incidencia_dias as id')
                ->join('incidencias as i', 'i.inciden_id', '=', 'id.id_incidencia')
                ->select('i.inciden_descripcion as descripcion')
                ->where('id.id_empleado', '=', $idEmpleado)
                ->whereBetween('id.inciden_dias_fechaI', [$fecha, $fecha])
                ->orWhere(function ($query) use ($fecha, $idEmpleado) {
                    $query->where('id.id_empleado', '=', $idEmpleado);
                    $query->where('id.inciden_dias_fechaI', '<=', $fecha);
                    $query->where('id.inciden_dias_fechaF', '>', $fecha);
                })
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
                'dispo_porArea',
                'idtipo_biometrico'
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
        $lectura = $request->lectura_ed;

        //*ASIGNANDO
        if (in_array("1", $lectura)) {
            $dispositivos->dispo_Manu = 1;
        } else {
            $dispositivos->dispo_Manu = 0;
        }

        if (in_array("2", $lectura)) {

            $dispositivos->dispo_Scan = 1;
        } else {
            $dispositivos->dispo_Scan = 0;
        }

        if (in_array("3", $lectura)) {

            $dispositivos->dispo_Cam = 1;
        } else {
            $dispositivos->dispo_Cam = 0;
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

    // * REPORTE POR EMPLEADO
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
                        "emple_id" => $empleado->emple_id
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
                if (!isset($resultado[$empleado->emple_id]->datos[$empleado->entradaModif][$empleado->idhorario]->incidencias)) {
                    $resultado[$empleado->emple_id]->datos[$empleado->entradaModif][$empleado->idhorario]->incidencias = array();
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
                $empleado = DB::table('empleado as e')
                    ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                    ->select(
                        'e.emple_id',
                        'o.organi_razonSocial',
                        'o.organi_direccion',
                        'o.organi_ruc',
                        'ar.area_descripcion',
                        'e.emple_nDoc',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion'
                    )
                    ->where('e.emple_id', $idemp)
                    ->get();
                $marcaciones = DB::table('empleado as e')
                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                    ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                    ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                    ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                    ->select(
                        'e.emple_id',
                        DB::raw('IF(hoe.horarioEmp_id is null,IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha)), DATE(hd.start)) as entradaModif'),
                        DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id) as idhorario'),
                        'mp.organi_id',
                        DB::raw('IF(hor.horario_id is null, 0 , hor.horario_descripcion) as horario'),
                        DB::raw("IF(hor.horaF is null , 0 , IF(hor.horaF > hor.horaI,CONCAT( DATE(hd.start),' ', hor.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', hor.horaF))) as horarioFin"),
                        DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                        'mp.marcaMov_id as idMarcacion',
                        'hor.horario_tolerancia as tolerancia',
                        DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                        DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida')
                    )
                    ->whereBetween(DB::raw('IF(hoe.horarioEmp_id is null,IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha)), DATE(hd.start))'), [$fecha, $fechaF])
                    ->where('e.emple_id', $idemp)
                    ->where('mp.organi_id', '=', session('sesionidorg'))
                    ->orderBy(
                        DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida) , DATE(mp.marcaMov_fecha))'),
                        'ASC'
                    )
                    ->orderBy(
                        DB::raw('IF(mp.marcaMov_fecha is null, TIME(mp.marcaMov_salida) , TIME(mp.marcaMov_fecha))'),
                        'ASC'
                    )
                    ->get();
                $marcaciones = agruparPorFechayHorario($marcaciones);
            } else {
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();
                if ($invitado_empleadoIn != null) {
                    $empleado = DB::table('empleado as e')
                        ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                        ->select(
                            'e.emple_id',
                            'o.organi_razonSocial',
                            'o.organi_direccion',
                            'o.organi_ruc',
                            'ar.area_descripcion',
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion'
                        )
                        ->where('e.emple_id', $idemp)
                        ->get();
                    $marcaciones = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                        ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                        ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                        ->select(
                            'e.emple_id',
                            'o.organi_razonSocial',
                            'o.organi_direccion',
                            'o.organi_ruc',
                            DB::raw('IF(hoe.horarioEmp_id is null,IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha)), DATE(hd.start)) as entradaModif'),
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
                        ->whereBetween(DB::raw('IF(hoe.horarioEmp_id is null,IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha)), DATE(hd.start))'), [$fecha, $fechaF])
                        ->where('e.emple_id', $idemp)
                        ->where('mp.organi_id', '=', session('sesionidorg'))
                        ->orderBy(
                            DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida) , DATE(mp.marcaMov_fecha))'),
                            'ASC'
                        )
                        ->orderBy(
                            DB::raw('IF(mp.marcaMov_fecha is null, TIME(mp.marcaMov_salida) , TIME(mp.marcaMov_fecha))'),
                            'ASC'
                        )
                        ->get();
                    $marcaciones = agruparPorFechayHorario($marcaciones);
                } else {
                    $empleado = DB::table('empleado as e')
                        ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                        ->select(
                            'e.emple_id',
                            'o.organi_razonSocial',
                            'o.organi_direccion',
                            'o.organi_ruc',
                            'ar.area_descripcion',
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion'
                        )
                        ->where('e.emple_id', $idemp)
                        ->get();
                    $marcaciones = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                        ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                        ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                        ->select(
                            'e.emple_id',
                            DB::raw('IF(hoe.horarioEmp_id is null,IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha)), DATE(hd.start)) as entradaModif'),
                            DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id) as idhorario'),
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
                        ->whereBetween(DB::raw('IF(hoe.horarioEmp_id is null,IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha)), DATE(hd.start))'), [$fecha, $fechaF])
                        ->where('e.emple_id', $idemp)
                        ->where('mp.organi_id', '=', session('sesionidorg'))
                        ->orderBy(
                            DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida) , DATE(mp.marcaMov_fecha))'),
                            'ASC'
                        )
                        ->orderBy(
                            DB::raw('IF(mp.marcaMov_fecha is null, TIME(mp.marcaMov_salida) , TIME(mp.marcaMov_fecha))'),
                            'ASC'
                        )
                        ->get();
                    $marcaciones = agruparPorFechayHorario($marcaciones);
                }
            }
        } else {
            $empleado = DB::table('empleado as e')
                ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                ->select(
                    'e.emple_id',
                    'o.organi_razonSocial',
                    'o.organi_direccion',
                    'o.organi_ruc',
                    'ar.area_descripcion',
                    'e.emple_nDoc',
                    'p.perso_nombre',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    'c.cargo_descripcion'
                )
                ->where('e.emple_id', $idemp)
                ->get();
            $marcaciones = DB::table('empleado as e')
                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                ->select(
                    'e.emple_id',
                    DB::raw('IF(hoe.horarioEmp_id is null,IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha)), DATE(hd.start)) as entradaModif'),
                    DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id) as idhorario'),
                    'mp.organi_id',
                    DB::raw('IF(hor.horario_id is null, 0 , hor.horario_descripcion) as horario'),
                    DB::raw("IF(hor.horaF is null , 0 , IF(hor.horaF > hor.horaI,CONCAT( DATE(hd.start),' ', hor.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', hor.horaF))) as horarioFin"),
                    DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                    'mp.marcaMov_id as idMarcacion',
                    'hor.horario_tolerancia as tolerancia',
                    DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                    DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida')
                )
                ->whereBetween(DB::raw('IF(hoe.horarioEmp_id is null,IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha)), DATE(hd.start))'), [$fecha, $fechaF])
                ->where('e.emple_id', $idemp)
                ->where('mp.organi_id', '=', session('sesionidorg'))
                ->orderBy(
                    DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida) , DATE(mp.marcaMov_fecha))'),
                    'ASC'
                )
                ->orderBy(
                    DB::raw('IF(mp.marcaMov_fecha is null, TIME(mp.marcaMov_salida) , TIME(mp.marcaMov_fecha))'),
                    'ASC'
                )
                ->get();
            $marcaciones = agruparPorFechayHorario($marcaciones);
        }
        if (sizeof($marcaciones) != 0) {
            foreach ($marcaciones as $m) {
                if ($m->emple_id == $empleado[0]->emple_id) {
                    $empleado[0]->datos = $m->datos;
                }
            }
        } else {
            $empleado[0]->datos = array();
        }
        $marcaciones = $empleado;
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
                // * *********************** INCIDENCIAS ***********************
                $idEmpleado = $m->emple_id;
                // * TABLA EVENTOS EMPLEADO
                $eventos = eventos_empleado::select('title as descripcion')
                    ->where('id_empleado', '=', $idEmpleado)
                    ->whereBetween(DB::raw('DATE(eventos_empleado.start)'), [$d, $d])
                    ->orWhere(function ($query) use ($d, $idEmpleado) {
                        $query->where('id_empleado', '=', $idEmpleado);
                        $query->whereNotNull('eventos_empleado.end');
                        $query->where(DB::raw('DATE(eventos_empleado.start)'), '<=', $d);
                        $query->where(DB::raw('DATE(eventos_empleado.end)'), '>', $d);
                    })
                    ->get();
                // * TABLA INCIDENCIAS DIA
                $incidencias = DB::table('incidencia_dias as id')
                    ->join('incidencias as i', 'i.inciden_id', '=', 'id.id_incidencia')
                    ->select('i.inciden_descripcion as descripcion')
                    ->where('id.id_empleado', '=', $idEmpleado)
                    ->whereBetween('id.inciden_dias_fechaI', [$d, $d])
                    ->orWhere(function ($query) use ($d, $idEmpleado) {
                        $query->where('id.id_empleado', '=', $idEmpleado);
                        $query->where('id.inciden_dias_fechaI', '<=', $d);
                        $query->where('id.inciden_dias_fechaF', '>', $d);
                    })
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
                    $arrayP = [];
                    // * PAUSAS
                    if ($idHorario != 0) {
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
                        foreach ($pausas as $p) {
                            array_push($arrayP, $p);
                        }
                    }
                    $m->datos[$key][$item]->pausas = $arrayP;
                }
            }
            $m->datos = Arr::flatten($m->datos);
        }
        return response()->json((array)Arr::first($marcaciones), 200);
    }

    // * BUSCAR MARCACIONES SIN PAREJA X EMPLEADO
    function buscarMarcacionPorEmpleado(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $fecha = $request->get('fecha');
        $marcacion = marcacion_puerta::select('marcaMov_id')
            ->leftJoin('horario_empleado as hoe', 'marcacion_puerta.horarioEmp_id', '=', 'hoe.horarioEmp_id')
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
            ->whereRaw("IF(hoe.horarioEmp_id is null, IF(marcaMov_fecha is null,DATE(marcaMov_salida) , DATE(marcaMov_fecha)) , DATE(hd.start)) = '$fecha'")
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
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
            ->select(
                'mp.marcaMov_id as id',
                'mp.marcaMov_salida as salida',
                DB::raw('IF(mp.horarioEmp_id is null, 0 , mp.horarioEmp_id ) as idH'),
                DB::raw('IF(h.horario_descripcion is null , "Sin horario",h.horario_descripcion) as horario')
            )
            ->whereNotNull('mp.marcaMov_salida')
            ->whereNull('mp.marcaMov_fecha')
            ->whereRaw("IF(he.horarioEmp_id is null, IF(marcaMov_fecha is null,DATE(marcaMov_salida) , DATE(marcaMov_fecha)) , DATE(hd.start)) = '$fecha'")
            ->where('mp.marcaMov_emple_id', '=', $idEmpleado)
            ->get();

        $salidas = agruparMarcacionesHorario($salidas);

        return response()->json($salidas, 200);
    }

    // * CAMBIAR ENTRADA
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
                $fechaS = Carbon::parse($fecha)->addDays(1)->isoFormat("YYYY-MM-DD");
            } else {
                $fecha = Carbon::parse($marcacionCambiar->marcaMov_salida)->isoFormat('YYYY-MM-DD');
                $fechaS = Carbon::parse($fecha)->addDays(1)->isoFormat("YYYY-MM-DD");
            }
            if (Carbon::parse($nuevaSalida)->gt(Carbon::parse($nuevaEntrada))) {
                // DB::enableQueryLog();
                if (!empty($marcacion->horarioEmp_id)) {
                    $marcacionesValidar = DB::table('marcacion_puerta as m')
                        ->select(
                            'm.marcaMov_id',
                            DB::raw('IF(m.marcaMov_fecha is null,0,m.marcaMov_fecha) AS entrada'),
                            DB::raw('IF(m.marcaMov_salida is null,0,m.marcaMov_salida) AS salida')
                        )
                        ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                        ->whereIn(DB::raw('IF(m.marcaMov_fecha is null,DATE(m.marcaMov_salida),DATE(m.marcaMov_fecha))'), [$fecha, $fechaS])
                        ->whereNotIn('m.marcaMov_id', [$marcacion->marcaMov_id])
                        ->get();
                }
                // dd(DB::getQueryLog());
                $respuesta = true;
                foreach ($marcacionesValidar as $mv) {
                    if (!($mv->marcaMov_id == $marcacionCambiar->marcaMov_id)) {
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
                        if ($tipo  == 2 ?  !empty($marcacionCambiar->marcaMov_fecha) : !empty($marcacionCambiar->marcaMov_salida)) {
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
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->select(
                                'h.horario_descripcion as descripcion',
                                DB::raw("CONCAT( DATE(hd.start),' ', h.horaI) as horaI"),
                                DB::raw("IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF)) as horaF"),
                                'h.horario_tolerancia as toleranciaI',
                                'h.horario_toleranciaF as toleranciaF',
                                'he.fuera_horario as fueraH',
                                DB::raw('IF(he.nHoraAdic is null, 0 ,he.nHoraAdic) as horasA'),
                                'h.horasObliga as horasO'
                            )
                            ->where('he.horarioEmp_id', '=', $marcacion->horarioEmp_id)
                            ->get()
                            ->first();
                        $horarioInicio = Carbon::parse($horario->horaI);
                        $horarioIniT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
                        $horarioFin = Carbon::parse($horario->horaF);
                        $horarioFinT = $horarioFin->copy()->subMinutes($horario->toleranciaF);
                        // : SUMAR TIEMPOS DE MARCACIONES
                        $sumaTotalDeHoras = DB::table('marcacion_puerta as m')
                            ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaMov_salida,m.marcaMov_fecha)))) as totalT'))
                            ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                            ->whereNotNull('m.marcaMov_fecha')
                            ->whereNotNull('m.marcaMov_salida')
                            ->where(DB::raw('DATE(marcaMov_fecha)'), '=', $entrada->copy()->isoFormat('YYYY-MM-DD'))
                            ->where('m.horarioEmp_id', '=', $marcacion->horarioEmp_id)
                            ->where('m.marcaMov_id', '!=', $marcacionCambiar->marcaMov_id)
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
                            if ($entrada->gte($horarioIniT) && $entrada->lte($horarioFinT)) {
                                // ! MARCACION A REGISTRAR ENTRADA
                                $marcacion->marcaMov_fecha = $nuevaEntrada;
                                if ($tipo == 2) {
                                    $marcacion->controladores_idControladores = $marcacionCambiar->controladores_salida;
                                    $marcacion->dispositivoEntrada = $marcacionCambiar->dispositivoSalida;
                                } else {
                                    $marcacion->controladores_idControladores  = $marcacionCambiar->controladores_idControladores;
                                    $marcacion->dispositivoEntrada = $marcacionCambiar->dispositivoEntrada;
                                }
                                $marcacion->save();
                                // ! MARCACION A CAMBIAR
                                if ($tipo == 2) {
                                    $marcacionCambiar->marcaMov_salida = NULL;
                                    $marcacionCambiar->controladores_salida = NULL;
                                    $marcacionCambiar->dispositivoSalida = NULL;
                                } else {
                                    $marcacionCambiar->marcaMov_fecha = NULL;
                                    $marcacionCambiar->controladores_idControladores  = NULL;
                                    $marcacionCambiar->dispositivoEntrada = NULL;
                                }
                                $marcacionCambiar->save();
                            } else {
                                return response()->json(
                                    array("respuesta" => "Marcación fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . $horario->horaI . " - " . $horario->horaF . " )"),
                                    200
                                );
                            }
                        } else {
                            return response()->json(
                                array("respuesta" => "Sobretiempo en la marcación."),
                                200
                            );
                        }
                    } else {
                        // ! MARCACION A REGISTRAR ENTRADA
                        $marcacion->marcaMov_fecha = $nuevaEntrada;
                        if ($tipo == 2) {
                            $marcacion->controladores_idControladores = $marcacionCambiar->controladores_salida;
                            $marcacion->dispositivoEntrada = $marcacionCambiar->dispositivoSalida;
                        } else {
                            $marcacion->controladores_idControladores  = $marcacionCambiar->controladores_idControladores;
                            $marcacion->dispositivoEntrada = $marcacionCambiar->dispositivoEntrada;
                        }
                        $marcacion->save();
                        // ! MARCACION A CAMBIAR
                        if ($tipo == 2) {
                            $marcacionCambiar->marcaMov_salida = NULL;
                            $marcacionCambiar->controladores_salida = NULL;
                            $marcacionCambiar->dispositivoSalida = NULL;
                        } else {
                            $marcacionCambiar->marcaMov_fecha = NULL;
                            $marcacionCambiar->controladores_idControladores  = NULL;
                            $marcacionCambiar->dispositivoEntrada = NULL;
                        }
                        $marcacionCambiar->save();
                    }
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
            if (!empty($marcacion->horarioEmp_id)) {
                $fecha = Carbon::parse($marcacion->marcaMov_salida);
                $horario = $horario = DB::table('horario_empleado as he')
                    ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->select(
                        'h.horario_descripcion as descripcion',
                        DB::raw("CONCAT( DATE(hd.start),' ', h.horaI) as horaI"),
                        DB::raw("IF(h.horaF is null , 0 , IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF))) as horaF"),
                        'h.horario_tolerancia as toleranciaI',
                        'h.horario_toleranciaF as toleranciaF',
                        'he.fuera_horario as fueraH',
                        DB::raw('IF(he.nHoraAdic is null, 0 ,he.nHoraAdic) as horasA'),
                        'h.horasObliga as horasO'
                    )
                    ->where('he.horarioEmp_id', '=', $marcacion->horarioEmp_id)
                    ->get()
                    ->first();
                $horarioInicio = Carbon::parse($horario->horaI);
                $horarioIniT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
                $horarioFin = Carbon::parse($horario->horaF);
                $horarioFinT = $horarioFin->copy()->subMinutes($horario->toleranciaF);
                if ($fecha->gte($horarioIniT) && $fecha->lte($horarioFinT)) {
                    $marcacion->marcaMov_fecha = $marcacion->marcaMov_salida;
                    $marcacion->controladores_idControladores = $marcacion->controladores_salida;
                    $marcacion->dispositivoEntrada = $marcacion->dispositivoSalida;
                    $marcacion->marcaMov_salida = NULL;
                    $marcacion->save();
                } else {
                    return response()->json(
                        array("respuesta" => "Marcación fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . Carbon::parse($horario->horaI)->isoFormat("HH:mm:ss") . " - " . Carbon::parse($horario->horaF)->isoFormat("HH:mm:ss") . " )"),
                        200
                    );
                }
            } else {
                $marcacion->marcaMov_fecha = $marcacion->marcaMov_salida;
                $marcacion->controladores_idControladores = $marcacion->controladores_salida;
                $marcacion->controladores_salida = NULL;
                $marcacion->dispositivoEntrada = $marcacion->dispositivoSalida;
                $marcacion->dispositivoSalida = NULL;
                $marcacion->marcaMov_salida = NULL;
                $marcacion->save();
            }

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
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
            ->select(
                'mp.marcaMov_id as id',
                'mp.marcaMov_fecha as entrada',
                DB::raw('IF(mp.horarioEmp_id is null, 0 , mp.horarioEmp_id ) as idH'),
                DB::raw('IF(h.horario_descripcion is null , "Sin horario",h.horario_descripcion) as horario')
            )
            ->whereNotNull('mp.marcaMov_fecha')
            ->whereNull('mp.marcaMov_salida')
            ->whereRaw("IF(he.horarioEmp_id is null, IF(marcaMov_fecha is null,DATE(marcaMov_salida) , DATE(marcaMov_fecha)) , DATE(hd.start)) = '$fecha'")
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
                $fechaS = Carbon::parse($fecha)->addDays(1)->isoFormat("YYYY-MM-DD");
            } else {
                $fecha = Carbon::parse($marcacionCambiar->marcaMov_salida)->isoFormat('YYYY-MM-DD');
                $fechaS = Carbon::parse($fecha)->addDays(1)->isoFormat("YYYY-MM-DD");
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
                    ->whereIn(DB::raw('IF(m.marcaMov_fecha is null,DATE(m.marcaMov_salida),DATE(m.marcaMov_fecha))'), [$fecha, $fechaS])
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
                    if (!empty($marcacion->horarioEmp_id)) {
                        $entrada = Carbon::parse($nuevaEntrada);
                        $salida = Carbon::parse($nuevaSalida);
                        $horario = DB::table('horario_empleado as he')
                            ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->select(
                                'h.horario_descripcion as descripcion',
                                DB::raw("CONCAT( DATE(hd.start),' ', h.horaI) as horaI"),
                                DB::raw("IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF)) as horaF"),
                                'h.horario_tolerancia as toleranciaI',
                                'h.horario_toleranciaF as toleranciaF',
                                'he.fuera_horario as fueraH',
                                DB::raw('IF(he.nHoraAdic is null, 0 ,he.nHoraAdic) as horasA'),
                                'h.horasObliga as horasO'
                            )
                            ->where('he.horarioEmp_id', '=', $marcacion->horarioEmp_id)
                            ->get()
                            ->first();
                        $horarioFin = Carbon::parse($horario->horaF);
                        $horarioFinT = $horarioFin->copy()->addMinutes($horario->toleranciaF);
                        // : SUMAR TIEMPOS DE MARCACIONES
                        $sumaTotalDeHoras = DB::table('marcacion_puerta as m')
                            ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaMov_salida,m.marcaMov_fecha)))) as totalT'))
                            ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                            ->whereNotNull('m.marcaMov_fecha')
                            ->whereNotNull('m.marcaMov_salida')
                            ->where(DB::raw('DATE(marcaMov_fecha)'), '=', $entrada->copy()->isoFormat('YYYY-MM-DD'))
                            ->where('m.horarioEmp_id', '=', $marcacion->horarioEmp_id)
                            ->where('m.marcaMov_id', '!=', $marcacionCambiar->marcaMov_id)
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
                                    $marcacion->marcaMov_salida = $nuevaSalida;
                                    if ($tipo == 2) {
                                        $marcacion->controladores_salida = $marcacionCambiar->controladores_salida;
                                        $marcacion->dispositivoSalida = $marcacionCambiar->dispositivoSalida;
                                    } else {
                                        $marcacion->controladores_salida  = $marcacionCambiar->controladores_idControladores;
                                        $marcacion->dispositivoSalida = $marcacionCambiar->dispositivoEntrada;
                                    }
                                    $marcacion->save();
                                    // ! MARCACION A CAMBIAR
                                    if ($tipo == 2) {
                                        $marcacionCambiar->marcaMov_salida = NULL;
                                        $marcacionCambiar->controladores_salida = NULL;
                                        $marcacionCambiar->dispositivoSalida = NULL;
                                    } else {
                                        $marcacionCambiar->marcaMov_fecha = NULL;
                                        $marcacionCambiar->controladores_idControladores  = NULL;
                                        $marcacionCambiar->dispositivoEntrada = NULL;
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
                                $marcacion->marcaMov_salida = $nuevaSalida;
                                if ($tipo == 2) {
                                    $marcacion->controladores_salida = $marcacionCambiar->controladores_salida;
                                    $marcacion->dispositivoSalida = $marcacionCambiar->dispositivoSalida;
                                } else {
                                    $marcacion->controladores_salida  = $marcacionCambiar->controladores_idControladores;
                                    $marcacion->dispositivoSalida = $marcacionCambiar->dispositivoEntrada;
                                }
                                $marcacion->save();
                                // ! MARCACION A CAMBIAR
                                if ($tipo == 2) {
                                    $marcacionCambiar->marcaMov_salida = NULL;
                                    $marcacionCambiar->controladores_salida = NULL;
                                    $marcacionCambiar->dispositivoSalida = NULL;
                                } else {
                                    $marcacionCambiar->marcaMov_fecha = NULL;
                                    $marcacionCambiar->controladores_idControladores  = NULL;
                                    $marcacionCambiar->dispositivoEntrada = NULL;
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
                        $marcacion->marcaMov_salida = $nuevaSalida;
                        if ($tipo == 2) {
                            $marcacion->controladores_salida = $marcacionCambiar->controladores_salida;
                            $marcacion->dispositivoSalida = $marcacionCambiar->dispositivoSalida;
                        } else {
                            $marcacion->controladores_salida  = $marcacionCambiar->controladores_idControladores;
                            $marcacion->dispositivoSalida = $marcacionCambiar->dispositivoEntrada;
                        }
                        $marcacion->save();
                        // ! MARCACION A CAMBIAR
                        if ($tipo == 2) {
                            $marcacionCambiar->marcaMov_salida = NULL;
                            $marcacionCambiar->controladores_salida = NULL;
                            $marcacionCambiar->dispositivoSalida = NULL;
                        } else {
                            $marcacionCambiar->marcaMov_fecha = NULL;
                            $marcacionCambiar->controladores_idControladores  = NULL;
                            $marcacionCambiar->dispositivoEntrada = NULL;
                        }
                        $marcacionCambiar->save();
                    }
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
            if (!empty($marcacion->horarioEmp_id)) {
                $fecha = Carbon::parse($marcacion->marcaMov_salida);
                $horario = $horario = DB::table('horario_empleado as he')
                    ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->select(
                        'h.horario_descripcion as descripcion',
                        DB::raw("CONCAT( DATE(hd.start),' ', h.horaI) as horaI"),
                        DB::raw("IF(h.horaF is null , 0 , IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF))) as horaF"),
                        'h.horario_tolerancia as toleranciaI',
                        'h.horario_toleranciaF as toleranciaF',
                        'he.fuera_horario as fueraH',
                        DB::raw('IF(he.nHoraAdic is null, 0 ,he.nHoraAdic) as horasA'),
                        'h.horasObliga as horasO'
                    )
                    ->where('he.horarioEmp_id', '=', $marcacion->horarioEmp_id)
                    ->get()
                    ->first();
                $horarioInicio = Carbon::parse($horario->horaI);
                $horarioIniT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
                $horarioFin = Carbon::parse($horario->horaF);
                $horarioFinT = $horarioFin->copy()->subMinutes($horario->toleranciaF);
                if ($horario->fueraH == 0) {
                    if ($fecha->gte($horarioIniT) && $fecha->lte($horarioFinT)) {
                        $marcacion->marcaMov_salida = $marcacion->marcaMov_fecha;
                        $marcacion->controladores_salida = $marcacion->controladores_idControladores;
                        $marcacion->controladores_idControladores = NULL;
                        $marcacion->dispositivoSalida = $marcacion->dispositivoEntrada;
                        $marcacion->dispositivoEntrada = NULL;
                        $marcacion->marcaMov_fecha = NULL;
                        $marcacion->save();
                    } else {
                        return response()->json(
                            array("respuesta" => "Marcación fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . Carbon::parse($horario->horaI)->isoFormat("HH:mm:ss") . " - " . Carbon::parse($horario->horaF)->isoFormat("HH:mm:ss") . " )"),
                            200
                        );
                    }
                } else {
                    $marcacion->marcaMov_salida = $marcacion->marcaMov_fecha;
                    $marcacion->controladores_salida = $marcacion->controladores_idControladores;
                    $marcacion->controladores_idControladores = NULL;
                    $marcacion->dispositivoSalida = $marcacion->dispositivoEntrada;
                    $marcacion->dispositivoEntrada = NULL;
                    $marcacion->marcaMov_fecha = NULL;
                    $marcacion->save();
                }
            } else {
                $marcacion->marcaMov_salida = $marcacion->marcaMov_fecha;
                $marcacion->controladores_salida = $marcacion->controladores_idControladores;
                $marcacion->controladores_idControladores = NULL;
                $marcacion->dispositivoSalida = $marcacion->dispositivoEntrada;
                $marcacion->dispositivoEntrada = NULL;
                $marcacion->marcaMov_fecha = NULL;
                $marcacion->save();
            }
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
        if (!empty($marcacion->horarioEmp_id)) {
            $fechaHorario = horario_empleado::where('horarioEmp_id', '=', $marcacion->horarioEmp_id)
                ->join('horario_dias as hd', 'hd.id', '=', 'horario_empleado.horario_dias_id')
                ->select(DB::raw('DATE(hd.start) as fecha'))
                ->get()
                ->first();
            $fechaM = $fechaHorario->fecha;
        } else {
            $fechaM = $tipo == 2 ? $marcacion->marcaMov_salida : $marcacion->marcaMov_fecha;
        }
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
            // * VALIDACION DE HORARIO CON EL TIEMPO DE MARCACION
            $arrayHorario = (object) array(
                "id" => $h->id,
                "horario_descripcion" => $h->horario_descripcion,
                "horaI" => $h->horaI,
                "horaF" => $h->horaF
            );

            array_push($respuesta, $arrayHorario);
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
        // * VALIDACION DE HORARIO
        if ($idHorarioE != 0) {
            // * HORARIO EMPLEADO, HORARIO Y HORA DIAS
            $horario = DB::table('horario_empleado as he')
                ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->select(
                    DB::raw("CONCAT( DATE(hd.start),' ', h.horaI) as horaI"),
                    DB::raw("IF(h.horaF is null , 0 , IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF))) as horaF"),
                    'h.horario_tolerancia as toleranciaI',
                    'h.horario_toleranciaF as toleranciaF',
                    'he.fuera_horario as fueraH',
                    DB::raw('IF(he.nHoraAdic is null, 0 ,he.nHoraAdic) as horasA'),
                    DB::raw('DATE(hd.start) as fecha'),
                    'he.empleado_emple_id as idEmpleado',
                    'h.organi_id',
                    'h.horasObliga as horasO'
                )
                ->where('he.horarioEmp_id', '=', $idHorarioE)
                ->get()
                ->first();
            // : TIEMPOS DE HORARIO
            $horarioInicio = Carbon::parse($horario->horaI)->subMinutes($horario->toleranciaI);
            $horarioFin = Carbon::parse($horario->horaF)->addMinutes($horario->toleranciaF);
            // * TOMAR MARCACION PARA NUEVA MARCACION
            if ($marcacionTipo == 1) {
                $marcacionFechaHora = Carbon::parse($marcacion->marcaMov_fecha);
            } else {
                $marcacionFechaHora = Carbon::parse($marcacion->marcaMov_salida);
            }
            // * VALIDAR CON HORARIO
            if ($tipo == 1) {
                if (!($marcacionFechaHora->gte($horarioInicio) && $marcacionFechaHora->lte($horarioFin))) {
                    return response()->json(array("respuesta" => "Marcación fuera de horario."), 200);
                }
            } else {
                if ($horario->fueraH == 0) {
                    if (!($marcacionFechaHora->gte($horarioInicio) && $marcacionFechaHora->lte($horarioFin))) {
                        return response()->json(array("respuesta" => "Marcación fuera de horario."), 200);
                    }
                } else {
                    if (!($marcacionFechaHora->gt($horarioInicio))) {
                        return response()->json(array("respuesta" => "Marcación fuera de horario."), 200);
                    }
                }
            }
        }
        if ($marcacionTipo == 1) {
            if ($idHorarioE == 0) {
                $fecha = Carbon::parse($marcacion->marcaMov_fecha)->isoFormat('YYYY-MM-DD');
            } else {
                $fecha = Carbon::parse($horario->fecha)->isoFormat('YYYY-MM-DD');
            }
            $fechaS = Carbon::parse($fecha)->addDays(1)->isoFormat("YYYY-MM-DD");
        } else {
            if ($idHorarioE == 0) {
                $fecha = Carbon::parse($marcacion->marcaMov_salida)->isoFormat('YYYY-MM-DD');
            } else {
                $fecha = Carbon::parse($horario->fecha)->isoFormat('YYYY-MM-DD');
            }
            $fechaS = Carbon::parse($fecha)->addDays(1)->isoFormat("YYYY-MM-DD");
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
            ->whereIn(DB::raw('IF(m.marcaMov_fecha is null,DATE(m.marcaMov_salida),DATE(m.marcaMov_fecha))'), [$fecha, $fechaS])
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
                // * DISPOSITIVO
                $dispositivoE = $marcacion->dispositivoEntrada;
                $dispositivoS = NULL;
                // * CONTROLADOR
                $controladorE = $marcacion->controladores_idControladores;
                $controladorS = NULL;
            } else {
                $newMarcacion->marcaMov_salida = $nuevaMarcacion;
                // * DISPOSITIVO
                $dispositivoS = $marcacion->dispositivoSalida;
                $dispositivoE = NULL;
                // * CONTROLADOR
                $controladorS = $marcacion->controladores_salida;
                $controladorE = NULL;
            }
            $newMarcacion->marcaMov_emple_id = $marcacion->marcaMov_emple_id;
            $newMarcacion->organi_id =  $marcacion->organi_id;
            $newMarcacion->horarioEmp_id = $idHorarioE == 0 ? NULL : $idHorarioE;
            $newMarcacion->marca_latitud = $marcacion->marca_latitud;
            $newMarcacion->marca_longitud = $marcacion->marca_longitud;
            $newMarcacion->marcaIdActivi  = $marcacion->marcaIdActivi;
            $newMarcacion->puntoC_id = $marcacion->puntoC_id;
            $newMarcacion->centC_id = $marcacion->centC_id;
            $newMarcacion->controladores_idControladores = $controladorE;
            $newMarcacion->controladores_salida = $controladorS;
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
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->select(
                    'h.horario_descripcion as descripcion',
                    DB::raw("CONCAT( DATE(hd.start),' ', h.horaI) as horaI"),
                    DB::raw("IF(h.horaF is null , 0 , IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF))) as horaF"),
                    'h.horario_tolerancia as toleranciaI',
                    'h.horario_toleranciaF as toleranciaF',
                    'he.fuera_horario as fueraH',
                    DB::raw('IF(he.nHoraAdic is null, 0 ,he.nHoraAdic) as horasA'),
                    'h.horasObliga as horasO'
                )
                ->where('he.horarioEmp_id', '=', $idhorarioE)
                ->get()
                ->first();
            // * OBTENER TIEMPO DE HORARIOS
            $horarioInicio = Carbon::parse($horario->horaI);
            $horarioFin = Carbon::parse($horario->horaF);
            if ($horarioFin->lt($horarioInicio)) {
                $nuevoTiempo = $horarioInicio->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo;
            } else {
                $inicioHora = $horarioInicio->copy()->isoFormat('HH:mm:ss');
                if ($tiempo < $inicioHora) {
                    $nuevaFecha = $horarioInicio->copy()->addDays(1)->isoFormat('YYYY-MM-DD');  // : OBTENEMOS LA FECHA DEL DIA SIGUIENTE
                    $nuevoTiempo = $nuevaFecha . " " . $tiempo;
                } else {
                    $nuevoTiempo = $horarioInicio->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo;
                }
            }
            $salida = Carbon::parse($nuevoTiempo);  //: OBTENEMOS EL TIEMPO DE SALIDA
        } else {
            $salida = Carbon::parse($entrada->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo);   //: OBTENEMOS EL TIEMPO DE SALIDA
        }
        // * VALIDAR QUE SALIDA DEBE SER MAYOR A ENTRADA
        if ($salida->gt($entrada)) {
            if ($idhorarioE == 0) {
                $fecha = $entrada->copy()->isoFormat('YYYY-MM-DD');
                $fechaS = $entrada->copy()->addDays(1)->isoFormat('YYYY-MM-DD');
            } else {
                $fecha = $horarioInicio->copy()->isoFormat('YYYY-MM-DD');
                $fechaS = $horarioInicio->copy()->addDays(1)->isoFormat('YYYY-MM-DD');
            }
            // * VALIDACION ENTRE CRUCES DE HORAS
            $marcacionesValidar = DB::table('marcacion_puerta as m')
                ->select(
                    'm.marcaMov_id',
                    DB::raw('IF(m.marcaMov_fecha is null,0,m.marcaMov_fecha) AS entrada'),
                    DB::raw('IF(m.marcaMov_salida is null,0,m.marcaMov_salida) AS salida')
                )
                ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                ->whereIn(DB::raw('IF(m.marcaMov_fecha is null,DATE(m.marcaMov_salida),DATE(m.marcaMov_fecha))'), [$fecha, $fechaS])
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
                    // : SUMAR TIEMPOS DE MARCACIONES
                    $sumaTotalDeHoras = DB::table('marcacion_puerta as m')
                        ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaMov_salida,m.marcaMov_fecha)))) as totalT'))
                        ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                        ->whereNotNull('m.marcaMov_fecha')
                        ->whereNotNull('m.marcaMov_salida')
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
                                $marcacion->marcaMov_salida = $salida;
                                $marcacion->dispositivoSalida = NULL;
                                $marcacion->controladores_salida = NULL;
                                $marcacion->save();
                                return response()->json($marcacion->marcaMov_id, 200);
                            } else {
                                return response()->json(
                                    array("respuesta" => "Marcación fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . Carbon::parse($horario->horaI)->isoFormat('HH:mm:ss') . " - " . Carbon::parse($horario->horaF)->isoFormat('HH:mm:ss') . ")"),
                                    200
                                );
                            }
                        } else {
                            $marcacion->marcaMov_salida = $salida;
                            $marcacion->dispositivoSalida = NULL;
                            $marcacion->controladores_salida = NULL;
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
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->select(
                    'h.horario_descripcion as descripcion',
                    DB::raw("CONCAT( DATE(hd.start),' ', h.horaI) as horaI"),
                    DB::raw("IF(h.horaF is null , 0 , IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF))) as horaF"),
                    'h.horario_tolerancia as toleranciaI',
                    'h.horario_toleranciaF as toleranciaF',
                    'he.fuera_horario as fueraH',
                    DB::raw('IF(he.nHoraAdic is null, 0 ,he.nHoraAdic) as horasA'),
                    'h.horasObliga as horasO'
                )
                ->where('he.horarioEmp_id', '=', $idhorarioE)
                ->get()
                ->first();
            // * OBTENER TIEMPO DE HORARIOS
            $horarioInicio = Carbon::parse($horario->horaI);
            $horarioFin = Carbon::parse($horario->horaF);
            if ($horarioFin->lt($horarioInicio)) {
                $nuevoTiempo = $horarioInicio->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo;
            } else {
                $inicioHora = $horarioInicio->copy()->isoFormat('HH:mm:ss');
                if ($tiempo < $inicioHora) {
                    $nuevaFecha = $horarioInicio->copy()->addDays(1)->isoFormat('YYYY-MM-DD');  // : OBTENEMOS LA FECHA DEL DIA SIGUIENTE
                    $nuevoTiempo = $nuevaFecha . " " . $tiempo;
                } else {
                    $nuevoTiempo = $horarioInicio->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo;
                }
            }
            $entrada = Carbon::parse($nuevoTiempo);  //: OBTENEMOS EL TIEMPO DE SALIDA
        } else {
            $entrada = Carbon::parse($salida->copy()->isoFormat('YYYY-MM-DD') . " " . $tiempo);   //: OBTENEMOS EL TIEMPO DE SALIDA
        }
        // * VALIDAR QUE SALIDA DEBE SER MAYOR A ENTRADA
        if ($salida->gt($entrada)) {
            if ($idhorarioE == 0) {
                $fecha = $entrada->copy()->isoFormat('YYYY-MM-DD');
                $fechaS = $entrada->copy()->addDays(1)->isoFormat('YYYY-MM-DD');
            } else {
                $fecha = $horarioInicio->copy()->isoFormat('YYYY-MM-DD');
                $fechaS = $horarioInicio->copy()->addDays(1)->isoFormat('YYYY-MM-DD');
            }
            // * VALIDACION ENTRE CRUCES DE HORAS
            $marcacionesValidar = DB::table('marcacion_puerta as m')
                ->select(
                    'm.marcaMov_id',
                    DB::raw('IF(m.marcaMov_fecha is null,0,m.marcaMov_fecha) AS entrada'),
                    DB::raw('IF(m.marcaMov_salida is null,0,m.marcaMov_salida) AS salida')
                )
                ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                ->whereIn(DB::raw('IF(m.marcaMov_fecha is null,DATE(m.marcaMov_salida),DATE(m.marcaMov_fecha))'), [$fecha, $fechaS])
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
                        $horarioInicioT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
                        $horarioFinT = $horarioFin->copy()->addMinutes($horario->toleranciaF);
                        if ($horario->fueraH == 0) {
                            // * VALIDAR SIN FUERA DE HORARIO
                            if ($entrada->gte($horarioInicioT) && $salida->lte($horarioFinT)) {
                                $marcacion->marcaMov_fecha = $entrada;
                                $marcacion->dispositivoEntrada = NULL;
                                $marcacion->controladores_idControladores = NULL;
                                $marcacion->save();
                                return response()->json($marcacion->marcaMov_id, 200);
                            } else {
                                return response()->json(
                                    array("respuesta" => "Marcación fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . Carbon::parse($horario->horaI)->isoFormat('HH:mm:ss') . " - " . Carbon::parse($horario->horaF)->isoFormat('HH:mm:ss') . " )"),
                                    200
                                );
                            }
                        } else {
                            if ($entrada->gte($horarioInicioT) && $entrada->lte($horarioFinT)) {
                                $marcacion->marcaMov_fecha = $entrada;
                                $marcacion->dispositivoEntrada = NULL;
                                $marcacion->controladores_idControladores = NULL;
                                $marcacion->save();
                                return response()->json($marcacion->marcaMov_id, 200);
                            } else {
                                return response()->json(
                                    array("respuesta" => "Marcación fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . Carbon::parse($horario->horaI)->isoFormat('HH:mm:ss') . " - " . Carbon::parse($horario->horaF)->isoFormat('HH:mm:ss') . " )"),
                                    200
                                );
                            }
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
                    $marcacion->controladores_idControladores = NULL;
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
                DB::raw('IF(he.nHoraAdic is null, 0 ,he.nHoraAdic) as horasAdicionales')
            )
            ->where(DB::raw('DATE(hd.start)'), '=', $fecha)
            ->where('he.empleado_emple_id', '=', $idEmpleado)
            ->where('he.estado', '=', 1)
            ->get();
        // dd(DB::getQueryLog());

        return response()->json($horario, 200);
    }
    // * LISTA DE MARCACIONES AGRUPADAS POR HORARIO
    public function marcacionesHorarioEmpleado(Request $request)
    {
        $fecha = $request->get('fecha');
        $idEmpleado =  $request->get('idEmpleado');

        function agruparMarcacionesH($array)
        {
            $resultado = array();
            foreach ($array as $marcacion) {
                if (!isset($resultado[$marcacion->horarioE])) {
                    $resultado[$marcacion->horarioE] = (object)array(
                        "descripcion" =>  $marcacion->descripcion,
                        "idHorarioE" => $marcacion->horarioE,
                        "data" => array()
                    );
                }
                $objectoMarcacion = (object)array(
                    "entrada" =>  $marcacion->entrada,
                    "salida" => $marcacion->salida,
                    "id" => $marcacion->marcaMov_id
                );
                array_push($resultado[$marcacion->horarioE]->data, $objectoMarcacion);
            }

            return array_values($resultado);
        }
        $marcaciones = DB::table('marcacion_puerta as mp')
            ->leftJoin('horario_empleado as he', 'mp.horarioEmp_id', '=', 'he.horarioEmp_id')
            ->leftJoin('horario as hor', 'he.horario_horario_id', '=', 'hor.horario_id')
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
            ->select(
                DB::raw('IF(hor.horario_descripcion is null, 0 ,hor.horario_descripcion ) as descripcion'),
                DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                'mp.marcaMov_id',
                DB::raw('IF(he.horarioEmp_id is null, 0, he.horarioEmp_id) as horarioE')
            )
            ->whereRaw("IF(he.horarioEmp_id is null, IF(mp.marcaMov_fecha is null,DATE(mp.marcaMov_salida) , DATE(mp.marcaMov_fecha)) , DATE(hd.start)) = '$fecha'")
            ->where('mp.marcaMov_emple_id', '=', $idEmpleado)
            ->get();
        $marcaciones = agruparMarcacionesH($marcaciones);

        return response()->json($marcaciones, 200);
    }
    // * CAMBIAR HORARIO DE MARCACIONES
    public function cambiarHorario(Request $request)
    {
        $nuevoHorarioE = $request->get('idNuevo');
        $idEmpleado = $request->get('idEmpleado');
        $marcaciones = (array)$request->get('idsMarcaciones');
        $marcacion = marcacion_puerta::whereIn('marcaMov_id', $marcaciones)->get();
        if ($nuevoHorarioE != 0) {
            $horario = DB::table('horario_empleado as he')
                ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->select(
                    DB::raw("CONCAT( DATE(hd.start),' ', h.horaI) as horaI"),
                    DB::raw("IF(h.horaF is null , 0 , IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF))) as horaF"),
                    'h.horario_tolerancia as toleranciaI',
                    'h.horario_toleranciaF as toleranciaF',
                    'he.nHoraAdic as horasA',
                    'he.fuera_horario',
                    'he.horarioEmp_id',
                    'h.horasObliga as horasO'
                )
                ->where('he.horarioEmp_id', '=', $nuevoHorarioE)
                ->get()
                ->first();
            // : TIEMPOS DE HORARIOS
            $horarioInicio = Carbon::parse($horario->horaI);
            $horarioFin = Carbon::parse($horario->horaF);
            $horarioInicioT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
            $horarioFinT = $horarioFin->copy()->addMinutes($horario->toleranciaF);
            // : HORARIO FUERA DE HORARIO == 1
            if ($horario->fuera_horario == 1) {
                $estadoHorario = true;
                foreach ($marcacion as $m) {
                    // : ENTRADA
                    if (!is_null($m->marcaMov_fecha)) {
                        // : ENTRADA DEBE ESTAR EN EL RANGO DEL HORARIO
                        if (!(Carbon::parse($m->marcaMov_fecha)->gte($horarioInicioT) && Carbon::parse($m->marcaMov_fecha)->lte($horarioFinT))) {
                            $estadoHorario = false;
                            // : DEVOLVEMOS INFORMACION INDICANDO ENTRADA FUERA DE RANGO
                            return response()->json(
                                array(
                                    "respuesta" => "Marcación entrada " . Carbon::parse($m->marcaMov_fecha)->isoFormat("HH:mm:ss") . " fuera de rango."
                                ),
                                200
                            );
                        }
                    }
                }
                // : TODAS LAS MARCACIONES, SUS ENTRADAS ESTAN DENTRO DEL RANGO
                if ($estadoHorario) {
                    // : ********************** VALIDAR CON CON SOBRETIEMPO ***********************
                    // : SUMA TOTAL DEL HORARIO EMPLEADO
                    $sumaTotalDeHoras = DB::table('marcacion_puerta as m')
                        ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaMov_salida,m.marcaMov_fecha)))) as totalT'))
                        ->where('m.marcaMov_emple_id', '=', $idEmpleado)
                        ->whereNotNull('m.marcaMov_fecha')
                        ->whereNotNull('m.marcaMov_salida')
                        ->where('m.horarioEmp_id', '=', $nuevoHorarioE)
                        ->get();
                    // : SUMA TOTAL DE LAS MARCACIONES
                    $sumaTotalMarcaciones = DB::table('marcacion_puerta as m')
                        ->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(m.marcaMov_salida,m.marcaMov_fecha))) as totalT'))
                        ->whereIn('marcaMov_id', $marcaciones)
                        ->whereNotNull('m.marcaMov_fecha')
                        ->whereNotNull('m.marcaMov_salida')
                        ->get();
                    // : CALCULAR TIEMPO
                    $sumaTotalDeHoras[0]->totalT = $sumaTotalDeHoras[0]->totalT == null ? "00:00:00" : $sumaTotalDeHoras[0]->totalT;
                    $sumaTotalMarcaciones[0]->totalT = $sumaTotalMarcaciones[0]->totalT == null ? 0 : $sumaTotalMarcaciones[0]->totalT;
                    // : -> TIEMPO TOTAL DEL HORARIO MÁS EL TIEMPO QUE CUENTA LAS MARCACIONES
                    $tiempoTotal = Carbon::parse($sumaTotalDeHoras[0]->totalT)->addSeconds($sumaTotalMarcaciones[0]->totalT);
                    $tiempoTotalDeHorario = Carbon::parse($horario->horasO)->addMinutes($horario->horasA * 60);
                    if ($tiempoTotal->lte($tiempoTotalDeHorario)) {
                        foreach ($marcacion as $m) {
                            $actualizarM = marcacion_puerta::findOrFail($m->marcaMov_id);
                            $actualizarM->horarioEmp_id = $horario->horarioEmp_id;
                            $actualizarM->save();
                        }
                        return response()->json($nuevoHorarioE, 200);
                    } else {
                        // : CALCULAMOS EL TIEMPO QUE SOBREPASA
                        $sobreTiempoHorario = $tiempoTotal->diffInSeconds($tiempoTotalDeHorario);
                        return response()->json(
                            array("respuesta" => "Sobretiempo de " . gmdate("H:i:s", $sobreTiempoHorario)),
                            200
                        );
                    }
                }
            } else {
                $respuesta = true;
                foreach ($marcacion as $m) {
                    if (!is_null($m->marcaMov_fecha)) {
                        $respuestaCheck = checkHora($horarioInicioT, $horarioFinT, $m->marcaMov_fecha);
                        if (!$respuestaCheck) {
                            $respuesta  = false;
                            // : DEVOLVEMOS INFORMACION INDICANDO ENTRADA FUERA DE RANGO
                            return response()->json(
                                array(
                                    "respuesta" => "Marcación entrada " . Carbon::parse($m->marcaMov_fecha)->isoFormat("HH:mm:ss") . " fuera de rango."
                                ),
                                200
                            );
                        }
                    }
                    if (!is_null($m->marcaMov_salida)) {
                        $respuestaCheck = checkHora($horarioInicioT, $horarioFinT, $m->marcaMov_salida);
                        if (!$respuestaCheck) {
                            $respuesta  = false;
                            // : DEVOLVEMOS INFORMACION INDICANDO SALIDA FUERA DE RANGO
                            return response()->json(
                                array(
                                    "respuesta" => "Marcación salida" . Carbon::parse($m->marcaMov_salida)->isoFormat("HH:mm:ss") . " fuera de rango."
                                ),
                                200
                            );
                        }
                    }
                }
                // : TODAS LAS MARCACIONES ESTAN DENTRO DEL RANGO
                if ($respuesta) {
                    // : ********************** VALIDAR CON CON SOBRETIEMPO ***********************
                    // : SUMA TOTAL DEL HORARIO EMPLEADO
                    $sumaTotalDeHoras = DB::table('marcacion_puerta as m')
                        ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaMov_salida,m.marcaMov_fecha)))) as totalT'))
                        ->where('m.marcaMov_emple_id', '=', $idEmpleado)
                        ->whereNotNull('m.marcaMov_fecha')
                        ->whereNotNull('m.marcaMov_salida')
                        ->where('m.horarioEmp_id', '=', $nuevoHorarioE)
                        ->get();
                    // : SUMA TOTAL DE LAS MARCACIONES
                    $sumaTotalMarcaciones = DB::table('marcacion_puerta as m')
                        ->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(m.marcaMov_salida,m.marcaMov_fecha))) as totalT'))
                        ->whereIn('marcaMov_id', $marcaciones)
                        ->whereNotNull('m.marcaMov_fecha')
                        ->whereNotNull('m.marcaMov_salida')
                        ->get();
                    // : CALCULAR TIEMPO
                    $sumaTotalDeHoras[0]->totalT = $sumaTotalDeHoras[0]->totalT == null ? "00:00:00" : $sumaTotalDeHoras[0]->totalT;
                    $sumaTotalMarcaciones[0]->totalT = $sumaTotalMarcaciones[0]->totalT == null ? 0 : $sumaTotalMarcaciones[0]->totalT;
                    // : -> TIEMPO TOTAL DEL HORARIO MÁS EL TIEMPO QUE CUENTA LAS MARCACIONES
                    $tiempoTotal = Carbon::parse($sumaTotalDeHoras[0]->totalT)->addSeconds($sumaTotalMarcaciones[0]->totalT);
                    $tiempoTotalDeHorario = Carbon::parse($horario->horasO)->addMinutes($horario->horasA * 60);
                    if ($tiempoTotal->lte($tiempoTotalDeHorario)) {
                        foreach ($marcacion as $m) {
                            $actualizarM = marcacion_puerta::findOrFail($m->marcaMov_id);
                            $actualizarM->horarioEmp_id = $horario->horarioEmp_id;
                            $actualizarM->save();
                        }
                        return response()->json($nuevoHorarioE, 200);
                    } else {
                        // : CALCULAMOS EL TIEMPO QUE SOBREPASA
                        $sobreTiempoHorario = $tiempoTotal->diffInSeconds($tiempoTotalDeHorario);
                        return response()->json(
                            array("respuesta" => "Sobretiempo de " . gmdate("H:i:s", $sobreTiempoHorario)),
                            200
                        );
                    }
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
        $fechaSiguinte = Carbon::parse($fechaM)->addDays(1)->isoFormat("YYYY-MM-DD");
        // ? ******************************** VALIDACION ENTRE CRUCES DE HORAS ***************************
        $marcacionesValidar = DB::table('marcacion_puerta as m')
            ->select(
                'm.marcaMov_id',
                DB::raw('IF(m.marcaMov_fecha is null,0,m.marcaMov_fecha) AS entrada'),
                DB::raw('IF(m.marcaMov_salida is null,0,m.marcaMov_salida) AS salida')
            )
            ->where('m.marcaMov_emple_id', '=', $idEmpleado)
            ->whereIn(DB::raw('IF(m.marcaMov_fecha is null,DATE(m.marcaMov_salida),DATE(m.marcaMov_fecha))'), [$fechaM, $fechaSiguinte])
            ->get();
        // ? ******************************* VALIDACION DE SIN HORARIO Y CON HORARIO ************************
        if ($idHorarioE != 0) {
            // * HORARIO EMPLEADO, HORARIO Y HORA DIAS
            $horario = DB::table('horario_empleado as he')
                ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->select(
                    DB::raw("CONCAT( DATE(hd.start),' ', h.horaI) as horaI"),
                    DB::raw("IF(h.horaF is null , 0 , IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF))) as horaF"),
                    'h.horario_tolerancia as toleranciaI',
                    'h.horario_toleranciaF as toleranciaF',
                    'he.fuera_horario as fueraH',
                    DB::raw('IF(he.nHoraAdic is null, 0 ,he.nHoraAdic) as horasA'),
                    'he.empleado_emple_id as idEmpleado',
                    DB::raw('DATE(hd.start) as fecha'),
                    'h.organi_id',
                    'h.horasObliga as horasO'
                )
                ->where('he.horarioEmp_id', '=', $idHorarioE)
                ->get()
                ->first();
            // : ********************************** HORARIO *********************************
            $horarioInicio = Carbon::parse($horario->horaI)->subMinutes($horario->toleranciaI);
            $horarioFin = Carbon::parse($horario->horaF)->addMinutes($horario->toleranciaF);
            // : *********************************** MARCACION *****************************
            // : HORA DE INICIO DE MARCACION
            if (!is_null($inicio)) {
                $entrada = Carbon::parse($inicio);
            } else {
                $entrada = NULL;
            }
            // : HORA DE FIN DE MARCACION
            if (!is_null($fin)) {
                $salida = Carbon::parse($fin);
            } else {
                $salida = NULL;
            }
            // * QUE NO SE ENCUENTRE VACIOS NINGUNO DE LOS DOS
            if (!is_null($salida) && !is_null($entrada)) {
                // : SALIDA MAYOR ENTRADA
                if ($salida->gt($entrada)) {
                    // : ENTRADA DEBE ESTAR EN EL RANGO DEL HORARIO
                    if ($entrada->gte($horarioInicio) && $entrada->lte($horarioFin)) {
                        $respuesta = true;
                        // : VALIDACION DE CRUCES
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
                            $sumaTotalDeHoras = DB::table('marcacion_puerta as m')
                                ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaMov_salida,m.marcaMov_fecha)))) as totalT'))
                                ->where('m.marcaMov_emple_id', '=', $idEmpleado)
                                ->whereNotNull('m.marcaMov_fecha')
                                ->whereNotNull('m.marcaMov_salida')
                                ->where(DB::raw('DATE(marcaMov_fecha)'), '=', $fechaM)
                                ->where('m.horarioEmp_id', '=', $idHorarioE)
                                ->get();
                            // * CALCULAR TIEMPO
                            $sumaTotalDeHoras[0]->totalT = $sumaTotalDeHoras[0]->totalT == null ? "00:00:00" : $sumaTotalDeHoras[0]->totalT;
                            $horaIParse = Carbon::parse($entrada);
                            $horaFParse = Carbon::parse($salida);
                            $totalDuration = $horaFParse->diffInSeconds($horaIParse);
                            $tiempoTotal = Carbon::parse($sumaTotalDeHoras[0]->totalT)->addSeconds($totalDuration);
                            $tiempoTotalDeHorario = Carbon::parse($horario->horasO)->addMinutes($horario->horasA * 60);
                            if ($tiempoTotal->lte($tiempoTotalDeHorario)) {
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
                                return response()->json(
                                    array("respuesta" => "Sobretiempo en la marcación."),
                                    200
                                );
                            }
                        } else {
                            return response()->json(array("respuesta" => "Posibilidad de cruce de hora."), 200);
                        }
                    } else {
                        return response()->json(array("respuesta" => "Marcación fuera de horario."), 200);
                    }
                } else {
                    return response()->json(array("respuesta" => "Hora salida debe ser mayor a la hora de entrada."), 200);
                }
            } else {
                if (!is_null($inicio)) {
                    // : ENTRADA DEBE ESTAR EN EL RANGO DE HORARIO
                    if ($entrada->gte($horarioInicio) && $entrada->lte($horarioFin)) {
                        $respuesta = true;
                        foreach ($marcacionesValidar as $mv) {
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
                        // ! SI NO ENCUENTRA CRUCES
                        if ($respuesta) {
                            $estado = true;
                            $respuestaCheck = checkHora($horarioInicio, $horarioFin, $entrada);
                            if (!$respuestaCheck) $estado = false;

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
                    } else {
                        return response()->json(array("respuesta" => "Marcación fuera de horario."), 200);
                    }
                } else {
                    if (!is_null($salida)) {
                        $respuesta = true;
                        foreach ($marcacionesValidar as $mv) {
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
                        // ! SI NO ENCUENTRA CRUCES
                        if ($respuesta) {
                            $estadoH = true;
                            if ($horario->fueraH == 0) {
                                if (!(Carbon::parse($salida)->gte($horarioInicio) && Carbon::parse($salida)->lte($horarioFin))) {
                                    $estadoH = false;
                                }
                            }
                            if ($estadoH) {
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
                }
            }
        } else {
            // ? ******************************** VALIDACION ENTRE CRUCES DE HORAS ***************************
            $entrada = $inicio == null ? NULL : Carbon::parse($inicio);
            $salida = $fin == null ? NULL : Carbon::parse($fin);
            // * QUE NO SE ENCUENTRE VACIOS NINGUNO DE LOS DOS
            if (!is_null($fin) && !is_null($inicio)) {
                if ($entrada->lte($salida)) {
                    $respuesta = true;
                    foreach ($marcacionesValidar as $mv) {
                        // : ENTRADA
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
                        // : SALIDA
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
                } else {
                    return response()->json(array("respuesta" => "Hora salida debe ser mayor a la hora de entrada."), 200);
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

    // * LISTA DE HORARIOS EXCLUYENDO SU ID DE HORARIO
    public function horariosDeEmpleado(Request $request)
    {
        $fecha = $request->get('fecha');
        $idEmpleado = $request->get('idEmpleado');
        $idHE = $request->get('idHE');
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
                DB::raw('IF(he.nHoraAdic is null, 0 ,he.nHoraAdic) as horasAdicionales')
            )
            ->where(DB::raw('DATE(hd.start)'), '=', $fecha)
            ->where('he.empleado_emple_id', '=', $idEmpleado)
            ->where('he.horarioEmp_id', '!=', $idHE)
            ->where('he.estado', '=', 1)
            ->get();
        // dd(DB::getQueryLog());

        return response()->json($horario, 200);
    }

    // * ACTUALIZAR HORARIO EN MARCACION
    public function actualizarHorarioMarcacion(Request $request)
    {
        $idMarcacion = $request->get('idM');
        $idHorarioE = $request->get('idHE');
        // : MARCACIÓN
        $marcacion = marcacion_puerta::findOrFail($idMarcacion);
        // : HORARIO EMPLEADO
        if (!empty($idHorarioE)) {           // : -> HORARIO EMPLEADO ES DIFERENTE A CERO
            $horario = DB::table('horario_empleado as he')
                ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->select(
                    'h.horario_descripcion as descripcion',
                    DB::raw("CONCAT( DATE(hd.start),' ', h.horaI) as horaI"),
                    DB::raw("IF(h.horaF is null , 0 , IF(h.horaF > h.horaI,CONCAT( DATE(hd.start),' ', h.horaF),CONCAT( DATE_ADD(DATE(hd.start), INTERVAL 1 DAY),' ', h.horaF))) as horaF"),
                    'h.horario_tolerancia as toleranciaI',
                    'h.horario_toleranciaF as toleranciaF',
                    'he.fuera_horario as fueraH',
                    DB::raw('IF(he.nHoraAdic is null, 0 ,he.nHoraAdic) as horasA'),
                    'h.horasObliga as horasO'
                )
                ->where('he.horarioEmp_id', '=', $idHorarioE)
                ->get()
                ->first();
            // : OBTENER TIEMPO DE HORARIOS
            $horarioInicio = Carbon::parse($horario->horaI);
            $horarioFin = Carbon::parse($horario->horaF);
            $horarioInicioT = $horarioInicio->copy()->subMinutes($horario->toleranciaI);
            $horarioFinT = $horarioFin->copy()->addMinutes($horario->toleranciaF);
            // : SUMA DE TIEMPO EN MARCACIONES
            $sumaTotalDeHoras = DB::table('marcacion_puerta as m')
                ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(m.marcaMov_salida,m.marcaMov_fecha)))) as totalT'))
                ->where('m.marcaMov_emple_id', '=', $marcacion->marcaMov_emple_id)
                ->whereNotNull('m.marcaMov_fecha')
                ->whereNotNull('m.marcaMov_salida')
                ->where('m.horarioEmp_id', '=', $idHorarioE)
                ->get();
            // : CALCULOS DE TIEMPO
            $sumaTotalDeHoras[0]->totalT = $sumaTotalDeHoras[0]->totalT == null ? "00:00:00" : $sumaTotalDeHoras[0]->totalT;
            // : SI MARCACIÓN TIENE ENTRADA Y SALIDA
            if (!empty($marcacion->marcaMov_fecha) && !empty($marcacion->marcaMov_salida)) {
                $entrada = Carbon::parse($marcacion->marcaMov_fecha);
                $salida = Carbon::parse($marcacion->marcaMov_salida);
                $totalDuration = $salida->diffInSeconds($entrada);
                $tiempoTotal = Carbon::parse($sumaTotalDeHoras[0]->totalT)->addSeconds($totalDuration);
                $tiempoTotalDeHorario = Carbon::parse($horario->horasO)->addMinutes($horario->horasA * 60);
                // : ENTRADA DEBE ESTAR ENTRE INICIO DEL HORARIO Y SALIDA DEL HORARIO
                if ($entrada->gte($horarioInicioT) && $entrada->lte($horarioFinT)) {
                    if ($horario->fueraH == 1) {
                        // : TIEMPO TOTAL DE LA MARCACIÓN MENOR O IGUAL AL TIEMPO DEL HORARIO
                        if ($tiempoTotal->lte($tiempoTotalDeHorario)) {
                            // : ACTUALIZAR HORARIO DE LA MARCACIÓN
                            $marcacion->horarioEmp_id = $idHorarioE;
                            $marcacion->save();
                            return response()->json($marcacion->marcaMov_id, 200);
                        } else {
                            return response()->json(
                                array("respuesta" => "Sobretiempo en la marcación."),
                                200
                            );
                        }
                    } else {
                        // : SALIDA DEBE ESTAR ENTRE INICIO DEL HORARIO Y SALIDA DEL HORARIO
                        if ($salida->gte($horarioInicioT) && $salida->lte($horarioFinT)) {
                            // : TIEMPO TOTAL DE LA MARCACIÓN MENOR O IGUAL AL TIEMPO DEL HORARIO
                            if ($tiempoTotal->lte($tiempoTotalDeHorario)) {
                                // : ACTUALIZAR HORARIO DE LA MARCACIÓN
                                $marcacion->horarioEmp_id = $idHorarioE;
                                $marcacion->save();
                                return response()->json($marcacion->marcaMov_id, 200);
                            } else {
                                return response()->json(
                                    array("respuesta" => "Sobretiempo en la marcación."),
                                    200
                                );
                            }
                        } else {
                            return response()->json(
                                array("respuesta" => "Marcación de salida fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . Carbon::parse($horario->horaI)->isoFormat('HH:mm:ss') . " - " . Carbon::parse($horario->horaF)->isoFormat('HH:mm:ss') . " )"),
                                200
                            );
                        }
                    }
                } else {
                    return response()->json(
                        array("respuesta" => "Marcación de entrada fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . Carbon::parse($horario->horaI)->isoFormat('HH:mm:ss') . " - " . Carbon::parse($horario->horaF)->isoFormat('HH:mm:ss') . " )"),
                        200
                    );
                }
            } else {
                // : SI MARCACIÓN SOLO TIENE ENTRADA
                if (!empty($marcacion->marcaMov_fecha)) {
                    $entrada = Carbon::parse($marcacion->marcaMov_fecha);
                    $tiempoTotal = Carbon::parse($sumaTotalDeHoras[0]->totalT);
                    $tiempoTotalDeHorario = Carbon::parse($horario->horasO)->addMinutes($horario->horasA * 60);
                    // : ENTRADA DEBE ESTAR ENTRE INICIO DEL HORARIO Y SALIDA DEL HORARIO
                    if ($entrada->gte($horarioInicioT) && $entrada->lte($horarioFinT)) {
                        if ($tiempoTotal->lt($tiempoTotalDeHorario)) {
                            // : ACTUALIZAR HORARIO DE LA MARCACIÓN
                            $marcacion->horarioEmp_id = $idHorarioE;
                            $marcacion->save();
                            return response()->json($marcacion->marcaMov_id, 200);
                        } else {
                            return response()->json(
                                array("respuesta" => "Sobretiempo en la marcación."),
                                200
                            );
                        }
                    } else {
                        return response()->json(
                            array("respuesta" => "Marcación de entrada fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . Carbon::parse($horario->horaI)->isoFormat('HH:mm:ss') . " - " . Carbon::parse($horario->horaF)->isoFormat('HH:mm:ss') . " )"),
                            200
                        );
                    }
                } else {
                    // : SI MARCACIÓN SOLO TIENE SALIDA
                    if (!empty($marcacion->marcaMov_salida)) {
                        $salida = Carbon::parse($marcacion->marcaMov_salida);
                        $tiempoTotal = Carbon::parse($sumaTotalDeHoras[0]->totalT);
                        $tiempoTotalDeHorario = Carbon::parse($horario->horasO)->addMinutes($horario->horasA * 60);
                        if ($horario->fueraH == 1) {
                            // : TIEMPO TOTAL DE LA MARCACIÓN MENOR AL TIEMPO DEL HORARIO
                            if ($tiempoTotal->lt($tiempoTotalDeHorario)) {
                                // : ACTUALIZAR HORARIO DE LA MARCACIÓN
                                $marcacion->horarioEmp_id = $idHorarioE;
                                $marcacion->save();
                                return response()->json($marcacion->marcaMov_id, 200);
                            } else {
                                return response()->json(
                                    array("respuesta" => "Sobretiempo en la marcación."),
                                    200
                                );
                            }
                        } else {
                            // : SALIDA DEBE ESTAR ENTRE INICIO DEL HORARIO Y SALIDA DEL HORARIO
                            if ($salida->gte($horarioInicioT) && $salida->lte($horarioFinT)) {
                                // : TIEMPO TOTAL DE LA MARCACIÓN MENOR AL TIEMPO DEL HORARIO
                                if ($tiempoTotal->lt($tiempoTotalDeHorario)) {
                                    // : ACTUALIZAR HORARIO DE LA MARCACIÓN
                                    $marcacion->horarioEmp_id = $idHorarioE;
                                    $marcacion->save();
                                    return response()->json($marcacion->marcaMov_id, 200);
                                } else {
                                    return response()->json(
                                        array("respuesta" => "Sobretiempo en la marcación."),
                                        200
                                    );
                                }
                            } else {
                                return response()->json(
                                    array("respuesta" => "Marcación de salida fuera de horario." . "<br>" . "Horario " . $horario->descripcion . " (" . Carbon::parse($horario->horaI)->isoFormat('HH:mm:ss') . " - " . Carbon::parse($horario->horaF)->isoFormat('HH:mm:ss') . " )"),
                                    200
                                );
                            }
                        }
                    }
                }
            }
        } else {
            $marcacion->horarioEmp_id = NULL;
            $marcacion->save();
            return response($marcacion->marcaMov_id, 200);
        }
    }
    // ? ******************************** TRAZABILIDAD DE MARCACIÓN **********************************
    // * INDEX
    public function indexTrazabilidadM()
    {
        $organizacion = DB::table('organizacion')
            ->where('organi_id', '=', session('sesionidorg'))
            ->get()->first();
        $nombreOrga = $organizacion->organi_razonSocial;

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
                        'organizacion' => $nombreOrga,
                        'empleado' => $empleados,
                        'modifReporte' => $invitadod->ModificarReportePuerta
                    ]);
                } else {
                    return redirect('/dashboard');
                }
                /*   */
            } else {
                return view('Dispositivos.trazabilidad', ['organizacion' => $nombreOrga, 'empleado' => $empleados]);
            }
        } else {
            return view('Dispositivos.trazabilidad', ['organizacion' => $nombreOrga, 'empleado' => $empleados]);
        }
    }
    // * DATA
    public function dataTrazabilidad(Request $request)
    {
        $fechaI = $request->get('fechaI');
        $fechaF = $request->get('fechaF');
        $idsEmpleado = $request->get('idsEmpleado');
        $fechaInicio = Carbon::parse($fechaI)->isoFormat("YYYY-MM-DD");
        $fechaFin = Carbon::parse($fechaF)->isoFormat("YYYY-MM-DD");
        // : ORGANIZACION
        $organizacion = DB::table('organizacion as o')
            ->select(
                'o.organi_razonSocial',
                'o.organi_direccion',
                'o.organi_ruc'
            )
            ->where('o.organi_id', '=', session('sesionidorg'))
            ->get()
            ->first();
        // : INCIDENCIAS DE ORGANIZACION
        $incidenciasOrganizacion = incidencias::select('inciden_descripcion as descripcion', 'inciden_id as id')
            ->where('organi_id', '=', session('sesionidorg'))
            ->orderBy('inciden_descripcion', 'ASC')
            ->get();
        function agruparEmpleadosMData($array)
        {
            $resultado = array();

            foreach ($array as $empleado) {
                if (!isset($resultado[$empleado->emple_id])) {
                    $resultado[$empleado->emple_id] = (object) array(
                        "emple_id" => $empleado->emple_id
                    );
                }
                if (!isset($resultado[$empleado->emple_id]->data)) {
                    $resultado[$empleado->emple_id]->data = array();
                }
                if (!isset($resultado[$empleado->emple_id]->data[$empleado->fecha])) {
                    $resultado[$empleado->emple_id]->data[$empleado->fecha] = array();
                }
                if (!isset($resultado[$empleado->emple_id]->data[$empleado->fecha][$empleado->tipoHora][$empleado->idHorarioE])) {
                    $resultado[$empleado->emple_id]->data[$empleado->fecha][$empleado->tipoHora][$empleado->idHorarioE] = array();
                }
                if (!isset($resultado[$empleado->emple_id]->data[$empleado->fecha][$empleado->tipoHora][$empleado->idHorarioE]["dataHorario"])) {
                    $resultado[$empleado->emple_id]->data[$empleado->fecha][$empleado->tipoHora][$empleado->idHorarioE]["dataHorario"] =  (object) array(
                        "horarioIni" => $empleado->horarioIni,
                        "idHorario" => $empleado->idHorario,
                        "toleranciaI" => $empleado->toleranciaI,
                        "idHorarioE" => $empleado->idHorarioE,
                        "estado" => $empleado->estado,
                        "horasObligadas" => $empleado->horasObligadas  == null ? 0 : $empleado->horasObligadas,
                        "horasAdicionales" => $empleado->horasAdicionales == null ? 0 : $empleado->horasAdicionales,
                        "entrada" => $empleado->entrada,
                        "salida" => $empleado->salida,
                        "totalT" => $empleado->totalT == null ? "00:00:00" : $empleado->totalT
                    );
                }
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
                if (empty($idsEmpleado)) {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->select(
                            'e.emple_id',
                            'e.emple_nDoc',
                            'e.emple_codigo',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'a.area_descripcion',
                            'e.emple_estado',
                            'e.organi_id'
                        )
                        ->where('e.organi_id', '=', session('sesionidorg'))
                        ->orderBy('p.perso_nombre', 'ASC')
                        ->get();
                } else {
                    $empleados = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                        ->select(
                            'e.emple_id',
                            'e.emple_nDoc',
                            'e.emple_codigo',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'a.area_descripcion',
                            'e.emple_estado',
                            'e.organi_id'
                        )
                        ->whereIn('e.emple_id', $idsEmpleado)
                        ->orderBy('p.perso_nombre', 'ASC')
                        ->get();
                }
            } else {
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();
                if ($invitado_empleadoIn != null) {
                    if (empty($idsEmpleado)) {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->select(
                                'e.emple_id',
                                'e.emple_nDoc',
                                'e.emple_codigo',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'a.area_descripcion',
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
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->select(
                                'e.emple_id',
                                'e.emple_nDoc',
                                'e.emple_codigo',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'a.area_descripcion',
                                'e.emple_estado',
                                'e.organi_id'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->whereIn('e.emple_id', $idsEmpleado)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->orderBy('p.perso_nombre', 'ASC')
                            ->get();
                    }
                } else {
                    if (empty($idsEmpleado)) {
                        $empleados = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->select(
                                'e.emple_id',
                                'e.emple_nDoc',
                                'e.emple_codigo',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'a.area_descripcion',
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
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                            ->select(
                                'e.emple_id',
                                'e.emple_nDoc',
                                'e.emple_codigo',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'a.area_descripcion',
                                'e.emple_estado',
                                'e.organi_id'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->whereIn('e.emple_id', $idsEmpleado)
                            ->where('e.organi_id', '=', session('sesionidorg'))
                            ->orderBy('p.perso_nombre', 'ASC')
                            ->get();
                    }
                }
            }
        } else {
            if (empty($idsEmpleado)) {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_nDoc',
                        'e.emple_codigo',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'a.area_descripcion',
                        'e.emple_estado',
                        'e.organi_id'
                    )
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->orderBy('p.perso_nombre', 'ASC')
                    ->get();
            } else {
                $empleados = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('area as a', 'e.emple_area', '=', 'a.area_id')
                    ->select(
                        'e.emple_id',
                        'e.emple_nDoc',
                        'e.emple_codigo',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'a.area_descripcion',
                        'e.emple_estado',
                        'e.organi_id'
                    )
                    ->whereIn('e.emple_id', $idsEmpleado)
                    ->where('e.organi_id', '=', session('sesionidorg'))
                    ->orderBy('p.perso_nombre', 'ASC')
                    ->get();
            }
        }
        $marcaciones = [];
        $data =  DB::table('empleado as e')
            ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
            ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
            ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
            ->select(
                'e.emple_id',
                DB::raw('IF(mp.marcaMov_fecha is null, IF(TIME(mp.marcaMov_salida) BETWEEN "06:01" AND "22:00", "normal", "nocturno"), IF(TIME(mp.marcaMov_fecha) BETWEEN "06:01" AND "22:00", "normal", "nocturno")) as tipoHora'),
                DB::raw('IF(hoe.horarioEmp_id is null,DATE(mp.marcaMov_fecha),DATE(hd.start)) as fecha'),
                DB::raw('IF(hor.horario_id is null, 0 , horario_id) as idHorario'),
                DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(mp.marcaMov_salida,mp.marcaMov_fecha)))) as totalT'),
                // DB::raw('MIN(IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)) as entrada'),
                DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                DB::raw('MAX(mp.marcaMov_salida) as salida'),
                DB::raw('IF(hoe.horarioEmp_id is null, 0 , hoe.horarioEmp_id) as idHorarioE'),
                'hor.horario_tolerancia as toleranciaI',
                'mp.marcaMov_id as idMarcacion',
                'hor.horasObliga as horasObligadas',
                'hoe.nHoraAdic as horasAdicionales',
                'hoe.estado'
            )
            ->whereBetween(DB::raw('IF(hoe.horarioEmp_id is null,DATE(mp.marcaMov_fecha),DATE(hd.start))'), [$fechaInicio, $fechaFin])
            ->where('mp.organi_id', '=', session('sesionidorg'))
            ->orderBy('mp.marcaMov_fecha', 'ASC')
            ->groupBy(
                DB::raw('IF(mp.marcaMov_fecha is null, IF(TIME(mp.marcaMov_salida) BETWEEN "06:01" AND "22:00", 0, 1), IF(TIME(mp.marcaMov_fecha) BETWEEN "06:01" AND "22:00", 0, 1))'),
                DB::raw('IF(hoe.horarioEmp_id is null,DATE(mp.marcaMov_fecha),DATE(hd.start))'),
                'hoe.horarioEmp_id',
                'e.emple_id'
            )
            ->get();
        $data = agruparEmpleadosMData($data);  //: CONVERTIR UN SOLO EMPLEADO CON VARIOS MARCACIONES
        // * UNIR EMPLEADOS CON DATA DE MARCACIONES
        for ($index = 0; $index < sizeof($empleados); $index++) {
            $ingreso = true;
            for ($element = 0; $element < sizeof($data); $element++) {
                if ($empleados[$index]->emple_id == $data[$element]->emple_id) {    //: BUSCAMOS EL ID EMPLEADO IGUAL
                    $ingreso = false;
                    $arrayNuevo = (object) array(
                        "emple_id" => $empleados[$index]->emple_id,
                        "emple_nDoc" => $empleados[$index]->emple_nDoc,
                        "emple_codigo" => empty($empleados[$index]->emple_codigo) == true ? "---" : $empleados[$index]->emple_codigo,
                        "nombres_apellidos" => $empleados[$index]->perso_nombre . " " . $empleados[$index]->perso_apPaterno . " " . $empleados[$index]->perso_apMaterno,
                        "area_descripcion" => empty($empleados[$index]->area_descripcionn) == true ? "---" : $empleados[$index]->area_descripcion,
                        "emple_estado" => $empleados[$index]->emple_estado,
                        "data" => $data[$element]->data
                    );
                    array_push($marcaciones, $arrayNuevo);
                }
            }
            if ($ingreso && $empleados[$index]->emple_estado == 1) {         //: VALIDAMOS PARA EMPLEADOS QUE NO TIENEN DATA DE ESA FECHA
                $arrayNuevo = (object) array(
                    "emple_id" => $empleados[$index]->emple_id,
                    "emple_nDoc" => $empleados[$index]->emple_nDoc,
                    "emple_codigo" => empty($empleados[$index]->emple_codigo) == true ? "---" : $empleados[$index]->emple_codigo,
                    "nombres_apellidos" => $empleados[$index]->perso_nombre . " " . $empleados[$index]->perso_apPaterno . " " . $empleados[$index]->perso_apMaterno,
                    "area_descripcion" => empty($empleados[$index]->area_descripcion) == true ? "---" : $empleados[$index]->area_descripcion,
                    "emple_estado" => $empleados[$index]->emple_estado,
                    "data" => array()
                );
                array_push($marcaciones, $arrayNuevo);
            }
        }
        // * COMPLETAR DATA DE DIAS
        $period = Carbon::parse($fechaInicio)->toPeriod($fechaFin);
        $dates = [];
        // : OBTENER TODAS LAS FECHAS
        foreach ($period as $key => $date) {
            array_push($dates, $date->format('Y-m-d'));
        }
        // : RECORREMOS FECHAS
        foreach ($dates as $d) {
            // : RECORREMOS MARCACIONES PARA COMPLETAR HORARIOS
            foreach ($marcaciones as $key => $m) {
                $idEmpleado = $m->emple_id;
                // : BUSCAMOS SI YA EXISTE LA FECHA EN EL ARRAY
                if (array_key_exists($d, $m->data)) {
                    $horarios = [];
                    if (array_key_exists("normal", $m->data[$d])) {
                        $horarios = array_keys($m->data[$d]["normal"]);   // : OBTENEMOS TODOS LOS HORARIOS DE ESA FECHA
                    }
                    if (array_key_exists("nocturno", $m->data[$d])) {
                        array_push($horarios, array_keys($m->data[$d]["nocturno"]));   // : OBTENEMOS TODOS LOS HORARIOS DE ESA FECHA
                    }
                    $horarios = Arr::flatten($horarios);
                    $clave = array_search(0, $horarios);     // : BUSCAMOS HORARIOS CON ID 0
                    if (!is_bool($clave)) {
                        unset($horarios[$clave]);            // : DESCARTAMOS LOS HORARIOS CON ID 0
                    }
                    $horarioEmpleado = DB::table('horario_empleado as he')
                        ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                        ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                        ->select(
                            'h.horario_id as idHorario',
                            'h.horario_tolerancia as toleranciaI',
                            'he.horarioEmp_id as idHorarioE',
                            DB::raw("IF(h.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', h.horaI)) as horarioIni"),
                            'he.estado',
                            'h.horasObliga as horasObligadas',
                            'he.nHoraAdic as horasAdicionales'
                        )
                        ->where(DB::raw('DATE(hd.start)'), '=', $d)
                        ->where('he.empleado_emple_id', '=', $idEmpleado)
                        ->whereNotIn('he.horarioEmp_id', $horarios)
                        ->where('he.estado', '=', 1)
                        ->get();
                    foreach ($horarioEmpleado as $he) {
                        // : AGREGAMOS LOS HORARIOS QUE FALTA EN ESA FECHA
                        $he->totalT = "00:00:00";
                        $he->horasAdicionales = $he->horasAdicionales == null ? 0 : $he->horasAdicionales;
                        $he->entrada = NULL;
                        $he->salida = NULL;
                        if (
                            Carbon::parse($he->horarioIni)->isoFormat("HH:mm") >= "06:01" ||
                            Carbon::parse($he->horarioIni)->isoFormat("HH:mm") <= "22:00"
                        ) {
                            if (!isset($marcaciones[$key]->data[$d]["normal"])) {
                                $marcaciones[$key]->data[$d]["normal"] = array();
                            }
                            $marcaciones[$key]->data[$d]["normal"][$he->idHorarioE]["dataHorario"] = $he;
                        } else {
                            if (!isset($marcaciones[$key]->data[$d]["nocturno"])) {
                                $marcaciones[$key]->data[$d]["nocturno"] = array();
                            }
                            $marcaciones[$key]->data[$d]["nocturno"][$he->idHorarioE]["dataHorario"] = $he;
                        }
                    }
                } else {
                    $horarioEmpleado = DB::table('horario_empleado as he')
                        ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                        ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
                        ->select(
                            'h.horario_id as idHorario',
                            'h.horario_tolerancia as toleranciaI',
                            'he.horarioEmp_id as idHorarioE',
                            DB::raw("IF(h.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', h.horaI)) as horarioIni"),
                            'he.estado',
                            'h.horasObliga as horasObligadas',
                            'he.nHoraAdic as horasAdicionales'
                        )
                        ->where(DB::raw('DATE(hd.start)'), '=', $d)
                        ->where('he.empleado_emple_id', '=', $idEmpleado)
                        ->where('he.estado', '=', 1)
                        ->get();
                    if (sizeof($horarioEmpleado) != 0) {
                        $marcaciones[$key]->data[$d] = array();
                        foreach ($horarioEmpleado as $he) {
                            // : AGREGAMOS LOS HORARIOS QUE FALTA EN ESA FECHA
                            $he->horasAdicionales = $he->horasAdicionales == null ? 0 : $he->horasAdicionales;
                            $he->totalT = "00:00:00";
                            $he->entrada = NULL;
                            $he->salida = NULL;
                            if (
                                Carbon::parse($he->horarioIni)->isoFormat("HH:mm") >= "06:01" ||
                                Carbon::parse($he->horarioIni)->isoFormat("HH:mm") <= "22:00"
                            ) {
                                if (!isset($marcaciones[$key]->data[$d]["normal"])) {
                                    $marcaciones[$key]->data[$d]["normal"] = array();
                                }
                                $marcaciones[$key]->data[$d]["normal"][$he->idHorarioE]["dataHorario"] = $he;
                            } else {
                                if (!isset($marcaciones[$key]->data[$d]["nocturno"])) {
                                    $marcaciones[$key]->data[$d]["nocturno"] = array();
                                }
                                $marcaciones[$key]->data[$d]["nocturno"][$he->idHorarioE]["dataHorario"] = $he;
                            }
                        }
                    }
                }
                ksort($m->data);
            }
        }
        foreach ($marcaciones as $key => $m) {
            $m->data = array_values($m->data);
            $marcaciones[$key]->incidencias = array();
            $idEmpleado = $m->emple_id;
            // * *********************** INCIDENCIAS ***********************
            // : TABLA INCIDENCIAS DIA
            // DB::enableQueryLog();
            $incidencias = DB::table('incidencia_dias as id')
                ->join('incidencias as i', 'i.inciden_id', '=', 'id.id_incidencia')
                ->where('id.id_empleado', '=', $idEmpleado)
                ->select('i.inciden_id as id', DB::raw('DATEDIFF(id.inciden_dias_fechaF,id.inciden_dias_fechaI) as total'))
                ->whereBetween(DB::raw('DATE(id.inciden_dias_fechaI)'), [$fechaInicio, $fechaFin])
                ->whereBetween(DB::raw('DATE(id.inciden_dias_fechaF)'), [$fechaInicio, $fechaFin])
                ->groupBy('id.id_incidencia')
                ->get();
            // dd(DB::getQueryLog());
            foreach ($incidencias as $i) {
                array_push($marcaciones[$key]->incidencias, $i);
            }
            foreach ($m->data as $item => $data) {
                if (array_key_exists("normal", $data)) {
                    $m->data[$item]["normal"] = Arr::flatten($m->data[$item]["normal"]);
                }
                if (array_key_exists("nocturno", $data)) {
                    $m->data[$item]["nocturno"] = Arr::flatten($m->data[$item]["nocturno"]);
                }
            }
        }
        return response()->json(array("marcaciones" => $marcaciones, "organizacion" => $organizacion, "incidencias" => $incidenciasOrganizacion), 200);
    }
}
