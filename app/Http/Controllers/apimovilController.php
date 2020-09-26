<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\dispositivos;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Support\Facades\DB;
class apimovilController extends Controller
{
    //ACTIVACION DISPOSITIVO
    public function apiActivacion(Request $request){
        $nroMovil=$request->nroMovil;
        $codigo=$request->codigo;
        $nombreDisp=$request->nombreDisp;

        $dispositivo=dispositivos::where('dispo_movil','=',$nroMovil)
        ->where('dispo_codigo','=',$codigo)->get()->first();

        if($dispositivo!=null){
            if($dispositivo->dispo_estado==2){
                return response()->json(array('status'=>400,'title' => 'Dispositivo ya verificado',
                'detail' => 'El Dispositivo ya fue verificado.'));
            } else{
                if($dispositivo->dispo_estado==1){
                    $dispositivosAc = dispositivos::findOrFail($dispositivo->idDispositivos);
                    $dispositivosAc->dispo_estado=2;
                    $dispositivosAc->dispo_codigoNombre=$nombreDisp;
                    $dispositivosAc->save();
                     $factory = JWTFactory::customClaims([
                        'sub' => env('API_id'),
                    ]);
                    $payload = $factory->make();
                    $token = JWTAuth::encode($payload);

                    $dispositivo_Controlador=DB::table('dispositivo_controlador as dc')
                    ->join('dispositivos as dis', 'dc.idDispositivos', '=', 'dis.idDispositivos')
                    ->join('controladores as con', 'dc.idControladores', '=', 'con.idControladores')
                    ->select('dis.dispo_descripUbicacion','dis.dispo_movil','dis.dispo_tSincro','dis.dispo_tMarca',
                    'con.cont_nombres','con.cont_ApPaterno','con.cont_ApMaterno')
                    ->where('dis.dispo_movil', '=',$nroMovil)
                    ->where('dis.dispo_codigo', '=', $codigo)
                    ->get();

                    $empleado = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id')
                    ->where('e.organi_id', '=',  $dispositivosAc->organi_id)
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('e.emple_id')
                    ->paginate();

                    return response()->json(array('status'=>200,"dispositivo" =>$dispositivosAc,"controladores" => $dispositivo_Controlador,
                    "empleados" => $empleado,"token" =>$token->get()));
                    /* return response()->json($dispositivo,200);     */
                }
            }
        }
        else{
            $dispositivo1=dispositivos::where('dispo_movil','=',$nroMovil)
            ->get()->first();
            if($dispositivo1!=null){
                return response()->json(array('status'=>400,'title' => 'Clave incorrecta',
                'detail' => 'Asegúrate de escribir la clave correcta'));

            } else{
                return response()->json(array('status'=>400,'title' => 'Dispositivo no existe',
                'detail' => 'Asegúrate de registrar el dispositivo desde la plataforma web'));


            }
        }
    }

    //LOGIN MOVIL
    public function loginMovil(Request $request){
        $nroMovil=$request->nroMovil;
        $codigo=$request->codigo;
        $codigoCon=$request->codigoCon;

        $dispositivo_Controlador=DB::table('dispositivo_controlador as dc')
        ->join('dispositivos as dis', 'dc.idDispositivos', '=', 'dis.idDispositivos')
        ->join('controladores as con', 'dc.idControladores', '=', 'con.idControladores')
        ->select('dis.dispo_descripUbicacion','dis.dispo_movil','dis.dispo_tSincro','dis.dispo_tMarca',
        'con.cont_nombres','con.cont_ApPaterno','con.cont_ApMaterno')
        ->where('dis.dispo_movil', '=',$nroMovil)
        ->where('dis.dispo_codigo', '=', $codigo)
        ->where('con.cont_codigo', '=',$codigoCon)
        ->get()->first();

       /*  if($dispositivo_Controlador!=null){
            return response()->json($dispositivo_Controlador,200);
        }
        else{
            return response()->json("Los datos no coinciden");
        } */

        return response()->json($dispositivo_Controlador,200);

    }
}
