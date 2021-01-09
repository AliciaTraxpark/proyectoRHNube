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
use App\software_vinculacion;
use App\vinculacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
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
                ->select('a.Activi_id', 'a.Activi_Nombre')
                ->where('a.Activi_id', '=', $act->idActividad)
                ->where('a.controlRemoto', '=', 1)
                ->where('a.estado', '=', 1)
                ->get()
                ->first();
            if ($actividad) {
                $actividad->empleado_emple_id = $act->idEmpleado;
                $actividad->estado = $act->estado;
                array_push($respuesta, $actividad);
            }
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
        return response()->json($actividad, 200);
    }


    public function captura(Request $request)
    {
        //* VALIDACION DE BACKEND
        $validacion = Validator::make($request->all(), [
            'idEmpleado' => 'required',
            'hora_ini' => 'required',
            'hora_fin' => 'required',
            'actividad' => 'required',
            'miniatura' => 'required',
            'imagen' => 'required',
            'idActividad' => 'required'
        ], [
            'required' => ':attribute es obligatorio'
        ]);
        $errores = [];
        //* ARRAYS DE ERRORES
        if ($validacion->fails()) {
            if (isset($validacion->failed()['idEmpleado'])) {
                array_push($errores, array("campo" => 'idEmpleado', "mensaje" => 'idEmpleado es obligatorio'));
            }
            if (isset($validacion->failed()['hora_ini'])) {
                array_push($errores, array("campo" => 'hora_ini', "mensaje" => 'hora_ini es obligatorio'));
            }
            if (isset($validacion->failed()['hora_fin'])) {
                array_push($errores, array("campo" => 'hora_fin', "mensaje" => 'hora_fin es obligatorio'));
            }
            if (isset($validacion->failed()['actividad'])) {
                array_push($errores, array("campo" => 'actividad', "mensaje" => 'actividad es obligatorio'));
            }
            if (isset($validacion->failed()['miniatura'])) {
                array_push($errores, array("campo" => 'miniatura', "mensaje" => 'miniatura es obligatorio'));
            }
            if (isset($validacion->failed()['imagen'])) {
                array_push($errores, array("campo" => 'imagen', "mensaje" => 'imagen es obligatorio'));
            }
            if (isset($validacion->failed()['idActividad'])) {
                array_push($errores, array("campo" => 'idActividad', "mensaje" => 'idActividad es obligatorio'));
            }
            return response()->json(array("errores" => $errores), 404);
        }

        //* FUNCION PARA CREAR CARPETA PARA IMAGENES
        function carpetaImg($miniatura, $idEmpleado, $horaI, $nombre)
        {
            $orgCarpeta = DB::table('empleado as e')
                ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                ->select('o.organi_id', 'o.organi_ruc')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get()->first();
            $codigoHashO = $orgCarpeta->organi_id . $orgCarpeta->organi_ruc;
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
            $captura_imagen->miniatura = $nombreM;
            $captura_imagen->imagen = $nombreI;
            $captura_imagen->save();

            $idHorario = $captura->idHorario_dias;

            //* PROMEDIO CAPTURA
            $capturaRegistrada = captura::where('idCaptura', '=', $idCaptura)->get()->first();
            $idHorario_dias = $idHorario;
            //* RESTA POR FECHA HORA DE CAPTURAS
            $fecha = Carbon::parse($capturaRegistrada->hora_ini);
            $fecha1 = Carbon::parse($capturaRegistrada->hora_fin);
            //* ACTIVIDAD DE CAPTURA
            $activ = $capturaRegistrada->actividad;
            //* VALIDACION DE CERO
            if ($fecha1->gt($fecha)) {
                //* PROMEDIO
                $totalP = $fecha1->diffInSeconds($fecha);
                $promedio = floatval($activ / $totalP);
                $promedioFinal = $promedio * 100;
                $round = round($promedioFinal, 2);
            } else {
                $totalP = 0;
                $round = 0;
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
        $respuesta = []; //: -> ARRAY DE RESPUESTA
        //* VALIDACION DE BACKEND
        foreach ($request->all() as $key => $atributo) {
            $errores = [];
            $validacion = Validator::make($atributo, [
                "idEmpleado" => "required",
                "hora_ini" => "required",
                "hora_fin" => "required",
                "actividad" => "required",
                "miniatura" => "required",
                "imagen" => "required",
                "idActividad" => "required"
            ], [
                "required" => ":attribute es obligatorio"
            ]);

            if ($validacion->fails()) {
                //: ARRAY DE ERRORES
                if (isset($validacion->failed()["idEmpleado"])) {
                    array_push($errores, array("campo" => "idEmpleado", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["hora_ini"])) {
                    array_push($errores, array("campo" => "hora_ini", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["hora_fin"])) {
                    array_push($errores, array("campo" => "hora_fin", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["actividad"])) {
                    array_push($errores, array("campo" => "actividad", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["miniatura"])) {
                    array_push($errores, array("campo" => "miniatura", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["imagen"])) {
                    array_push($errores, array("campo" => "imagen", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["idActividad"])) {
                    array_push($errores, array("campo" => "idActividad", "mensaje" => "Es obligatorio"));
                }

                return response()->json(array("errores" => $errores), 404);
            }
        }
        //* FUNCION PARA CREAR CARPETA PARA IMAGENES
        function carpetaImgA($miniatura, $idEmpleado, $horaI, $nombre)
        {
            $orgCarpeta = DB::table('empleado as e')
                ->join('organizacion as o', 'o.organi_id', '=', 'e.organi_id')
                ->select('o.organi_id', 'o.organi_ruc')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get()->first();
            $codigoHashO = $orgCarpeta->organi_id .  $orgCarpeta->organi_ruc;
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
        // * FOREACH PARA REGISTRAR DATOS
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
                $captura_imagen->miniatura = $nombreM;
                $captura_imagen->imagen = $nombreI;
                $captura_imagen->save();
            } else {
                $nombreM = carpetaImgA($value['miniatura'], $value['idEmpleado'], $value['hora_ini'], 'miniatura');
                $nombreI = carpetaImgA($value['imagen'], $value['idEmpleado'], $value['hora_ini'], 'captura');
                $captura = new captura();
                $captura->actividad = $value['actividad'];
                $captura->hora_ini = $value['hora_ini'];
                $captura->hora_fin = $value['hora_fin'];
                $captura->idHorario_dias = $value['idHorario_dias'];
                $captura->idActividad = $value['idActividad'];
                $captura->idEmpleado = $value['idEmpleado'];
                $captura->save();

                $idCaptura = $captura->idCaptura;
                $idHorario = $captura->idHorario_dias;

                //* CAPTURA_IMAGEN
                $captura_imagen = new captura_imagen();
                $captura_imagen->idCaptura = $idCaptura;
                $captura_imagen->miniatura = $nombreM;
                $captura_imagen->imagen = $nombreI;
                $captura_imagen->save();

                //*  PROMEDIO CAPTURA
                $capturaRegistrada = captura::where('idCaptura', '=', $idCaptura)->get()->first();
                $idHorario_dias = $idHorario;
                //* RESTA POR FECHA HORA DE CAPTURAS
                $fecha = Carbon::parse($capturaRegistrada->hora_ini);
                $fecha1 = Carbon::parse($capturaRegistrada->hora_fin);
                //* ACTIVIDAD DE CAPTURA
                $activ = $capturaRegistrada->actividad;
                //* VALIDACION DE CERO
                if ($fecha1->gt($fecha)) {
                    //* PROMEDIO 
                    $totalP = $fecha1->diffInSeconds($fecha);
                    $promedio = floatval($activ / $totalP);
                    $promedioFinal = $promedio * 100;
                    $round = round($promedioFinal, 2);
                } else {
                    $totalP = 0;
                    $round = 0;
                }
                $promedio_captura = new promedio_captura();
                $promedio_captura->idCaptura = $idCaptura;
                $promedio_captura->idHorario = $idHorario_dias;
                $promedio_captura->promedio = $round;
                $promedio_captura->tiempo_rango = $totalP;
                $promedio_captura->save();
            }
            $fechaS = Carbon::now('America/Lima');
            $horaActual = $fechaS->isoFormat('YYYY-MM-DD HH:mm:ss');
            $arrayRespuesta = array(
                "hora_fin" => $value['hora_fin'],
                "hora_servidor" => $horaActual,
                "mensaje" => "registro exitoso"
            );
            array_push($respuesta, $arrayRespuesta);
        }

        return response()->json($respuesta, 200);
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
            $email = "info@rhnube.com.pe";

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
                $segundos = Carbon::createFromTimestampUTC($horario->horasObligadas)->secondsSinceMidnight();
                $horario->horasObligadas = $segundos;
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
                ->select('he.horario_dias_id', 'he.horario_horario_id', 'he.horarioComp', 'he.fuera_horario', 'he.horaAdic', 'he.nHoraAdic')
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
                    ->select('ph.idpausas_horario', 'ph.pausH_descripcion as decripcion', 'ph.pausH_Inicio as pausaI', 'ph.pausH_Fin as pausaF')
                    ->where('ph.horario_id', '=', $horario->horario_id)
                    ->get();
                $horario->idHorario_dias = $horario_dias->id;
                $horario->horarioCompensable = $resp->horarioComp;
                $horario->fueraHorario = $resp->fuera_horario;
                $horario->horaAdicional = $resp->horaAdic;
                $horario->numeroHoraAdicional = $resp->nHoraAdic == null ? 0 : $resp->nHoraAdic;
                $horario->pausas = $pausas;
                $fecha = Carbon::now();
                $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                $horaActual = $fecha->isoFormat('HH:mm:ss');
                $horaComparar = Carbon::parse($horario->horaF)->addMinutes($horario->tolerancia_final);
                if ($horario_dias->start == $fechaHoy) {
                    $fechaC = "";
                    if (Carbon::parse($horario->horaF)->lt(Carbon::parse($horario->horaI))) {
                        $despues = new Carbon('tomorrow');
                        $fechaMan = $despues->isoFormat('YYYY-MM-DD');
                        $horario->horaI = $fechaHoy . " " . $horario->horaI;
                        $horario->horaF = $fechaMan . " " . $horario->horaF;
                    } else {
                        $horario->horaI = $fechaHoy . " " . $horario->horaI;
                        $horario->horaF = $fechaHoy . " " . $horario->horaF;
                    }
                    $horaComparar = Carbon::parse($horario->horaF)->addMinutes($horario->tolerancia_final);
                    if (Carbon::parse($horaActual)->lte($horaComparar)) {
                        //* CALCULAR TIEMPO POR HORARIO
                        $horas = DB::table('empleado as e')
                            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                            ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
                            ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
                            ->select(
                                DB::raw('TIME_FORMAT(SEC_TO_TIME(SUM(promedio.tiempo_rango)), "%H:%i:%s") as Total_Envio')
                            )
                            ->where(DB::raw('DATE(h.start)'), '=', $fechaHoy)
                            ->where('e.emple_id', '=', $request->get('idEmpleado'))
                            ->get()
                            ->first();
                        $horario->tiempo = $horas->Total_Envio == null ? "00:00:00" : $horas->Total_Envio;
                        array_push($respuesta, $horario);
                    }
                } else {
                    if (Carbon::parse($horario->horaF)->lt(Carbon::parse($horario->horaI))) {
                        $fechaAyer = new Carbon('yesterday');
                        $fechaA = $fechaAyer->isoFormat('YYYY-MM-DD');
                        if ($horario_dias->start == $fechaA) {
                            $horario->horaI = $fechaA . " " . $horario->horaI;
                            $horario->horaF = $fechaHoy . " " . $horario->horaF;
                            $horaComparar = Carbon::parse($horario->horaF)->addMinutes($horario->tolerancia_final);
                            //* ***********************************************
                            if (Carbon::parse($horaActual)->lte($horaComparar)) {
                                //* CALCULAR TIEMPO DE HOARIO
                                $horas = DB::table('empleado as e')
                                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                                    ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
                                    ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
                                    ->select(
                                        DB::raw('TIME_FORMAT(SEC_TO_TIME(SUM(promedio.tiempo_rango)), "%H:%i:%s") as Total_Envio')
                                    )
                                    ->where(DB::raw('DATE(h.start)'), '=', $fechaA)
                                    ->where('e.emple_id', '=', $request->get('idEmpleado'))
                                    ->get()
                                    ->first();
                                $horario->tiempo = $horas->Total_Envio == null ? "00:00:00" : $horas->Total_Envio;
                                array_push($respuesta, $horario);
                            }
                        }
                    }
                }
            }
            return response()->json($respuesta, 200);
        }
        return response()->json("Empleado no encontrado", 400);
    }

    // INVALIDAR TOKEN
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

    //* MEJORACIONES EN LOGIN
    public function verificacionLogin(Request $request)
    {
        $nroD = $request->get('nroDocumento');
        $codigo = $request->get('codigo');
        $buscarCodigo = vinculacion::where('hash', '=', $codigo)->get()->first();
        if ($buscarCodigo) {
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
                    $empleadoHash = empleado::select('emple_nDoc')->where('emple_id', '=', $vinculacion->idEmpleado)->get()->first();
                    if ($empleadoHash->emple_nDoc == $nroD) {
                        $licencia = licencia_empleado::where('id', '=', $vinculacion->idLicencia)->where('disponible', '!=', 'i')->get()->first();
                        if ($licencia) {
                            if ($vinculacion->hash == $request->get('codigo')) {
                                //* OBTENER HORAS
                                $fecha = Carbon::now();
                                $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                                // * FUNCION PARA UNIR DATOS POR HORAS Y MINUTOS
                                $horasRHbox = DB::table('empleado as e')
                                    ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
                                    ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
                                    ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
                                    ->select(
                                        DB::raw('TIME(cp.hora_ini) as hora_ini'),
                                        DB::raw('TIME(cp.hora_fin) as hora_fin'),
                                        DB::raw('DATE(cp.hora_ini) as fecha'),
                                        DB::raw('TIME(cp.hora_ini) as hora'),
                                        'promedio.tiempo_rango as rango'
                                    )
                                    ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '=', $fechaHoy)
                                    ->where('e.emple_id', '=', $empleado->emple_id)
                                    ->orderBy('cp.hora_ini', 'asc')
                                    ->get();
                                $horasRHbox = horasRemotoRutaJson($horasRHbox);
                                //* OBTENER HORAS DEL EMPLEADO EN RUTA
                                $horasRuta = DB::table('empleado as e')
                                    ->join('ubicacion as u', 'u.idEmpleado', '=', 'e.emple_id')
                                    ->leftJoin('horario_dias as h', 'h.id', '=', 'u.idHorario_dias')
                                    ->select(
                                        DB::raw('TIME(u.hora_ini) as hora_ini'),
                                        DB::raw('TIME(u.hora_fin) as hora_fin'),
                                        DB::raw('DATE(u.hora_ini) as fecha'),
                                        DB::raw('TIME(u.hora_ini) as hora'),
                                        'u.rango as rango'
                                    )
                                    ->where(DB::raw('IF(h.id is null, DATE(u.hora_ini), DATE(h.start))'), '=', $fechaHoy)
                                    ->where('e.emple_id', '=', $empleado->emple_id)
                                    ->orderBy('u.hora_ini', 'asc')
                                    ->get();
                                $horasRuta = horasRemotoRutaJson($horasRuta);
                                if (sizeof($horasRHbox) != 0 && sizeof($horasRuta) != 0) {
                                    $rango = 0;
                                    for ($hora = 0; $hora < 24; $hora++) {
                                        $busquedaHora = true;
                                        for ($i = 0; $i < sizeof($horasRHbox); $i++) {
                                            for ($j = 0; $j < sizeof($horasRuta); $j++) {
                                                //* RECORREMOS EN FORMATO HORAS
                                                if ($horasRHbox[$i]["hora"] == $hora && $horasRuta[$j]["hora"] == $hora) {
                                                    $busquedaHora = false;
                                                    //* RECORREMOS EN FORMATO MINUTOS
                                                    for ($m = 0; $m < 6; $m++) {
                                                        if (isset($horasRHbox[$i]["minuto"][$m]) && isset($horasRuta[$j]["minuto"][$m])) {
                                                            $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                                            $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                                            //: DATOS DE RH BOX
                                                            $horaInicioRHbox = "23:00:00";
                                                            $horaFinRHbox = "00:00:00";
                                                            $rangoRHbox = 0;
                                                            //: DATOS DE RUTA
                                                            $horaInicioRuta = "23:00:00";
                                                            $horaFinRuta = "00:00:00";
                                                            $rangoRuta = 0;
                                                            //* RECORREMOS MINUTOS RH BOX
                                                            for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                                                if (Carbon::parse($horaInicioRHbox) > Carbon::parse($arrayMinutoRHbox[$index]->hora_ini)) {
                                                                    $horaInicioRHbox = $arrayMinutoRHbox[$index]->hora_ini;
                                                                }
                                                                if (Carbon::parse($horaFinRHbox) < Carbon::parse($arrayMinutoRHbox[$index]->hora_fin)) {
                                                                    $horaFinRHbox = $arrayMinutoRHbox[$index]->hora_fin;
                                                                }
                                                                $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                                            }
                                                            //* RECORREMOS MINUTOS RUTA
                                                            for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                                                if (Carbon::parse($horaInicioRuta) > Carbon::parse($arrayMinutoRuta[$element]->hora_ini)) {
                                                                    $horaInicioRuta = $arrayMinutoRuta[$element]->hora_ini;
                                                                }
                                                                if (Carbon::parse($horaFinRuta) < Carbon::parse($arrayMinutoRuta[$element]->hora_fin)) {
                                                                    $horaFinRuta = $arrayMinutoRuta[$element]->hora_fin;
                                                                }
                                                                $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                                            }
                                                            //* COMPARAMOS TIEMPOS
                                                            if (Carbon::parse($horaInicioRHbox) < Carbon::parse($horaInicioRuta)) {
                                                                //* PARAMETROS PARA ENVIAR A FUNCION
                                                                $horaInicioRango = $horaInicioRHbox;
                                                                $horaFinRango = $horaFinRHbox;
                                                                $horaNowRango = $horaInicioRuta;
                                                                //* *********************************
                                                                $check = checkHora($horaInicioRango, $horaFinRango, $horaNowRango);
                                                                if ($check) {
                                                                    $nuevoRango = ($rangoRHbox + $rangoRuta) / 2;
                                                                    $rango = $rango + $nuevoRango;
                                                                } else {
                                                                    $nuevoRango = $rangoRHbox + $rangoRuta;
                                                                    $rango = $rango + $nuevoRango;
                                                                }
                                                            } else {
                                                                //* PARAMETROS PARA ENVIAR A FUNCION
                                                                $horaInicioRango = $horaInicioRuta;
                                                                $horaFinRango = $horaFinRuta;
                                                                $horaNowRango = $horaInicioRHbox;
                                                                //* *********************************
                                                                $check = checkHora($horaInicioRango, $horaFinRango, $horaNowRango);
                                                                if ($check) {
                                                                    $nuevoRango = ($rangoRHbox + $rangoRuta) / 2;
                                                                    $rango = $rango + $nuevoRango;
                                                                } else {
                                                                    $nuevoRango = $rangoRHbox + $rangoRuta;
                                                                    $rango = $rango + $nuevoRango;
                                                                }
                                                            }
                                                        } else {
                                                            if (isset($horasRHbox[$i]["minuto"][$m])) {
                                                                $rangoRHbox = 0;
                                                                $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                                                //* RECORREMOS MINUTOS RH BOX
                                                                for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                                                    $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                                                }
                                                                $rango = $rango + $rangoRHbox;
                                                            } else {
                                                                if (isset($horasRuta[$j]["minuto"][$m])) {
                                                                    $rangoRuta = 0;
                                                                    $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                                                    //* RECORREMOS MINUTOS RUTA
                                                                    for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                                                        $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                                                    }
                                                                    $rango = $rango + $rangoRuta;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if ($busquedaHora) {
                                            for ($i = 0; $i < sizeof($horasRHbox); $i++) {
                                                if ($horasRHbox[$i]["hora"] == $hora) {
                                                    //* RECORREMOS EN FORMATO MINUTOS
                                                    for ($m = 0; $m < 6; $m++) {
                                                        if (isset($horasRHbox[$i]["minuto"][$m])) {
                                                            $rangoRHbox = 0;
                                                            $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                                            //* RECORREMOS MINUTOS RH BOX
                                                            for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                                                $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                                            }
                                                            $rango = $rango + $rangoRHbox;
                                                        }
                                                    }
                                                }
                                            }
                                            for ($j = 0; $j < sizeof($horasRuta); $j++) {
                                                if ($horasRuta[$j]["hora"] == $hora) {
                                                    //* RECORREMOS EN FORMATO MINUTOS
                                                    for ($m = 0; $m < 6; $m++) {
                                                        if (isset($horasRuta[$j]["minuto"][$m])) {
                                                            $rangoRuta = 0;
                                                            $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                                            //* RECORREMOS MINUTOS RUTA
                                                            for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                                                $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                                            }
                                                            $rango = $rango + $rangoRuta;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if (sizeof($horasRHbox) != 0) {
                                        $rango = 0;
                                        for ($i = 0; $i < sizeof($horasRHbox); $i++) {
                                            //* RECORREMOS EN FORMATO HORAS
                                            for ($hora = 0; $hora < 24; $hora++) {
                                                if ($horasRHbox[$i]["hora"] == $hora) {
                                                    //* RECORREMOS EN FORMATO MINUTOS
                                                    for ($m = 0; $m < 6; $m++) {
                                                        if (isset($horasRHbox[$i]["minuto"][$m])) {
                                                            $rangoRHbox = 0;
                                                            $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                                            //* RECORREMOS MINUTOS RH BOX
                                                            for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                                                $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                                            }
                                                            $rango = $rango + $rangoRHbox;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if (sizeof($horasRuta) != 0) {
                                            $rango = 0;
                                            for ($j = 0; $j < sizeof($horasRuta); $j++) {
                                                //* RECORREMOS EN FORMATO HORAS
                                                for ($hora = 0; $hora < 24; $hora++) {
                                                    if ($horasRuta[$j]["hora"] == $hora) {
                                                        //* RECORREMOS EN FORMATO MINUTOS
                                                        for ($m = 0; $m < 6; $m++) {
                                                            if (isset($horasRuta[$j]["minuto"][$m])) {
                                                                $rangoRuta = 0;
                                                                $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                                                //* RECORREMOS MINUTOS RUTA
                                                                for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                                                    $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                                                }
                                                                $rango = $rango + $rangoRuta;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            $rango = 0;
                                        }
                                    }
                                }
                                // VERSION GLOBAL
                                $versionGlobal = DB::table('versionrhbox as vr')
                                    ->select('vr.descripcion', 'vr.obligatorio')
                                    ->get()
                                    ->first();
                                // **************
                                if ($vinculacion->serieDisco ==  null) {
                                    if ($vinculacion->idSoftware == null) {
                                        // AGREGAR TABLA DE SOFTWARE VINCULACIÓN
                                        $software_vinculacion = new software_vinculacion();
                                        $software_vinculacion->version = $request->get('version');
                                        $software_vinculacion->fechaActualizacion = Carbon::now();
                                        $software_vinculacion->save();

                                        $idSoftware = $software_vinculacion->id;

                                        // UNIR SOFTWARE A VINCULACIÓN
                                        $vinculacion->idSoftware = $idSoftware;
                                    } else {
                                        $software_vinculacion = software_vinculacion::findOrFail($vinculacion->idSoftware);
                                        if ($software_vinculacion) {
                                            if ($software_vinculacion->version != $request->get('version')) {
                                                $software_vinculacion->version = $request->get('version');
                                                $software_vinculacion->fechaActualizacion = Carbon::now();
                                                $software_vinculacion->save();
                                            }
                                        } else {
                                            return response()->json("software_erroneo", 400);
                                        }
                                    }
                                    $vinculacion->pc_mac = $request->get('pc_mac');
                                    $vinculacion->serieDisco = $request->get('serieD');
                                    $vinculacion->save();
                                    $factory = JWTFactory::customClaims([
                                        'sub' => env('API_id'),
                                    ]);
                                    $payload = $factory->make();
                                    $token = JWTAuth::encode($payload);
                                    $organizacion = organizacion::where('organi_id', '=', $idOrganizacion)->get()->first();

                                    //* TIEMPO EN EL SERVIDOR
                                    $fecha = Carbon::now('America/Lima');
                                    $horaActual = $fecha->isoFormat('YYYY-MM-DDTHH:mm:ss');
                                    return response()->json(array(
                                        "corte" => $organizacion->corteCaptura,
                                        "idEmpleado" => $empleado->emple_id,
                                        "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
                                        'idUser' => $idOrganizacion,
                                        'tiempo' => gmdate('H:i:s', $rango),
                                        'version' => $software_vinculacion->version,
                                        'versionGlobal' => $versionGlobal->descripcion,
                                        'versionObligatorio' => $versionGlobal->obligatorio,
                                        'horaActual' => $horaActual,
                                        'token' => $token->get()
                                    ), 200);
                                } else {
                                    if ($vinculacion->serieDisco == $request->get('serieD')) {
                                        if ($vinculacion->idSoftware == null) {
                                            // AGREGAR TABLA DE SOFTWARE VINCULACIÓN
                                            $software_vinculacion = new software_vinculacion();
                                            $software_vinculacion->version = $request->get('version');
                                            $software_vinculacion->fechaActualizacion = Carbon::now();
                                            $software_vinculacion->save();

                                            $idSoftware = $software_vinculacion->id;

                                            // UNIR SOFTWARE A VINCULACIÓN
                                            $vinculacion->idSoftware = $idSoftware;
                                        } else {
                                            $software_vinculacion = software_vinculacion::findOrFail($vinculacion->idSoftware);
                                            if ($software_vinculacion) {
                                                if ($software_vinculacion->version != $request->get('version')) {
                                                    $software_vinculacion->version = $request->get('version');
                                                    $software_vinculacion->fechaActualizacion = Carbon::now();
                                                    $software_vinculacion->save();
                                                }
                                            } else {
                                                return response()->json("software_erroneo", 400);
                                            }
                                        }
                                        $vinculacion->pc_mac = $request->get('pc_mac');
                                        $vinculacion->save();
                                        $factory = JWTFactory::customClaims([
                                            'sub' => env('API_id'),
                                        ]);
                                        $payload = $factory->make();
                                        $token = JWTAuth::encode($payload);
                                        $organizacion = organizacion::where('organi_id', '=', $idOrganizacion)->get()->first();
                                        //* TIEMPO EN EL SERVIDOR
                                        $fecha = Carbon::now('America/Lima');
                                        $horaActual = $fecha->isoFormat('YYYY-MM-DDTHH:mm:ss');
                                        return response()->json(array(
                                            "corte" => $organizacion->corteCaptura,
                                            "idEmpleado" => $empleado->emple_id, "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
                                            'idUser' => $idOrganizacion,
                                            'tiempo' => gmdate('H:i:s', $rango),
                                            'version' => $software_vinculacion->version,
                                            'versionGlobal' => $versionGlobal->descripcion,
                                            'versionObligatorio' => $versionGlobal->obligatorio,
                                            'horaActual' => $horaActual,
                                            'token' => $token->get()
                                        ), 200);
                                    } else {
                                        if (strpos($vinculacion->serieDisco, 'RHbox')) {
                                            if ($vinculacion->idSoftware == null) {
                                                //* AGREGAR TABLA DE SOFTWARE VINCULACIÓN
                                                $software_vinculacion = new software_vinculacion();
                                                $software_vinculacion->version = $request->get('version');
                                                $software_vinculacion->fechaActualizacion = Carbon::now();
                                                $software_vinculacion->save();

                                                $idSoftware = $software_vinculacion->id;

                                                //* UNIR SOFTWARE A VINCULACIÓN
                                                $vinculacion->idSoftware = $idSoftware;
                                            } else {
                                                $software_vinculacion = software_vinculacion::findOrFail($vinculacion->idSoftware);
                                                if ($software_vinculacion) {
                                                    if ($software_vinculacion->version != $request->get('version')) {
                                                        $software_vinculacion->version = $request->get('version');
                                                        $software_vinculacion->fechaActualizacion = Carbon::now();
                                                        $software_vinculacion->save();
                                                    }
                                                } else {
                                                    return response()->json("software_erroneo", 400);
                                                }
                                            }
                                            $vinculacion->pc_mac = $request->get('pc_mac');
                                            $vinculacion->serieDisco = $request->get('serieD');
                                            $vinculacion->save();
                                            $factory = JWTFactory::customClaims([
                                                'sub' => env('API_id'),
                                            ]);
                                            $payload = $factory->make();
                                            $token = JWTAuth::encode($payload);
                                            $organizacion = organizacion::where('organi_id', '=', $idOrganizacion)->get()->first();

                                            //* TIEMPO EN EL SERVIDOR
                                            $fecha = Carbon::now('America/Lima');
                                            $horaActual = $fecha->isoFormat('YYYY-MM-DDTHH:mm:ss');
                                            return response()->json(array(
                                                "corte" => $organizacion->corteCaptura,
                                                "idEmpleado" => $empleado->emple_id,
                                                "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
                                                'idUser' => $idOrganizacion,
                                                'tiempo' => gmdate('H:i:s', $rango),
                                                'version' => $software_vinculacion->version,
                                                'versionGlobal' => $versionGlobal->descripcion,
                                                'versionObligatorio' => $versionGlobal->obligatorio,
                                                'horaActual' => $horaActual,
                                                'token' => $token->get()
                                            ), 200);
                                        }
                                        return response()->json("disco_erroneo", 400);
                                    }
                                }
                            }
                            return response()->json("codigo_erroneo", 400);
                        }
                        return response()->json("licencia_de_baja", 400);
                    } else {
                        return response()->json("codigo_no_exite", 400);
                    }
                }
                return response()->json("sin_dispositivo", 400);
            }
            return response()->json("empleado_no_exite", 400);
        }
        return response()->json("codigo_no_exite", 400);
    }
    public function downloadActualizacionx64(Request $request)
    {
        $codigo = $request->get('codigo');
        $decode = base_convert(intval($codigo), 10, 36);
        $explode = explode("s", $decode);
        $vinculacion = vinculacion::where('id', '=', $explode[1])->get()->first();
        if ($vinculacion) {
            if ($vinculacion->idSoftware == null) {
                // AGREGAR TABLA DE SOFTWARE VINCULACIÓN
                $software_vinculacion = new software_vinculacion();
                $software_vinculacion->version = $request->get('version');
                $software_vinculacion->fechaActualizacion = Carbon::now();
                $software_vinculacion->save();

                $idSoftware = $software_vinculacion->id;

                // UNIR SOFTWARE A VINCULACIÓN
                $vinculacion->idSoftware = $idSoftware;
                $vinculacion->save();
                return response()->download(app_path() . "/file/RH box/RHbox.zip");
            } else {
                $software_vinculacion = software_vinculacion::findOrFail($vinculacion->idSoftware);
                if ($software_vinculacion) {
                    if ($software_vinculacion->version != $request->get('version')) {
                        $software_vinculacion->version = $request->get('version');
                        $software_vinculacion->fechaActualizacion = Carbon::now();
                        $software_vinculacion->save();
                        return response()->download(app_path() . "/file/RH box/RHbox.zip");
                    }
                } else {
                    return response()->json("software_erroneo", 400);
                }
            }
        }
        return response()->json("sin_dispositivo", 400);
    }
    // ? UPDATE DE DOWNLOAND 64
    public function updteDonwloand64()
    {
        return response()->download(app_path() . "/file/x64/RHnubeX64.zip");
    }
    //? TIEMPO DEL SERVIDOR
    public function horaServidor()
    {
        $fecha = Carbon::now('America/Lima');
        $horaActual = $fecha->isoFormat('YYYY-MM-DDTHH:mm:ss');

        $respuesta = array("hora" => $horaActual);
        return response()->json($respuesta, 200);
    }

    // ? TIEMPO DE EMPLEADO
    function tiempoEmpleado(Request $request)
    {
        //* OBTENER FECHA DE HOY
        $fecha = Carbon::now();
        $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
        $idEmpleado = $request->get('idEmpleado');
        $horasRHbox = DB::table('empleado as e')
            ->join('captura as cp', 'cp.idEmpleado', '=', 'e.emple_id')
            ->join('promedio_captura as promedio', 'promedio.idCaptura', '=', 'cp.idCaptura')
            ->leftJoin('horario_dias as h', 'h.id', '=', 'promedio.idHorario')
            ->select(
                'cp.actividad',
                DB::raw('TIME(cp.hora_ini) as hora_ini'),
                DB::raw('TIME(cp.hora_fin) as hora_fin'),
                DB::raw('DATE(cp.hora_ini) as fecha'),
                DB::raw('TIME(cp.hora_ini) as hora'),
                'promedio.tiempo_rango as rango'
            )
            ->where(DB::raw('IF(h.id is null, DATE(cp.hora_ini), DATE(h.start))'), '=', $fechaHoy)
            ->where('e.emple_id', '=', $idEmpleado)
            ->orderBy('cp.hora_ini', 'asc')
            ->get();
        $horasRHbox = horasRemotoRutaJson($horasRHbox);
        //* OBTENER HORAS DEL EMPLEADO EN RUTA
        $horasRuta = DB::table('empleado as e')
            ->join('ubicacion as u', 'u.idEmpleado', '=', 'e.emple_id')
            ->leftJoin('horario_dias as h', 'h.id', '=', 'u.idHorario_dias')
            ->select(
                'u.actividad_ubicacion as actividad',
                DB::raw('TIME(u.hora_ini) as hora_ini'),
                DB::raw('TIME(u.hora_fin) as hora_fin'),
                DB::raw('DATE(u.hora_ini) as fecha'),
                DB::raw('TIME(u.hora_ini) as hora'),
                'u.rango as rango'
            )
            ->where(DB::raw('IF(h.id is null, DATE(u.hora_ini), DATE(h.start))'), '=', $fechaHoy)
            ->where('e.emple_id', '=', $idEmpleado)
            ->orderBy('u.hora_ini', 'asc')
            ->get();
        $horasRuta = horasRemotoRutaJson($horasRuta);
        if (sizeof($horasRHbox) != 0 && sizeof($horasRuta) != 0) {
            $rango = 0;
            $actividad = 0;
            for ($hora = 0; $hora < 24; $hora++) {
                $busquedaHora = true;
                for ($i = 0; $i < sizeof($horasRHbox); $i++) {
                    for ($j = 0; $j < sizeof($horasRuta); $j++) {
                        //* RECORREMOS EN FORMATO HORAS
                        if ($horasRHbox[$i]["hora"] == $hora && $horasRuta[$j]["hora"] == $hora) {
                            $busquedaHora = false;
                            //* RECORREMOS EN FORMATO MINUTOS
                            for ($m = 0; $m < 6; $m++) {
                                if (isset($horasRHbox[$i]["minuto"][$m]) && isset($horasRuta[$j]["minuto"][$m])) {
                                    $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                    $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                    //: DATOS DE RH BOX
                                    $horaInicioRHbox = "23:00:00";
                                    $horaFinRHbox = "00:00:00";
                                    $rangoRHbox = 0;
                                    $actividadRHbox = 0;
                                    //: DATOS DE RUTA
                                    $horaInicioRuta = "23:00:00";
                                    $horaFinRuta = "00:00:00";
                                    $rangoRuta = 0;
                                    $actividadRuta = 0;
                                    //* RECORREMOS MINUTOS RH BOX
                                    for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                        if (Carbon::parse($horaInicioRHbox) > Carbon::parse($arrayMinutoRHbox[$index]->hora_ini)) {
                                            $horaInicioRHbox = $arrayMinutoRHbox[$index]->hora_ini;
                                        }
                                        if (Carbon::parse($horaFinRHbox) < Carbon::parse($arrayMinutoRHbox[$index]->hora_fin)) {
                                            $horaFinRHbox = $arrayMinutoRHbox[$index]->hora_fin;
                                        }
                                        $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                        $actividadRHbox = $actividadRHbox + $arrayMinutoRHbox[$index]->actividad;
                                    }
                                    //* RECORREMOS MINUTOS RUTA
                                    for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                        if (Carbon::parse($horaInicioRuta) > Carbon::parse($arrayMinutoRuta[$element]->hora_ini)) {
                                            $horaInicioRuta = $arrayMinutoRuta[$element]->hora_ini;
                                        }
                                        if (Carbon::parse($horaFinRuta) < Carbon::parse($arrayMinutoRuta[$element]->hora_fin)) {
                                            $horaFinRuta = $arrayMinutoRuta[$element]->hora_fin;
                                        }
                                        $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                        $actividadRuta = $actividadRuta + $arrayMinutoRuta[$element]->actividad;
                                    }
                                    //* COMPARAMOS TIEMPOS
                                    if (Carbon::parse($horaInicioRHbox) < Carbon::parse($horaInicioRuta)) {
                                        //* PARAMETROS PARA ENVIAR A FUNCION
                                        $horaInicioRango = $horaInicioRHbox;
                                        $horaFinRango = $horaFinRHbox;
                                        $horaNowRango = $horaInicioRuta;
                                        //* *********************************
                                        $check = checkHora($horaInicioRango, $horaFinRango, $horaNowRango);
                                        if ($check) {
                                            // ! RANGOS
                                            $nuevoRango = ($rangoRHbox + $rangoRuta) / 2;
                                            $rango = $rango + $nuevoRango;
                                            // ! ACTIVIDAD
                                            $nuevaActividad = ($actividadRHbox + $actividadRuta) / 2;
                                            $actividad = $actividad + $nuevaActividad;
                                        } else {
                                            // ! RANGOS
                                            $nuevoRango = $rangoRHbox + $rangoRuta;
                                            $rango = $rango + $nuevoRango;
                                            // ! ACTIVIDAD
                                            $nuevaActividad = $actividadRHbox + $actividadRuta;
                                            $actividad = $actividad + $nuevaActividad;
                                        }
                                    } else {
                                        //* PARAMETROS PARA ENVIAR A FUNCION
                                        $horaInicioRango = $horaInicioRuta;
                                        $horaFinRango = $horaFinRuta;
                                        $horaNowRango = $horaInicioRHbox;
                                        //* *********************************
                                        $check = checkHora($horaInicioRango, $horaFinRango, $horaNowRango);
                                        if ($check) {
                                            // ! RANGOS
                                            $nuevoRango = ($rangoRHbox + $rangoRuta) / 2;
                                            $rango = $rango + $nuevoRango;
                                            // ! ACTIVIDAD
                                            $nuevaActividad = ($actividadRHbox + $actividadRuta) / 2;
                                            $actividad = $actividad + $nuevaActividad;
                                        } else {
                                            // ! RANGOS
                                            $nuevoRango = $rangoRHbox + $rangoRuta;
                                            $rango = $rango + $nuevoRango;
                                            // ! ACTIVIDAD
                                            $nuevaActividad = $actividadRHbox + $actividadRuta;
                                            $actividad = $actividad + $nuevaActividad;
                                        }
                                    }
                                } else {
                                    if (isset($horasRHbox[$i]["minuto"][$m])) {
                                        $rangoRHbox = 0;
                                        $actividadRHbox = 0;
                                        $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                        //* RECORREMOS MINUTOS RH BOX
                                        for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                            $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                            $actividadRHbox = $actividadRHbox + $arrayMinutoRHbox[$index]->actividad;
                                        }
                                        $rango = $rango + $rangoRHbox;               //: -> RANGO
                                        $actividad = $actividad + $actividadRHbox;   //: -> ACTIVIDAD
                                    } else {
                                        if (isset($horasRuta[$j]["minuto"][$m])) {
                                            $rangoRuta = 0;
                                            $actividadRuta = 0;
                                            $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                            //* RECORREMOS MINUTOS RUTA
                                            for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                                $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                                $actividadRuta = $actividadRuta + $arrayMinutoRuta[$element]->actividad;
                                            }
                                            $rango = $rango + $rangoRuta;                //: -> RANGO
                                            $actividad = $actividad + $actividadRuta;    //: -> ACTIVIDAD
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if ($busquedaHora) {
                    for ($i = 0; $i < sizeof($horasRHbox); $i++) {
                        if ($horasRHbox[$i]["hora"] == $hora) {
                            //* RECORREMOS EN FORMATO MINUTOS
                            for ($m = 0; $m < 6; $m++) {
                                if (isset($horasRHbox[$i]["minuto"][$m])) {
                                    $rangoRHbox = 0;
                                    $actividadRHbox = 0;
                                    $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                    //* RECORREMOS MINUTOS RH BOX
                                    for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                        $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                        $actividadRHbox = $actividadRHbox + $arrayMinutoRHbox[$index]->actividad;
                                    }
                                    $rango = $rango + $rangoRHbox;                //: -> RANGO
                                    $actividad = $actividad + $actividadRHbox;   //: -> ACTIVIDAD
                                }
                            }
                        }
                    }
                    for ($j = 0; $j < sizeof($horasRuta); $j++) {
                        if ($horasRuta[$j]["hora"] == $hora) {
                            //* RECORREMOS EN FORMATO MINUTOS
                            for ($m = 0; $m < 6; $m++) {
                                if (isset($horasRuta[$j]["minuto"][$m])) {
                                    $rangoRuta = 0;
                                    $actividadRuta = 0;
                                    $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                    //* RECORREMOS MINUTOS RUTA
                                    for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                        $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                        $actividadRuta = $actividadRuta + $arrayMinutoRuta[$element]->actividad;
                                    }
                                    $rango = $rango + $rangoRuta;               //: -> RANGO
                                    $actividad = $actividad + $actividadRuta;   //: -> ACTIVIDAD
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if (sizeof($horasRHbox) != 0) {
                $rango = 0;
                $actividad = 0;
                for ($i = 0; $i < sizeof($horasRHbox); $i++) {
                    //* RECORREMOS EN FORMATO HORAS
                    for ($hora = 0; $hora < 24; $hora++) {
                        if ($horasRHbox[$i]["hora"] == $hora) {
                            //* RECORREMOS EN FORMATO MINUTOS
                            for ($m = 0; $m < 6; $m++) {
                                if (isset($horasRHbox[$i]["minuto"][$m])) {
                                    $rangoRHbox = 0;
                                    $actividadRHbox = 0;
                                    $arrayMinutoRHbox = $horasRHbox[$i]["minuto"][$m];
                                    //* RECORREMOS MINUTOS RH BOX
                                    for ($index = 0; $index < sizeof($arrayMinutoRHbox); $index++) {
                                        $rangoRHbox = $rangoRHbox + $arrayMinutoRHbox[$index]->rango;
                                        $actividadRHbox = $actividadRHbox + $arrayMinutoRHbox[$index]->actividad;
                                    }
                                    $rango = $rango + $rangoRHbox;               //: -> RANGO
                                    $actividad = $actividad + $actividadRHbox;   //: -> ACTIVIDAD
                                }
                            }
                        }
                    }
                }
            } else {
                if (sizeof($horasRuta) != 0) {
                    $rango = 0;
                    $actividad = 0;
                    for ($j = 0; $j < sizeof($horasRuta); $j++) {
                        //* RECORREMOS EN FORMATO HORAS
                        for ($hora = 0; $hora < 24; $hora++) {
                            if ($horasRuta[$j]["hora"] == $hora) {
                                //* RECORREMOS EN FORMATO MINUTOS
                                for ($m = 0; $m < 6; $m++) {
                                    if (isset($horasRuta[$j]["minuto"][$m])) {
                                        $rangoRuta = 0;
                                        $actividadRuta = 0;
                                        $arrayMinutoRuta = $horasRuta[$j]["minuto"][$m];
                                        //* RECORREMOS MINUTOS RUTA
                                        for ($element = 0; $element < sizeof($arrayMinutoRuta); $element++) {
                                            $rangoRuta = $rangoRuta + $arrayMinutoRuta[$element]->rango;
                                            $actividadRuta = $actividadRuta + $arrayMinutoRuta[$element]->actividad;
                                        }
                                        $rango = $rango + $rangoRuta;
                                        $actividad = $actividad + $actividadRuta;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $rango = 0;
                    $actividad = 0;
                }
            }
        }
        $productividad = 0;
        if ($rango != 0) {
            $productividad = ($actividad / $rango) * 100;
            $productividad = (float) number_format($productividad, 2);
        }
        $tiempo = array("tiempo" => gmdate('H:i:s', $rango), "productividad" => $productividad);
        return response()->json($tiempo, 200);
    }
}
