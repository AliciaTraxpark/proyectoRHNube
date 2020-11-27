<?php

namespace App\Http\Controllers;

use App\controladores;
use App\dispo_nombres;
use Illuminate\Http\Request;
use App\dispositivos;
use App\marcacion_movil;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SoporteApiMovil;
use App\Mail\SugerenciaApiMovil;
class apimovilController extends Controller
{
    //ACTIVACION DISPOSITIVO
    public function apiActivacion(Request $request){
        $nroMovil=$request->nroMovil;
        $codigo=$request->codigo;
        $nombreDisp=$request->nombreDisp;

        $dispositivo=dispositivos::where('dispo_movil','=',$nroMovil)
        ->where('dispo_codigo','=',$codigo)->where('dispo_estadoActivo','=',[1,2])->get()->first();

        if($dispositivo!=null){
            if($dispositivo->dispo_estado==2){
                if($dispositivo->dispo_codigo==$codigo){
                    $factory = JWTFactory::customClaims([
                        'sub' => env('API_id'),
                    ]);
                    $payload = $factory->make();
                    $token = JWTAuth::encode($payload);

                  /*   $dispositivo_Controlador=DB::table('dispositivo_controlador as dc')
                    ->join('dispositivos as dis', 'dc.idDispositivos', '=', 'dis.idDispositivos')
                    ->join('controladores as con', 'dc.idControladores', '=', 'con.idControladores')
                    ->select('dis.dispo_descripUbicacion','dis.dispo_movil','dis.dispo_tSincro','dis.dispo_tMarca',
                    'con.cont_nombres','con.cont_ApPaterno','con.cont_ApMaterno')
                    ->where('dis.dispo_movil', '=',$nroMovil)
                    ->where('dis.dispo_codigo', '=', $codigo)
                    ->get();
 */
                   $nombreDs=DB::table('dispo_nombres')
                   ->where('idDispositivos','=',$dispositivo->idDispositivos)
                   ->where('dispo_CodigoNombre','=',$nombreDisp)
                   ->get();
                   if($nombreDs->isEmpty()){
                    $dispo_nombre=new dispo_nombres();
                    $dispo_nombre->dispo_CodigoNombre=$nombreDisp;
                    $dispo_nombre->idDispositivos=$dispositivo->idDispositivos;
                    $dispo_nombre->save();
                    return response()->json(array('status'=>200,"dispositivo" =>$dispositivo,
                    "disponombre" =>$dispo_nombre,"token" =>$token->get()));

                   } else{
                    return response()->json(array('status'=>200,"dispositivo" =>$dispositivo,
                    "disponombre" =>$nombreDs,"token" =>$token->get()));
                   }



                } else{
                    return response()->json(array('status'=>400,'title' => 'Clave incorrecta',
                    'detail' => 'Asegúrate de escribir la clave correcta'),400);
                }

            } else{
                if($dispositivo->dispo_estado==1){
                    $dispositivosAc = dispositivos::findOrFail($dispositivo->idDispositivos);
                    $dispositivosAc->dispo_estado=2;

                    $dispositivosAc->save();

                    $dispo_nombre=new dispo_nombres();
                    $dispo_nombre->dispo_CodigoNombre=$nombreDisp;
                    $dispo_nombre->idDispositivos=$dispositivosAc->idDispositivos;
                    $dispo_nombre->save();
                     $factory = JWTFactory::customClaims([
                        'sub' => env('API_id'),
                    ]);
                    $payload = $factory->make();
                    $token = JWTAuth::encode($payload);

                   /*  $dispositivo_Controlador=DB::table('dispositivo_controlador as dc')
                    ->join('dispositivos as dis', 'dc.idDispositivos', '=', 'dis.idDispositivos')
                    ->join('controladores as con', 'dc.idControladores', '=', 'con.idControladores')
                    ->select('dis.dispo_descripUbicacion','dis.dispo_movil','dis.dispo_tSincro','dis.dispo_tMarca',
                    'con.cont_nombres','con.cont_ApPaterno','con.cont_ApMaterno')
                    ->where('dis.dispo_movil', '=',$nroMovil)
                    ->where('dis.dispo_codigo', '=', $codigo)
                    ->get();
 */


                    return response()->json(array('status'=>200,"dispositivo" =>$dispositivosAc,
                    "disponombre" =>$dispo_nombre,"token" =>$token->get()));
                    /* return response()->json($dispositivo,200);     */
                }
            }
        }
        else{
            $dispositivo1=dispositivos::where('dispo_movil','=',$nroMovil)
            ->get()->first();
            if($dispositivo1!=null){
                return response()->json(array('status'=>400,'title' => 'Clave incorrecta o dispositivo no activo',
                'detail' => 'Asegúrate de escribir la clave correcta'),400);

            } else{
                return response()->json(array('status'=>400,'title' => 'Dispositivo no existe',
                'detail' => 'Asegúrate de registrar el dispositivo desde la plataforma web'),400);


            }
        }
    }

