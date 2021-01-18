<?php

namespace App\Http\Controllers;

use App\dispositivo_controlador;
use App\dispositivos;
use App\horario;
use App\marcacion_puerta;
use App\pausas_horario;
use App\tardanza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use LengthException;

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
        ->where('ar.organi_id','=',session('sesionidorg'))
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
                         'modifPuerta' => $permiso_invitado->modifPuerta, 'controladores' => $controladores,'area'=>$area,'empleado'=>$empleados
                    ]);
                } else {
                    return redirect('/dashboard');
                }
                /*   */
            } else {
                return view('Dispositivos.dispositivos', ['controladores' => $controladores,'area'=>$area,'empleado'=>$empleados]);
            }
        } else {

            $empleados = DB::table('empleado as e')
                ->join('persona as p', 'p.perso_id', '=', 'e.emple_persona')
                ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                ->where('e.emple_estado', '=', 1)
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->get();

            return view('Dispositivos.dispositivos', ['controladores' => $controladores,'area'=>$area,'empleado'=>$empleados]);
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


           $nroCel= substr($dispositivosAc->dispo_movil, 2);

            $mensaje = "Dispositivo " .$nroCel. " registrado en RH nube - Modo Asistencia en puerta,tu codigo es " . $codigo. " - Descargalo en https://play.google.com/store/apps/details?id=com.pe.rhnube";

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
        $nroCel= substr($dispositivosAc->dispo_movil, 2);

        $mensaje = "Dispositivo " .$nroCel. " registrado en RH nube - Modo Asistencia en puerta,tu codigo es " . $codigo. " - Descargalo en https://play.google.com/store/apps/details?id=com.pe.rhnube";
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
        $nroCel= substr($dispositivosAc->dispo_movil, 2);

            $mensaje = "Dispositivo " .$nroCel. " registrado en RH nube - Modo Asistencia en puerta,tu codigo es " . $codigo. " - Descargalo en https://play.google.com/store/apps/details?id=com.pe.rhnube";
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
        $fecha = Carbon::create($fechaR);

        function agruparEmpleadosMarcaciones($array)
        {
            $resultado = array();

            foreach ($array as $empleado) {
                if (!isset($resultado[$empleado->emple_id])) {
                    $resultado[$empleado->emple_id] = (object) array(
                        "emple_id" => $empleado->emple_id,
                        "marcaMov_id" => $empleado->marcaMov_id,
                        "emple_nDoc" => $empleado->emple_nDoc,
                        "perso_nombre" => $empleado->perso_nombre,
                        "perso_apPaterno" => $empleado->perso_apPaterno,
                        "perso_apMaterno" => $empleado->perso_apMaterno,
                        "cargo_descripcion" => $empleado->cargo_descripcion,
                        "organi_id" => $empleado->organi_id
                    );
                }
                if (!isset($resultado[$empleado->emple_id]->marcaciones)) {
                    $resultado[$empleado->emple_id]->marcaciones = array();
                }
                $arrayMarcacion = (object) array(
                    "idMarcacion" => $empleado->idMarcacion,
                    "entrada" => $empleado->entrada,
                    "salida" => $empleado->salida,
                    "idHorario" => $empleado->idHorario,
                    "horarioIni" => $empleado->horarioIni
                );
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
                        ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                        ->select(
                            'e.emple_id',
                            'mp.marcaMov_id',
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'mp.organi_id',
                            DB::raw('IF(hor.horario_id is null, 0 , horario_id) as idHorario'),
                            DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                            DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                            DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                            'mp.marcaMov_id as idMarcacion'
                        )
                        ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)
                        ->where('mp.organi_id', '=', session('sesionidorg'))
                        ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC', 'p.perso_nombre', 'ASC')
                        ->get();
                    $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
                } else {
                    $marcaciones = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                        ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                        ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                        ->select(
                            'e.emple_id',
                            'mp.marcaMov_id',
                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'mp.organi_id',
                            DB::raw('IF(hor.horario_id is null, 0 , horario_id) as idHorario'),
                            DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                            DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                            DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                            'mp.marcaMov_id as idMarcacion'
                        )
                        ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)
                        ->where('e.emple_id', $idemp)

                        ->where('mp.organi_id', '=', session('sesionidorg'))
                        ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC', 'p.perso_nombre', 'ASC')
                        ->get();
                    $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
                }
            } else {
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
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
                            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                            ->select(
                                'e.emple_id',
                                'mp.marcaMov_id',
                                'e.emple_nDoc',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'mp.organi_id',
                                DB::raw('IF(hor.horario_id is null, 0 , horario_id) as idHorario'),
                                DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                                DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                                DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                                'mp.marcaMov_id as idMarcacion'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)

                            ->where('mp.organi_id', '=', session('sesionidorg'))
                            ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC', 'p.perso_nombre', 'ASC')
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
                            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                            ->select(
                                'e.emple_id',
                                'mp.marcaMov_id',
                                'e.emple_nDoc',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'mp.organi_id',
                                DB::raw('IF(hor.horario_id is null, 0 , horario_id) as idHorario'),
                                DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                                DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                                DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                                'mp.marcaMov_id as idMarcacion'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)
                            ->where('e.emple_id', $idemp)

                            ->where('mp.organi_id', '=', session('sesionidorg'))
                            ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC', 'p.perso_nombre', 'ASC')
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
                            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                            ->select(
                                'e.emple_id',
                                'mp.marcaMov_id',
                                'e.emple_nDoc',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'mp.organi_id',
                                DB::raw('IF(hor.horario_id is null, 0 , horario_id) as idHorario'),
                                DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                                DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                                DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                                'mp.marcaMov_id as idMarcacion'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)

                            ->where('mp.organi_id', '=', session('sesionidorg'))
                            ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC', 'p.perso_nombre', 'ASC')
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
                            ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                            ->select(
                                'e.emple_id',
                                'mp.marcaMov_id',
                                'e.emple_nDoc',
                                'p.perso_nombre',
                                'p.perso_apPaterno',
                                'p.perso_apMaterno',
                                'c.cargo_descripcion',
                                'mp.organi_id',
                                DB::raw('IF(hor.horario_id is null, 0 , horario_id) as idHorario'),
                                DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                                DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                                DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                                'mp.marcaMov_id as idMarcacion'
                            )
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)
                            ->where('e.emple_id', $idemp)

                            ->where('mp.organi_id', '=', session('sesionidorg'))
                            ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC', 'p.perso_nombre', 'ASC')
                            ->get();
                        $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
                    }
                }
            }
        } else {
            if ($idemp == 0 || $idemp == ' ') {
                $marcaciones = DB::table('empleado as e')
                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                    ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                    ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                    ->select(
                        'e.emple_id',

                        'mp.marcaMov_id',
                        'e.emple_nDoc',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion',
                        'mp.organi_id',
                        DB::raw('IF(hor.horario_id is null, 0 , horario_id) as idHorario'),
                        DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                        DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                        DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                        'mp.marcaMov_id as idMarcacion'
                    )
                    ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)
                    ->where('mp.organi_id', '=', session('sesionidorg'))
                    ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC', 'p.perso_nombre', 'ASC')
                    ->get();
                $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
            } else {
                $marcaciones = DB::table('empleado as e')
                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                    ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                    ->leftJoin('horario_dias as hd', 'hd.id', '=', 'hoe.horario_dias_id')
                    ->select(
                        'e.emple_id',
                        'mp.marcaMov_id',
                        'e.emple_nDoc',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion',
                        'mp.organi_id',
                        DB::raw('IF(hor.horario_id is null, 0 , horario_id) as idHorario'),
                        DB::raw("IF(hor.horaI is null , 0 ,CONCAT( DATE(hd.start),' ', hor.horaI)) as horarioIni"),
                        DB::raw('IF(mp.marcaMov_fecha is null, 0 , mp.marcaMov_fecha) as entrada'),
                        DB::raw('IF(mp.marcaMov_salida is null, 0 , mp.marcaMov_salida) as salida'),
                        'mp.marcaMov_id as idMarcacion'
                    )
                    ->where(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), '=', $fecha)
                    ->where('e.emple_id', $idemp)

                    ->where('mp.organi_id', '=', session('sesionidorg'))
                    ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)'), 'ASC', 'p.perso_nombre', 'ASC')
                    ->get();
                $marcaciones = agruparEmpleadosMarcaciones($marcaciones);
            }
        }
        foreach ($marcaciones as $m) {
            $marcacion = $m->marcaciones;
            $arrayHorario = [];
            $arrayPausas = [];
            foreach ($marcacion as $mm) {
                if ($mm->idHorario != 0) {
                    // * AÑADIR DATOS DEL HORARIO
                    $horario = horario::select(
                        'horario_descripcion as horario',
                        'horaI as horarioIni',
                        'horaF as horarioFin',
                        'horario_id as idHorario'
                    )
                        ->where('horario_id', '=', $mm->idHorario)->get()->first();
                    if ($horario) {
                        if (!in_array($horario, $arrayHorario)) {  //* BUSCAMOS SI NO SE ENCUENTRA YA REGSITRADO
                            array_push($arrayHorario, $horario);
                            // * AÑADIR PAUSAS DEL HORARIO
                            $pausas = pausas_horario::select(
                                'idpausas_horario',
                                'pausH_descripcion',
                                'pausH_Inicio',
                                'pausH_Fin'
                            )
                                ->where('horario_id', '=', $mm->idHorario)->get();
                            if (sizeof($pausas) != 0) {
                                array_push($arrayPausas, $pausas);
                            }
                        }
                    }
                }
            }
            usort($arrayHorario, object_sorter('horarioIni'));
            usort($arrayPausas, object_sorter('pausH_Inicio'));
            $m->horarios = $arrayHorario;
            $m->pausas = $arrayPausas;
        }
        return response()->json($marcaciones, 200);
    }

    public function datosDispoEditar(Request $request)
    {
        $idDispo = $request->id;
        $dispositivo = dispositivos::where('dispositivos.organi_id', '=', session('sesionidorg'))
            ->leftJoin('dispositivo_controlador as dc', 'dispositivos.idDispositivos', '=', 'dc.idDispositivos')
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
                'idControladores',
                'version_firmware',
                'dispo_codigo'
            )->get();
        return $dispositivo;
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

    public function cambiarEntrada(Request $request)
    {

        $idMarca = $request->idMarca;

        //PRIMERO VERIFICO SI SOLO TIENE ENTRADA LA MARCACION
        $marcacion_puerta = marcacion_puerta::findOrFail($idMarca);
        $nuevaFecha = $marcacion_puerta->marcaMov_fecha;
        if ($marcacion_puerta->marcaMov_salida != null) {

            //CAMBIO A NULL LA MARCACION DE ENTRADA
            $marcacion_puerta->marcaMov_fecha = null;
            $marcacion_puerta->save();

            //CREO NUEVA MARCACION CON ESA FECHA
            $nuevaMarcacion = new marcacion_puerta();
            $nuevaMarcacion->marcaMov_emple_id = $marcacion_puerta->marcaMov_emple_id;
            $nuevaMarcacion->controladores_idControladores = $marcacion_puerta->controladores_idControladores;
            $nuevaMarcacion->dispositivos_idDispositivos = $marcacion_puerta->dispositivos_idDispositivos;
            $nuevaMarcacion->organi_id = $marcacion_puerta->organi_id;
            $nuevaMarcacion->horarioEmp_id = $marcacion_puerta->horarioEmp_id;
            $nuevaMarcacion->marcaMov_salida = $nuevaFecha;
            $nuevaMarcacion->marca_latitud = $marcacion_puerta->marca_latitud;
            $nuevaMarcacion->marca_longitud = $marcacion_puerta->marca_longitud;
            $nuevaMarcacion->save();
        } else {
            $marcacion_puerta->marcaMov_fecha = null;
            $marcacion_puerta->marcaMov_salida = $nuevaFecha;
            $marcacion_puerta->save();
        }
    }

    public function cambiarSalida(Request $request)
    {

        $idMarca = $request->idMarca;

        //PRIMERO VERIFICO SI SOLO TIENE SALIDA LA MARCACION
        $marcacion_puerta = marcacion_puerta::findOrFail($idMarca);
        $nuevaFecha = $marcacion_puerta->marcaMov_salida;
        if ($marcacion_puerta->marcaMov_fecha != null) {

            //CAMBIO A NULL LA MARCACION DE SALIDA
            $marcacion_puerta->marcaMov_salida = null;
            $marcacion_puerta->save();

            //CREO NUEVA MARCACION CON ESA FECHA
            $nuevaMarcacion = new marcacion_puerta();
            $nuevaMarcacion->marcaMov_emple_id = $marcacion_puerta->marcaMov_emple_id;
            $nuevaMarcacion->controladores_idControladores = $marcacion_puerta->controladores_idControladores;
            $nuevaMarcacion->dispositivos_idDispositivos = $marcacion_puerta->dispositivos_idDispositivos;
            $nuevaMarcacion->organi_id = $marcacion_puerta->organi_id;
            $nuevaMarcacion->horarioEmp_id = $marcacion_puerta->horarioEmp_id;
            $nuevaMarcacion->marcaMov_fecha = $nuevaFecha;
            $nuevaMarcacion->marca_latitud = $marcacion_puerta->marca_latitud;
            $nuevaMarcacion->marca_longitud = $marcacion_puerta->marca_longitud;
            $nuevaMarcacion->save();
        } else {
            $marcacion_puerta->marcaMov_salida = null;
            $marcacion_puerta->marcaMov_fecha = $nuevaFecha;
            $marcacion_puerta->save();
        }
    }
    public function registrarNEntrada(Request $request)
    {
        $idMarca = $request->idMarca;
        $hora = $request->hora;
        $fecha = $request->fecha;
        $fecha1 = Carbon::create($fecha);

        $marcacion_puerta = marcacion_puerta::findOrFail($idMarca);
        $fechaSalida = $marcacion_puerta->marcaMov_salida;

        if ($fecha1->gte($fechaSalida)) {
            return 0;
        } else {
            $marcacion_puerta->marcaMov_fecha = $fecha1;
            $marcacion_puerta->save();
            return 1;
        }
    }

    public function registrarNSalida(Request $request)
    {
        $idMarca = $request->idMarca;
        $hora = $request->hora;
        $fecha = $request->fecha;
        $fecha1 = Carbon::create($fecha);

        $marcacion_puerta = marcacion_puerta::findOrFail($idMarca);
        $fechaEntrada = $marcacion_puerta->marcaMov_fecha;

        if ($fecha1->lte($fechaEntrada)) {
            return [0, 'Hora de salida debe ser mayor a la hora de entrada.'];
        } else {
            $fecha111 = Carbon::create($marcacion_puerta->marcaMov_fecha)->toDateString();
            $marcacion_puerta00 = DB::table('marcacion_puerta as mv')
                ->where('mv.marcaMov_emple_id', '=', $marcacion_puerta->marcaMov_emple_id)
                ->where('mv.marcaMov_fecha', '!=', null)
                ->where('mv.marcaMov_fecha', '!=', $fechaEntrada)
                ->where('mv.marcaMov_fecha', '>', $marcacion_puerta->marcaMov_fecha)
                ->whereDate('mv.marcaMov_fecha', '=', $fecha111)
                /*   ->where('mv.marcaMov_fecha', '>',$req['fechaMarcacion'] ) */
                ->orderby('marcaMov_fecha', 'ASC')
                ->get()->first();

            if ($marcacion_puerta00) {

                $fechaEPosterir = Carbon::create($marcacion_puerta00->marcaMov_fecha);
                /*     dd($fechaEPosterir,$fecha1); */
                if ($fecha1->gte($fechaEPosterir)) {

                    return [0, 'Tienes registrado otra entrada, la hora de salida debe ser menor a esta.'];
                } else {

                    $marcacion_puerta->marcaMov_salida = $fecha1;
                    $marcacion_puerta->save();
                    return 1;
                }
            } else {
                $marcacion_puerta->marcaMov_salida = $fecha1;
                $marcacion_puerta->save();
                return 1;
            }

            //////////////////////////////////////////////////////////////////

        }
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
        /*  dd($fechaR); */
        $idemp = $request->idemp;
        $fecha = Carbon::create($fechaR);
        /*  $año = $fecha->year;
        $mes = $fecha->month;
        $dia = $fecha->day;
        $ndia = $dia + 1; */

        $fecha2 = $request->fecha2;
        $fechaF = Carbon::create($fecha2);

        $invitadod = DB::table('invitado')
            ->where('user_Invitado', '=', Auth::user()->id)
            ->where('organi_id', '=', session('sesionidorg'))
            ->where('rol_id', '=', 3)
            ->get()->first();
        if ($invitadod) {
            if ($invitadod->verTodosEmps == 1) {

                $marcaciones = DB::table('empleado as e')
                    ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                    ->leftJoin('actividad as acti', 'mp.marcaIdActivi', '=', 'acti.Activi_id')
                    ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                    ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                    ->select(
                        'e.emple_id',
                        DB::raw('IF(mp.marcaMov_fecha is null,mp.marcaMov_salida ,mp.marcaMov_fecha) as entradaModif'),
                        DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id) as idhorario'),

                        'ar.area_descripcion',
                        /* 'mp.marcaMov_id', */
                        'e.emple_nDoc',
                        'p.perso_nombre',
                        'p.perso_apPaterno',
                        'p.perso_apMaterno',
                        'c.cargo_descripcion',
                        'mp.organi_id',
                        DB::raw('IF(hor.horario_id is null, 0 , hor.horario_descripcion) as horario')


                    )
                    ->whereBetween(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), [$fecha, $fechaF])
                    ->where('e.emple_id', $idemp)
                    ->groupBy(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida) , DATE(mp.marcaMov_fecha))'), DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id)'))
                    ->where('mp.organi_id', '=', session('sesionidorg'))
                    /* ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)', 'ASC')) */
                    ->get();

                foreach ($marcaciones as $tab) {
                    $fechaEntr1 = Carbon::create($tab->entradaModif);
                    $fechaEntr2 = $fechaEntr1->isoFormat('YYYY-MM-DD');

                    $marcacion_puerta = DB::table('marcacion_puerta as map')
                        ->leftJoin('horario_empleado as hoeM', 'map.horarioEmp_id', '=', 'hoeM.horarioEmp_id')
                        ->leftJoin('horario as horM', 'hoeM.horario_horario_id', '=', 'horM.horario_id')
                        ->select(
                            'map.marcaMov_id as idMarcacion',
                            'map.marcaMov_emple_id',

                            DB::raw('IF(map.marcaMov_fecha is null, 0 , map.marcaMov_fecha) as entrada'),
                            DB::raw('IF(map.marcaMov_salida is null, 0 , map.marcaMov_salida) as salida')
                        )
                        ->orderBy(DB::raw('IF(map.marcaMov_fecha is null, map.marcaMov_salida , map.marcaMov_fecha)', 'ASC'))
                        ->whereBetween(DB::raw('IF(map.marcaMov_fecha is null, DATE(map.marcaMov_salida), DATE(map.marcaMov_fecha))'), [$fecha, $fechaF])
                        ->where('map.marcaMov_emple_id', '=', $idemp)
                        ->whereDate(DB::raw('IF(map.marcaMov_fecha is null, DATE(map.marcaMov_salida) , DATE(map.marcaMov_fecha))'), '=', $fechaEntr2)
                        ->where(DB::raw('IF(horM.horario_id is null, 0 ,horM.horario_id)'), '=', $tab->idhorario)

                        /*    ->distinct('map.marcaMov_id') */
                        ->get();

                    $tab->marcaciones = $marcacion_puerta;
                }
            } else {
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                    ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
                    ->where('invem.area_id', '=', null)
                    ->where('invem.emple_id', '!=', null)
                    ->get()->first();
                if ($invitado_empleadoIn != null) {

                    $marcaciones = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                        ->leftJoin('actividad as acti', 'mp.marcaIdActivi', '=', 'acti.Activi_id')
                        ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                        ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                        ->select(
                            'e.emple_id',
                            DB::raw('IF(mp.marcaMov_fecha is null,mp.marcaMov_salida ,mp.marcaMov_fecha) as entradaModif'),
                            DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id) as idhorario'),
                            'ar.area_descripcion',

                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'mp.organi_id',
                            DB::raw('IF(hor.horario_id is null, 0 , hor.horario_descripcion) as horario')

                        )
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->whereBetween(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), [$fecha, $fechaF])
                        ->where('e.emple_id', $idemp)

                        ->groupBy(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida) , DATE(mp.marcaMov_fecha))'), DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id)'))
                        ->where('mp.organi_id', '=', session('sesionidorg'))
                        ->get();

                    foreach ($marcaciones as $tab) {
                        $fechaEntr1 = Carbon::create($tab->entradaModif);
                        $fechaEntr2 = $fechaEntr1->isoFormat('YYYY-MM-DD');

                        $marcacion_puerta = DB::table('marcacion_puerta as map')
                            ->leftJoin('horario_empleado as hoeM', 'map.horarioEmp_id', '=', 'hoeM.horarioEmp_id')
                            ->leftJoin('horario as horM', 'hoeM.horario_horario_id', '=', 'horM.horario_id')
                            ->select(
                                'map.marcaMov_id as idMarcacion',
                                'map.marcaMov_emple_id',

                                DB::raw('IF(map.marcaMov_fecha is null, 0 , map.marcaMov_fecha) as entrada'),
                                DB::raw('IF(map.marcaMov_salida is null, 0 , map.marcaMov_salida) as salida')
                            )
                            ->orderBy(DB::raw('IF(map.marcaMov_fecha is null, map.marcaMov_salida , map.marcaMov_fecha)', 'ASC'))
                            ->whereBetween(DB::raw('IF(map.marcaMov_fecha is null, DATE(map.marcaMov_salida), DATE(map.marcaMov_fecha))'), [$fecha, $fechaF])
                            ->where('map.marcaMov_emple_id', '=', $idemp)
                            ->whereDate(DB::raw('IF(map.marcaMov_fecha is null, DATE(map.marcaMov_salida) , DATE(map.marcaMov_fecha))'), '=', $fechaEntr2)
                            ->where(DB::raw('IF(horM.horario_id is null, 0 ,horM.horario_id)'), '=', $tab->idhorario)

                            /*    ->distinct('map.marcaMov_id') */
                            ->get();

                        $tab->marcaciones = $marcacion_puerta;
                    }
                } else {

                    $marcaciones = DB::table('empleado as e')
                        ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                        ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                        ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                        ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                        ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                        ->leftJoin('actividad as acti', 'mp.marcaIdActivi', '=', 'acti.Activi_id')
                        ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                        ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                        ->select(
                            'e.emple_id',
                            DB::raw('IF(mp.marcaMov_fecha is null,mp.marcaMov_salida ,mp.marcaMov_fecha) as entradaModif'),
                            DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id) as idhorario'),
                            'ar.area_descripcion',

                            'e.emple_nDoc',
                            'p.perso_nombre',
                            'p.perso_apPaterno',
                            'p.perso_apMaterno',
                            'c.cargo_descripcion',
                            'mp.organi_id',

                            DB::raw('IF(hor.horario_id is null, 0 , hor.horario_descripcion) as horario')
                        )
                        ->where('invi.estado', '=', 1)
                        ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                        ->whereBetween(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), [$fecha, $fechaF])
                        ->where('e.emple_id', $idemp)

                        ->groupBy(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida) , DATE(mp.marcaMov_fecha))'), DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id)'))
                        ->where('mp.organi_id', '=', session('sesionidorg'))

                        ->get();

                    foreach ($marcaciones as $tab) {
                        $fechaEntr1 = Carbon::create($tab->entradaModif);
                        $fechaEntr2 = $fechaEntr1->isoFormat('YYYY-MM-DD');

                        $marcacion_puerta = DB::table('marcacion_puerta as map')
                            ->leftJoin('horario_empleado as hoeM', 'map.horarioEmp_id', '=', 'hoeM.horarioEmp_id')
                            ->leftJoin('horario as horM', 'hoeM.horario_horario_id', '=', 'horM.horario_id')
                            ->select(
                                'map.marcaMov_id as idMarcacion',
                                'map.marcaMov_emple_id',

                                DB::raw('IF(map.marcaMov_fecha is null, 0 , map.marcaMov_fecha) as entrada'),
                                DB::raw('IF(map.marcaMov_salida is null, 0 , map.marcaMov_salida) as salida')
                            )
                            ->orderBy(DB::raw('IF(map.marcaMov_fecha is null, map.marcaMov_salida , map.marcaMov_fecha)', 'ASC'))
                            ->whereBetween(DB::raw('IF(map.marcaMov_fecha is null, DATE(map.marcaMov_salida), DATE(map.marcaMov_fecha))'), [$fecha, $fechaF])
                            ->where('map.marcaMov_emple_id', '=', $idemp)
                            ->whereDate(DB::raw('IF(map.marcaMov_fecha is null, DATE(map.marcaMov_salida) , DATE(map.marcaMov_fecha))'), '=', $fechaEntr2)
                            ->where(DB::raw('IF(horM.horario_id is null, 0 ,horM.horario_id)'), '=', $tab->idhorario)

                            /*    ->distinct('map.marcaMov_id') */
                            ->get();

                        $tab->marcaciones = $marcacion_puerta;
                    }
                }
            }
        } else {

            $marcaciones = DB::table('empleado as e')
                ->join('marcacion_puerta as mp', 'mp.marcaMov_emple_id', '=', 'e.emple_id')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->leftJoin('cargo as c', 'e.emple_cargo', '=', 'c.cargo_id')
                ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                ->leftJoin('actividad as acti', 'mp.marcaIdActivi', '=', 'acti.Activi_id')
                ->leftJoin('horario_empleado as hoe', 'mp.horarioEmp_id', '=', 'hoe.horarioEmp_id')
                ->leftJoin('horario as hor', 'hoe.horario_horario_id', '=', 'hor.horario_id')
                ->select(
                    'e.emple_id',
                    DB::raw('IF(mp.marcaMov_fecha is null,mp.marcaMov_salida ,mp.marcaMov_fecha) as entradaModif'),
                    DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id) as idhorario'),

                    'ar.area_descripcion',
                    /* 'mp.marcaMov_id', */
                    'e.emple_nDoc',
                    'p.perso_nombre',
                    'p.perso_apPaterno',
                    'p.perso_apMaterno',
                    'c.cargo_descripcion',
                    'mp.organi_id',
                    DB::raw('IF(hor.horario_id is null, 0 , hor.horario_descripcion) as horario')


                )
                ->whereBetween(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida), DATE(mp.marcaMov_fecha))'), [$fecha, $fechaF])
                ->where('e.emple_id', $idemp)
                ->groupBy(DB::raw('IF(mp.marcaMov_fecha is null, DATE(mp.marcaMov_salida) , DATE(mp.marcaMov_fecha))'), DB::raw('IF(hor.horario_id is null, 0 , hor.horario_id)'))
                ->where('mp.organi_id', '=', session('sesionidorg'))
                /* ->orderBy(DB::raw('IF(mp.marcaMov_fecha is null, mp.marcaMov_salida , mp.marcaMov_fecha)', 'ASC')) */
                ->get();

            foreach ($marcaciones as $tab) {
                $fechaEntr1 = Carbon::create($tab->entradaModif);
                $fechaEntr2 = $fechaEntr1->isoFormat('YYYY-MM-DD');

                $marcacion_puerta = DB::table('marcacion_puerta as map')
                    ->leftJoin('horario_empleado as hoeM', 'map.horarioEmp_id', '=', 'hoeM.horarioEmp_id')
                    ->leftJoin('horario as horM', 'hoeM.horario_horario_id', '=', 'horM.horario_id')
                    ->select(
                        'map.marcaMov_id as idMarcacion',
                        'map.marcaMov_emple_id',

                        DB::raw('IF(map.marcaMov_fecha is null, 0 , map.marcaMov_fecha) as entrada'),
                        DB::raw('IF(map.marcaMov_salida is null, 0 , map.marcaMov_salida) as salida')
                    )
                    ->orderBy(DB::raw('IF(map.marcaMov_fecha is null, map.marcaMov_salida , map.marcaMov_fecha)', 'ASC'))
                    ->whereBetween(DB::raw('IF(map.marcaMov_fecha is null, DATE(map.marcaMov_salida), DATE(map.marcaMov_fecha))'), [$fecha, $fechaF])
                    ->where('map.marcaMov_emple_id', '=', $idemp)
                    ->whereDate(DB::raw('IF(map.marcaMov_fecha is null, DATE(map.marcaMov_salida) , DATE(map.marcaMov_fecha))'), '=', $fechaEntr2)
                    ->where(DB::raw('IF(horM.horario_id is null, 0 ,horM.horario_id)'), '=', $tab->idhorario)

                    /*    ->distinct('map.marcaMov_id') */
                    ->get();

                $tab->marcaciones = $marcacion_puerta;
            }
        }
        $marcacionesX = Arr::flatten($marcaciones);
        return response()->json($marcacionesX, 200);
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
}
