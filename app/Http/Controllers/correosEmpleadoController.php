<?php

namespace App\Http\Controllers;

use App\empleado;
use App\Mail\CorreoEmpleadoMail;
use App\persona;
use App\vinculacion;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Claims\DatetimeTrait;

class correosEmpleadoController extends Controller
{
    public function encode(Request $request)
    {
        $idEmpleado = $request->get('idEmpleado');
        $correoE = DB::table('empleado as e')
            ->select('e.emple_Correo')
            ->where('e.emple_id', '=', $idEmpleado)
            ->get();
        if ($correoE) {
            $codV = DB::table('vinculacion as v')
                ->select('v.id')
                ->where('v.idEmpleado', '=', $idEmpleado)
                ->get()->first();
            if ($codV) {
                $vinculacion = vinculacion::findOrFail($codV->id);
                $vinculacion->reenvio = Carbon::now();
                $vinculacion->save();
                $datos = [];
                $datos["correo"] = $correoE[0]->emple_Correo;
                $email = array($datos["correo"]);
                $codigoP = DB::table('empleado as e')
                    ->select('emple_persona')
                    ->where('e.emple_id', '=', $idEmpleado)
                    ->get();
                $codP = [];
                $codP["id"] = $codigoP[0]->emple_persona;
                $persona = persona::find($codP["id"]);
                Mail::to($email)->queue(new CorreoEmpleadoMail($vinculacion, $persona));
                return json_encode(array("result" => true));
            } else {
                $codigoEmpresa = DB::table('users as u')
                    ->join('usuario_organizacion as uo', 'uo.user_id', '=', 'u.id')
                    ->select('uo.organi_id')
                    ->where('u.id', '=', Auth::user()->id)
                    ->get();
                $codigoEmpleado = DB::table('empleado as e')
                    ->select('e.emple_codigo')
                    ->where('e.emple_id', '=', $idEmpleado)
                    ->get();
                $codigoP = DB::table('empleado as e')
                    ->select('emple_persona')
                    ->where('e.emple_id', '=', $idEmpleado)
                    ->get();
                $codP = [];
                $codP["id"] = $codigoP[0]->emple_persona;
                $persona = persona::find($codP["id"]);
                if ($codigoEmpleado != '') {
                    $codigoHash = $codigoEmpresa[0]->organi_id . $idEmpleado . $codigoEmpleado[0]->emple_codigo;
                    //$encode = rtrim(strtr(base64_encode($codigoHash), '+/', '-_'));
                    $encode = intval($codigoHash, 36);
                } else {
                    $codigoHash = $codigoEmpresa[0]->organi_id . $idEmpleado;
                    //$encode = rtrim(strtr(base64_encode($codigoHash), '+/', '-_'));
                    $encode = intval($codigoHash, 36);
                }

                $vinculacion = new vinculacion();
                $vinculacion->idEmpleado = $idEmpleado;
                $vinculacion->hash = $encode;
                $vinculacion->envio = Carbon::now();
                $vinculacion->save();

                $datos = [];
                $datos["correo"] = $correoE[0]->emple_Correo;
                $email = array($datos["correo"]);
                Mail::to($email)->queue(new CorreoEmpleadoMail($vinculacion, $persona));
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
            if ($correoE) {
                $codV = DB::table('vinculacion as v')
                    ->select('v.id')
                    ->where('v.idEmpleado', '=', $idEm)
                    ->get()->first();
                if ($codV) {
                    $vinculacion = vinculacion::findOrFail($codV->id);
                    if ($vinculacion->reenvio == null) {
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
                        Mail::to($email)->queue(new CorreoEmpleadoMail($vinculacion, $persona));
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
                        ->select('e.emple_codigo')
                        ->where('e.emple_id', '=', $idEm)
                        ->get();
                    $codigoP = DB::table('empleado as e')
                        ->select('emple_persona')
                        ->where('e.emple_id', '=', $idEm)
                        ->get();
                    $codP = [];
                    $codP["id"] = $codigoP[0]->emple_persona;
                    $persona = persona::find($codP["id"]);
                    if ($codigoEmpleado != '') {
                        $codigoHash = $codigoEmpresa[0]->organi_id . $idEm . $codigoEmpleado[0]->emple_codigo;
                        //$encode = rtrim(strtr(base64_encode($codigoHash), '+/', '-_'));
                        $encode = intval($codigoHash, 36);
                    } else {
                        $codigoHash = $codigoEmpresa[0]->organi_id . $idEm;
                        //$encode = rtrim(strtr(base64_encode($codigoHash), '+/', '-_'));
                        $encode = intval($codigoHash, 36);
                    }

                    $vinculacion = new vinculacion();
                    $vinculacion->idEmpleado = $idEm;
                    $vinculacion->hash = $encode;
                    $vinculacion->envio = Carbon::now();
                    $vinculacion->save();

                    $datos = [];
                    $datos["correo"] = $correoE->emple_Correo;
                    $email = array($datos["correo"]);
                    Mail::to($email)->queue(new CorreoEmpleadoMail($vinculacion, $persona));
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
}