    //LOGIN MOVIL
    public function EmpleadoMovil(Request $request){
        $organi_id=$request->organi_id;
        $empleado = DB::table('empleado as e')
        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'e.emple_id')

        ->where('e.organi_id', '=', $organi_id)
        ->where('e.emple_estado', '=', 1)
        ->where('e.asistencia_puerta', '=', 1)

        ->paginate();
        if($empleado!=null){
             return response()->json(array('status'=>200,"empleados"=>$empleado));
        }
        else{
            return response()->json(array('status'=>400,'title' => 'Empleados no encontrados',
            'detail' => 'No se encontro empleados relacionados con este dispositivo'),400);
        }


    }
    //CTIVIDADES
    public function ActivMovil(Request $request){
        $organi_id=$request->organi_id;
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

        if($actividades!=null){
             return response()->json(array('status'=>200,"actividades"=>$actividades));
        }
        else{
            return response()->json(array('status'=>400,'title' => 'Actividades no encontradas',
            'detail' => 'No se encontro actividades en esta organizacion'),400);
        }


    }
    public function controladoresAct(Request $request){
        $organi_id=$request->organi_id;
        $idDispo=$request->idDispo;

        $dispositivo_Controlador=DB::table('dispositivo_controlador as dc')
        ->join('controladores as con', 'dc.idControladores', '=', 'con.idControladores')
        ->join('dispositivos as dis', 'dc.idDispositivos', '=', 'dis.idDispositivos')
        ->select('con.idControladores','con.cont_codigo','con.cont_nombres','con.cont_ApPaterno','con.cont_ApMaterno','con.cont_correo',
        'con.cont_estado')
        ->where('dis.idDispositivos', '=',$idDispo)
        ->where('dis.organi_id', '=',$organi_id)
        ->where('con.cont_estado', '=',1)
        ->get();


        if($dispositivo_Controlador!=null){
             return response()->json(array('status'=>200,"controladores"=> $dispositivo_Controlador));
        }
        else{
            return response()->json(array('status'=>400,'title' => 'Controladores no encontrados',
            'detail' => 'No se encontro controladores relacionados con este dispositivo'),400);
        }


    }

    public function marcacionMovil(Request $request){
       /*  $organi_id=$request->organi_id;
        $tipoMarcacion=$request->tipoMarcacion;
        $fechaMarcacion=$request->fechaMarcacion;
        $idControlador=$request->idControlador;
        $idDisposi=$request->idDisposi;
        $idEmpleado=$request->idEmpleado;
        $idHoraEmp=$request->idHoraEmp; */
        foreach($request->all() as $req){
            if($req['tipoMarcacion']==1){
                $marcacion_movil=new marcacion_movil();
           /*  $marcacion_movil->marcaMov_tipo=$req['tipoMarcacion']; */
            $marcacion_movil->marcaMov_fecha= $req['fechaMarcacion'];
            $marcacion_movil->marcaMov_emple_id=$req['idEmpleado'];
            $marcacion_movil->controladores_idControladores=$req['idControlador'];
            $marcacion_movil->dispositivos_idDispositivos=$req['idDisposi'];
            $marcacion_movil->organi_id=$req['organi_id'];

            if(empty($req['idHoraEmp'])) {}
            else{
                $marcacion_movil->horarioEmp_id=$req['idHoraEmp'];
            }
            if(empty($req['latitud'])) {}
            else{
                $marcacion_movil->marca_latitud=$req['latitud'];
            }
            if(empty($req['longitud'])) {}
            else{
                $marcacion_movil->marca_longitud=$req['longitud'];
            }
            $marcacion_movil->save();
            } else{
                $marcacion_movil1 =DB::table('marcacion_movil as mv')
                ->where('mv.marcaMov_emple_id', '=',$req['idEmpleado'] )
                ->where('mv.marcaMov_salida', '=',null )
                ->where('mv.controladores_idControladores', '=',$req['idControlador'] )
                ->where('mv.dispositivos_idDispositivos', '=',$req['idDisposi'])
                ->get();

            if($marcacion_movil1->isEmpty()){
                $marcacion_movil=new marcacion_movil();
           /*  $marcacion_movil->marcaMov_tipo=$req['tipoMarcacion']; */
            $marcacion_movil->marcaMov_salida= $req['fechaMarcacion'];
            $marcacion_movil->marcaMov_emple_id=$req['idEmpleado'];
            $marcacion_movil->controladores_idControladores=$req['idControlador'];
            $marcacion_movil->dispositivos_idDispositivos=$req['idDisposi'];
            $marcacion_movil->organi_id=$req['organi_id'];

            if(empty($req['idHoraEmp'])) {}
            else{
                $marcacion_movil->horarioEmp_id=$req['idHoraEmp'];
            }
            if(empty($req['latitud'])) {}
            else{
                $marcacion_movil->marca_latitud=$req['latitud'];
            }
            if(empty($req['longitud'])) {}
            else{
                $marcacion_movil->marca_longitud=$req['longitud'];
            }
            $marcacion_movil->save();
            } else{
                $marcacion_movil =DB::table('marcacion_movil as mv')
                ->where('mv.marcaMov_emple_id', '=',$req['idEmpleado'] )
                ->where('mv.marcaMov_salida', '=',null )
                ->where('mv.controladores_idControladores', '=',$req['idControlador'] )
                ->where('mv.dispositivos_idDispositivos', '=',$req['idDisposi'])
                ->update(['mv.marcaMov_salida' => $req['fechaMarcacion']]);

            }
            }
            /*  */



        }


        if($marcacion_movil){
            return response()->json(array('status'=>200,'title' => 'Marcacion registrada correctamente',
            'detail' => 'Marcacion registrada correctamente en la base de datos'),200);
        }
        else{
            return response()->json(array('status'=>400,'title' => 'No se pudo registrar marcacion',
            'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'),400);
        }



    }
    public function empleadoHorario(Request $request){

        $organi_id=$request->organi_id;
        $fecha = Carbon::now();
        $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
        $empleado = DB::table('empleado as e')
        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
        ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc as dni',
         'e.emple_id as idempleado','h.horaI','h.horaF','h.horario_tolerancia as toleranciaIni',
         'h.horario_toleranciaF as toleranciaFin','he.horarioEmp_id','hd.start as diaActual',
         'he.fuera_horario as trabajafueraHor','he.horarioComp as horarioCompensable','he.horaAdic as horasAdicionales')
        ->join('horario_empleado as he','e.emple_id','=','he.empleado_emple_id')
        ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
        ->join('horario_dias as hd', 'he.horario_dias_id', '=', 'hd.id')
        ->where('e.organi_id', '=', $organi_id)
        ->where('e.emple_estado', '=', 1)
        ->where('e.asistencia_puerta', '=', 1)
        ->where('hd.start', '=',  $fechaHoy)
        ->paginate();
        if($empleado!=null){
             return response()->json(array('status'=>200,"empleados"=>$empleado));
        }
        else{
            return response()->json(array('status'=>400,'title' => 'Empleados no encontrados',
            'detail' => 'No se encontro empleados relacionados con este dispositivo'),400);
        }

    }

    public function ticketSoporte(Request $request)
    {
        $idControlador = $request->get('idControlador');
        $tipo = $request->get('tipo');
        $contenido = $request->get('contenido');
        $asunto = $request->get('asunto');
        $celular = $request->get('celular');


        $controlador = controladores::findOrFail($idControlador);
        if ($controlador) {
            $controlador = controladores::findOrFail($idControlador);
            $email = "info@rhnube.com.pe";

            if ($tipo == "soporte") {

                Mail::to($email)->queue(new SoporteApiMovil($contenido, $controlador, $asunto, $celular));
                return response()->json("Correo Enviado con éxito", 200);
            }
            if ($tipo == "sugerencia") {
                Mail::to($email)->queue(new SugerenciaApiMovil($contenido, $controlador, $asunto, $celular));
                return response()->json("Correo Enviado con éxito", 200);
            }
        }

        return response()->json("Controlador no se encuentra registrado.", 400);
    }
}
