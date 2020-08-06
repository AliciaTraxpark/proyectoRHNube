<?php

namespace App\Http\Controllers;

use App\empleado;
use App\licencia_empleado;
use App\Mail\AndroidMail;
use App\Mail\CorreoEmpleadoMail;
use App\Mail\CorreoMasivoMail;
use App\Mail\MasivoWindowsMail;
use App\modo;
use App\persona;
use App\vinculacion;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Claims\DatetimeTrait;

class correosEmpleadoController extends Controller
{
    public function envioWindows(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $idVinculacion = $request->get('idVinculacion');
        $empleado = DB::table('empleado as e')
            ->select('e.emple_Correo')
            ->where('e.emple_id', '=', $idEmpleado)
            ->get()->first();
        $vinculacion = vinculacion::findOrFail($idVinculacion);
        if ($empleado->emple_Correo != "") {
            $licencia_empleado = licencia_empleado::findOrFail($vinculacion->idLicencia);
            $vinculacion->descarga = STR::random(25);
            $vinculacion->save();
            $datos = [];
            $datos["correo"] = $empleado->emple_Correo;
            $email = array($datos["correo"]);
            $codigoP = DB::table('empleado as e')
                ->select('emple_persona')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get();
            $codP = [];
            $codP["id"] = $codigoP[0]->emple_persona;
            $persona = persona::find($codP["id"]);
            Mail::to($email)->queue(new CorreoEmpleadoMail($vinculacion, $persona, $licencia_empleado));
            $vinculacion->fecha_entrega = Carbon::now();
            $envio = $vinculacion->envio;
            $suma = $envio + 1;
            $licencia_empleado->disponible = 'e';
            $licencia_empleado->save();
            $vinculacion->envio = $suma;
            $vinculacion->save();
            $respuesta = [];
            $respuesta['envio'] = $vinculacion->envio;
            $respuesta['disponible'] = $licencia_empleado->disponible;
            return response()->json($respuesta, 200);
        }
        return response()->json(null, 400);
    }

