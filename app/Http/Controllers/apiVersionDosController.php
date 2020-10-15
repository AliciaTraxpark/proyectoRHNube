<?php

namespace App\Http\Controllers;

use App\actividad;
use App\actividad_empleado;
use App\captura;
use App\captura_imagen;
use App\empleado;
use App\licencia_empleado;
use App\Mail\SoporteApi;
use App\Mail\SugerenciaApi;
use App\organizacion;
use App\persona;
use App\promedio_captura;
use App\vinculacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class apiVersionDosController extends Controller
{

    public function verificacion(Request $request)
    {
        $nroD = $request->get('nroDocumento');
        $codigo = $request->get('codigo');
        $decode = base_convert(intval($codigo), 10, 36);
        $explode = explode("s", $decode);
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
            ->where('emple_nDoc', '=', $nroD)
            ->where('e.organi_id', '=', $explode[0])
            ->where('e.emple_estado', '=', 1)
            ->get()->first();

        $idOrganizacion = $explode[0];

        if ($empleado) {
            $vinculacion = vinculacion::where('id', '=', $explode[1])->get()->first();
            if ($vinculacion) {
                $licencia = licencia_empleado::where('id', '=', $vinculacion->idLicencia)->where('disponible', '!=', 'i')->get()->first();
                if ($licencia) {
                    if ($vinculacion->hash == $request->get('codigo')) {
                        // OBTENER HORAS
                        $fecha = Carbon::now();
                        $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                        $horas = DB::table('empleado as e')
                            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                            ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
                            ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
                            ->select(
                                DB::raw('TIME_FORMAT(SEC_TO_TIME(SUM(promedio.tiempo_rango)), "%H:%i:%s") as Total_Envio')
                            )
                            ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '=', $fechaHoy)
                            ->where('e.emple_id', '=', $empleado->emple_id)
                            ->get()
                            ->first();
                        // *****************
                        if ($vinculacion->serieDisco ==  null) {
                            $vinculacion->pc_mac = $request->get('pc_mac');
                            $vinculacion->serieDisco = $request->get('serieD');
                            $vinculacion->save();
                            $factory = JWTFactory::customClaims([
                                'sub' => env('API_id'),
                            ]);
                            $payload = $factory->make();
                            $token = JWTAuth::encode($payload);
                            $organizacion = organizacion::where('organi_id', '=', $idOrganizacion)->get()->first();
                            return response()->json(array(
                                "corte" => $organizacion->corteCaptura, "idEmpleado" => $empleado->emple_id, "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
                                'idUser' => $idOrganizacion, 'tiempo' => $horas->Total_Envio == null ? "00:00:00" : $horas->Total_Envio, 'token' => $token->get()
                            ), 200);
                        } else {
                            if ($vinculacion->serieDisco == $request->get('serieD')) {
                                $vinculacion->pc_mac = $request->get('pc_mac');
                                $vinculacion->save();
                                $factory = JWTFactory::customClaims([
                                    'sub' => env('API_id'),
                                ]);
                                $payload = $factory->make();
                                $token = JWTAuth::encode($payload);
                                $organizacion = organizacion::where('organi_id', '=', $idOrganizacion)->get()->first();
                                return response()->json(array(
                                    "corte" => $organizacion->corteCaptura, "idEmpleado" => $empleado->emple_id, "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
                                    'idUser' => $idOrganizacion, 'tiempo' => $horas->Total_Envio == null ? "00:00:00" : $horas->Total_Envio, 'token' => $token->get()
                                ), 200);
                            } else {
                                return response()->json("disco_erroneo", 400);
                            }
                        }
                    }
                    return response()->json("codigo_erroneo", 400);
                }
                return response()->json("licencia_de_baja", 400);
            }
            return response()->json("sin_dispositivo", 400);
        }
        return response()->json("empleado_no_exite", 400);
    }

    function selectActividad(Request $request)
    {
        $empleado = $request->get('emple_id');
        $respuesta = [];
        $actividad_empleado = actividad_empleado::where('idEmpleado', '=', $empleado)->get();
        foreach ($actividad_empleado as $act) {
            $actividad = DB::table('actividad as a')
                ->select('a.Activi_id','a.Activi_Nombre', 'a.estado')
                ->where('a.Activi_id', '=', $act->id)
                ->get()
                ->first();
            $actividad->empleado_emple_id = $act->idEmpleado;
            array_push($respuesta, $actividad);
        }
        return response()->json($respuesta, 200);
    }

    function actividad(Request $request)
    {
        $cambio = $request->get('cambio');
        if ($cambio == 'n') {
            $actividad = new actividad();
            $actividad->Activi_Nombre = $request['Activi_Nombre'];
            $actividad->empleado_emple_id = $request['emple_id'];
            $actividad->save();
        }
        if ($cambio == 'm') {
            $id = $request['Activi_id'];
            $actividad = actividad::where('id', $id)->first();
            if ($actividad) {
                $actividad->Activi_Nombre = $request['Activi_Nombre'];
                $actividad->save();
            }
        }
        // if ($cambio == 'e') {
        //     $actividad = actividad_empleado::where('id', $request->get('idActividad'))->first();
        //     if ($actividad) {
        //         $actividad->estado = 0;
        //         $actividad->save();
        //     }
        // }
        return response()->json($actividad, 200);
    }


    public function captura(Request $request)
    {
        function carpetaImg($miniatura, $idEmpleado, $horaI, $nombre)
        {
            $orgCarpeta = DB::table('empleado as e')
                ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                ->select('o.organi_id', 'o.organi_ruc')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get()->first();
            $codigoHashO = $orgCarpeta->organi_id . "s" . $orgCarpeta->organi_ruc;
            $encodeO = intval($codigoHashO, 36);
            $empCarpeta = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->select('p.perso_id')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get()
                ->first();
            $codigoHashE = $idEmpleado . "s" . $empCarpeta->perso_id;
            $encodeE = intval($codigoHashE, 36);
            $fechaC = Carbon::parse($horaI)->isoFormat('YYYYMMDD');
            // dd($fechaC);
            if (!file_exists(app_path() . '/images/' . $encodeO . '/' . $encodeE . '/' . $fechaC . '/' . $nombre)) {
                File::makeDirectory(app_path() . '/images/' . $encodeO . '/' . $encodeE . '/' . $fechaC . '/' . $nombre, $mode = 0777, true, true);
            }
            $data = $miniatura;
            $data = str_replace('data:image/png;base64,', '', $data);
            $data = str_replace(' ', '+', $data);
            $path = app_path();
            $image = base64_decode($data);
            $fileName =  '/images/' . $encodeO . '/' . $encodeE . '/' . $fechaC . '/' . $nombre . '/' . uniqid() . '.jpeg';
            $success = file_put_contents($path . $fileName, $image);

            return $fileName;
        }
        $capturaBuscar = captura::where("idEmpleado", "=", $request->get('idEmpleado'))
            ->where('hora_ini', '=', $request->get('hora_ini'))
            ->where('actividad', '=', $request->get('actividad'))
            ->get()
            ->first();

        if ($capturaBuscar) {
            $nombreM = carpetaImg($request->get('miniatura'), $request->get('idEmpleado'), $request->get('hora_ini'), 'miniatura');
            $nombreI = carpetaImg($request->get('imagen'), $request->get('idEmpleado'), $request->get('hora_ini'), 'captura');
            $captura_imagen = new captura_imagen();
            $captura_imagen->idCaptura = $capturaBuscar->idCaptura;
            // $captura_imagen->miniatura = $request->get('miniatura');
            // $captura_imagen->imagen = $request->get('imagen');
            $captura_imagen->miniatura = $nombreM;
            $captura_imagen->imagen = $nombreI;
            $captura_imagen->save();
            return response()->json($capturaBuscar, 200);
        } else {
            $nombreM = carpetaImg($request->get('miniatura'), $request->get('idEmpleado'), $request->get('hora_ini'), 'miniatura');
            $nombreI = carpetaImg($request->get('imagen'), $request->get('idEmpleado'), $request->get('hora_ini'), 'captura');
            $captura = new captura();
            $captura->estado = $request->get('estado');
            $captura->actividad = $request->get('actividad');
            $captura->hora_ini = $request->get('hora_ini');
            $captura->hora_fin = $request->get('hora_fin');
            $captura->ultimo_acumulado = $request->get('ultimo_acumulado');
            $captura->acumulador = $request->get('acumulador');
            $captura->idHorario_dias = $request->get('idHorario_dias');
            $captura->idActividad = $request->get('idActividad');
            $captura->idEmpleado = $request->get('idEmpleado');
            $captura->save();

            $idCaptura = $captura->idCaptura;

            $captura_imagen = new captura_imagen();
            $captura_imagen->idCaptura = $idCaptura;
            // $captura_imagen->miniatura = $request->get('miniatura');
            // $captura_imagen->imagen = $request->get('imagen');
            $captura_imagen->miniatura = $nombreM;
            $captura_imagen->imagen = $nombreI;
            $captura_imagen->save();

            $idHorario = $captura->idHorario_dias;

            //  PROMEDIO CAPTURA
            $capturaRegistrada = captura::where('idCaptura', '=', $idCaptura)->get()->first();
            $idHorario_dias = $idHorario;
            //RESTA POR FECHA HORA DE   CAPTURAS
            // $fecha = Carbon::create($capturaRegistrada->hora_ini)->format('H:i:s');
            // $explo = explode(":", $fecha);
            // $calSegund = $explo[0] * 3600 + $explo[1] * 60 + $explo[2];
            // $fecha1 = Carbon::create($capturaRegistrada->hora_fin)->format('H:i:s');
            // $explo1 = explode(":", $fecha1);
            // $calSegund1 = $explo1[0] * 3600 + $explo1[1] * 60 + $explo1[2];
            // $totalP = $calSegund1 - $calSegund;
            $fecha = Carbon::parse($capturaRegistrada->hora_ini);
            $fecha1 = Carbon::parse($capturaRegistrada->hora_fin);
            $totalP = $fecha1->diffInSeconds($fecha);
            // ACTIVIDAD DE CAPTURA
            $activ = $capturaRegistrada->actividad;
            //VALIDACION DE CERO
            if ($totalP == 0) {
                $round = 0;
            } else {
                //PROMEDIO
                $promedio = floatval($activ / $totalP);
                $promedioFinal = $promedio * 100;
                $round = round($promedioFinal, 2);
            }
            $promedio_captura = new promedio_captura();
            $promedio_captura->idCaptura = $idCaptura;
            $promedio_captura->idHorario = $idHorario_dias;
            $promedio_captura->promedio = $round;
            $promedio_captura->tiempo_rango = $totalP;
            $promedio_captura->save();
            return response()->json($captura, 200);
        }
    }

    public function capturaArray(Request $request)
    {
        function carpetaImgA($miniatura, $idEmpleado, $horaI, $nombre)
        {
            $orgCarpeta = DB::table('empleado as e')
                ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                ->select('o.organi_id', 'o.organi_ruc')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get()->first();
            $codigoHashO = $orgCarpeta->organi_id . "s" . $orgCarpeta->organi_ruc;
            $encodeO = intval($codigoHashO, 36);
            $empCarpeta = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->select('p.perso_id')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get()
                ->first();
            $codigoHashE = $idEmpleado . "s" . $empCarpeta->perso_id;
            $encodeE = intval($codigoHashE, 36);
            $fechaC = Carbon::parse($horaI)->isoFormat('YYYYMMDD');
            // dd($fechaC);
            if (!file_exists(app_path() . '/images/' . $encodeO . '/' . $encodeE . '/' . $fechaC . '/' . $nombre)) {
                File::makeDirectory(app_path() . '/images/' . $encodeO . '/' . $encodeE . '/' . $fechaC . '/' . $nombre, $mode = 0777, true, true);
            }
            $data = $miniatura;
            $data = str_replace('data:image/png;base64,', '', $data);
            $data = str_replace(' ', '+', $data);
            $path = app_path();
            $image = base64_decode($data);
            $fileName =  '/images/' . $encodeO . '/' . $encodeE . '/' . $fechaC . '/' . $nombre . '/' . uniqid() . '.jpeg';
            $success = file_put_contents($path . $fileName, $image);

            return $fileName;
        }
        foreach ($request->all() as $key => $value) {
            $capturaBuscar = captura::where("idEmpleado", "=", $value['idEmpleado'])
                ->where('hora_ini', '=', $value['hora_ini'])
                ->where('actividad', '=', $value['actividad'])
                ->get()
                ->first();
            if ($capturaBuscar) {
                $nombreM = carpetaImgA($value['miniatura'], $value['idEmpleado'], $value['hora_ini'], 'miniatura');
                $nombreI = carpetaImgA($value['imagen'], $value['idEmpleado'], $value['hora_ini'], 'captura');
                $captura_imagen = new captura_imagen();
                $captura_imagen->idCaptura = $capturaBuscar->idCaptura;
                // $captura_imagen->miniatura = $value['miniatura'];
                // $captura_imagen->imagen = $value['imagen'];
                $captura_imagen->miniatura = $nombreM;
                $captura_imagen->imagen = $nombreI;
                $captura_imagen->save();
            } else {
                $nombreM = carpetaImgA($value['miniatura'], $value['idEmpleado'], $value['hora_ini'], 'miniatura');
                $nombreI = carpetaImgA($value['imagen'], $value['idEmpleado'], $value['hora_ini'], 'captura');
                $captura = new captura();
                $captura->estado = $value['estado'];
                $captura->actividad = $value['actividad'];
                $captura->hora_ini = $value['hora_ini'];
                $captura->hora_fin = $value['hora_fin'];
                $captura->ultimo_acumulado = $value['ultimo_acumulado'];
                $captura->acumulador = $value['acumulador'];
                $captura->idHorario_dias = $value['idHorario_dias'];
                $captura->idActividad = $value['idActividad'];
                $captura->idEmpleado = $value['idEmpleado'];
                $captura->save();

                $idCaptura = $captura->idCaptura;
                $idHorario = $captura->idHorario_dias;

                //CAPTURA_IMAGEN
                $captura_imagen = new captura_imagen();
                $captura_imagen->idCaptura = $idCaptura;
                // $captura_imagen->miniatura = $value['miniatura'];
                // $captura_imagen->imagen = $value['imagen'];
                $captura_imagen->miniatura = $nombreM;
                $captura_imagen->imagen = $nombreI;
                $captura_imagen->save();

                //  PROMEDIO CAPTURA
                $capturaRegistrada = captura::where('idCaptura', '=', $idCaptura)->get()->first();
                $idHorario_dias = $idHorario;
                //RESTA POR FECHA HORA DE   CAPTURAS
                $fecha = Carbon::parse($capturaRegistrada->hora_ini);
                $fecha1 = Carbon::parse($capturaRegistrada->hora_fin);
                $totalP = $fecha1->diffInSeconds($fecha);
                // ACTIVIDAD DE CAPTURA
                $activ = $capturaRegistrada->actividad;
                //VALIDACION DE CERO
                if ($totalP == 0) {
                    $round = 0;
                } else {
                    //PROMEDIO
                    $promedio = floatval($activ / $totalP);
                    $promedioFinal = $promedio * 100;
                    $round = round($promedioFinal, 2);
                }
                $promedio_captura = new promedio_captura();
                $promedio_captura->idCaptura = $idCaptura;
                $promedio_captura->idHorario = $idHorario_dias;
                $promedio_captura->promedio = $round;
                $promedio_captura->tiempo_rango = $totalP;
                $promedio_captura->save();
            }
        }
        return response()->json($request->all(), 200);
    }

    public function ticketSoporte(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $tipo = $request->get('tipo');
        $contenido = $request->get('contenido');
        $asunto = $request->get('asunto');
        $celular = $request->get('celular');
        $cont = $request->get('contenido');
        $asunt = $request->get('asunto');
        $cel = $request->get('celular');

        $empleado = empleado::findOrFail($idEmpleado);
        if ($empleado) {
            $persona = persona::findOrFail($empleado->emple_persona);
            $email = $email = env('MAIL_FROM_ADDRESS');

            if ($tipo == "soporte") {

                Mail::to($email)->queue(new SoporteApi($contenido, $persona, $asunto, $celular));
                return response()->json("Correo Enviado con éxito", 200);
            }
            if ($tipo == "sugerencia") {
                Mail::to($email)->queue(new SugerenciaApi($contenido, $persona, $asunto, $celular));
                return response()->json("Correo Enviado con éxito", 200);
            }
        }

        return response()->json("Empleado no se encuentra registrado.", 400);
    }

    public function downloadActualizacion()
    {
        return response()->download(app_path() . "/file/RH box/RHbox.zip");
    }
    public function horario(Request $request)
    {
        $respuesta = [];
        $horario_empleado = DB::table('empleado as e')
            ->where('e.emple_id', '=', $request->get('idEmpleado'))
            ->get()
            ->first();
        if ($horario_empleado) {
            $horario = DB::table('horario_empleado as he')
                ->select('he.horario_dias_id', 'he.horario_horario_id', 'he.horarioComp', 'he.fuera_horario', 'he.horaAdic')
                ->where('he.empleado_emple_id', '=', $request->get('idEmpleado'))
                ->get();

            foreach ($horario as $resp) {
                $horario_dias = DB::table('horario_dias  as hd')
                    ->select(DB::raw('DATE(hd.start) as start'), 'hd.id')
                    ->where('hd.id', '=', $resp->horario_dias_id)
                    ->get()->first();
                $horario = DB::table('horario as h')
                    ->select('h.horario_id', 'h.horario_descripcion', 'h.horaI', 'h.horaF', 'h.horasObliga as horasObligadas', 'h.horario_tolerancia as tolerancia_inicio', 'h.horario_toleranciaF as tolerancia_final')
                    ->where('h.horario_id', '=', $resp->horario_horario_id)
                    ->get()->first();
                $pausas = DB::table('pausas_horario as ph')
                    ->select('ph.pausH_descripcion as decripcion', 'ph.pausH_Inicio as pausaI', 'ph.pausH_Fin as pausaF')
                    ->where('ph.horario_id', '=', $horario->horario_id)
                    ->get();
                $horario->idHorario_dias = $horario_dias->id;
                $horario->horarioCompensable = $resp->horarioComp;
                $horario->fueraHorario = $resp->fuera_horario;
                $horario->horaAdicional = $resp->horaAdic;
                $horario->pausas = $pausas;
                $fecha = Carbon::now();
                $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                if ($horario_dias->start == $fechaHoy) {
                    array_push($respuesta, $horario);
                }
            }
            return response()->json($respuesta, 200);
        }
        return response()->json("Empleado no encontrado", 400);
    }

    public function horarioV2(Request $request)
    {
        $respuesta = [];
        $horario_empleado = DB::table('empleado as e')
            ->where('e.emple_id', '=', $request->get('idEmpleado'))
            ->get()
            ->first();
        if ($horario_empleado) {
            $horario = DB::table('horario_empleado as he')
                ->select('he.horario_dias_id', 'he.horario_horario_id', 'he.horarioComp', 'he.fuera_horario', 'he.horaAdic')
                ->where('he.empleado_emple_id', '=', $request->get('idEmpleado'))
                ->get();

            foreach ($horario as $resp) {
                $horario_dias = DB::table('horario_dias  as hd')
                    ->select(DB::raw('DATE(hd.start) as start'), 'hd.id')
                    ->where('hd.id', '=', $resp->horario_dias_id)
                    ->get()->first();
                $horario = DB::table('horario as h')
                    ->select('h.horario_id', 'h.horario_descripcion', 'h.horaI', 'h.horaF', 'h.horasObliga as horasObligadas', 'h.horario_tolerancia as tolerancia_inicio', 'h.horario_toleranciaF as tolerancia_final')
                    ->where('h.horario_id', '=', $resp->horario_horario_id)
                    ->get()->first();
                $pausas = DB::table('pausas_horario as ph')
                    ->select('ph.pausH_Inicio as pausaI', 'ph.pausH_Fin as pausaF')
                    ->where('ph.horario_id', '=', $horario->horario_id)
                    ->get();
                $horario->horaI = $horario_dias->start . " " . $horario->horaI;
                $horario->horaF = $horario_dias->start . " " . $horario->horaF;
                $horario->idHorario_dias = $horario_dias->id;
                $horario->horarioCompensable = $resp->horarioComp;
                $horario->fueraHorario = $resp->fuera_horario;
                $horario->horaAdicional = $resp->horaAdic;
                $horario->pausas = $pausas;
                $fecha = Carbon::now();
                $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                if ($horario_dias->start == $fechaHoy) {
                    array_push($respuesta, $horario);
                }
            }
            return response()->json($respuesta, 200);
        }
        return response()->json("Empleado no encontrado", 400);
    }

    public function logoutToken(Request $request)
    {
        $token = $request->header('Authorization');
        try {
            JWTAuth::setToken($token)->invalidate(); // setToken and check
            return response()->json(array('message' => 'token_logout'), 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(array('message' => 'token_expired'), 404);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(array('message' => 'token_invalid'), 404);
        }
    }
}
