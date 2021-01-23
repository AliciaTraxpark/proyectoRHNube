<?php

namespace App\Http\Controllers;

use App\controladores_tareo;
use App\dispositivos_tareo;
use App\dispoTareo_nombres;
use App\Mail\SoporteApiTareo;
use App\Mail\SugerenciaApiTareo;
use App\marcacion_tareo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class apimarcacionTareoController extends Controller
{
    //
    //ACTIVACION DISPOSITIVO
    public function apiActivacion(Request $request)
    {

        /* OBTENEMOS PARAMETROS */
        $nroMovil = $request->nroMovil;
        $codigo = $request->codigo;
        $nombreDisp = $request->nombreDisp;
        /* -------------------------- */

        /* VERIFICACOM SI DATOS COINCIDEN CON DISPOSTIVO REGISTRADO */
        $dispositivo = dispositivos_tareo::where('dispoT_movil', '=', $nroMovil)
            ->where('dispoT_codigo', '=', $codigo)->where('dispoT_estadoActivo', '=', [1, 2])->get()->first();
        /* -------------------------------------------------------- */

        /* SI EL DISPOSITIVO EXISTE */
        if ($dispositivo != null) {

            /* SI EL DISPOSITO YA AH SIDO PREVIAMENTE CONFIRMADO */
            if ($dispositivo->dispoT_estado == 2) {

                /* SI LA CLAVE ES LA CORRECTA */
                if ($dispositivo->dispoT_codigo == $codigo) {

                    /* CREAMOS TOKEN */
                    $factory = JWTFactory::customClaims([
                        'sub' => env('API_id'),
                    ]);
                    $payload = $factory->make();
                    $token = JWTAuth::encode($payload);
                    /* ----------------- */

                    /* VERIFICAMOS SI YA ESTA REGISTRADO EL NOMBRE/IMEI DE DISPOSITIVO */
                    $nombreDs = DB::table('dispoTareo_nombres')
                        ->where('iddispositivos_tareo', '=', $dispositivo->iddispositivos_tareo)
                        ->where('dispoT_CodigoNombre', '=', $nombreDisp)
                        ->get();

                    /* SI NO ESTA REGISTRADO  */
                    if ($nombreDs->isEmpty()) {

                        /* REGISTRAMOS EN LA BD */
                        $dispo_nombre = new dispoTareo_nombres();
                        $dispo_nombre->dispoT_CodigoNombre = $nombreDisp;
                        $dispo_nombre->iddispositivos_tareo = $dispositivo->iddispositivos_tareo;
                        $dispo_nombre->save();
                        return response()->json(array('status' => 200, "dispositivo" => $dispositivo,
                            "disponombre" => $dispo_nombre, "token" => $token->get()));

                    } else {
                        return response()->json(array('status' => 200, "dispositivo" => $dispositivo,
                            "disponombre" => $nombreDs, "token" => $token->get()));
                    }

                } else {
                    /* SI LA CLAVE ES INCORRECTA */
                    return response()->json(array('status' => 400, 'title' => 'Clave incorrecta',
                        'detail' => 'Asegúrate de escribir la clave correcta'), 400);
                }

            } else {
                /* SI NO ESTA CONFIRMADO Y SOLO ENVIADO SMS */
                if ($dispositivo->dispoT_estado == 1) {

                    /* ACTUALIZAMOS EL ESTADO DE DISPOSITIVO */
                    $dispositivosAc = dispositivos_tareo::findOrFail($dispositivo->iddispositivos_tareo);
                    $dispositivosAc->dispoT_estado = 2;
                    $dispositivosAc->save();
                    /* ------------------------------------ */

                    /* CREAMOS NUEVA ASOCIACION CON NOMBRE */
                    $dispo_nombre = new dispoTareo_nombres();
                    $dispo_nombre->dispoT_CodigoNombre = $nombreDisp;
                    $dispo_nombre->iddispositivos_tareo = $dispositivosAc->iddispositivos_tareo;
                    $dispo_nombre->save();
                    /* ------------------------------------- */

                    /* CREAMOS TOKEN */
                    $factory = JWTFactory::customClaims([
                        'sub' => env('API_id'),
                    ]);
                    $payload = $factory->make();
                    $token = JWTAuth::encode($payload);
                    /* --------------------- */

                    return response()->json(array('status' => 200, "dispositivo" => $dispositivosAc,
                        "disponombre" => $dispo_nombre, "token" => $token->get()));

                }
            }
        } else {
            /* VERIFICACOMO SI TENEMOS AL MENOS EL NUMERO DE DISPOSITOV REGISTRADO */
            $dispositivo1 = dispositivos_tareo::where('dispoT_movil', '=', $nroMovil)
                ->get()->first();
            /* ----------------------------------------------------------- */

            /* SI LO TENEMOS REGISTRADO  */
            if ($dispositivo1 != null) {
                return response()->json(array('status' => 400, 'title' => 'Clave incorrecta o dispositivo no activo',
                    'detail' => 'Asegúrate de escribir la clave correcta'), 400);

            } else {
                /* SI NO LO TENEMOS REGISTRADO */
                return response()->json(array('status' => 400, 'title' => 'Dispositivo no existe',
                    'detail' => 'Asegúrate de registrar el dispositivo desde la plataforma web'), 400);

            }
        }
    }

    //EMPLEADOS
    public function EmpleadoTareo(Request $request)
    {

        /* OBTENEMOS EL ID DE ORGANIZACION */
        $organi_id = $request->organi_id;
        /* ----------------------------------- */

        /* OBTEMOS EMPLEADOS CON ID DE ORGANIZACION RECIBIDA, QUE ESTEN ACTIVOS, Y TENGAS MODO TAREO */
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'e.emple_id')
            ->where('e.organi_id', '=', $organi_id)
            ->where('e.emple_estado', '=', 1)
            ->where('e.modoTareo', '=', 1)
            ->paginate();
        /* --------------------------------------------------------------------------------------------------- */

        /* SI EXISTE EMPLEADOS */
        if ($empleado != null) {
            return response()->json(array('status' => 200, "empleados" => $empleado));
        } else {
            return response()->json(array('status' => 400, 'title' => 'Empleados no encontrados',
                'detail' => 'No se encontro empleados relacionados con este dispositivo'), 400);
        }

    }

    //ACTIVIDADES
    public function ActivTareo(Request $request)
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
            ->where('a.modoTareo', '=', 1)
            ->get();

        foreach ($actividades as $actividadesSub) {
            $Subactividades = DB::table('actividad_subactividad as asu')
            ->join('subactividad as su','asu.subActividad','=','su.idsubActividad')
                ->select(
                    'asu.Activi_id',
                    'su.idsubActividad',
                    'su.subAct_nombre',
                    'su.subAct_codigo',
                    'su.estado',
                    'su.modoTareo',
                    'su.organi_id'
                )
                ->where('asu.Activi_id', '=',  $actividadesSub->Activi_id)
                ->where('su.estado', '=', 1)
                ->where('su.modoTareo', '=', 1)
                ->groupBy('asu.idactividad_subactividad')
                ->get();

                $actividadesSub->subactividades = $Subactividades;

        }

        /* ---------------------------------------------------------------- */

        /* VERIFICAMOS QUE EXISTAN LAS ACTIVIDADES */
        if ($actividades != null) {
            return response()->json(array('status' => 200, "actividades" => $actividades));
        } else {
            return response()->json(array('status' => 400, 'title' => 'Actividades no encontradas',
                'detail' => 'No se encontro actividades en esta organizacion'), 400);
        }

    }

    //CONTROLADORES
    public function controladoresActTareo(Request $request)
    {

        /* OBTENEMOS PARAMENTROS */
        $organi_id = $request->organi_id;
        $idDispo = $request->idDispo;
        /* ----------------------- */

        /* OBTENEMOS DISPOSITIVOS QUE TENGAN  CONTROLADORES */
        $dispositivo_Controlador = DB::table('dispositivo_controlador_tareo as dc')
            ->join('controladores_tareo as con', 'dc.idcontroladores_tareo', '=', 'con.idcontroladores_tareo')
            ->join('dispositivos_tareo as dis', 'dc.iddispositivos_tareo', '=', 'dis.iddispositivos_tareo')
            ->select('con.idcontroladores_tareo', 'con.contrT_codigo', 'con.contrT_nombres',
                'con.contrT_ApPaterno', 'con.contrT_ApMaterno',
                'con.contrT_estado')
            ->where('dis.iddispositivos_tareo', '=', $idDispo)
            ->where('dis.organi_id', '=', $organi_id)
            ->where('con.contrT_estado', '=', 1)
            ->get();
        /* ------------------------------------------------------------------ */

        /* VERIFIACMOS SI EXISTEN */
        if ($dispositivo_Controlador != null) {
            return response()->json(array('status' => 200, "controladores" => $dispositivo_Controlador));
        } else {
            return response()->json(array('status' => 400, 'title' => 'Controladores no encontrados',
                'detail' => 'No se encontro controladores relacionados con este dispositivo'), 400);
        }

    }

    /* MARCACIONES EN PUERTA  */
    public function marcacionTareo(Request $request)
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
            if (empty($req['idsubActividad'])) {
                $idsubActividad = null;
            } else {
                $idsubActividad = $req['idsubActividad'];
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
            $datos = ['tipoMarcacion' => $tipoMarcacion, 'fechaMarcacion' => $fechaMarcacion,
                'idEmpleado' => $idEmpleado, 'idControlador' => $idControlador,
                'idDisposi' => $idDisposi, 'organi_id' => $organi_id, 'activ_id' => $activ_id,'idsubActividad' => $idsubActividad,
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
                $marcacion_tareo = new marcacion_tareo();
                $marcacion_tareo->marcaTareo_entrada = $req['fechaMarcacion'];
                $marcacion_tareo->marcaTareo_idempleado = $req['idEmpleado'];
                $marcacion_tareo->idcontroladores_tareo = $req['idControlador'];
                $marcacion_tareo->iddispositivos_tareo = $req['idDisposi'];
                $marcacion_tareo->organi_id = $req['organi_id'];
                if (empty($req['activ_id'])) {} else {
                    $marcacion_tareo->Activi_id = $req['activ_id'];
                }
                if (empty($req['idsubActividad'])) {} else {
                    $marcacion_tareo->idsubActividad = $req['idsubActividad'];
                }

                if (empty($req['idHoraEmp'])) {} else {
                    $marcacion_tareo->horarioEmp_id = $req['idHoraEmp'];
                }
                if (empty($req['latitud'])) {} else {
                    $marcacion_tareo->marcaTareo_latitud = $req['latitud'];
                }
                if (empty($req['longitud'])) {} else {
                    $marcacion_tareo->marcaTareo_longitud = $req['longitud'];
                }

                if (empty($req['puntoC_id'])) {} else {
                    $marcacion_tareo->puntoC_id = $req['puntoC_id'];
                }
                if (empty($req['centC_id'])) {} else {
                    $marcacion_tareo->centroC_id = $req['centC_id'];
                }
                $marcacion_tareo->save();
            } else {

                /* OBTENEMOS LA FECHA EN FORMATO DATE */
                $fecha1 = Carbon::create($req['fechaMarcacion'])->toDateString();

                /* VERIFICAMOS SI EXISTE OTRA MARCACION CON EL MISMO DIA Y EMPLEADO */
                $marcacion_tareo00 = DB::table('marcacion_tareo as mt')
                    ->where('mt.marcaTareo_idempleado', '=', $req['idEmpleado'])
                    ->where('mt.marcaTareo_salida', '!=', null)
                    ->where('mt.marcaTareo_entrada', '!=', null)
                    ->whereDate('mt.marcaTareo_entrada', '=', $fecha1)
                    ->where('mt.idcontroladores_tareo', '=', $req['idControlador'])
                    ->where('mt.iddispositivos_tareo', '=', $req['idDisposi'])
                    ->orderby('marcaTareo_entrada', 'ASC')
                    ->get()->last();
                /* ---------------------------------------------------------------- */

                /* SI EXISTE MARCACION ANTERIOR */
                if ($marcacion_tareo00) {
                    /*  SI LA MARCACION ANTERIOR LA ENTRADA ES MAYOR QUE LA SALIDA QUE RECIBO */
                    if ($marcacion_tareo00->marcaTareo_entrada > $req['fechaMarcacion']) {

                        /* VERIFICAMOS SI EXISTE MARCACION SIN SALIDA */
                        $marcacion_tareo1 = DB::table('marcacion_tareo as mt')
                            ->where('mt.marcaTareo_idempleado', '=', $req['idEmpleado'])
                            ->where('mt.marcaTareo_salida', '=', null)
                            ->whereDate('mt.marcaTareo_entrada', '=', $fecha1)
                            ->where('mt.marcaTareo_entrada', '<=', $req['fechaMarcacion'])
                            ->where('mt.idcontroladores_tareo', '=', $req['idControlador'])
                            ->where('mt.iddispositivos_tareo', '=', $req['idDisposi'])
                            ->orderby('marcaTareo_entrada', 'ASC')
                            ->get()->first();
                    } else {
                        $marcacion_tareo1 = [];
                        $marcacion_tareo1 == null;
                    }

                } else {
                    $marcacion_tareo1 = DB::table('marcacion_tareo as mt')
                        ->where('mt.marcaTareo_idempleado', '=', $req['idEmpleado'])
                        ->where('mt.marcaTareo_salida', '=', null)
                        ->whereDate('mt.marcaTareo_entrada', '=', $fecha1)
                        ->where('mt.marcaTareo_entrada', '<=', $req['fechaMarcacion'])
                        ->where('mt.idcontroladores_tareo', '=', $req['idControlador'])
                        ->where('mt.iddispositivos_tareo', '=', $req['idDisposi'])
                        ->orderby('marcaTareo_entrada', 'ASC')
                        ->get()->last();
                }

                /* SI NO EXISTE MARCACION SIN SALIDA */
                if ($marcacion_tareo1 == null) {
                    /* creamos nueva marcacion */
                    $marcacion_tareo = new marcacion_tareo();
                    $marcacion_tareo->marcaTareo_salida = $req['fechaMarcacion'];
                    $marcacion_tareo->marcaTareo_idempleado = $req['idEmpleado'];
                    $marcacion_tareo->idcontroladores_tareo = $req['idControlador'];
                    $marcacion_tareo->iddispositivos_tareo = $req['idDisposi'];
                    $marcacion_tareo->organi_id = $req['organi_id'];
                    if (empty($req['activ_id'])) {} else {
                        /*  $marcacion_tareo->marcaIdActivi=$req['activ_id']; */
                    }
                    if (empty($req['idsubActividad'])) {} else {
                       /*  $marcacion_tareo->idsubActividad = $req['idsubActividad']; */
                    }

                    if (empty($req['idHoraEmp'])) {} else {
                        $marcacion_tareo->horarioEmp_id = $req['idHoraEmp'];
                    }
                    if (empty($req['latitud'])) {} else {
                        $marcacion_tareo->marcaTareo_latitud = $req['latitud'];
                    }
                    if (empty($req['longitud'])) {} else {
                        $marcacion_tareo->marcaTareo_longitud = $req['longitud'];
                    }
                    if (empty($req['puntoC_id'])) {} else {

                    }
                    if (empty($req['centC_id'])) {} else {

                    }
                    $marcacion_tareo->save();
                } else {

                    /* EMPAREJAMOS CON LA MARCACION SIN SALIDA QUE ENCONTRAMOS */
                    $marcacion_tareo = marcacion_tareo::find($marcacion_tareo1->idmarcaciones_tareo);
                    $marcacion_tareo->marcaTareo_salida = $req['fechaMarcacion'];
                    $marcacion_tareo->save();
                }
            }

        }

        if ($marcacion_tareo) {
            return response()->json(array('status' => 200, 'title' => 'Marcacion registrada correctamente',
                'detail' => 'Marcacion registrada correctamente en la base de datos'), 200);
        } else {
            return response()->json(array('status' => 400, 'title' => 'No se pudo registrar marcacion',
                'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 400);
        }

    }

    /* HORARIOS DEL DIA DE EMPELADO  */
    public function empleadoHorarioTareo(Request $request)
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
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc as dni',
                'e.emple_id as idempleado', 'h.horaI', 'h.horaF', 'h.horario_tolerancia as toleranciaIni',
                'h.horario_toleranciaF as toleranciaFin', 'he.horarioEmp_id', 'hd.start as diaActual',
                'he.fuera_horario as trabajafueraHor', 'he.horarioComp as horarioCompensable', 'he.horaAdic as horasAdicionales')
            ->join('horario_empleado as he', 'e.emple_id', '=', 'he.empleado_emple_id')
            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
            ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
            ->where('e.organi_id', '=', $organi_id)
            ->where('e.emple_estado', '=', 1)
            ->where('e.modoTareo', '=', 1)
            ->where('hd.start', '=', $fechaHoy)
            ->where('he.estado', '=', 1)
            ->paginate();
        /* -------------------------------------------------------- */

        /* VERIFICAMOS SI EXISTEN */
        if ($empleado != null) {
            return response()->json(array('status' => 200, "empleados" => $empleado));
        } else {
            return response()->json(array('status' => 400, 'title' => 'Empleados no encontrados',
                'detail' => 'No se encontro empleados relacionados con este dispositivo'), 400);
        }

    }

    /* TICKET SOPORTE */
    public function ticketSoporteTareo(Request $request)
    {
        /* OBTENEMOS DATOS DE PARAMETROS RECIBIDOS */
        $idControlador = $request->get('idControlador');
        $tipo = $request->get('tipo');
        $contenido = $request->get('contenido');
        $asunto = $request->get('asunto');
        $celular = $request->get('celular');
        /* ----------------------------------------- */

        /* VERIFICAMOS QUE CONTROLADOR EXISTA */
        $controlador = controladores_tareo::findOrFail($idControlador);
        /* ---------------------------------------- */

        /* SI EXISTE EL CONTROLADOR */
        if ($controlador) {
            $controlador = controladores_tareo::findOrFail($idControlador);
            $email = "info@rhnube.com.pe";

            /* ENVIAMOS EMAIL DE TIPO SOPORTE */
            if ($tipo == "soporte") {

                Mail::to($email)->queue(new SoporteApiTareo($contenido, $controlador, $asunto, $celular));
                return response()->json("Correo Enviado con éxito", 200);
            }
            /* ---------------------------------------- */

            /* ENVIAMOS EMAIL DE TIPO SUGERENCIA */
            if ($tipo == "sugerencia") {
                Mail::to($email)->queue(new SugerenciaApiTareo($contenido, $controlador, $asunto, $celular));
                return response()->json("Correo Enviado con éxito", 200);
            }
            /* ---------------------------------------- */
        }

        return response()->json("Controlador no se encuentra registrado.", 400);
    }

    //CENTRO COSTO
    public function centroCostosTareo(Request $request)
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
            ->get();
        /* ------------------------------------------------ */

        /* VERIFICAMOS SI EXISTE */
        if ($centroCosto != null) {
            return response()->json(array('status' => 200, "centroCosto" => $centroCosto));
        } else {
            return response()->json(array('status' => 400, 'title' => 'Centros de costos no encontrados',
                'detail' => 'No se encontro centro de costos en esta organizacion'), 400);
        }
    }

    //PUNTO CONTROL
    public function puntoControlTareo(Request $request)
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
            ->where('pc.ModoTareo', '=', 1)
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
            return response()->json(array('status' => 400, 'title' => 'puntos de control no encontrados',
                'detail' => 'No se encontro puntos de control en esta organizacion'), 400);
        }

    }
}