    public function encodeMasivoN(Request $request)
    {
        $idEmpleados = $request->ids;
        $idEmp = explode(",", $idEmpleados);
        $resultado = [];
        $c = true;
        $r = true;
        foreach ($idEmp as $idEm) {
            $empleado = empleado::where('emple_id', '=', $idEm)->get()->first();
            $persona = persona::where('perso_id', '=', $empleado->emple_persona)->get()->first();
            $correoE = DB::table('empleado as e')
                ->select('e.emple_Correo')
                ->where('e.emple_id', '=', $idEm)
                ->get()->first();
            if ($correoE->emple_Correo != "") {
                $codV = DB::table('vinculacion as v')
                    ->select('v.id')
                    ->where('v.idEmpleado', '=', $idEm)
                    ->get()->first();
                if ($codV) {
                    $vinculacion = vinculacion::findOrFail($codV->id);
                    $codL = DB::table('licencia_empleado as le')
                        ->select('le.id')
                        ->where('le.idEmpleado', '=', $idEm)
                        ->get()->first();
                    $licencia_empleado = licencia_empleado::findOrFail($codL->id);
                    if ($vinculacion->reenvio == null) {
                        $vinculacion->descarga = STR::random(25);
                        $vinculacion->reenvio = Carbon::now();
                        $vinculacion->save();
                        $datos = [];
                        $datos["correo"] = $correoE->emple_Correo;
                        $email = array($datos["correo"]);
                        $codigoP = DB::table('empleado as e')
                            ->select('emple_persona')
                            ->where('e.emple_id', '=', $idEm)
                            ->get();
                        $codP = [];
                        $codP["id"] = $codigoP[0]->emple_persona;
                        $persona = persona::find($codP["id"]);
                        Mail::to($email)->queue(new CorreoEmpleadoMail($vinculacion, $persona, $licencia_empleado));
                        array_push($resultado, array("Persona" => $persona, "Correo" => $c, "Reenvio" => $r));
                    } else {
                        $r = false;
                        array_push($resultado, array("Persona" => $persona, "Correo" => $c, "Reenvio" => $r));
                    }
                } else {
                    $codigoEmpresa = DB::table('users as u')
                        ->join('usuario_organizacion as uo', 'uo.user_id', '=', 'u.id')
                        ->select('uo.organi_id')
                        ->where('u.id', '=', Auth::user()->id)
                        ->get();
                    $codigoEmpleado = DB::table('empleado as e')
                        ->select('e.emple_codigo', 'e.emple_persona', 'e.created_at')
                        ->where('e.emple_id', '=', $idEm)
                        ->get();
                    $codigoP = DB::table('empleado as e')
                        ->select('emple_persona')
                        ->where('e.emple_id', '=', $idEm)
                        ->get();
                    $codP = [];
                    $codP["id"] = $codigoP[0]->emple_persona;
                    $persona = persona::find($codP["id"]);
                    $codigoU = Auth::user()->id;
                    if ($codigoEmpleado[0]->emple_codigo != '') {
                        $codigoHash = $codigoU . "s" . $codigoEmpresa[0]->organi_id . $idEm . $codigoEmpleado[0]->emple_codigo;
                        $encode = intval($codigoHash, 36);
                        $codigoLicencia = $idEm . '.' . $codigoEmpleado[0]->created_at . $codigoEmpresa[0]->organi_id;
                        $encodeLicencia = rtrim(strtr(base64_encode($codigoLicencia), '+/', '-_'));
                    } else {
                        $codigoHash = $codigoU . "s" . $codigoEmpresa[0]->organi_id . $idEm . $codigoEmpleado[0]->emple_persona;
                        $encode = intval($codigoHash, 36);
                        $codigoLicencia = $idEm . '.' . $codigoEmpleado[0]->created_at . $codigoEmpresa[0]->organi_id;
                        $encodeLicencia = rtrim(strtr(base64_encode($codigoLicencia), '+/', '-_'));
                    }

                    $vinculacion = new vinculacion();
                    $vinculacion->idEmpleado = $idEm;
                    $vinculacion->hash = $encode;
                    $vinculacion->envio = Carbon::now();
                    $vinculacion->descarga = STR::random(25);
                    $vinculacion->save();

                    $idVinculacion = $vinculacion->id;

                    $licencia_empleado = new licencia_empleado();
                    $licencia_empleado->idEmpleado = $idEm;
                    $licencia_empleado->licencia = $encodeLicencia;
                    $licencia_empleado->idVinculacion = $idVinculacion;
                    $licencia_empleado->save();

                    $datos = [];
                    $datos["correo"] = $correoE->emple_Correo;
                    $email = array($datos["correo"]);
                    Mail::to($email)->queue(new CorreoEmpleadoMail($vinculacion, $persona, $licencia_empleado));
                    array_push($resultado, array("Persona" => $persona, "Correo" => $c, "Reenvio" => $r));
                }
            } else {
                $c = false;
                array_push($resultado, array("Persona" => $persona, "Correo" => $c, "Reenvio" => $r));
            }
        }
        return response()->json($resultado, 200);
    }
    public function envioAndroid(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $idVinculacion = $request->get('idVinculacion');
        $correoE = DB::table('empleado as e')
            ->select('e.emple_Correo')
            ->where('e.emple_id', '=', $idEmpleado)
            ->get()->first();
        if ($correoE->emple_Correo != "") {
            $datos = [];
            $datos["correo"] = $correoE->emple_Correo;
            $email = array($datos["correo"]);
            $codigoP = DB::table('empleado as e')
                ->select('emple_persona')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get()->first();
            $codP = [];
            $codP["id"] = $codigoP->emple_persona;
            $persona = persona::find($codP["id"]);
            Mail::to($email)->queue(new AndroidMail($persona));
            $vinculacion = vinculacion::where('id', '=', $idVinculacion)->get()->first();
            $envio = $vinculacion->envio;
            $suma = $envio + 1;
            $vinculacion->envio = $suma;
            $vinculacion->save();
            $licencia_empleado = licencia_empleado::findOrFail($vinculacion->idLicencia);
            $licencia_empleado->disponible = 'e';
            $licencia_empleado->save();
            $respuesta = [];
            $respuesta['envio'] = $vinculacion->envio;
            $respuesta['disponible'] = $licencia_empleado->disponible;
            return response()->json($respuesta, 200);
        }
        return response()->json(null, 400);
    }

