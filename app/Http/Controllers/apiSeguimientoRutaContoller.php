<?php

namespace App\Http\Controllers;

use App\actividad_empleado;
use App\vinculacion_ruta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class apiSeguimientoRutaContoller extends Controller
{
    public function login(Request $request)
    {
        $nroD = $request->get('nroDocumento');
        $codigo = $request->get('codigo');
        $decode = base_convert(intval($codigo), 10, 36);
        $explode = explode("d", $decode);
        $idOrganizacion = $explode[1];
        $idVinculacion = $explode[0];
        $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('e.emple_id', 'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
            ->where('emple_nDoc', '=', $nroD)
            ->where('e.organi_id', '=', $idOrganizacion)
            ->where('e.emple_estado', '=', 1)
            ->get()->first();
        if ($empleado) {
            $vinculacion_ruta = vinculacion_ruta::where('id', '=', $idVinculacion)->get()->first();
            if ($vinculacion_ruta) {
                if ($vinculacion_ruta->hash == $codigo) {
                    // ? ACTIVIDADES DEL EMPLEADO -> POR AHORA ACTIVIDADES DE CONTROL REMOTO
                    $respuesta = [];
                    $actividad_empleado = actividad_empleado::where('idEmpleado', '=', $empleado->emple_id)->get();
                    foreach ($actividad_empleado as $act) {
                        $actividad = DB::table('actividad as a')
                            ->select('a.Activi_id', 'a.Activi_Nombre')
                            ->where('a.Activi_id', '=', $act->idActividad)
                            ->get()
                            ->first();
                        $actividad->estado = $act->estado;
                        array_push($respuesta, $actividad);
                    }
                    //? GUARDANDO MODELO DE CELULAR
                    $vinculacion_ruta->modelo = $request->get('modelo');
                    $vinculacion_ruta->save();
                    // ? GENERANDO TOKEN
                    $factory = JWTFactory::customClaims([
                        'sub' => env('API_id'),
                    ]);
                    $payload = $factory->make();
                    $token = JWTAuth::encode($payload);

                    return response()->json(array(
                        "idEmpleado" => $empleado->emple_id,
                        "empleado" => $empleado->perso_nombre . " " . $empleado->perso_apPaterno . " " . $empleado->perso_apMaterno,
                        "actividades" => $respuesta,
                        "token" => $token->get()
                    ), 200);
                } else {
                    return response()->json("codigo_erroneo", 400);
                }
            } else {
                return response()->json("sin_dispositivo", 400);
            }
        }
        return response()->json("empleado_no_exite", 400);
    }
}
