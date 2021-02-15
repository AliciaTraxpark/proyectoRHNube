<?php

namespace App\Http\Controllers;

use App\controladores;
use App\dispositivos;
use App\dispo_nombres;
use App\Mail\SoporteApiMovil;
use App\Mail\SugerenciaApiMovil;
use App\marcacion_puerta;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class apimovilController extends Controller
{
    //ACTIVACION DISPOSITIVO
    public function apiActivacion(Request $request)
    {

        /* OBTENEMOS PARAMETROS */
        $nroMovil = $request->nroMovil;
        $codigo = $request->codigo;
        $nombreDisp = $request->nombreDisp;
        /* -------------------------- */

        /* VERIFICACOM SI DATOS COINCIDEN CON DISPOSTIVO REGISTRADO */
        $dispositivo = dispositivos::where('dispo_movil', '=', $nroMovil)
            ->where('dispo_codigo', '=', $codigo)->where('dispo_estadoActivo', '=', [1, 2])->get()->first();
        /* -------------------------------------------------------- */

        /* SI EL DISPOSITIVO EXISTE */
        if ($dispositivo != null) {

            /* SI EL DISPOSITO YA AH SIDO PREVIAMENTE CONFIRMADO */
            if ($dispositivo->dispo_estado == 2) {

                /* SI LA CLAVE ES LA CORRECTA */
                if ($dispositivo->dispo_codigo == $codigo) {

                    /* CREAMOS TOKEN */
                    $factory = JWTFactory::customClaims([
                        'sub' => env('API_id'),
                    ]);
                    $payload = $factory->make();
                    $token = JWTAuth::encode($payload);
                    /* ----------------- */

                    /* VERIFICAMOS SI YA ESTA REGISTRADO EL NOMBRE/IMEI DE DISPOSITIVO */
                    $nombreDs = DB::table('dispo_nombres')
                        ->where('idDispositivos', '=', $dispositivo->idDispositivos)
                        ->where('dispo_CodigoNombre', '=', $nombreDisp)
                        ->get();

                    /* SI NO ESTA REGISTRADO  */
                    if ($nombreDs->isEmpty()) {

                        /* REGISTRAMOS EN LA BD */
                        $dispo_nombre = new dispo_nombres();
                        $dispo_nombre->dispo_CodigoNombre = $nombreDisp;
                        $dispo_nombre->idDispositivos = $dispositivo->idDispositivos;
                        $dispo_nombre->save();
                        return response()->json(array(
                            'status' => 200, "dispositivo" => $dispositivo,
                            "disponombre" => $dispo_nombre, "token" => $token->get(),
                        ));
                    } else {
                        return response()->json(array(
                            'status' => 200, "dispositivo" => $dispositivo,
                            "disponombre" => $nombreDs, "token" => $token->get(),
                        ));
                    }
                } else {
                    /* SI LA CLAVE ES INCORRECTA */
                    return response()->json(array(
                        'status' => 400, 'title' => 'Clave incorrecta',
                        'detail' => 'Asegúrate de escribir la clave correcta',
                    ), 400);
                }
            } else {
                /* SI NO ESTA CONFIRMADO Y SOLO ENVIADO SMS */
                if ($dispositivo->dispo_estado == 1) {

                    /* ACTUALIZAMOS EL ESTADO DE DISPOSITIVO */
                    $dispositivosAc = dispositivos::findOrFail($dispositivo->idDispositivos);
                    $dispositivosAc->dispo_estado = 2;
                    $dispositivosAc->save();
                    /* ------------------------------------ */

                    /* CREAMOS NUEVA ASOCIACION CON NOMBRE */
                    $dispo_nombre = new dispo_nombres();
                    $dispo_nombre->dispo_CodigoNombre = $nombreDisp;
                    $dispo_nombre->idDispositivos = $dispositivosAc->idDispositivos;
                    $dispo_nombre->save();
                    /* ------------------------------------- */

                    /* CREAMOS TOKEN */
                    $factory = JWTFactory::customClaims([
                        'sub' => env('API_id'),
                    ]);
                    $payload = $factory->make();
                    $token = JWTAuth::encode($payload);
                    /* --------------------- */

                    return response()->json(array(
                        'status' => 200, "dispositivo" => $dispositivosAc,
                        "disponombre" => $dispo_nombre, "token" => $token->get(),
                    ));
                }
            }
        } else {
            /* VERIFICACOMO SI TENEMOS AL MENOS EL NUMERO DE DISPOSITOV REGISTRADO */
            $dispositivo1 = dispositivos::where('dispo_movil', '=', $nroMovil)
                ->get()->first();
            /* ----------------------------------------------------------- */

            /* SI LO TENEMOS REGISTRADO  */
            if ($dispositivo1 != null) {
                return response()->json(array(
                    'status' => 400, 'title' => 'Clave incorrecta o dispositivo no activo',
                    'detail' => 'Asegúrate de escribir la clave correcta',
                ), 400);
            } else {
                /* SI NO LO TENEMOS REGISTRADO */
                return response()->json(array(
                    'status' => 400, 'title' => 'Dispositivo no existe',
                    'detail' => 'Asegúrate de registrar el dispositivo desde la plataforma web',
                ), 400);
            }
        }
    }

    //EMPLEADOS
    public function EmpleadoMovil(Request $request)
    {

        /* OBTENEMOS EL ID DE ORGANIZACION */
        $organi_id = $request->organi_id;
        /* ----------------------------------- */

        /* OBTEMOS EMPLEADOS CON ID DE ORGANIZACION RECIBIDA, QUE ESTEN ACTIVOS, Y TENGAS ASISTENCIA EN PUERTA */
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'e.emple_id')
            ->where('e.organi_id', '=', $organi_id)
            ->where('e.emple_estado', '=', 1)
            ->where('e.asistencia_puerta', '=', 1)
            ->paginate();
        /* --------------------------------------------------------------------------------------------------- */

        /* SI EXISTE EMPLEADOS */
        if ($empleado != null) {
            return response()->json(array('status' => 200, "empleados" => $empleado));
        } else {
            return response()->json(array(
                'status' => 400, 'title' => 'Empleados no encontrados',
                'detail' => 'No se encontro empleados relacionados con este dispositivo',
            ), 400);
        }
    }
    //ACTIVIDADES
    public function ActivMovil(Request $request)
    {

        /* OBTENEMOS EL ID DE ORGANIZACION */
        $organi_id = $request->organi_id;
        /* ------------------------------ */

        /* obtenemos actividades de esta organizacion que esten activas */
        $actividades = DB::table('actividad as a')
            ->select(
                'a.Activi_id',
                'a.Activi_Nombre',
                'a.organi_id',
                'a.codigoActividad'
            )
            ->where('a.organi_id', '=', $organi_id)
            ->where('a.estado', '=', 1)
            ->where('a.asistenciaPuerta', '=', 1)
            ->get();

        /* ---------------------------------------------------------------- */

        /* VERIFICAMOS QUE EXISTAN LAS ACTIVIDADES */
        if ($actividades != null) {
            return response()->json(array('status' => 200, "actividades" => $actividades));
        } else {
            return response()->json(array(
                'status' => 400, 'title' => 'Actividades no encontradas',
                'detail' => 'No se encontro actividades en esta organizacion',
            ), 400);
        }
    }
    public function controladoresAct(Request $request)
    {

        /* OBTENEMOS PARAMENTROS */
        $organi_id = $request->organi_id;
        $idDispo = $request->idDispo;
        /* ----------------------- */

        /* OBTENEMOS DISPOSITIVOS QUE TENGAN  CONTROLADORES */
        $dispositivo_Controlador = DB::table('dispositivo_controlador as dc')
            ->join('controladores as con', 'dc.idControladores', '=', 'con.idControladores')
            ->join('dispositivos as dis', 'dc.idDispositivos', '=', 'dis.idDispositivos')
            ->select(
                'con.idControladores',
                'con.cont_codigo',
                'con.cont_nombres',
                'con.cont_ApPaterno',
                'con.cont_ApMaterno',
                'con.cont_estado'
            )
            ->where('dis.idDispositivos', '=', $idDispo)
            ->where('dis.organi_id', '=', $organi_id)
            ->where('con.cont_estado', '=', 1)
            ->get();
        /* ------------------------------------------------------------------ */

        /* VERIFIACMOS SI EXISTEN */
        if ($dispositivo_Controlador != null) {
            return response()->json(array('status' => 200, "controladores" => $dispositivo_Controlador));
        } else {
            return response()->json(array(
                'status' => 400, 'title' => 'Controladores no encontrados',
                'detail' => 'No se encontro controladores relacionados con este dispositivo',
            ), 400);
        }
    }

    /* MARCACIONES EN PUERTA  */
    public function marcacionMovil(Request $request)
    {

        /* --------------ORDENAMOS DE MENOR A MAYOR-------------------------------------------------- */

        $arrayDatos = new Collection();

        /* RECORREMOS ARRAY RECIBIDO */
        foreach ($request->all() as $req) {

            /* OBTENEMOS PARAMENTROS */
            $tipoMarcacion = $req['tipoMarcacion'];
            $fechaMarcacion = $req['fechaMarcacion'];
            $idEmpleado = $req['idEmpleado'];
            $idControlador = $req['idControlador'];
            $idDisposi = $req['idDisposi'];
            $organi_id = $req['organi_id'];

            if (empty($req['activ_id'])) {
                $activ_id = null;
            } else {
                $activ_id = $req['activ_id'];
            }

            if (empty($req['idHoraEmp'])) {
                $idHoraEmp = null;
            } else {
                $idHoraEmp = $req['idHoraEmp'];
            }

            if (empty($req['latitud'])) {
                $latitud = null;
            } else {
                $latitud = $req['latitud'];
            }

            if (empty($req['longitud'])) {
                $longitud = null;
            } else {
                $longitud = $req['longitud'];
            }

            if (empty($req['puntoC_id'])) {
                $puntoC_id = null;
            } else {
                $puntoC_id = $req['puntoC_id'];
            }

            if (empty($req['centC_id'])) {
                $centC_id = null;
            } else {
                $centC_id = $req['centC_id'];
            }

            /* INSERTAMOS EN COLLECTION */
            $datos = [
                'tipoMarcacion' => $tipoMarcacion, 'fechaMarcacion' => $fechaMarcacion,
                'idEmpleado' => $idEmpleado, 'idControlador' => $idControlador,
                'idDisposi' => $idDisposi, 'organi_id' => $organi_id, 'activ_id' => $activ_id,
                'idHoraEmp' => $idHoraEmp, 'latitud' => $latitud, 'longitud' => $longitud,
                'puntoC_id' => $puntoC_id, 'centC_id' => $centC_id,
            ];

            $arrayDatos->push($datos);
        }
        /* ORDENAMOS POR FECHA */
        $arrayOrdenado = $arrayDatos->sortBy('fechaMarcacion');
        $arrayOrdenado->values()->all();
        /* ----------------------------------------------------------------------------------------------------*/

        /* RECORREMOS ARRAY ORDENADO PARA REGISTRAR */
        foreach ($arrayOrdenado as $req) {

            /* SI ES ENTRADA */
            if ($req['tipoMarcacion'] == 1) {
                /* --------CREAMOS NUEVA MARCACION---------------------- */
                $marcacion_puerta = new marcacion_puerta();
                $marcacion_puerta->marcaMov_fecha = $req['fechaMarcacion'];
                $marcacion_puerta->marcaMov_emple_id = $req['idEmpleado'];
                $marcacion_puerta->controladores_idControladores = $req['idControlador'];
                $marcacion_puerta->dispositivoEntrada = $req['idDisposi'];
                $marcacion_puerta->organi_id = $req['organi_id'];
                if (empty($req['activ_id'])) {
                } else {
                    $marcacion_puerta->marcaIdActivi = $req['activ_id'];
                }

                if (empty($req['idHoraEmp'])) {
                } else {
                    $marcacion_puerta->horarioEmp_id = $req['idHoraEmp'];
                }
                if (empty($req['latitud'])) {
                } else {
                    $marcacion_puerta->marca_latitud = $req['latitud'];
                }
                if (empty($req['longitud'])) {
                } else {
                    $marcacion_puerta->marca_longitud = $req['longitud'];
                }

                if (empty($req['puntoC_id'])) {
                } else {
                    $marcacion_puerta->puntoC_id = $req['puntoC_id'];
                }
                if (empty($req['centC_id'])) {
                } else {
                    $marcacion_puerta->centC_id = $req['centC_id'];
                }
                $marcacion_puerta->save();
            } else {

                /* OBTENEMOS LA FECHA EN FORMATO DATE */
                $fecha1 = Carbon::create($req['fechaMarcacion'])->toDateString();

                /* VERIFICAMOS SI EXISTE OTRA MARCACION CON EL MISMO DIA Y EMPLEADO */
                $marcacion_puerta00 = DB::table('marcacion_puerta as mv')
                    ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                    ->where('dis.tipoDispositivo', '=', 2)
                    ->orderby('marcaMov_fecha', 'ASC')
                    ->get()->last();
                /* ---------------------------------------------------------------- */

                /* SI EXISTE MARCACION ANTERIOR */
                if ($marcacion_puerta00) {
                    if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida != null) {
                        /*  SI LA MARCACION ANTERIOR LA ENTRADA ES MAYOR QUE LA SALIDA QUE RECIBO */
                        if ($marcacion_puerta00->marcaMov_fecha > $req['fechaMarcacion']) {

                            /* VERIFICAMOS SI EXISTE MARCACION SIN SALIDA */
                            $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                                ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                ->where('mv.marcaMov_salida', '=', null)
                                ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                                ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])

                                ->where('dis.tipoDispositivo', '=', 2)
                                ->orderby('marcaMov_fecha', 'ASC')
                                ->get()->first();
                        } else {
                            $marcacion_puerta1 = [];
                            $marcacion_puerta1 == null;
                        }
                    } else {
                        $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                            ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                            ->where('mv.marcaMov_salida', '=', null)
                            ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                            ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])

                            ->where('dis.tipoDispositivo', '=', 2)
                            ->orderby('marcaMov_fecha', 'ASC')
                            ->get()->last();
                    }
                } else {
                    $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                        ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                        ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                        ->where('mv.marcaMov_salida', '=', null)
                        ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                        ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])

                        ->where('dis.tipoDispositivo', '=', 2)
                        ->orderby('marcaMov_fecha', 'ASC')
                        ->get()->last();
                }

                /* SI NO EXISTE MARCACION SIN SALIDA */
                if ($marcacion_puerta1 == null) {
                    /* creamos nueva marcacion */
                    $marcacion_puerta = new marcacion_puerta();
                    $marcacion_puerta->marcaMov_salida = $req['fechaMarcacion'];
                    $marcacion_puerta->marcaMov_emple_id = $req['idEmpleado'];
                    $marcacion_puerta->controladores_salida = $req['idControlador'];
                    $marcacion_puerta->dispositivoSalida = $req['idDisposi'];
                    $marcacion_puerta->organi_id = $req['organi_id'];
                    if (empty($req['activ_id'])) {
                    } else {
                        /*  $marcacion_puerta->marcaIdActivi=$req['activ_id']; */
                    }

                    if (empty($req['idHoraEmp'])) {
                    } else {
                        $marcacion_puerta->horarioEmp_id = $req['idHoraEmp'];
                    }
                    if (empty($req['latitud'])) {
                    } else {
                        $marcacion_puerta->marca_latitud = $req['latitud'];
                    }
                    if (empty($req['longitud'])) {
                    } else {
                        $marcacion_puerta->marca_longitud = $req['longitud'];
                    }
                    if (empty($req['puntoC_id'])) {
                    } else {
                    }
                    if (empty($req['centC_id'])) {
                    } else {
                    }
                    $marcacion_puerta->save();
                } else {
                    /* EMPAREJAMOS CON LA MARCACION SIN SALIDA QUE ENCONTRAMOS */
                    $marcacion_puerta = marcacion_puerta::find($marcacion_puerta1->marcaMov_id);
                    $marcacion_puerta->marcaMov_salida = $req['fechaMarcacion'];
                    $marcacion_puerta->dispositivoSalida = $req['idDisposi'];
                    $marcacion_puerta->controladores_salida = $req['idControlador'];
                    $marcacion_puerta->save();
                }
            }
        }

        if ($marcacion_puerta) {
            return response()->json(array(
                'status' => 200, 'title' => 'Marcacion registrada correctamente',
                'detail' => 'Marcacion registrada correctamente en la base de datos',
            ), 200);
        } else {
            return response()->json(array(
                'status' => 400, 'title' => 'No se pudo registrar marcacion',
                'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos',
            ), 400);
        }
    }

    public function marcacionMovilActual(Request $request)
    {
        /* --------------ORDENAMOS DE MENOR A MAYOR-------------------------------------------------- */

        $arrayDatos = new Collection();

        /* RECORREMOS ARRAY RECIBIDO */
        foreach ($request->all() as $req) {

            /* OBTENEMOS PARAMENTROS */
            
            $fechaMarcacion = $req['fechaMarcacion'];
            $idEmpleado = $req['idEmpleado'];
            $idControlador = $req['idControlador'];
            $idDisposi = $req['idDisposi'];
            $organi_id = $req['organi_id'];

            if (empty($req['activ_id'])) {
                $activ_id = null;
            } else {
                $activ_id = $req['activ_id'];
            }

            if (empty($req['latitud'])) {
                $latitud = null;
            } else {
                $latitud = $req['latitud'];
            }

            if (empty($req['longitud'])) {
                $longitud = null;
            } else {
                $longitud = $req['longitud'];
            }

            if (empty($req['puntoC_id'])) {
                $puntoC_id = null;
            } else {
                $puntoC_id = $req['puntoC_id'];
            }

            if (empty($req['centC_id'])) {
                $centC_id = null;
            } else {
                $centC_id = $req['centC_id'];
            }

            /* INSERTAMOS EN COLLECTION */
            $datos = [
                'fechaMarcacion' => $fechaMarcacion,
                'idEmpleado' => $idEmpleado, 'idControlador' => $idControlador,
                'idDisposi' => $idDisposi, 'organi_id' => $organi_id, 'activ_id' => $activ_id,
                'latitud' => $latitud, 'longitud' => $longitud,
                'puntoC_id' => $puntoC_id, 'centC_id' => $centC_id,
            ];

            $arrayDatos->push($datos);
        }
        /* ORDENAMOS POR FECHA */
        $arrayOrdenado = $arrayDatos->sortBy('fechaMarcacion');
        $arrayOrdenado->values()->all();
        /* ----------------------------------------------------------------------------------------------------*/

        //*************** */ AHORA AVERIGUAREMOS SI TIENE HORARIO********************************************* */
        foreach ($arrayOrdenado as $req) {
            //*FECHA DE MARCACION
            $fecha1V = Carbon::create($req['fechaMarcacion'])->toDateString();

            /* --------------------------------------------------------------- */

            /*-------------- VERIFICAMOS SI TIENE HORARIO--------------------------- */
            $horarioEmpleado = DB::table('horario_empleado as he')
                ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->select(
                    'he.horarioEmp_id as idHorarioEmpleado',
                    'h.horaI as horaI',
                    'h.horaF as horaF',
                    'h.horario_tolerancia as toleranciaI',
                    'h.horario_toleranciaF as toleranciaF',
                    'hd.start'
                )
                ->where('he.empleado_emple_id', '=', $req['idEmpleado'])
                ->where(DB::raw('DATE(hd.start)'), '=', $fecha1V)
                ->where('he.estado', '=', 1)
                ->orderBy('h.horaI', 'ASC')
                ->get();
           
            //* SI NO TIENE HORARIO
            if($horarioEmpleado->isEmpty()){
                $conhorario=0;
            }
            else{
                //*SI TIENE HORARIO 
               
                foreach($horarioEmpleado as $horarioEmpleados){

                    //*verificamos si hora fin de horario pertenece a hoy
                    $fecha = Carbon::create($horarioEmpleados->start);
                    $fechaHorario = $fecha->isoFormat('YYYY-MM-DD');
                    $despues = $fecha->addDays(1);
                    $fechaMan = $despues->isoFormat('YYYY-MM-DD');

                    if (Carbon::parse($horarioEmpleados->horaF)->lt(Carbon::parse($horarioEmpleados->horaI))) {

                        $horarioEmpleados->horaI = Carbon::parse($fechaHorario . " " . $horarioEmpleados->horaI)->subMinutes($horarioEmpleados->toleranciaI);
                        $horarioEmpleados->horaF = Carbon::parse($fechaMan . " " . $horarioEmpleados->horaF)->addMinutes($horarioEmpleados->toleranciaF);
                    } else {
                        $horarioEmpleados->horaI =Carbon::parse($fechaHorario . " " . $horarioEmpleados->horaI)->subMinutes($horarioEmpleados->toleranciaI);
                        $horarioEmpleados->horaF = Carbon::parse($fechaHorario . " " . $horarioEmpleados->horaF)->addMinutes($horarioEmpleados->toleranciaF);
                    } 
                }
              
                //*verificamos si esta dentro de horario
               
                foreach($horarioEmpleado as $horarioDentro){
                   
                    $fechaHorahoy=Carbon::create($req['fechaMarcacion']);
                    if ($fechaHorahoy->gte($horarioDentro->horaI) && $fechaHorahoy->lte($horarioDentro->horaF)) {
                         //*se encontro 1 horario y se detiene foreach
                         $conhorario=$horarioDentro->idHorarioEmpleado;
                         break;  
                    } else {
                        //*NO SE ENCONTRO HORARIO
                      
                        $conhorario=0;
                    }
                     
                }             
            }
            /* --------------------CALCULAMOS EL TIPO DE MARCACION----------------------- */
            /* ----------------------SI RECIBO SIN HORARIO----------------------------- */
            if ($conhorario==0) {

                //************ARRAY GENERALES**************************************
                //*marcacion cualquiera d empleado de hoy
                $marcacion_puertaVacio = DB::table('marcacion_puerta as mv')
                    ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha1V)
                    ->whereNull('mv.horarioEmp_id')
                    ->get();

                //*ultima marcacion de emmpleado
                $marcacion_puertaVerif = DB::table('marcacion_puerta as mv')
                ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha1V)
                ->whereNull('mv.horarioEmp_id')
                ->orderby(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), 'ASC')
                ->get()->last();

                //* SI NO TENGO SALIDA
                $marcacion_puertaVerif2 = DB::table('marcacion_puerta as mv')
                ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                ->where('mv.marcaMov_salida', '=', null)
                ->whereDate('mv.marcaMov_fecha', '=', $fecha1V)
                ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                ->whereNull('mv.horarioEmp_id')
                ->orderby('marcaMov_fecha', 'ASC')
                ->get()->first();

                /* **********FIN ARRAY GENERALES************** */

                /* VERIFICO SI NO HAY MARCACIONES ANTES */

                if ($marcacion_puertaVacio->isEmpty()) {

                    //* entrada porque es la 1ra marcacion
                    $tipoMarcacion = 1;
                }

                else {
                    /* YA HAY MARCACIONES PARA ESTE EMPLEADO Y FECHA */
                    /* ------------------------------------------------------ */
                    /* SI HAY MARCACION CON TODOS LOS DATOS */
                        if ($marcacion_puertaVerif->marcaMov_fecha != null && $marcacion_puertaVerif->marcaMov_salida != null) {

                            //* entrada porque mi anterior marcacion ya tiene entrrada y salida
                            $tipoMarcacion = 1;
                        } else {

                            //*si no tengo salida
                            if ($marcacion_puertaVerif2) {

                                //*salida
                                $tipoMarcacion = 0;
                            }
                            else{
                                //*entrada->en casa que por la web se crea marcacion con salida
                                $tipoMarcacion = 1;
                            }
                        }
                }
            }
            /* ------------------------------------------------------------------ */
            else { 
               

                /* ------CON HORARIO-------------- */
                //************ARRAY GENERALES**************************************
                //*marcacion cualquiera d empleado de hoy
                $marcacion_puertaVacio = DB::table('marcacion_puerta as mv')
                    ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha1V)
                    ->where('mv.horarioEmp_id','=',$conhorario)
                    ->get();

                //*ultima marcacion de emmpleado
                $marcacion_puertaVerif = DB::table('marcacion_puerta as mv')
                ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha1V)
                ->where('mv.horarioEmp_id','=',$conhorario)
                ->orderby(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), 'ASC')
                ->get()->last();

                //* SI NO TENGO SALIDA
                $marcacion_puertaVerif2 = DB::table('marcacion_puerta as mv')
                ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                ->where('mv.marcaMov_salida', '=', null)
                ->whereDate('mv.marcaMov_fecha', '=', $fecha1V)
                ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                ->where('mv.horarioEmp_id','=',$conhorario)
                ->orderby('marcaMov_fecha', 'ASC')
                ->get()->first();

                /* **********FIN ARRAY GENERALES************** */

               /* VERIFICO SI NO HAY MARCACIONES ANTES */

               if ($marcacion_puertaVacio->isEmpty()) {

                //* entrada porque es la 1ra marcacion
                $tipoMarcacion = 1;
               }

                else {
                    /* YA HAY MARCACIONES PARA ESTE EMPLEADO Y FECHA */
                    /* ------------------------------------------------------ */
                    /* SI HAY MARCACION CON TODOS LOS DATOS */
                        if ($marcacion_puertaVerif->marcaMov_fecha != null && $marcacion_puertaVerif->marcaMov_salida != null) {

                            //* entrada porque mi anterior marcacion ya tiene entrrada y salida
                            $tipoMarcacion = 1;
                        } else {

                            //*si no tengo salida
                            if ($marcacion_puertaVerif2) {

                                //*salida
                                $tipoMarcacion = 0;
                            }
                            else{
                                //*entrada->en casa que por la web se crea marcacion con salida
                                $tipoMarcacion = 1;
                            }
                        }
                }
               
                
               
                /* ---------------FIN DE FUNCION-------------------------- */
            }
           
            /* --------------------------------------------------------------- */
            /* SI ES ENTRADA */
            if ($tipoMarcacion == 1) {
                /* --------CREAMOS NUEVA MARCACION---------------------- */
                $marcacion_puerta = new marcacion_puerta();
                $marcacion_puerta->marcaMov_fecha = $req['fechaMarcacion'];
                $marcacion_puerta->marcaMov_emple_id = $req['idEmpleado'];
                $marcacion_puerta->controladores_idControladores = $req['idControlador'];
                $marcacion_puerta->dispositivoEntrada = $req['idDisposi'];
                $marcacion_puerta->organi_id = $req['organi_id'];
                if (empty($req['activ_id'])) {
                } else {
                    $marcacion_puerta->marcaIdActivi = $req['activ_id'];
                }

                if($conhorario==0){
                    $marcacion_puerta->horarioEmp_id = null;
                   }
                   else{
                   $marcacion_puerta->horarioEmp_id = $conhorario;
                   }
                if (empty($req['latitud'])) {
                } else {
                    $marcacion_puerta->marca_latitud = $req['latitud'];
                }
                if (empty($req['longitud'])) {
                } else {
                    $marcacion_puerta->marca_longitud = $req['longitud'];
                }

                if (empty($req['puntoC_id'])) {
                } else {
                    $marcacion_puerta->puntoC_id = $req['puntoC_id'];
                }
                if (empty($req['centC_id'])) {
                } else {
                    $marcacion_puerta->centC_id = $req['centC_id'];
                }
                $marcacion_puerta->save();
            } else {

                /* SI ES SALIDA */
                /* CONVERTIMOS LA FECHA DE MARCACION EN DATE */
                $fecha1 = Carbon::create($req['fechaMarcacion'])->toDateString();

                /* VERIFICAMOS  PARA EMPAREJAR  */

                if($conhorario==0){
                    $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                    ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->where('mv.marcaMov_salida', '=', null)
                    ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                    ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                    ->whereNull('mv.horarioEmp_id')
                    ->orderby('marcaMov_fecha', 'ASC')
                    ->get()->last();
                }
                else{
                    $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                    ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->where('mv.marcaMov_salida', '=', null)
                    ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                    ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                    ->where('mv.horarioEmp_id','=',$conhorario)
                    ->orderby('marcaMov_fecha', 'ASC')
                    ->get()->last();
                }
                

                if ($marcacion_puerta1) {
                    $marcacion_puerta = marcacion_puerta::find($marcacion_puerta1->marcaMov_id);
                    $marcacion_puerta->marcaMov_salida = $req['fechaMarcacion'];
                    $marcacion_puerta->dispositivoSalida = $req['idDisposi'];
                    $marcacion_puerta->controladores_salida = $req['idControlador'];
                    $marcacion_puerta->save();

                }


            }

           
        }
        //* ************************************************************************************************** */
        if ($marcacion_puerta) {
            return response()->json(array(
                'status' => 200, 'title' => 'Marcacion registrada correctamente',
                'detail' => 'Marcacion registrada correctamente en la base de datos',
            ), 200);
        } else {
            return response()->json(array(
                'status' => 400, 'title' => 'No se pudo registrar marcacion',
                'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos',
            ), 400);
        }
    }
    public function empleadoHorario(Request $request)
    {

        /* RECIBIMOS ID ORGANIZACION */
        $organi_id = $request->organi_id;
        /* ------------------------- */

        /* OBTENEMOS LA FECHA ACTUAL */
        $fecha = Carbon::now();
        /* -------------------------- */

        /* CAMBIAMOS FORMATO DE FECHA */
        $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
        /* ------------------------------------- */

        /* BUSCAMOS EMPLEADOS CON HORARIOS DE ESTA FECHA ACTUAL */
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select(
                'p.perso_nombre',
                'p.perso_apPaterno',
                'p.perso_apMaterno',
                'e.emple_nDoc as dni',
                'e.emple_id as idempleado',
                'h.horaI',
                'h.horaF',
                'h.horario_tolerancia as toleranciaIni',
                'h.horario_toleranciaF as toleranciaFin',
                'he.horarioEmp_id',
                'hd.start as diaActual',
                'he.fuera_horario as trabajafueraHor',
                'he.horarioComp as horarioCompensable',
                'he.horaAdic as horasAdicionales'
            )
            ->join('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('e.organi_id', '=', $organi_id)
            ->where('e.emple_estado', '=', 1)
            ->where('e.asistencia_puerta', '=', 1)
            ->where('hd.start', '=', $fechaHoy)
            ->where('he.estado', '=', 1)
            ->paginate();
        /* -------------------------------------------------------- */

        /* VERIFICAMOS SI EXISTEN */
        if ($empleado != null) {
            return response()->json(array('status' => 200, "empleados" => $empleado));
        } else {
            return response()->json(array(
                'status' => 400, 'title' => 'Empleados no encontrados',
                'detail' => 'No se encontro empleados relacionados con este dispositivo',
            ), 400);
        }
    }

    public function ticketSoporte(Request $request)
    {
        /* OBTENEMOS DATOS DE PARAMETROS RECIBIDOS */
        $idControlador = $request->get('idControlador');
        $tipo = $request->get('tipo');
        $contenido = $request->get('contenido');
        $asunto = $request->get('asunto');
        $celular = $request->get('celular');
        /* ----------------------------------------- */

        /* VERIFICAMOS QUE CONTROLADOR EXISTA */
        $controlador = controladores::findOrFail($idControlador);
        /* ---------------------------------------- */

        /* SI EXISTE EL CONTROLADOR */
        if ($controlador) {
            $controlador = controladores::findOrFail($idControlador);
            $email = "info@rhnube.com.pe";

            /* ENVIAMOS EMAIL DE TIPO SOPORTE */
            if ($tipo == "soporte") {

                Mail::to($email)->queue(new SoporteApiMovil($contenido, $controlador, $asunto, $celular));
                return response()->json("Correo Enviado con éxito", 200);
            }
            /* ---------------------------------------- */

            /* ENVIAMOS EMAIL DE TIPO SUGERENCIA */
            if ($tipo == "sugerencia") {
                Mail::to($email)->queue(new SugerenciaApiMovil($contenido, $controlador, $asunto, $celular));
                return response()->json("Correo Enviado con éxito", 200);
            }
            /* ---------------------------------------- */
        }

        return response()->json("Controlador no se encuentra registrado.", 400);
    }

    //CENTRO COSTO
    public function centroCostos(Request $request)
    {

        /* OBTENEMOS EL ID DE ORGANIZACION */
        $organi_id = $request->organi_id;
        /* ------------------------------- */

        /* OBTENEMOS CENTRO DE COSTOS DE ESTA ORGANIZACION */
        $centroCosto = DB::table('centro_costo as cc')
            ->select(
                'cc.centroC_id',
                'cc.centroC_descripcion',
                'cc.organi_id'
            )
            ->where('cc.organi_id', '=', $organi_id)
            ->where('cc.estado', '=', 1)
            ->where('cc.asistenciaPuerta', '=', 1)
            ->get();
        /* ------------------------------------------------ */

        /* VERIFICAMOS SI EXISTE */
        if ($centroCosto != null) {
            return response()->json(array('status' => 200, "centroCosto" => $centroCosto));
        } else {
            return response()->json(array(
                'status' => 400, 'title' => 'Centros de costos no encontrados',
                'detail' => 'No se encontro centro de costos en esta organizacion',
            ), 400);
        }
    }

    //PUNTO CONTROL
    public function puntoControl(Request $request)
    {

        /* OBTENEMOS EL ID DE ORGANIZACION */
        $organi_id = $request->organi_id;
        /* ------------------------------- */

        /* OBTENEMOS PUNTOS DE CONTROL DE ESTA ORGANIZACION */
        $punto_control = DB::table('punto_control as pc')
            ->select(
                'pc.id',
                'pc.descripcion',
                'pc.codigoControl',
                'pc.verificacion',
                'pc.estado'
            )
            ->where('pc.organi_id', '=', $organi_id)
            ->where('pc.asistenciaPuerta', '=', 1)
            ->where('pc.estado', '=', 1)
            ->get();
        /* ------------------------------------------------- */

        /* recorremos punto de de geo de cada punto de control */
        foreach ($punto_control as $tab) {
            $punto_control_geo = DB::table('punto_control_geo as pcg')
                ->select('pcg.id', 'pcg.latitud', 'pcg.longitud', 'pcg.radio')
                ->where('pcg.idPuntoControl', '=', $tab->id)
                ->distinct('pcg.id')
                ->get();

            /* INSERTAMOS PUNTOS GEO */
            $tab->puntosGeo = $punto_control_geo;
        }

        /* VERIFICAMOS DI EXISTE */
        if ($punto_control != null) {
            return response()->json(array('status' => 200, "puntosControl" => $punto_control));
        } else {
            return response()->json(array(
                'status' => 400, 'title' => 'puntos de control no encontrados',
                'detail' => 'No se encontro puntos de control en esta organizacion',
            ), 400);
        }
    }

    // * MARCACION PUERTA
    public function registroMarcaciones(Request $request)
    {
        // : VALIDACIONES DE MARCACIONES EN BACKEND
        foreach ($request->all() as $key => $atributo) {
            $errores = [];
            $validacion = Validator::make($atributo, [
                'tipoMarcacion' => 'required',
                'fechaMarcacion' => 'required',
                'idEmpleado' => 'required',
                'idControlador' => 'required',
                'idDisposi' => 'required',
                'organi_id' => 'required',
            ], [
                'required' => ':attribute es obligatorio',
            ]);
            if ($validacion->fails()) {
                //: ARRAY DE ERRORES
                if (isset($validacion->failed()["tipoMarcacion"])) {
                    array_push($errores, array("campo" => "tipoMarcacion", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["fechaMarcacion"])) {
                    array_push($errores, array("campo" => "fechaMarcacion", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["idEmpleado"])) {
                    array_push($errores, array("campo" => "idEmpleado", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["idControlador"])) {
                    array_push($errores, array("campo" => "idControlador", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["idDisposi"])) {
                    array_push($errores, array("campo" => "idDisposi", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["organi_id"])) {
                    array_push($errores, array("campo" => "organi_id", "mensaje" => "Es obligatorio"));
                }
                return response()->json(array("errores" => $errores), 400);
            }
        }
        // : OBTENIENDO DATA
        $arrayDatos = [];
        foreach ($request->all() as $dato) {
            // : OBTENEMOS PARAMETROS
            $tipoMarcacion = $dato['tipoMarcacion'];
            $fechaMarcacion = $dato['fechaMarcacion'];
            $idEmpleado = $dato['idEmpleado'];
            $idControlador = $dato['idControlador'];
            $idDispositivo = $dato['idDisposi'];
            $organi_id = $dato['organi_id'];
            $idActividad = empty($dato['activ_id']) == true ? null : $dato['activ_id'];
            $latitud = empty($dato['latitud']) == true ? null : $dato['latitud'];
            $longitud = empty($dato['longitud']) == true ? null : $dato['longitud'];
            $idPuntoControl = empty($dato['puntoC_id']) == true ? null : $dato['puntoC_id'];
            $idCentroCosto = empty($dato['centC_id']) == true ? null : $dato['centC_id'];
            $datos = (object) array(
                "tipoMarcacion" => $tipoMarcacion,
                "fechaMarcacion" => $fechaMarcacion,
                "idEmpleado" => $idEmpleado,
                "idControlador" => $idControlador,
                "idDispositivo" => $idDispositivo,
                "organi_id" => $organi_id,
                "idActividad" => $idActividad,
                "latitud" => $latitud,
                "longitud" => $longitud,
                "idPuntoControl" => $idPuntoControl,
                "idCentroCosto" => $idCentroCosto,
            );
            // : ID EMPLEADO
            if (!isset($arrayDatos[$idEmpleado])) {
                $arrayDatos[$idEmpleado] = (object) array("idEmpleado" => $idEmpleado, "datos" => array());
            }
            // : OBTENER FECHA
            $grupoFecha = Carbon::parse($fechaMarcacion)->isoFormat("YYYY-MM-DD");
            if (!isset($arrayDatos[$idEmpleado]->datos[$grupoFecha])) {
                $arrayDatos[$idEmpleado]->datos[$grupoFecha] = (object) array("fecha" => $grupoFecha, "marcaciones" => array());
            }
            array_push($arrayDatos[$idEmpleado]->datos[$grupoFecha]->marcaciones, $datos);
        }
        $arrayDatos = array_values($arrayDatos);
        // *: RECORREMOS ARRAY
        foreach ($arrayDatos as $key => $data) {
            $arrayDatos[$key]->datos = array_values($arrayDatos[$key]->datos);
            foreach ($data->datos as $item => $dataPorFecha) {
                usort($data->datos[$item]->marcaciones, object_sorter('fechaMarcacion'));
                // : DATOS PARA OBTENER HORARIO EMPLEADO
                $idEmpleado = $data->idEmpleado;
                $fecha = $dataPorFecha->fecha;
                // : HORARIOS EMPLEADOS
                $horarioEmpleado = DB::table('horario_empleado as he')
                    ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->select(
                        'he.horarioEmp_id as idHorarioEmpleado',
                        'he.fuera_horario as fueraHorario',
                        'he.nHoraAdic as horasAdicionales',
                        'h.horaI as horaInicio',
                        'h.horaF as horaFin',
                        'h.horario_tolerancia as toleranciaInicio',
                        'h.horario_toleranciaF as toleranciaFin',
                        'h.horasObliga as horasObligadas'
                    )
                    ->where('he.empleado_emple_id', '=', $idEmpleado)
                    ->where(DB::raw('DATE(hd.start)'), '=', $fecha)
                    ->where('he.estado', '=', 1)
                    ->orderBy('h.horaI', 'ASC')
                    ->get();
                // : RECORRER MARCACIONES
                for ($index = 0; $index < sizeof($dataPorFecha->marcaciones); $index++) {
                    $dataMarcacion = $dataPorFecha->marcaciones[$index];
                    if (sizeof($horarioEmpleado) == 0) {
                        $idHorarioE = 0;
                    } else {
                        $idHorarioE = unirMarcacionConHorarioEmpleado($horarioEmpleado, $fecha, $dataMarcacion->fechaMarcacion);
                    }
                    // if ($dataMarcacion->tipoMarcacion == 1) {

                    // }
                }
            }
        }
        dd($arrayDatos);
    }
}
