<?php

namespace App\Http\Controllers;

use App\empleado;
use App\licencia_empleado;
use App\Mail\CorreoEmpleadoMail;
use App\persona;
use App\vinculacion;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class detallesActivacionController extends Controller
{
    public function cambiarEstadoLicencia(Request $request)
    {
        $licencia = licencia_empleado::where('id', '=', $request->get('idL'))->get()->first();
        if ($licencia) {
            $empleado = empleado::where('emple_id', '=', $request->get('idE'))->get()->first();
            if ($empleado->emple_Correo != "") {
                $vinculacion = vinculacion::where('id', '=', $licencia->idVinculacion)->get()->first();
                $licencia->idVinculacion = null;
                $licencia->save();
                $vinculacion->delete();
                $codigoEmpresa = DB::table('users as u')
                    ->join('usuario_organizacion as uo', 'uo.user_id', '=', 'u.id')
                    ->select('uo.organi_id')
                    ->where('u.id', '=', Auth::user()->id)
                    ->get();
                $codigoEmpleado = DB::table('empleado as e')
                    ->select('e.emple_codigo', 'e.emple_persona', 'e.created_at')
                    ->where('e.emple_id', '=', $request->get('idE'))
                    ->get();
                $codigoP = DB::table('empleado as e')
                    ->select('emple_persona')
                    ->where('e.emple_id', '=', $request->get('idE'))
                    ->get();
                $codP = [];
                $codP["id"] = $codigoP[0]->emple_persona;
                $persona = persona::find($codP["id"]);
                $codigoU = Auth::user()->id;
                if ($codigoEmpleado[0]->emple_codigo != '') {
                    $codigoHash = $codigoU . "s" . $codigoEmpresa[0]->organi_id . $request->get('idE') . $codigoEmpleado[0]->emple_codigo;
                    $encode = intval($codigoHash, 36);
                } else {
                    $codigoHash = $codigoU . "s" . $codigoEmpresa[0]->organi_id . $request->get('idE') . $codigoEmpleado[0]->emple_persona;
                    $encode = intval($codigoHash, 36);
                }
                $vinculacionN = new vinculacion();
                $vinculacionN->idEmpleado = $request->get('idE');
                $vinculacionN->hash = $encode;
                $vinculacionN->envio = Carbon::now();
                $vinculacionN->descarga = STR::random(25);
                $vinculacionN->save();

                $idVinculacion = $vinculacionN->id;
                $licencia->disponible = 1;
                $licencia->idVinculacion = $idVinculacion;
                $licencia->save();

                $datos = [];
                $datos["correo"] = $empleado->emple_Correo;
                $email = array($datos["correo"]);

                Mail::to($email)->queue(new CorreoEmpleadoMail($vinculacionN, $persona, $licencia));
                return json_encode(array("result" => true));
            }
            return response()->json(null, 403);
        }
    }
}
