<?php

namespace App\Http\Controllers;

use App\dispositivos;
use App\marcacion_biometrico;
use App\User;
use App\organizacion;
use App\invitado;
use App\usuario_organizacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Facades\JWTAuth;
class apiBiometricoController extends Controller
{
    //

    public function logueoBiometrico()
    {
        $credentials = $this->validate(request(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {

            /* OBTENEMOS EL ESTADO DE LA ORGANIZACION */
            $usuario_organizacion=usuario_organizacion::where('user_id','=', Auth::user()->id)->get()->first();
            $organiEstado=organizacion::where('organi_id',$usuario_organizacion->organi_id)->get()->first();
            $estadoOrg=$organiEstado->organi_estado;

            /* SETEAMOS ID DE LA ORGANIZACION */
            $vars=$usuario_organizacion->organi_id;
            session(['sesionidorg' => $vars]);

            /* VERIFICACOM CUANTAS ORGANIZACIONES TIENE */
            $comusuario_organizacion=usuario_organizacion::where('user_id','=', Auth::user()->id)->count();



            if($comusuario_organizacion>1) {

                /* SI TIENE MAS DE 2 ORGANIZACIONES */
                $factory = JWTFactory::customClaims([
                    'sub' => env('API_id'),
                ]);
                $payload = $factory->make();
                $token = JWTAuth::encode($payload);


                $usuario=DB::table('users as u')
                ->select('u.id','u.email',
                'p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno')
                ->where('u.id','=',Auth::user()->id)
                ->join('persona as p','u.perso_id','=','p.perso_id')
                ->get();


                foreach ($usuario as $tab) {
                    $organizacion=DB::table('usuario_organizacion as uso')
                    ->select('uso.usua_orga_id as idusuario_organizacion','uso.user_id as idusuario','uso.rol_id','o.organi_id','o.organi_razonSocial')
                    ->where('user_id','=',$tab->id)
                    ->join('users as u','uso.user_id','=','u.id')
                    ->join('organizacion as o','uso.organi_id','=','o.organi_id')
                    ->get();
                    $tab->organizacion =  $organizacion;
                }


                return response()->json(array(
                    "id" => $usuario[0]->id,
                    "email" => $usuario[0]->email,
                    "perso_nombre" => $usuario[0]->perso_nombre,
                    "perso_apPaterno" => $usuario[0]->perso_apPaterno,
                    "perso_apMaterno" => $usuario[0]->perso_apMaterno,
                    "organizacion" => $usuario[0]->organizacion,
                    'token' => $token->get()
                ), 200);

            }

            /* CUADNO SOLO TIENE UNA ORGANIZACION */

            else{

                /* VERIFICAMOS SI ES USUARIO INVITADO */
             $invitado=invitado::where('user_Invitado','=', Auth::user()->id)
             ->where('organi_id','=', session('sesionidorg'))
            ->get()->first();
             /* SI ESTA ACTIVA LA ORGANIZACION */
            if($estadoOrg==1){

                /* SI ES USUARIO INVITADO  O ADMIN */
                if($invitado){
                    /* verificar si esta activo */
                    if($invitado->estado_condic==1 && $invitado->estado==1){
                        /* VERIFICAR SI ES ADMIN */
                        if($invitado->rol_id==1){
                            $factory = JWTFactory::customClaims([
                                'sub' => env('API_id'),
                            ]);
                            $payload = $factory->make();
                            $token = JWTAuth::encode($payload);


                            $usuario=DB::table('users as u')
                            ->select('u.id','u.email',
                            'p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno')
                            ->where('u.id','=',Auth::user()->id)
                            ->join('persona as p','u.perso_id','=','p.perso_id')
                            ->get();

                            $organizacion=DB::table('usuario_organizacion as uso')
                            ->select('uso.usua_orga_id as idusuario_organizacion','uso.user_id as idusuario','uso.rol_id','o.organi_id','o.organi_razonSocial')
                            ->where('user_id','=',Auth::user()->id)
                            ->join('users as u','uso.user_id','=','u.id')
                            ->join('organizacion as o','uso.organi_id','=','o.organi_id')
                            ->get();

                            foreach ($organizacion as $tab) {
                                $biometricos=DB::table('dispositivos')
                                ->select('idDispositivos','dispo_descripUbicacion as descripcion','dispo_movil as ipPuerto',
                                'dispo_codigo as serie','version_firmware')
                                ->where('tipoDispositivo','=',3)
                                ->where('organi_id','=',$tab->organi_id)
                                ->get();

                                $tab->biometricos = $biometricos;
                            }

                            foreach ($usuario as $tab) {

                                $tab->organizacion =  $organizacion;
                            }

                            return response()->json(array(
                                "id" => $usuario[0]->id,
                                "email" => $usuario[0]->email,
                                "perso_nombre" => $usuario[0]->perso_nombre,
                                "perso_apPaterno" => $usuario[0]->perso_apPaterno,
                                "perso_apMaterno" => $usuario[0]->perso_apMaterno,
                                "organizacion" => $usuario[0]->organizacion,
                                'token' => $token->get()
                            ), 200);
                        }
                        else{
                            /* VERIFICAR SI TIENE PERMISO PARA EXTRACTOR */
                            if($invitado->extractorRH==1){
                              /*   dd('soy admin con reestricciones'); */
                              $factory = JWTFactory::customClaims([
                                'sub' => env('API_id'),
                            ]);
                            $payload = $factory->make();
                            $token = JWTAuth::encode($payload);


                            $usuario=DB::table('users as u')
                            ->select('u.id','u.email',
                            'p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno')
                            ->where('u.id','=',Auth::user()->id)
                            ->join('persona as p','u.perso_id','=','p.perso_id')
                            ->get();

                            $organizacion=DB::table('usuario_organizacion as uso')
                            ->select('uso.usua_orga_id as idusuario_organizacion','uso.user_id as idusuario','uso.rol_id','o.organi_id','o.organi_razonSocial')
                            ->where('user_id','=',Auth::user()->id)
                            ->join('users as u','uso.user_id','=','u.id')
                            ->join('organizacion as o','uso.organi_id','=','o.organi_id')
                            ->get();

                            foreach ($organizacion as $tab) {
                                $biometricos=DB::table('dispositivos')
                                ->select('idDispositivos','dispo_descripUbicacion as descripcion','dispo_movil as ipPuerto',
                                'dispo_codigo as serie','version_firmware')
                                ->where('tipoDispositivo','=',3)
                                ->where('organi_id','=',$tab->organi_id)
                                ->get();

                                $tab->biometricos = $biometricos;
                            }

                            foreach ($usuario as $tab) {

                                $tab->organizacion =  $organizacion;
                            }
                            return response()->json(array(
                                "id" => $usuario[0]->id,
                                "email" => $usuario[0]->email,
                                "perso_nombre" => $usuario[0]->perso_nombre,
                                "perso_apPaterno" => $usuario[0]->perso_apPaterno,
                                "perso_apMaterno" => $usuario[0]->perso_apMaterno,
                                "organizacion" => $usuario[0]->organizacion,
                                'token' => $token->get()
                            ), 200);
                            }
                            else{
                                Auth::logout();
                                session()->forget('sesionidorg');
                                session()->flush();
                                return response()->json(array('status' => 400, 'title' => 'Usuario no tiene permiso',
                                'detail' => 'Usuario no tiene permiso para extractor RHnube'), 400);
                            }

                        }
                    }
                    else{
                        /* INVITADO NO ACTIVO */
                        Auth::logout();
                        session()->forget('sesionidorg');
                        session()->flush();
                        return response()->json(array('status' => 400, 'title' => 'Usuario no activo',
                        'detail' => 'El usuario invitado esta desactivado'), 400);
                    }
                } else{
                    /* dd('soy admin'); */

                    $factory = JWTFactory::customClaims([
                        'sub' => env('API_id'),
                    ]);
                    $payload = $factory->make();
                    $token = JWTAuth::encode($payload);


                    $usuario=DB::table('users as u')
                    ->select('u.id','u.email',
                    'p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno')
                    ->where('u.id','=',Auth::user()->id)
                    ->join('persona as p','u.perso_id','=','p.perso_id')
                    ->get();

                    $organizacion=DB::table('usuario_organizacion as uso')
                    ->select('uso.usua_orga_id as idusuario_organizacion','uso.user_id as idusuario','uso.rol_id','o.organi_id','o.organi_razonSocial')
                    ->where('user_id','=',Auth::user()->id)
                    ->join('users as u','uso.user_id','=','u.id')
                    ->join('organizacion as o','uso.organi_id','=','o.organi_id')
                    ->get();

                    foreach ($organizacion as $tab) {
                        $biometricos=DB::table('dispositivos')
                        ->select('idDispositivos','dispo_descripUbicacion as descripcion','dispo_movil as ipPuerto',
                        'dispo_codigo as serie','version_firmware')
                        ->where('tipoDispositivo','=',3)
                        ->where('organi_id','=',$tab->organi_id)
                        ->get();

                        $tab->biometricos = $biometricos;
                    }

                    foreach ($usuario as $tab) {

                        $tab->organizacion =  $organizacion;
                    }
                    return response()->json(array(
                        "id" => $usuario[0]->id,
                        "email" => $usuario[0]->email,
                        "perso_nombre" => $usuario[0]->perso_nombre,
                        "perso_apPaterno" => $usuario[0]->perso_apPaterno,
                        "perso_apMaterno" => $usuario[0]->perso_apMaterno,
                        "organizacion" => $usuario[0]->organizacion,
                        'token' => $token->get()
                    ), 200);
                }

            } else{
                /* SI ORGANIZACION ESTA DESACTIVADA */
                Auth::logout();
                session()->forget('sesionidorg');
                session()->flush();
                return response()->json(array('status' => 400, 'title' => 'Organizacion desactivada',
                'detail' => 'La organizacion se encuentra desactivada'), 400);
            }

            }



        } else {
            /* CUANDO DATOS SON INVALIDOS */
            $user = User::where('email', '=', request()->get('email'))->get()->first();
            if ($user) {

                return response()->json(array('status' => 400, 'title' => 'Correo electronico o contraseña incorrecta',
                    'detail' => 'Datos incorrectos,correo electronico o contraseña incorrecta'), 400);
            } else {

                return response()->json(array('status' => 400, 'title' => 'Usuario no registrado',
                    'detail' => 'No se encontro usuario registrado con este Email'), 400);
            }
        }
    }

    public function elegirOrganizacionBio(Request $request){

        $idUsuarioOrgani=$request->idusuario_organizacion;
        $usuario_organizacion=DB::table('usuario_organizacion as uso')
        ->select('uso.usua_orga_id as idusuario_organizacion','uso.user_id as idusuario','uso.rol_id','o.organi_id','o.organi_razonSocial','O.organi_estado')
        ->where('uso.usua_orga_id','=',$idUsuarioOrgani)
        ->join('users as u','uso.user_id','=','u.id')
        ->join('organizacion as o','uso.organi_id','=','o.organi_id')
        ->get()->first();

        /* PRIMERO VERIFICAMOS QUE LA ORGANIZACION ESTE ACTIVA */
        if($usuario_organizacion->organi_estado==1){
            /*  */
            $invitado=invitado::where('user_Invitado','=', $usuario_organizacion->idusuario)
            ->where('organi_id','=', $usuario_organizacion->organi_id)
           ->get()->first();
            if($invitado){
                /* verificar si esta activo */
                if($invitado->estado_condic==1 && $invitado->estado==1){
                    /* VERIFICAR SI ES ADMIN */
                    if($invitado->rol_id==1){

                            $biometricos=DB::table('dispositivos')
                            ->select('idDispositivos','dispo_descripUbicacion as descripcion','dispo_movil as ipPuerto',
                            'dispo_codigo as serie','version_firmware')
                            ->where('tipoDispositivo','=',3)
                            ->where('organi_id','=', $usuario_organizacion->organi_id)
                            ->get();
                            return response()->json(
                                $biometricos
                            , 200);
                    }
                    else{
                        /* VERIFICAR SI TIENE PERMISO PARA EXTRACTOR */
                        if($invitado->extractorRH==1){
                          /*   dd('soy admin con reestricciones'); */
                          $biometricos=DB::table('dispositivos')
                          ->select('idDispositivos','dispo_descripUbicacion as descripcion','dispo_movil as ipPuerto',
                          'dispo_codigo as serie','version_firmware')
                          ->where('tipoDispositivo','=',3)
                          ->where('organi_id','=', $usuario_organizacion->organi_id)
                          ->get();
                          return response()->json(
                            $biometricos
                        , 200);
                        }
                        else{
                            Auth::logout();
                            session()->forget('sesionidorg');
                            session()->flush();
                            return response()->json(array('status' => 400, 'title' => 'Usuario no tiene permiso',
                            'detail' => 'Usuario no tiene permiso para extractor RHnube'), 400);
                        }

                    }
                }
                else{
                    /* INVITADO NO ACTIVO */
                    Auth::logout();
                    session()->forget('sesionidorg');
                    session()->flush();
                    return response()->json(array('status' => 400, 'title' => 'Usuario no activo',
                    'detail' => 'El usuario invitado esta desactivado'), 400);
                }
            } else{
                /* dd('soy admin'); */

                $biometricos=DB::table('dispositivos')
                            ->select('idDispositivos','dispo_descripUbicacion as descripcion','dispo_movil as ipPuerto',
                            'dispo_codigo as serie','version_firmware')
                            ->where('tipoDispositivo','=',3)
                            ->where('organi_id','=', $usuario_organizacion->organi_id)
                            ->get();
                        return response()->json(
                            $biometricos
                        , 200);
            }
            /*  */
        }
        else{
            return response()->json(array('status' => 400, 'title' => 'Organizacion desactivada',
            'detail' => 'La organizacion se encuentra desactivada'), 400);
        }

       /*  if($usuario_organizacion->rol_id==3) {
           dd('soy inv');
        }
        else{
            dd('soy admin');
        } */

    }


    public function editarDispositivo(Request $request){

        $idDispositivos=$request->idDispositivos;
        $descripcion=$request->descripcion;
        $ipPuerto=$request->ipPuerto;
        $serie=$request->serie;
        $version_firmware=$request->version_firmware;

        $dispositivo = dispositivos::findOrFail($idDispositivos);
        if(empty($request->descripcion)) {}
            else{
                $dispositivo->dispo_descripUbicacion=$descripcion;
            }

            if(empty($request->ipPuerto)) {}
            else{
                $dispositivo->dispo_movil=$ipPuerto;
            }

            if(empty($request->serie)) {}
            else{
                if($dispositivo->dispo_codigo==null){
                    $dispositivo->dispo_codigo=$serie;
                } else{

                }

            }

            if(empty($request->version_firmware)) {}
            else{
                $dispositivo->version_firmware=$version_firmware;
            }
        $dispositivo->save();

        if($dispositivo){
            return response()->json(array('status'=>200,'title' => 'Dispositivo editado correctamente',
            'detail' => 'Dispositivo editado correctamente en la base de datos'),200);
        }
        else{
            return response()->json(array('status'=>400,'title' => 'No se pudo editar dispositivo',
            'detail' => 'No se pudo editar dispositivo, compruebe que los datos sean validos'),400);
        }


    }


    public function empleadosBiometrico(Request $request){

        $idUsuarioOrgani=$request->idusuario_organizacion;
        $usuario_organizacion=DB::table('usuario_organizacion as uso')
        ->select('uso.usua_orga_id as idusuario_organizacion','uso.user_id as idusuario','uso.rol_id','o.organi_id','o.organi_razonSocial','O.organi_estado')
        ->where('uso.usua_orga_id','=',$idUsuarioOrgani)
        ->join('users as u','uso.user_id','=','u.id')
        ->join('organizacion as o','uso.organi_id','=','o.organi_id')
        ->get()->first();

        /* PRIMERO VER SI ES INVITADO O NO */
        if($usuario_organizacion->rol_id==3){

           /* SI ES INVITADO VER PERMISOS */
           $invitadod = DB::table('invitado')
            ->where('user_Invitado', '=',  $usuario_organizacion->idusuario)
            ->where('organi_id', '=',  $usuario_organizacion->organi_id)
            ->where('rol_id', '=', 3)
            ->get()->first();

            if ($invitadod->verTodosEmps == 1) {
                /* CUANDO TIENE TODOS LOS EMPELADOS */
                $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc as dni',
             'e.emple_id as idempleado')
            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
            ->where('e.emple_estado', '=', 1)
            ->where('e.asistencia_puerta', '=', 1)
            ->paginate();
            }
            else{
                /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                ->where('invem.idinvitado', '=',  $invitadod->idinvitado)
                ->where('invem.area_id', '=', null)
                ->where('invem.emple_id', '!=', null)
                ->get()->first();
                /* empleados x id */
                if ($invitado_empleadoIn != null) {
                    $empleado = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                    ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc as dni',
                     'e.emple_id as idempleado')
                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                    ->where('e.emple_estado', '=', 1)
                    ->where('e.asistencia_puerta', '=', 1)
                    ->where('invi.estado', '=', 1)
                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                    ->paginate();
                }
                else{
                    /* EMPLEADOS POR AREA */
                    $empleado = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                    ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc as dni',
                     'e.emple_id as idempleado')
                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                    ->where('e.emple_estado', '=', 1)
                    ->where('e.asistencia_puerta', '=', 1)
                    ->where('invi.estado', '=', 1)
                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)

                    ->paginate();
                }
            }

        }
        else
        {
            $empleado = DB::table('empleado as e')
            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
            ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc as dni',
             'e.emple_id as idempleado')
            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
            ->where('e.emple_estado', '=', 1)
            ->where('e.asistencia_puerta', '=', 1)
            ->paginate();
        }

        if($empleado!=null){
            return response()->json(array("empleados"=>$empleado));
       }
       else{
           return response()->json(array('status'=>400,'title' => 'Empleados no encontrados',
           'detail' => 'No se encontro empleados relacionados con este dispositivo'),400);
       }
    }
    public function marcacionBiometrico(Request $request)
    {
        $fechaHoy = Carbon::now('America/Lima');
        $horaActual = $fechaHoy->isoFormat('YYYY-MM-DD HH:mm:ss');

        foreach ($request->all() as $req) {

            $marcacion_biometrico = new marcacion_biometrico();
            $marcacion_biometrico->tipoMarcacion = $req['tipoMarcacion'];
            /* VALIDANDO FECHA  */
            if (Carbon::create($req['fechaMarcacion'])->gt(Carbon::create($horaActual))) {
                return response()->json(array('status' => 500, 'title' => 'No se pudo validar fecha',
                    'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 500);
            } else {
                $marcacion_biometrico->fechaMarcacion = $req['fechaMarcacion'];
            }

            $marcacion_biometrico->idEmpleado = $req['idEmpleado'];
            $marcacion_biometrico->idDisposi = $req['idDisposi'];
            /* VALIDANDO EMPLEADOIIIII */
            $empleados = DB::table('empleado as e')
                ->join('organizacion as or', 'e.organi_id', '=', 'or.organi_id')
                ->where('e.emple_id', '=', $req['idEmpleado'])
                ->get()->first();
            if ($empleados) {
                $marcacion_biometrico->organi_id = $empleados->organi_id;

                if (empty($req['idHoraEmp'])) {} else {
                    $marcacion_biometrico->idHoraEmp = $req['idHoraEmp'];
                }

                $marcacion_biometrico->save();
            } else {
                return response()->json(array('status' => 500, 'title' => 'No se pudo encontrar empleado',
                    'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 500);
            }

        }

        if ($marcacion_biometrico) {
            return response()->json(array('status' => 200, 'title' => 'Marcacion registrada correctamente',
                'detail' => 'Marcacion registrada correctamente en la base de datos'), 200);
        } else {
            return response()->json(array('status' => 400, 'title' => 'No se pudo registrar marcacion',
                'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 400);
        }
    }
}
