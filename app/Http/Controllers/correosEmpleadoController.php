<?php

namespace App\Http\Controllers;

use App\empleado;
use App\licencia_empleado;
use App\Mail\AndroidMail;
use App\Mail\CorreoEmpleadoMail;
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
    public function encode(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $empleado = DB::table('empleado as e')
            ->select('e.emple_Correo')
            ->where('e.emple_id', '=', $idEmpleado)
            ->get()->first();
        if ($empleado->emple_Correo != "") {
            $codV = DB::table('vinculacion as v')
                ->select('v.id', 'v.envio')
                ->where('v.idEmpleado', '=', $idEmpleado)
                ->get()->first();
            if ($codV) {
                $codL = DB::table('licencia_empleado as le')
                    ->select('le.id')
                    ->where('le.idEmpleado', '=', $idEmpleado)
                    ->get()->first();
                $vinculacion = vinculacion::findOrFail($codV->id);
                $vinculacion->reenvio = Carbon::now();
                $vinculacion->descarga = STR::random(25);
                $vinculacion->save();
                $licencia_empleado = licencia_empleado::findOrFail($codL->id);
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
                return json_encode(array("result" => true));
            } else {
                $codigoEmpresa = DB::table('users as u')
                    ->join('usuario_organizacion as uo', 'uo.user_id', '=', 'u.id')
                    ->select('uo.organi_id')
                    ->where('u.id', '=', Auth::user()->id)
                    ->get();
                $codigoEmpleado = DB::table('empleado as e')
                    ->select('e.emple_codigo', 'e.emple_persona', 'e.created_at')
                    ->where('e.emple_id', '=', $idEmpleado)
                    ->get();
                $codigoP = DB::table('empleado as e')
                    ->select('emple_persona')
                    ->where('e.emple_id', '=', $idEmpleado)
                    ->get();
                $codP = [];
                $codP["id"] = $codigoP[0]->emple_persona;
                $persona = persona::find($codP["id"]);
                if ($codigoEmpleado[0]->emple_codigo != '') {
                    $codigoHash = $codigoEmpresa[0]->organi_id . $idEmpleado . $codigoEmpleado[0]->emple_codigo;
                    $encode = intval($codigoHash, 36);
                    $codigoLicencia = $idEmpleado . '.' . $codigoEmpleado[0]->created_at . $codigoEmpresa[0]->organi_id;
                    $encodeLicencia = rtrim(strtr(base64_encode($codigoLicencia), '+/', '-_'));
                } else {
                    $codigoHash = $codigoEmpresa[0]->organi_id . $idEmpleado . $codigoEmpleado[0]->emple_persona;
                    $encode = intval($codigoHash, 36);
                    $codigoLicencia = $idEmpleado . '.' . $codigoEmpleado[0]->created_at . $codigoEmpresa[0]->organi_id;
                    $encodeLicencia = rtrim(strtr(base64_encode($codigoLicencia), '+/', '-_'));
                }

                $vinculacion = new vinculacion();
                $vinculacion->idEmpleado = $idEmpleado;
                $vinculacion->hash = $encode;
                $vinculacion->envio = Carbon::now();
                $vinculacion->descarga = STR::random(25);
                $vinculacion->save();

                $licencia_empleado = new licencia_empleado();
                $licencia_empleado->idEmpleado = $idEmpleado;
                $licencia_empleado->licencia = $encodeLicencia;
                $licencia_empleado->save();
                $datos = [];
                $datos["correo"] = $empleado->emple_Correo;
                $email = array($datos["correo"]);
                Mail::to($email)->queue(new CorreoEmpleadoMail($vinculacion, $persona, $licencia_empleado));
                return json_encode(array("result" => true));
            }
        }
        return response()->json(null, 403);
    }

    public function encodeMasivo(Request $request)
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

                    $licencia_empleado = new licencia_empleado();
                    $licencia_empleado->idEmpleado = $idEm;
                    $licencia_empleado->licencia = $encodeLicencia;
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
    public function reenvio(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');

        $reenvio = DB::table('vinculacion as v')
            ->select('v.reenvio')
            ->where('v.idEmpleado', '=', $idEmpleado)
            ->get();

        if ($reenvio[0]->reenvio != null) {
            return 1;
        }
    }
    public function envioA(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
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
            return json_encode(array("result" => true));
        }
        return response()->json(null, 403);
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

    public function nuevoEncode(Request $request)
    {
        $respuesta = [];
        $c = true;
        $l = true;
        $idEmpleado = $request->get('idEmpleado');
        $empleado = DB::table('empleado as e')
            ->select('e.emple_Correo')
            ->where('e.emple_id', '=', $idEmpleado)
            ->get()->first();
        if ($empleado->emple_Correo != "") {
            $codV = DB::table('vinculacion as v')
                ->select('v.id', 'v.envio')
                ->where('v.idEmpleado', '=', $idEmpleado)
                ->get()->first();
            $codigoEmpleado = DB::table('empleado as e')
                ->select('e.emple_codigo', 'e.emple_persona', 'e.created_at')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get();
            $codigoP = DB::table('empleado as e')
                ->select('emple_persona')
                ->where('e.emple_id', '=', $idEmpleado)
                ->get();
            $codP = [];
            $codP["id"] = $codigoP[0]->emple_persona;
            $persona = persona::find($codP["id"]);
            $cantidad = DB::table('licencia_empleado as le')
                ->select(DB::raw('COUNT(le.id) as total'))
                ->where('le.idEmpleado', '=', $idEmpleado)
                ->get()->first();
            if ($cantidad->total < 3) {
                if ($codigoEmpleado[0]->emple_codigo != '') {
                    $nuevaLivencia = STR::random(8);
                    $codigoLicencia = $nuevaLivencia . '.' . $idEmpleado . '*';
                    $encodeLicencia = rtrim(strtr(base64_encode($codigoLicencia), '+/', '-_'));
                } else {
                    $nuevaLivencia = STR::random(8);
                    $codigoLicencia = $nuevaLivencia . '.' . $idEmpleado . '*';
                    $encodeLicencia = rtrim(strtr(base64_encode($codigoLicencia), '+/', '-_'));
                }

                $licencia_empleado = new licencia_empleado();
                $licencia_empleado->idEmpleado = $idEmpleado;
                $licencia_empleado->licencia = $encodeLicencia;
                $licencia_empleado->save();
                $vinculacion = vinculacion::findOrFail($codV->id);
                $datos = [];
                $datos["correo"] = $empleado->emple_Correo;
                $email = array($datos["correo"]);
                Mail::to($email)->queue(new CorreoEmpleadoMail($vinculacion, $persona, $licencia_empleado));
                return json_encode(array("result" => true));
            }
            $l = false;
            array_push($respuesta, array('limite' => $l, 'correo' => $c));
            return response()->json($respuesta, 403);
        }
        $c = false;
        array_push($respuesta, array('limite' => $l, 'correo' => $c));
        return response()->json($respuesta, 403);
    }
}