    public function envioAndroidM(Request $request)
    {
        $idEmpleados = $request->ids;
        $idEmp = explode(",", $idEmpleados);
        $resultado = [];
        $c = true;
        foreach ($idEmp as $idEm) {
            $empleado = empleado::where('emple_id', '=', $idEm)->get()->first();
            $persona = persona::where('perso_id', '=', $empleado->emple_persona)->get()->first();
            $correoE = DB::table('empleado as e')
                ->select('e.emple_Correo')
                ->where('e.emple_id', '=', $idEm)
                ->get()->first();
            if ($correoE->emple_Correo != "") {
                $datos = [];
                $datos["correo"] = $correoE->emple_Correo;
                $email = array($datos["correo"]);
                Mail::to($email)->queue(new AndroidMail($persona));
                array_push($resultado, array("Persona" => $persona, "Correo" => $c));
            } else {
                $c = false;
                array_push($resultado, array("Persona" => $persona, "Correo" => $c));
            }
        }
        return response()->json($resultado, 200);
    }

    public function envioMasivoWindows(Request $request)
    {
        $idEmpleado = $request->ids;
        $idEmp = explode(",", $idEmpleado);
        $resultado = [];
        $arrayVinculacion = [];
        $c = true;
        $v = true;
        foreach ($idEmp as $idEm) {
            $empleado = empleado::where('emple_id', '=', $idEm)->get()->first();
            $persona = persona::where('perso_id', '=', $empleado->emple_persona)->get()->first();
            if ($empleado->emple_Correo != "") {
                $codV = DB::table('vinculacion as v')
                    ->join('modo as m', 'm.id', '=', 'v.idModo')
                    ->select('v.id')
                    ->where('v.idEmpleado', '=', $idEm)
                    ->where('m.idTipoDispositivo', '=', 1)
                    ->get();
                foreach ($codV as $vinc) {
                    $vinculacion = vinculacion::findOrFail($vinc->id);
                    $licenciaEmpleado = licencia_empleado::findOrFail($vinculacion->idLicencia);
                    $vinculacion->licencia = $licenciaEmpleado;
                    array_push($arrayVinculacion, $vinculacion);
                }
                if (sizeof($arrayVinculacion) >= 1) {
                    $datos["correo"] = $empleado->emple_Correo;
                    $email = array($datos["correo"]);
                    Mail::to($email)->queue(new MasivoWindowsMail($arrayVinculacion, $persona));
                    array_push($resultado, array("Persona" => $persona, "Correo" => $c, "Vinculacion" => $v));
                } else {
                    $v = false;
                    array_push($resultado, array("Persona" => $persona, "Correo" => $c, "Vinculacion" => $v));
                }
            } else {
                $c = false;
                array_push($resultado, array("Persona" => $persona, "Correo" => $c));
            }
        }
        return response()->json($resultado, 200);
    }

