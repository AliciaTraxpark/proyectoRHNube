<?php

namespace App\Http\Controllers;

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

            /* SI ESTA ACTIVA LA ORGANIZACION */
            if($estadoOrg==1){

                /* VERIFICAMOS SI ES USUARIO INVITADO */
                $invitado=invitado::where('user_Invitado','=', Auth::user()->id)
                        ->where('organi_id','=', session('sesionidorg'))
                       ->get()->first();

                /* SI ES USUARIO INVITADO  O ADMIN */
                if($invitado){
                    /* verificar si esta activo */
                    if($invitado->estado_condic==1){
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
                            ->select('uso.user_id as id','uso.rol_id','o.organi_id','o.organi_razonSocial')
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


                            return response()->json(array('status'=>200,"usuario" =>$usuario, "organizacion" => $organizacion,
                            "token" =>$token->get()));
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
                            ->select('uso.user_id as id','uso.rol_id','o.organi_id','o.organi_razonSocial')
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


                            return response()->json(array('status'=>200,"usuario" =>$usuario, "organizacion" => $organizacion,
                            "token" =>$token->get()));
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
                    ->select('uso.user_id as id','uso.rol_id','o.organi_id','o.organi_razonSocial')
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


                    return response()->json(array('status'=>200,"usuario" =>$usuario, "organizacion" => $organizacion,
                    "token" =>$token->get()));
                }

            } else{
                /* SI ORGANIZACION ESTA DESACTIVADA */
                Auth::logout();
                session()->forget('sesionidorg');
                session()->flush();
                return response()->json(array('status' => 400, 'title' => 'Organizacion desactivada',
                'detail' => 'La organizacion se encuentra desactivada'), 400);
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