    public function ambasPlataformas(Request $request)
    {
        $idEmpleados = $request->ids;
        $idEmp = explode(",", $idEmpleados);
        $resultado = [];
        $c = true;
        $r = true;
        foreach ($idEmp as $idEm) {
            $empleado = empleado::where('emple_id', '=', $idEm)->get()->first();
            $persona = persona::where('perso_id', '=', $empleado->emple_persona)->get()->first();
            $correoE = DB::table('empleado as e')
                ->select('e.emple_Correo')
                ->where('e.emple_id', '=', $idEm)
                ->get()->first();
            if ($correoE->emple_Correo != "") {
                $codV = DB::table('vinculacion as v')
                    ->select('v.id')
                    ->where('v.idEmpleado', '=', $idEm)
                    ->get()->first();
                if ($codV) {
                    $vinculacion = vinculacion::findOrFail($codV->id);
                    $codL = DB::table('licencia_empleado as le')
                        ->select('le.id')
                        ->where('le.idEmpleado', '=', $idEm)
                        ->get()->first();
                    $licencia_empleado = licencia_empleado::findOrFail($codL->id);
                    if ($vinculacion->reenvio == null) {
                        $vinculacion->descarga = STR::random(25);
                        $vinculacion->reenvio = Carbon::now();
                        $vinculacion->save();
                        $datos = [];
                        $datos["correo"] = $correoE->emple_Correo;
                        $email = array($datos["correo"]);
                        $codigoP = DB::table('empleado as e')
                            ->select('emple_persona')
                            ->where('e.emple_id', '=', $idEm)
                            ->get();
                        $codP = [];
                        $codP["id"] = $codigoP[0]->emple_persona;
                        $persona = persona::find($codP["id"]);
                        Mail::to($email)->queue(new CorreoMasivoMail($vinculacion, $persona, $licencia_empleado));
                        array_push($resultado, array("Persona" => $persona, "Correo" => $c, "Reenvio" => $r));
                    } else {
                        $r = false;
                        array_push($resultado, array("Persona" => $persona, "Correo" => $c, "Reenvio" => $r));
                    }
                } else {
                    $codigoEmpresa = DB::table('users as u')
                        ->join('usuario_organizacion as uo', 'uo.user_id', '=', 'u.id')
                        ->select('uo.organi_id')
                        ->where('u.id', '=', Auth::user()->id)
                        ->get();
                    $codigoEmpleado = DB::table('empleado as e')
                        ->select('e.emple_codigo', 'e.emple_persona', 'e.created_at')
                        ->where('e.emple_id', '=', $idEm)
                        ->get();
                    $codigoP = DB::table('empleado as e')
                        ->select('emple_persona')
                        ->where('e.emple_id', '=', $idEm)
                        ->get();
                    $codP = [];
                    $codP["id"] = $codigoP[0]->emple_persona;
                    $persona = persona::find($codP["id"]);
                    if ($codigoEmpleado[0]->emple_codigo != '') {
                        $codigoHash = $codigoEmpresa[0]->organi_id . $idEm . $codigoEmpleado[0]->emple_codigo;
                        $encode = intval($codigoHash, 36);
                        $codigoLicencia = $idEm . '.' . $codigoEmpleado[0]->created_at . $codigoEmpresa[0]->organi_id;
                        $encodeLicencia = rtrim(strtr(base64_encode($codigoLicencia), '+/', '-_'));
                    } else {
                        $codigoHash = $codigoEmpresa[0]->organi_id . $idEm . $codigoEmpleado[0]->emple_persona;
                        $encode = intval($codigoHash, 36);
                        $codigoLicencia = $idEm . '.' . $codigoEmpleado[0]->created_at . $codigoEmpresa[0]->organi_id;
                        $encodeLicencia = rtrim(strtr(base64_encode($codigoLicencia), '+/', '-_'));
                    }

                    $vinculacion = new vinculacion();
                    $vinculacion->idEmpleado = $idEm;
                    $vinculacion->hash = $encode;
                    $vinculacion->envio = Carbon::now();
                    $vinculacion->descarga = STR::random(25);
                    $vinculacion->save();

                    $idVinculacion = $vinculacion->id;

                    $licencia_empleado = new licencia_empleado();
                    $licencia_empleado->idEmpleado = $idEm;
                    $licencia_empleado->licencia = $encodeLicencia;
                    $licencia_empleado->idVinculacion = $idVinculacion;
                    $licencia_empleado->save();

                    $datos = [];
                    $datos["correo"] = $correoE->emple_Correo;
                    $email = array($datos["correo"]);
                    Mail::to($email)->queue(new CorreoMasivoMail($vinculacion, $persona, $licencia_empleado));
                    array_push($resultado, array("Persona" => $persona, "Correo" => $c, "Reenvio" => $r));
                }
            } else {
                $c = false;
                array_push($resultado, array("Persona" => $persona, "Correo" => $c, "Reenvio" => $r));
            }
        }
        return response()->json($resultado, 200);
    }
}
