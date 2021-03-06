<?php

namespace App\Http\Controllers;

use App\dispositivos;
use App\invitado;
use App\marcacion_puerta;
use App\organizacion;
use App\plantilla_empleadobio;
use App\User;
use App\usuario_organizacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

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
            $usuario_organizacion = usuario_organizacion::where('user_id', '=', Auth::user()->id)->get()->first();
            $organiEstado = organizacion::where('organi_id', $usuario_organizacion->organi_id)->get()->first();
            $estadoOrg = $organiEstado->organi_estado;

            /* SETEAMOS ID DE LA ORGANIZACION */
            $vars = $usuario_organizacion->organi_id;
            session(['sesionidorg' => $vars]);

            /* VERIFICACOM CUANTAS ORGANIZACIONES TIENE */
            $comusuario_organizacion = usuario_organizacion::where('user_id', '=', Auth::user()->id)->count();

            if ($comusuario_organizacion > 1) {

                /* SI TIENE MAS DE 2 ORGANIZACIONES */
                $factory = JWTFactory::customClaims([
                    'sub' => env('API_id'),
                ]);
                $payload = $factory->make();
                $token = JWTAuth::encode($payload);

                $usuario = DB::table('users as u')
                    ->select('u.id', 'u.email',
                        'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                    ->where('u.id', '=', Auth::user()->id)
                    ->join('persona as p', 'u.perso_id', '=', 'p.perso_id')
                    ->get();

                foreach ($usuario as $tab) {
                    $organizacion = DB::table('usuario_organizacion as uso')
                        ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario',
                            'uso.rol_id', 'r.rol_nombre', 'o.organi_id', 'o.organi_razonSocial', 'o.organi_ruc', 'o.created_at as fechaRegistro', 'o.organi_tipo', 'o.sinc_Biometrico')
                        ->where('user_id', '=', $tab->id)
                        ->where('o.organi_estado', '=',1)
                        ->join('users as u', 'uso.user_id', '=', 'u.id')
                        ->join('rol as r', 'uso.rol_id', '=', 'r.rol_id')
                        ->join('organizacion as o', 'uso.organi_id', '=', 'o.organi_id')
                        ->get();
                        $OrganizacionesReg=new Collection();
                        if($organizacion->isNotEmpty()){
                            foreach($organizacion as $organizaciones ){
                                if($organizaciones->rol_id==1){
                                    $OrganizacionesReg->push($organizaciones) ;
                                }
                                else{
                                    $invitado = invitado::where('user_Invitado', '=', Auth::user()->id)
                                    ->where('organi_id', '=', $organizaciones->organi_id)
                                    ->get()->first();
                                    if($invitado){
                                        if ($invitado->estado_condic == 1 && $invitado->estado == 1) {
                                            if ($invitado->extractorRH == 1) {
                                                $OrganizacionesReg->push($organizaciones) ;
                                            }
                                        }
                                    }

                                }
                            }
                        }
                        $tab->organizacion = $OrganizacionesReg;

                }

                return response()->json(array(
                    "id" => $usuario[0]->id,
                    "email" => $usuario[0]->email,
                    "perso_nombre" => $usuario[0]->perso_nombre,
                    "perso_apPaterno" => $usuario[0]->perso_apPaterno,
                    "perso_apMaterno" => $usuario[0]->perso_apMaterno,
                    "organizacion" => $usuario[0]->organizacion,
                    'token' => $token->get(),
                ), 200);

            }

            /* CUADNO SOLO TIENE UNA ORGANIZACION */

            else {

                /* VERIFICAMOS SI ES USUARIO INVITADO */
                $invitado = invitado::where('user_Invitado', '=', Auth::user()->id)
                    ->where('organi_id', '=', session('sesionidorg'))
                    ->get()->first();
                /* SI ESTA ACTIVA LA ORGANIZACION */
                if ($estadoOrg == 1) {

                    /* SI ES USUARIO INVITADO  O ADMIN */
                    if ($invitado) {
                        /* verificar si esta activo */
                        if ($invitado->estado_condic == 1 && $invitado->estado == 1) {
                            /* VERIFICAR SI ES ADMIN */
                            if ($invitado->rol_id == 1) {
                                $factory = JWTFactory::customClaims([
                                    'sub' => env('API_id'),
                                ]);
                                $payload = $factory->make();
                                $token = JWTAuth::encode($payload);

                                $usuario = DB::table('users as u')
                                    ->select('u.id', 'u.email',
                                        'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                                    ->where('u.id', '=', Auth::user()->id)
                                    ->join('persona as p', 'u.perso_id', '=', 'p.perso_id')
                                    ->get();

                                $organizacion = DB::table('usuario_organizacion as uso')
                                    ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario',
                                        'uso.rol_id', 'r.rol_nombre', 'o.organi_id', 'o.organi_razonSocial', 'o.organi_ruc', 'o.created_at as fechaRegistro', 'o.organi_tipo', 'o.sinc_Biometrico')
                                    ->where('user_id', '=', Auth::user()->id)
                                    ->where('o.organi_estado', '=',1)
                                    ->join('users as u', 'uso.user_id', '=', 'u.id')
                                    ->join('rol as r', 'uso.rol_id', '=', 'r.rol_id')
                                    ->join('organizacion as o', 'uso.organi_id', '=', 'o.organi_id')
                                    ->get();

                                foreach ($usuario as $tab) {

                                    $tab->organizacion = $organizacion;
                                }

                                return response()->json(array(
                                    "id" => $usuario[0]->id,
                                    "email" => $usuario[0]->email,
                                    "perso_nombre" => $usuario[0]->perso_nombre,
                                    "perso_apPaterno" => $usuario[0]->perso_apPaterno,
                                    "perso_apMaterno" => $usuario[0]->perso_apMaterno,
                                    "organizacion" => $usuario[0]->organizacion,
                                    'token' => $token->get(),
                                ), 200);
                            } else {
                                /* VERIFICAR SI TIENE PERMISO PARA EXTRACTOR */
                                if ($invitado->extractorRH == 1) {
                                    /*   dd('soy admin con reestricciones'); */
                                    $factory = JWTFactory::customClaims([
                                        'sub' => env('API_id'),
                                    ]);
                                    $payload = $factory->make();
                                    $token = JWTAuth::encode($payload);

                                    $usuario = DB::table('users as u')
                                        ->select('u.id', 'u.email',
                                            'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                                        ->where('u.id', '=', Auth::user()->id)
                                        ->join('persona as p', 'u.perso_id', '=', 'p.perso_id')
                                        ->get();

                                    $organizacion = DB::table('usuario_organizacion as uso')
                                        ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario',
                                            'uso.rol_id', 'r.rol_nombre', 'o.organi_id', 'o.organi_razonSocial', 'o.organi_ruc', 'o.created_at as fechaRegistro', 'o.organi_tipo', 'o.sinc_Biometrico')
                                        ->where('user_id', '=', Auth::user()->id)
                                        ->where('o.organi_estado', '=',1)
                                        ->join('users as u', 'uso.user_id', '=', 'u.id')
                                        ->join('rol as r', 'uso.rol_id', '=', 'r.rol_id')
                                        ->join('organizacion as o', 'uso.organi_id', '=', 'o.organi_id')
                                        ->get();

                                    foreach ($usuario as $tab) {

                                        $tab->organizacion = $organizacion;
                                    }
                                    return response()->json(array(
                                        "id" => $usuario[0]->id,
                                        "email" => $usuario[0]->email,
                                        "perso_nombre" => $usuario[0]->perso_nombre,
                                        "perso_apPaterno" => $usuario[0]->perso_apPaterno,
                                        "perso_apMaterno" => $usuario[0]->perso_apMaterno,
                                        "organizacion" => $usuario[0]->organizacion,
                                        'token' => $token->get(),
                                    ), 200);
                                } else {
                                    Auth::logout();
                                    session()->forget('sesionidorg');
                                    session()->flush();
                                    return response()->json(array('status' => 400, 'title' => 'Usuario no tiene permiso para extractor RH nube',
                                        'detail' => 'Usuario no tiene permiso para extractor RH nube'), 400);
                                }

                            }
                        } else {
                            /* INVITADO NO ACTIVO */
                            Auth::logout();
                            session()->forget('sesionidorg');
                            session()->flush();
                            return response()->json(array('status' => 400, 'title' => 'Usuario no activo',
                                'detail' => 'El usuario invitado esta desactivado'), 400);
                        }
                    } else {
                        /* dd('soy admin'); */

                        $factory = JWTFactory::customClaims([
                            'sub' => env('API_id'),
                        ]);
                        $payload = $factory->make();
                        $token = JWTAuth::encode($payload);

                        $usuario = DB::table('users as u')
                            ->select('u.id', 'u.email',
                                'p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno')
                            ->where('u.id', '=', Auth::user()->id)
                            ->join('persona as p', 'u.perso_id', '=', 'p.perso_id')
                            ->get();

                        $organizacion = DB::table('usuario_organizacion as uso')
                            ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario',
                                'uso.rol_id', 'r.rol_nombre', 'o.organi_id', 'o.organi_razonSocial', 'o.organi_ruc', 'o.created_at as fechaRegistro', 'o.organi_tipo', 'o.sinc_Biometrico')
                            ->where('user_id', '=', Auth::user()->id)
                            ->where('o.organi_estado', '=',1)
                            ->join('rol as r', 'uso.rol_id', '=', 'r.rol_id')
                            ->join('users as u', 'uso.user_id', '=', 'u.id')
                            ->join('organizacion as o', 'uso.organi_id', '=', 'o.organi_id')
                            ->get();

                        foreach ($usuario as $tab) {

                            $tab->organizacion = $organizacion;
                        }
                        return response()->json(array(
                            "id" => $usuario[0]->id,
                            "email" => $usuario[0]->email,
                            "perso_nombre" => $usuario[0]->perso_nombre,
                            "perso_apPaterno" => $usuario[0]->perso_apPaterno,
                            "perso_apMaterno" => $usuario[0]->perso_apMaterno,
                            "organizacion" => $usuario[0]->organizacion,
                            'token' => $token->get(),
                        ), 200);
                    }

                } else {
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

                return response()->json(array('status' => 400, 'title' => 'Verifique sus credenciales.',
                    'detail' => 'Verifique sus credenciales.'), 400);
            } else {

                return response()->json(array('status' => 400, 'title' => 'Usuario no registrado',
                    'detail' => 'No se encontr?? usuario registrado con este correo'), 400);
            }
        }
    }

    public function elegirOrganizacionBio(Request $request)
    {

        $idUsuarioOrgani = $request->idusuario_organizacion;
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario', 'uso.rol_id', 'o.organi_id', 'o.organi_razonSocial', 'O.organi_estado')
            ->where('uso.usua_orga_id', '=', $idUsuarioOrgani)
            ->join('users as u', 'uso.user_id', '=', 'u.id')
            ->join('organizacion as o', 'uso.organi_id', '=', 'o.organi_id')
            ->get()->first();

        /* PRIMERO VERIFICAMOS QUE LA ORGANIZACION ESTE ACTIVA */
        if ($usuario_organizacion->organi_estado == 1) {
            /*  */
            $invitado = invitado::where('user_Invitado', '=', $usuario_organizacion->idusuario)
                ->where('organi_id', '=', $usuario_organizacion->organi_id)
                ->get()->first();
            if ($invitado) {
                /* verificar si esta activo */
                if ($invitado->estado_condic == 1 && $invitado->estado == 1) {
                    /* VERIFICAR SI ES ADMIN */
                    if ($invitado->rol_id == 1) {

                        $biometricos = DB::table('dispositivos')
                            ->leftJoin('tipo_biometrico as tipBi','dispositivos.idtipo_biometrico', '=', 'tipBi.idtipo_biometrico')
                            ->select('idDispositivos', 'dispo_descripUbicacion as descripcion', 'dispo_movil as ipPuerto',
                                'dispo_codigo as serie', 'version_firmware', 'dispo_todosEmp', 'dispo_porEmp','dispo_Data as diasData','tipBi.cod_tipo as tipoBiometrico')
                            ->where('tipoDispositivo', '=', 3)
                            ->where('dispo_estadoActivo', '=', 1)
                            ->where('organi_id', '=', $usuario_organizacion->organi_id)
                            ->get();
                        foreach ($biometricos as $biometricosT) {
                            if ($biometricosT->dispo_todosEmp == 1) {
                                $empleados = DB::table('empleado as e')
                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->distinct('e.emple_id')
                                    ->count();
                                $biometricosT->totalEmpleados = $empleados;
                                unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);
                            } else {
                                /* SI ES POR EMPLEADOS PERSONALIZADSO */
                                if ($biometricosT->dispo_porEmp == 1) {
                                    $dispositivosBiEmp = DB::table('dispositivo_empleado as de')
                                        ->join('dispositivos as di', 'de.idDispositivos', '=', 'di.idDispositivos')
                                        ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                        ->where('de.idDispositivos', '=', $biometricosT->idDispositivos)
                                        ->where('di.dispo_estadoActivo', '=', 1)
                                        ->where('de.estado', '=', 1)
                                        ->where('e.emple_estado', '=', 1)
                                        ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                        ->count();
                                    $biometricosT->totalEmpleados = $dispositivosBiEmp;
                                    unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);
                                }

                                /* SI ES POR AREAS */
                                else {
                                    $dispositivosBiAr = DB::table('empleado as e')
                                        ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                                        ->join('dispositivos as di', 'da.idDispositivos', '=', 'di.idDispositivos')
                                        ->where('da.idDispositivos', '=', $biometricosT->idDispositivos)
                                        ->where('di.dispo_estadoActivo', '=', 1)
                                        ->where('da.estado', '=', 1)
                                        ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                        ->where('e.emple_estado', '=', 1)
                                        ->where('e.asistencia_puerta', '=', 1)
                                        ->count();

                                    $biometricosT->totalEmpleados = $dispositivosBiAr;
                                    unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);

                                }
                            }

                        }
                        return response()->json(
                            $biometricos
                            , 200);
                    } else {
                        /* VERIFICAR SI TIENE PERMISO PARA EXTRACTOR */
                        if ($invitado->extractorRH == 1) {
                            /*   dd('soy admin con reestricciones'); */
                            $biometricos = DB::table('dispositivos')
                            ->leftJoin('tipo_biometrico as tipBi','dispositivos.idtipo_biometrico', '=', 'tipBi.idtipo_biometrico')
                                ->select('idDispositivos', 'dispo_descripUbicacion as descripcion', 'dispo_movil as ipPuerto',
                                    'dispo_codigo as serie', 'version_firmware', 'dispo_todosEmp', 'dispo_porEmp','dispo_Data  as diasData','tipBi.cod_tipo as tipoBiometrico')
                                ->where('tipoDispositivo', '=', 3)
                                ->where('dispo_estadoActivo', '=', 1)
                                ->where('organi_id', '=', $usuario_organizacion->organi_id)
                                ->get();
                            if ($invitado->verTodosEmps == 1) {

                                foreach ($biometricos as $biometricosT) {
                                    if ($biometricosT->dispo_todosEmp == 1) {
                                        $empleados = DB::table('empleado as e')
                                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                            ->where('e.emple_estado', '=', 1)
                                            ->where('e.asistencia_puerta', '=', 1)
                                            ->distinct('e.emple_id')
                                            ->count();
                                        $biometricosT->totalEmpleados = $empleados;
                                        unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);
                                    } else {
                                        /* SI ES POR EMPLEADOS PERSONALIZADSO */
                                        if ($biometricosT->dispo_porEmp == 1) {
                                            $dispositivosBiEmp = DB::table('dispositivo_empleado as de')
                                                ->join('dispositivos as di', 'de.idDispositivos', '=', 'di.idDispositivos')
                                                ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                                ->where('de.idDispositivos', '=', $biometricosT->idDispositivos)
                                                ->where('di.dispo_estadoActivo', '=', 1)
                                                ->where('de.estado', '=', 1)
                                                ->where('e.emple_estado', '=', 1)
                                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                                ->count();
                                            $biometricosT->totalEmpleados = $dispositivosBiEmp;
                                            unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);
                                        }

                                        /* SI ES POR AREAS */
                                        else {
                                            $dispositivosBiAr = DB::table('empleado as e')
                                                ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                                                ->join('dispositivos as di', 'da.idDispositivos', '=', 'di.idDispositivos')
                                                ->where('da.idDispositivos', '=', $biometricosT->idDispositivos)
                                                ->where('di.dispo_estadoActivo', '=', 1)
                                                ->where('da.estado', '=', 1)
                                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                                ->where('e.emple_estado', '=', 1)
                                                ->where('e.asistencia_puerta', '=', 1)
                                                ->count();

                                            $biometricosT->totalEmpleados = $dispositivosBiAr;
                                            unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);

                                        }
                                    }

                                }
                                return response()->json(
                                    $biometricos
                                    , 200);
                            } else {
                                if ($invitado->empleado == 1) {
                                    foreach ($biometricos as $biometricosT) {
                                        if ($biometricosT->dispo_todosEmp == 1) {
                                            $empleados = DB::table('empleado as e')
                                                ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                                ->where('e.emple_estado', '=', 1)
                                                ->where('e.asistencia_puerta', '=', 1)
                                                ->distinct('e.emple_id')
                                                ->where('invi.estado', '=', 1)
                                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                                ->count();
                                            $biometricosT->totalEmpleados = $empleados;
                                            unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);
                                        } else {
                                            /* SI ES POR EMPLEADOS PERSONALIZADSO */
                                            if ($biometricosT->dispo_porEmp == 1) {
                                                $dispositivosBiEmp = DB::table('dispositivo_empleado as de')
                                                    ->join('dispositivos as di', 'de.idDispositivos', '=', 'di.idDispositivos')
                                                    ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                                    ->where('de.idDispositivos', '=', $biometricosT->idDispositivos)
                                                    ->where('invi.estado', '=', 1)
                                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                                    ->where('di.dispo_estadoActivo', '=', 1)
                                                    ->where('de.estado', '=', 1)
                                                    ->where('e.emple_estado', '=', 1)
                                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                                    ->count();
                                                $biometricosT->totalEmpleados = $dispositivosBiEmp;
                                                unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);
                                            }

                                            /* SI ES POR AREAS */
                                            else {
                                                $dispositivosBiAr = DB::table('empleado as e')
                                                    ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                                                    ->join('dispositivos as di', 'da.idDispositivos', '=', 'di.idDispositivos')
                                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                                    ->where('da.idDispositivos', '=', $biometricosT->idDispositivos)
                                                    ->where('di.dispo_estadoActivo', '=', 1)
                                                    ->where('da.estado', '=', 1)
                                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                                    ->where('e.emple_estado', '=', 1)
                                                    ->where('e.asistencia_puerta', '=', 1)
                                                    ->where('invi.estado', '=', 1)
                                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)

                                                    ->count();

                                                $biometricosT->totalEmpleados = $dispositivosBiAr;
                                                unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);

                                            }
                                        }

                                    }
                                    return response()->json(
                                        $biometricos
                                        , 200);
                                } else {
                                    foreach ($biometricos as $biometricosT) {
                                        if ($biometricosT->dispo_todosEmp == 1) {
                                            $empleados = DB::table('empleado as e')
                                                ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                                ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                                ->where('e.emple_estado', '=', 1)
                                                ->where('e.asistencia_puerta', '=', 1)
                                                ->where('invi.estado', '=', 1)
                                                ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                                ->distinct('e.emple_id')
                                                ->count();
                                            $biometricosT->totalEmpleados = $empleados;
                                            unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);
                                        } else {
                                            /* SI ES POR EMPLEADOS PERSONALIZADSO */
                                            if ($biometricosT->dispo_porEmp == 1) {
                                                $dispositivosBiEmp = DB::table('dispositivo_empleado as de')
                                                    ->join('dispositivos as di', 'de.idDispositivos', '=', 'di.idDispositivos')
                                                    ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                                    ->where('de.idDispositivos', '=', $biometricosT->idDispositivos)
                                                    ->where('di.dispo_estadoActivo', '=', 1)
                                                    ->where('de.estado', '=', 1)
                                                    ->where('e.emple_estado', '=', 1)
                                                    ->where('invi.estado', '=', 1)
                                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                                    ->count();
                                                $biometricosT->totalEmpleados = $dispositivosBiEmp;
                                                unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);
                                            }

                                            /* SI ES POR AREAS */
                                            else {
                                                $dispositivosBiAr = DB::table('empleado as e')
                                                    ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                                                    ->join('dispositivos as di', 'da.idDispositivos', '=', 'di.idDispositivos')
                                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                                    ->where('da.idDispositivos', '=', $biometricosT->idDispositivos)
                                                    ->where('di.dispo_estadoActivo', '=', 1)
                                                    ->where('da.estado', '=', 1)
                                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                                    ->where('e.emple_estado', '=', 1)
                                                    ->where('e.asistencia_puerta', '=', 1)
                                                    ->where('invi.estado', '=', 1)
                                                    ->where('invi.idinvitado', '=', $invitado->idinvitado)
                                                    ->count();

                                                $biometricosT->totalEmpleados = $dispositivosBiAr;
                                                unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);

                                            }
                                        }

                                    }
                                    return response()->json(
                                        $biometricos
                                        , 200);

                                }
                            }

                        } else {
                            Auth::logout();
                            session()->forget('sesionidorg');
                            session()->flush();
                            return response()->json(array('status' => 400, 'title' => 'Usuario no tiene permiso para extractor RH nube',
                                'detail' => 'Usuario no tiene permiso para extractor RH nube'), 400);
                        }

                    }
                } else {
                    /* INVITADO NO ACTIVO */
                    Auth::logout();
                    session()->forget('sesionidorg');
                    session()->flush();
                    return response()->json(array('status' => 400, 'title' => 'Usuario no activo',
                        'detail' => 'El usuario invitado esta desactivado'), 400);
                }
            } else {
                /* dd('soy admin'); */

                $biometricos = DB::table('dispositivos')
                    ->leftJoin('tipo_biometrico as tipBi','dispositivos.idtipo_biometrico', '=', 'tipBi.idtipo_biometrico')
                    ->select('idDispositivos', 'dispo_descripUbicacion as descripcion', 'dispo_movil as ipPuerto',
                        'dispo_codigo as serie', 'version_firmware', 'dispo_todosEmp', 'dispo_porEmp','dispo_Data  as diasData','tipBi.cod_tipo as tipoBiometrico')
                    ->where('tipoDispositivo', '=', 3)
                    ->where('dispo_estadoActivo', '=', 1)
                    ->where('organi_id', '=', $usuario_organizacion->organi_id)
                    ->get();

                foreach ($biometricos as $biometricosT) {
                    if ($biometricosT->dispo_todosEmp == 1) {
                        $empleados = DB::table('empleado as e')
                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->distinct('e.emple_id')
                            ->count();
                        $biometricosT->totalEmpleados = $empleados;
                        unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);
                    } else {
                        /* SI ES POR EMPLEADOS PERSONALIZADSO */
                        if ($biometricosT->dispo_porEmp == 1) {
                            $dispositivosBiEmp = DB::table('dispositivo_empleado as de')
                                ->join('dispositivos as di', 'de.idDispositivos', '=', 'di.idDispositivos')
                                ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                ->where('de.idDispositivos', '=', $biometricosT->idDispositivos)
                                ->where('di.dispo_estadoActivo', '=', 1)
                                ->where('de.estado', '=', 1)
                                ->where('e.emple_estado', '=', 1)
                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                ->count();
                            $biometricosT->totalEmpleados = $dispositivosBiEmp;
                            unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);
                        }

                        /* SI ES POR AREAS */
                        else {
                            $dispositivosBiAr = DB::table('empleado as e')
                                ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                                ->join('dispositivos as di', 'da.idDispositivos', '=', 'di.idDispositivos')
                                ->where('da.idDispositivos', '=', $biometricosT->idDispositivos)
                                ->where('di.dispo_estadoActivo', '=', 1)
                                ->where('da.estado', '=', 1)
                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                ->where('e.emple_estado', '=', 1)
                                ->where('e.asistencia_puerta', '=', 1)
                                ->count();

                            $biometricosT->totalEmpleados = $dispositivosBiAr;
                            /*  $biometricos->pull($biometricosT->dispo_todosEmp); */
                            unset($biometricosT->dispo_todosEmp, $biometricosT->dispo_porEmp);

                        }
                    }

                }

                /*  $biometricos->pull('dispo_todosEmp','dispo_porEmp'); */
                return response()->json(
                    $biometricos
                    , 200);

            }
            /*  */
        } else {
            return response()->json(array('status' => 400, 'title' => 'Organizacion desactivada',
                'detail' => 'La organizacion se encuentra desactivada'), 400);
        }

    }

    public function editarDispositivo(Request $request)
    {

        $idDispositivos = $request->idDispositivos;
        $descripcion = $request->descripcion;
        $ipPuerto = $request->ipPuerto;
        $serie = $request->serie;
        $version_firmware = $request->version_firmware;

        $dispositivo = dispositivos::findOrFail($idDispositivos);
        if (empty($request->descripcion)) {} else {
            $dispositivo->dispo_descripUbicacion = $descripcion;
        }

        if (empty($request->ipPuerto)) {} else {
            $dispositivo->dispo_movil = $ipPuerto;
        }

        if (empty($request->serie) || $request->serie == null) {} else {
            if ($dispositivo->dispo_codigo == null) {
                /* BUSCAR DISPOSITIVO CON LA MISMA SERIE */
                $dispoSerie = DB::table('dispositivos')
                    ->leftJoin('tipo_biometrico as tipBi','dispositivos.idtipo_biometrico', '=', 'tipBi.idtipo_biometrico')
                    ->select('idDispositivos', 'dispo_descripUbicacion as descripcion', 'dispo_movil as ipPuerto',
                        'dispo_codigo as serie', 'version_firmware','dispo_Data  as diasData','tipBi.cod_tipo as tipoBiometrico')
                    ->where('tipoDispositivo', '=', 3)
                    ->where('organi_id', '=', $dispositivo->organi_id)
                    ->where('dispo_codigo', '=', $serie)
                    ->get();

                /* SI NO HAY DISPOSITIVO CON LA MISMA SERIE */
                if ($dispoSerie->isEmpty()) {
                    /* REGISTRAMOS LA SERIE  */
                    $dispositivo->dispo_codigo = $serie;
                } else {
                    return response()->json(array('status' => 400, 'title' => 'No se pudo editar dispositivo',
                        'detail' => 'No se pudo editar dispositivo, la serie ya existe en otro dispositivo'), 400);

                }

            } else {
                return response()->json(array('status' => 400, 'title' => 'No se pudo editar serie de dispositivo',
                    'detail' => 'No se pudo editar serie de dispositivo, el dispositivo ya tiene una serie asignada'), 400);
            }

        }

        if (empty($request->version_firmware)) {} else {
            $dispositivo->version_firmware = $version_firmware;
        }
        $dispositivo->save();

        if ($dispositivo) {
            return response()->json(array('status' => 200, 'title' => 'Dispositivo editado correctamente',
                'detail' => 'Dispositivo editado correctamente en la base de datos'), 200);
        } else {
            return response()->json(array('status' => 400, 'title' => 'No se pudo editar dispositivo',
                'detail' => 'No se pudo editar dispositivo, compruebe que los datos sean validos'), 400);
        }

    }

    public function empleadosBiometrico(Request $request)
    {

        $idUsuarioOrgani = $request->idusuario_organizacion;
        $idbiometrico = $request->idbiometrico;

        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario', 'uso.rol_id', 'o.organi_id', 'o.organi_razonSocial', 'O.organi_estado')
            ->where('uso.usua_orga_id', '=', $idUsuarioOrgani)
            ->join('users as u', 'uso.user_id', '=', 'u.id')
            ->join('organizacion as o', 'uso.organi_id', '=', 'o.organi_id')
            ->get()->first();

        /* FUNCION PARA AGRUPAR CON ID DE BIOMETRICOS */
        function agruparIDBiometricos($empleado,$idorganiz)
        {

            $idBiometricos = array();
            foreach ($empleado as $tab1) {

                /* VERIFICAMOS DISPOSITIVOS CON TODOS LOS EMPLEADOS */
                $dispositivosBi = DB::table('dispositivos as di')
                    ->where('di.dispo_porEmp', '=', 1)
                    ->where('di.dispo_todosEmp', '=', 1)
                    ->where('di.tipoDispositivo', '=', 3)
                    ->where('di.dispo_estadoActivo', '=', 1)
                    ->where('di.organi_id', '=', $idorganiz)
                    ->get();
                if ($dispositivosBi->isNotEmpty()) {
                    foreach ($dispositivosBi as $dispositivosBis) {
                        $datos1 = ["idDispositivos" => $dispositivosBis->idDispositivos];
                        array_push($idBiometricos, $datos1);

                    }
                }

                /* ------------------------------------------------- */

                /* VERIFICAR DISPOSTIVOS CON EMPLEADOS */
                $dispositivosBiEmp = DB::table('dispositivo_empleado as de')
                    ->join('dispositivos as di', 'de.idDispositivos', '=', 'di.idDispositivos')
                    ->where('de.emple_id', '=', $tab1->idempleado)
                    ->where('di.dispo_estadoActivo', '=', 1)
                    ->where('di.organi_id', '=', $idorganiz)
                    ->where('de.estado', '=', 1)
                    ->get();
                if ($dispositivosBiEmp->isNotEmpty()) {
                    foreach ($dispositivosBiEmp as $dispositivosBiEmps) {
                        $datos2 = ["idDispositivos" => $dispositivosBiEmps->idDispositivos];
                        array_push($idBiometricos, $datos2);
                    }
                }

                /* ---------------------------------------------- */
                /* VERIFICAR DISPOSITIVOS POR AREAS */
                $dispositivosBiAr = DB::table('dispositivo_area as da')
                    ->join('dispositivos as di', 'da.idDispositivos', '=', 'di.idDispositivos')
                    ->where('da.area_id', '=', $tab1->emple_area)
                    ->where('di.dispo_estadoActivo', '=', 1)
                    ->where('da.estado', '=', 1)
                     ->where('di.organi_id', '=', $idorganiz)
                    ->get();
                if ($dispositivosBiAr->isNotEmpty()) {
                    foreach ($dispositivosBiAr as $dispositivosBiArs) {
                        $datos3 = ["idDispositivos" => $dispositivosBiArs->idDispositivos];
                        array_push($idBiometricos, $datos3);
                    }
                }
                /* ------------------------------------- */
                $idUnicos = array_unique($idBiometricos, SORT_REGULAR);

                $tab1->biometricos = array_values($idUnicos);

                unset($tab1->emple_area);
            }

            return $empleado;
        }
        /* ---------------------------------- */
        /* SI BIOMETRICO ES NULL DEVOLVEMOS LOS EMPLEADOS SIN FILTRAR POR BIOMETIRICO */
        if ($idbiometrico == null) {
            /* PRIMERO VER SI ES INVITADO O NO */
            if ($usuario_organizacion->rol_id == 3) {

                /* SI ES INVITADO VER PERMISOS */
                $invitadod = DB::table('invitado')
                    ->where('user_Invitado', '=', $usuario_organizacion->idusuario)
                    ->where('organi_id', '=', $usuario_organizacion->organi_id)
                    ->where('rol_id', '=', 3)
                    ->get()->first();

                if ($invitadod->verTodosEmps == 1) {
                    /* CUANDO TIENE TODOS LOS EMPELADOS */
                    $empleado = DB::table('empleado as e')
                        ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')

                        ->select('e.emple_id as idempleado',
                            'p.perso_nombre as nombre',
                            DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                            'e.emple_nDoc as dni', 'e.emple_area'
                        )
                        ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                        ->where('e.emple_estado', '=', 1)
                        ->where('e.asistencia_puerta', '=', 1)
                        ->distinct('e.emple_id')
                        ->paginate();
                    $empleado = agruparIDBiometricos($empleado,$usuario_organizacion->organi_id);

                } else {
                    /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    /* empleados x id */
                    if ($invitado_empleadoIn != null) {
                        $empleado = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('e.emple_id as idempleado',
                                'p.perso_nombre as nombre',
                                DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                'e.emple_nDoc as dni', 'e.emple_area'
                            )
                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->distinct('e.emple_id')
                            ->paginate();
                        $empleado = agruparIDBiometricos($empleado,$usuario_organizacion->organi_id);

                    } else {
                        /* EMPLEADOS POR AREA */
                        $empleado = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                            ->select('e.emple_id as idempleado',
                                'p.perso_nombre as nombre',
                                DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                'e.emple_nDoc as dni', 'e.emple_area'
                            )
                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->distinct('e.emple_id')
                            ->paginate();
                        $empleado = agruparIDBiometricos($empleado,$usuario_organizacion->organi_id);

                    }
                }

            } else {
                $empleado = DB::table('empleado as e')
                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                    ->select('e.emple_id as idempleado',
                        'p.perso_nombre as nombre',
                        DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                        'e.emple_nDoc as dni', 'e.emple_area'
                    )
                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                    ->where('e.emple_estado', '=', 1)
                    ->where('e.asistencia_puerta', '=', 1)
                    ->distinct('e.emple_id')
                    ->paginate();
                $empleado = agruparIDBiometricos($empleado,$usuario_organizacion->organi_id);

            }
        } else {
            /* SI EXISTE ID DE BIOMETRICO ENTONCES FILTRAMOS POR BIOMETRICOS */

            /* OBTENEMOS LOS PERMISO QUE TIENE EL BIOMETRICO  */
            $biometricoPer = DB::table('dispositivos')
                ->where('idDispositivos', '=', $idbiometrico)
                ->where('organi_id', '=', $usuario_organizacion->organi_id)
                ->get()->first();

            /* ------------------------------------------------------------ */

            /* VERIFIAMOS SI TIENE PERMISO POR EMPLEADO O POR AREA */

            /* 1. PRIMERO VERIFICAMOS SI TIENE PERMISO POR EMPLEADO */
            if ($biometricoPer->dispo_porEmp == 1) {

                /* COMO TIENE PERMISO PARA EMPLEADO VERIFICAMOS SI TIENE PERMISO PARA TODOS */
                if ($biometricoPer->dispo_todosEmp == 1) {
                    /* SI TIENE PERMISO PARA TODOS ENTONCES AQUI NO FILTRAMO MUCHO */
                    /* AQUI PONDRE VALIDACIONES POR USUARIO */

                    /* PRIMERO VERIFICAMOS SI ES USUARIO INVITADO */
                    if ($usuario_organizacion->rol_id == 3) {

                        /* SI ES INVITADO VER PERMISOS */
                        $invitadod = DB::table('invitado')
                            ->where('user_Invitado', '=', $usuario_organizacion->idusuario)
                            ->where('organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('rol_id', '=', 3)
                            ->get()->first();

                        if ($invitadod->verTodosEmps == 1) {
                            /* CUANDO TIENE TODOS LOS EMPELADOS */
                            $empleado = DB::table('empleado as e')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->select('e.emple_id as idempleado',
                                    'p.perso_nombre as nombre',
                                    DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                    'e.emple_nDoc as dni', 'e.emple_area'
                                )
                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                ->where('e.emple_estado', '=', 1)
                                ->where('e.asistencia_puerta', '=', 1)
                                ->distinct('e.emple_id')
                                ->paginate();
                            $empleado = agruparIDBiometricos($empleado,$usuario_organizacion->organi_id);

                        } else {
                            /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                            $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                                ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                                ->where('invem.area_id', '=', null)
                                ->where('invem.emple_id', '!=', null)
                                ->get()->first();
                            /* empleados x id */
                            if ($invitado_empleadoIn != null) {
                                $empleado = DB::table('empleado as e')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id as idempleado',
                                        'p.perso_nombre as nombre',
                                        DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                        'e.emple_nDoc as dni', 'e.emple_area'
                                    )
                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->distinct('e.emple_id')
                                    ->paginate();
                                $empleado = agruparIDBiometricos($empleado,$usuario_organizacion->organi_id);

                            } else {
                                /* EMPLEADOS POR AREA */
                                $empleado = DB::table('empleado as e')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                                    ->select('e.emple_id as idempleado',
                                        'p.perso_nombre as nombre',
                                        DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                        'e.emple_nDoc as dni', 'e.emple_area'
                                    )
                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->distinct('e.emple_id')
                                    ->paginate();
                                $empleado = agruparIDBiometricos($empleado,$usuario_organizacion->organi_id);

                            }
                        }

                    } else {
                        $empleado = DB::table('empleado as e')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->select('e.emple_id as idempleado',
                                'p.perso_nombre as nombre',
                                DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                'e.emple_nDoc as dni', 'e.emple_area'
                            )
                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->distinct('e.emple_id')
                            ->paginate();
                        $empleado = agruparIDBiometricos($empleado,$usuario_organizacion->organi_id);

                    }

                    /* ---------------------------------------- */
                } else {
                    /* AQUI FILTRAMOS CON LA TABLA DISPOSITIVOS EMPLEADO */

                    if ($usuario_organizacion->rol_id == 3) {

                        /* SI ES INVITADO VER PERMISOS */
                        $invitadod = DB::table('invitado')
                            ->where('user_Invitado', '=', $usuario_organizacion->idusuario)
                            ->where('organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('rol_id', '=', 3)
                            ->get()->first();

                        if ($invitadod->verTodosEmps == 1) {
                            /* CUANDO TIENE TODOS LOS EMPELADOS */
                            /* ------TODOS LO EMLEADOS POR BIOMETRICO ------------------------------*/
                            $empleado = DB::table('dispositivo_empleado as de')
                                ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->select('e.emple_id as idempleado',
                                    'p.perso_nombre as nombre',
                                    DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                    'e.emple_nDoc as dni', 'e.emple_area'
                                )
                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                ->where('e.emple_estado', '=', 1)
                                ->where('de.estado', '=', 1)
                                ->where('e.asistencia_puerta', '=', 1)
                                ->where('de.idDispositivos', '=', $idbiometrico)
                                ->distinct('e.emple_id')
                                ->paginate();
                            $empleado = agruparIDBiometricos($empleado,$usuario_organizacion->organi_id);
                            /* --------------------------------------------------------------------- */

                        } else {
                            /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                            $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                                ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                                ->where('invem.area_id', '=', null)
                                ->where('invem.emple_id', '!=', null)
                                ->get()->first();
                            /* empleados x id */
                            if ($invitado_empleadoIn != null) {

                                /* FILTRO DE EMPLEADOS POR BIOMETRICO FILTRADO POR INVITADO EMPLEADOS */
                                $empleado = DB::table('dispositivo_empleado as de')
                                    ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id as idempleado',
                                        'p.perso_nombre as nombre',
                                        DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                        'e.emple_nDoc as dni', 'e.emple_area'
                                    )
                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('de.estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->where('de.idDispositivos', '=', $idbiometrico)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->distinct('e.emple_id')
                                    ->paginate();
                                $empleado = agruparIDBiometricos($empleado, $usuario_organizacion->organi_id);
                                /* ----------------------------------------------------------------- */

                            } else {
                                /* EMPLEADOS POR AREA */

                                /* FILTRO DE EMPLEADOS POR BIOMETRICO FILTRADO POR INVITADO AREAS */
                                $empleado = DB::table('dispositivo_empleado as de')
                                    ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                                    ->select('e.emple_id as idempleado',
                                        'p.perso_nombre as nombre',
                                        DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                        'e.emple_nDoc as dni', 'e.emple_area'
                                    )
                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('de.estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->where('de.idDispositivos', '=', $idbiometrico)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->distinct('e.emple_id')
                                    ->paginate();
                                $empleado = agruparIDBiometricos($empleado, $usuario_organizacion->organi_id);
                                /* -------------------------------------------------------------- */

                            }
                        }

                    } else {
                        /* ------TODOS LO EMLEADOS POR BIOMETRICO ------------------------------*/
                        $empleado = DB::table('dispositivo_empleado as de')
                            ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->select('e.emple_id as idempleado',
                                'p.perso_nombre as nombre',
                                DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                'e.emple_nDoc as dni', 'e.emple_area'
                            )
                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('e.emple_estado', '=', 1)
                            ->where('de.estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->where('de.idDispositivos', '=', $idbiometrico)
                            ->distinct('e.emple_id')
                            ->paginate();
                        $empleado = agruparIDBiometricos($empleado, $usuario_organizacion->organi_id);
                        /* --------------------------------------------------------------------- */

                    }

                }

            } else {
                /* VERIFICAMOS POR PRECAUSIO SI TIENE PERMSO PARA AREA  */
                if ($biometricoPer->dispo_porArea == 1) {

                    /* PRIMERO TODO DENTRO DE VALIDACIONES POR USUARIO */
                    if ($usuario_organizacion->rol_id == 3) {

                        /* SI ES INVITADO VER PERMISOS */
                        $invitadod = DB::table('invitado')
                            ->where('user_Invitado', '=', $usuario_organizacion->idusuario)
                            ->where('organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('rol_id', '=', 3)
                            ->get()->first();

                        if ($invitadod->verTodosEmps == 1) {
                            /* CUANDO TIENE TODOS LOS EMPELADOS */
                            /* ------TODOS LO EMLEADOS DE AREA POR BIOMETRICO ------------------------------*/
                            $empleado = DB::table('dispositivo_area as da')
                                ->join('empleado as e', 'da.area_id', '=', 'e.emple_area')
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->select('e.emple_id as idempleado',
                                    'p.perso_nombre as nombre',
                                    DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                    'e.emple_nDoc as dni', 'e.emple_area'
                                )
                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                ->where('e.emple_estado', '=', 1)
                                ->where('da.estado', '=', 1)
                                ->where('e.asistencia_puerta', '=', 1)
                                ->where('da.idDispositivos', '=', $idbiometrico)
                                ->distinct('e.emple_id')
                                ->paginate();
                            $empleado = agruparIDBiometricos($empleado, $usuario_organizacion->organi_id);
                            /* --------------------------------------------------------------------- */

                        } else {
                            /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                            $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                                ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                                ->where('invem.area_id', '=', null)
                                ->where('invem.emple_id', '!=', null)
                                ->get()->first();
                            /* empleados x id */
                            if ($invitado_empleadoIn != null) {

                                /* FILTRO DE AREAS POR BIOMETRICO FILTRADO POR INVITADO EMPLEADOS */
                                $empleado = DB::table('dispositivo_area as da')
                                    ->join('empleado as e', 'da.area_id', '=', 'e.emple_area')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('e.emple_id as idempleado',
                                        'p.perso_nombre as nombre',
                                        DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                        'e.emple_nDoc as dni', 'e.emple_area'
                                    )
                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('da.estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->where('da.idDispositivos', '=', $idbiometrico)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->distinct('e.emple_id')
                                    ->paginate();
                                $empleado = agruparIDBiometricos($empleado, $usuario_organizacion->organi_id);
                                /* ----------------------------------------------------------------- */

                            } else {
                                /* EMPLEADOS POR AREA */

                                /* FILTRO DE AREA POR BIOMETRICO FILTRADO POR INVITADO AREAS */

                                $empleado = DB::table('dispositivo_area as da')
                                    ->join('empleado as e', 'da.area_id', '=', 'e.emple_area')
                                    ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                                    ->select('e.emple_id as idempleado',
                                        'p.perso_nombre as nombre',
                                        DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                        'e.emple_nDoc as dni', 'e.emple_area'
                                    )
                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('da.estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->where('da.idDispositivos', '=', $idbiometrico)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->distinct('e.emple_id')
                                    ->paginate();
                                $empleado = agruparIDBiometricos($empleado, $usuario_organizacion->organi_id);
                                /* -------------------------------------------------------------- */

                            }
                        }

                    } else {
                        /*  dd('entre cuando soy admin'); */
                        /* ------TODOS LO EMLEADOS POR BIOMETRICO AREA ------------------------------*/
                        $empleado = DB::table('dispositivo_area as da')
                            ->join('empleado as e', 'da.area_id', '=', 'e.emple_area')
                            ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                            ->select('e.emple_id as idempleado',
                                'p.perso_nombre as nombre',
                                DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as apellidos'),
                                'e.emple_nDoc as dni', 'e.emple_area'
                            )
                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('e.emple_estado', '=', 1)
                            ->where('da.estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->where('da.idDispositivos', '=', $idbiometrico)
                            ->distinct('e.emple_id')
                            ->paginate();
                        $empleado = agruparIDBiometricos($empleado, $usuario_organizacion->organi_id);
                        /* --------------------------------------------------------------------- */

                    }
                    /* ------------------------------------------------- */

                }
            }
            /* ------ */

            /* --------------------------------------------------------- */

        }
        if ($empleado != null) {
            return response()->json($empleado);
        } else {
            return response()->json(array('status' => 400, 'title' => 'Empleados no encontrados',
                'detail' => 'No se encontro empleados relacionados con este dispositivo'), 400);
        }
    }

    public function empleadosHorarioBi(Request $request)
    {

        /* --------DATOS RECIBIDOS-------------- */
        $idUsuarioOrgani = $request->idusuario_organizacion;
        $idbiometrico = $request->idbiometrico;
        /* --------------------------------------------- */

        /* DATOS DE USUARIO ORGANIZACION */
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario', 'uso.rol_id', 'o.organi_id', 'o.organi_razonSocial', 'O.organi_estado')
            ->where('uso.usua_orga_id', '=', $idUsuarioOrgani)
            ->join('users as u', 'uso.user_id', '=', 'u.id')
            ->join('organizacion as o', 'uso.organi_id', '=', 'o.organi_id')
            ->get()->first();
        /* ------------------------------------------- */

        /* DATOS CUADNO PUEDE EXISTIR INVITADO */
        $invitadod = DB::table('invitado')
            ->where('user_Invitado', '=', $usuario_organizacion->idusuario)
            ->where('organi_id', '=', $usuario_organizacion->organi_id)
            ->where('rol_id', '=', 3)
            ->get()->first();
        /* ------------------------------------------- */

        /* OBTENER FECHA ACTUAL Y DE MA??ANA EN FORMATO DATE */
        $fecha = Carbon::now('America/Lima');
        $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
        /* --------------------------------------- */
        /* SI BIOMETRICO ES NULL DEVOLVEMOS LOS EMPLEADOS SIN FILTRAR POR BIOMETIRICO */

        /* --FUNCION PARA INSERTAR PAUSAS----------- */
        function insertarPausasH($horario)
        {
            /* OBTENER FECHA ACTUAL Y DE MA??ANA EN FORMATO DATE */
            $fecha = Carbon::now('America/Lima');
            $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
            /* --------------------------------------- */
            foreach ($horario as $tab2) {

                if (Carbon::parse($tab2->horaF)->lt(Carbon::parse($tab2->horaI))) {
                    $despues = new Carbon('tomorrow');
                    $fechaMan = $despues->isoFormat('YYYY-MM-DD');
                    $tab2->horaI = $fechaHoy . " " . $tab2->horaI;
                    $tab2->horaF = $fechaMan . " " . $tab2->horaF;
                } else {
                    $tab2->horaI = $fechaHoy . " " . $tab2->horaI;
                    $tab2->horaF = $fechaHoy . " " . $tab2->horaF;
                }

            }

            /* horaaas de inicio y fin horariio */

            /* INSERTO PAUSAS */
            foreach ($horario as $tab1) {
                $pausas_horario = DB::table('pausas_horario as pauh')
                    ->select('idpausas_horario as idpausa', 'pausH_descripcion as descripcion', 'pausH_Inicio as horaI',
                        'pausH_Fin as horaF', 'pauh.tolerancia_inicio as toleranciaI', 'pauh.tolerancia_fin as toleranciaF',
                        'inactivar as inhabilitar')
                    ->where('pauh.horario_id', '=', $tab1->horario_id)
                    ->distinct('pauh.idpausas_horario')
                    ->get();
                $horaIV = $tab1->horaI;
                $horaFV = $tab1->horaF;

                foreach ($pausas_horario as $tab3) {

                    if (Carbon::parse($tab3->horaF)->lt(Carbon::parse($tab3->horaI))) {
                        $despues = new Carbon('tomorrow');
                        $fechaMan = $despues->isoFormat('YYYY-MM-DD');
                        $tab3->horaI = $fechaHoy . " " . $tab3->horaI;
                        $tab3->horaF = $fechaMan . " " . $tab3->horaF;
                    } else {
                        if (Carbon::parse($tab3->horaI)->lt(Carbon::parse($horaIV))) {
                            $tab3->horaI = $fechaMan . " " . $tab3->horaI;
                        } else {
                            $tab3->horaI = $fechaHoy . " " . $tab3->horaI;
                        }

                        if (Carbon::parse($tab3->horaF)->lt(Carbon::parse($horaIV))) {
                            $tab3->horaF = $fechaMan . " " . $tab3->horaF;
                        } else {
                            $tab3->horaF = $fechaHoy . " " . $tab3->horaF;
                        }
                    }

                }
                $tab1->pausas = $pausas_horario;

            }
            return $horario;
        }
        /* ---------------FIN DE FUNCION-------------------------- */

        /* SI BIOMETRICO ES NULL DEVOLVEMOS LOS EMPLEADOS SIN FILTRAR POR BIOMETIRICO */
        if ($idbiometrico == null) {
            /* PRIMERO VER SI ES INVITADO O NO */
            if ($usuario_organizacion->rol_id == 3) {

                /* SI ES INVITADO VER PERMISOS */

                if ($invitadod->verTodosEmps == 1) {
                    /* CUANDO TIENE TODOS LOS EMPELADOS */

                    /*  dd($fechaHoy); */
                    $horario = DB::table('horario_empleado as he')
                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                        ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                        ->where('e.emple_estado', '=', 1)
                        ->where('e.asistencia_puerta', '=', 1)
                        ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                            'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')
                        ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                        ->where('he.estado', '=', 1)
                        ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                        ->orderBy('he.empleado_emple_id')
                        ->paginate();
                    $horario = insertarPausasH($horario);

                } else {
                    /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    /* empleados x id */
                    if ($invitado_empleadoIn != null) {

                        $horario = DB::table('horario_empleado as he')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                            ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')

                        /*   DB::raw('IF(h.horaI> hd.start,CONCAT(DATE(hd.start)," ",h.horaF) , CONCAT('.$fechaSum.'," ",h.horaF)) as horaF')) */

                            ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                            ->where('he.estado', '=', 1)
                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->orderBy('he.empleado_emple_id')
                            ->paginate();

                        $horario = insertarPausasH($horario);

                    } else {
                        /* EMPLEADOS POR AREA */
                        $horario = DB::table('horario_empleado as he')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                            ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')

                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)

                            ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                            ->where('he.estado', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->orderBy('he.empleado_emple_id')
                            ->paginate();

                        $horario = insertarPausasH($horario);

                    }
                }

            } else {

                $horario = DB::table('horario_empleado as he')
                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                    ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                    ->where('e.emple_estado', '=', 1)
                    ->where('e.asistencia_puerta', '=', 1)
                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')
                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                    ->where('he.estado', '=', 1)
                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                    ->orderBy('he.empleado_emple_id')
                    ->paginate();

                $horario = insertarPausasH($horario);

            }
        } else {
            /* ----------------------------------------------------------------------------- */
            /* SI BIOMETRICO NO ES NULL ENTONCES LOS EMPLEADOS SE FILTRARA X DISPOSTIVOS */
            /* ---------------------------------------------------------------------------- */
            /* SI EXISTE ID DE BIOMETRICO ENTONCES FILTRAMOS POR BIOMETRICOS */

            /* OBTENEMOS LOS PERMISO QUE TIENE EL BIOMETRICO  */
            $biometricoPer = DB::table('dispositivos')
                ->where('idDispositivos', '=', $idbiometrico)
                ->where('organi_id', '=', $usuario_organizacion->organi_id)
                ->get()->first();

            /* ------------------------------------------------------------ */
            /* VERIFIAMOS SI TIENE PERMISO POR EMPLEADO O POR AREA */

            /* 1. PRIMERO VERIFICAMOS SI TIENE PERMISO POR EMPLEADO */
            if ($biometricoPer->dispo_porEmp == 1) {
                /* COMO TIENE PERMISO PARA EMPLEADO VERIFICAMOS SI TIENE PERMISO PARA TODOS */
                if ($biometricoPer->dispo_todosEmp == 1) {
                    /* SI TIENE PERMISO PARA TODOS ENTONCES AQUI NO FILTRAMO MUCHO */
                    /* AQUI PONDRE VALIDACIONES POR USUARIO */
                    /* PRIMERO VER SI ES INVITADO O NO */
                    if ($usuario_organizacion->rol_id == 3) {

                        /* SI ES INVITADO VER PERMISOS */

                        if ($invitadod->verTodosEmps == 1) {
                            /* CUANDO TIENE TODOS LOS EMPELADOS */

                            $horario = DB::table('horario_empleado as he')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                                ->where('e.emple_estado', '=', 1)
                                ->where('e.asistencia_puerta', '=', 1)
                                ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                    'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')
                                ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                                ->where('he.estado', '=', 1)
                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                ->orderBy('he.empleado_emple_id')
                                ->paginate();

                            $horario = insertarPausasH($horario);

                        } else {
                            /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                            $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                                ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                                ->where('invem.area_id', '=', null)
                                ->where('invem.emple_id', '!=', null)
                                ->get()->first();
                            /* empleados x id */
                            if ($invitado_empleadoIn != null) {

                                $horario = DB::table('horario_empleado as he')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')

                                /*   DB::raw('IF(h.horaI> hd.start,CONCAT(DATE(hd.start)," ",h.horaF) , CONCAT('.$fechaSum.'," ",h.horaF)) as horaF')) */

                                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                                    ->where('he.estado', '=', 1)
                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->orderBy('he.empleado_emple_id')
                                    ->paginate();

                                $horario = insertarPausasH($horario);

                            } else {
                                /* EMPLEADOS POR AREA */
                                $horario = DB::table('horario_empleado as he')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')

                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)

                                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                                    ->where('he.estado', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->orderBy('he.empleado_emple_id')
                                    ->paginate();

                                $horario = insertarPausasH($horario);

                            }
                        }

                    } else {

                        $horario = DB::table('horario_empleado as he')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                            ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')
                            ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                            ->where('he.estado', '=', 1)
                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->orderBy('he.empleado_emple_id')
                            ->paginate();

                        $horario = insertarPausasH($horario);
                    }
                    /* --------------------------------------- */
                } else {
                    /* AQUI CUANDO SE FILTRA X EMPLEADO */

                    /* AQUI FILTRAMOS CON LA TABLA DISPOSITIVOS EMPLEADO */

                    if ($usuario_organizacion->rol_id == 3) {

                        /* SI ES INVITADO VER PERMISOS */

                        if ($invitadod->verTodosEmps == 1) {
                            /* CUANDO TIENE TODOS LOS EMPELADOS */
                            /* CUANDO TIENE TODOS LOS EMPELADOS */

                            $horario = DB::table('horario_empleado as he')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                ->join('dispositivo_empleado as de', 'he.empleado_emple_id', '=', 'de.emple_id')
                                ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')

                                ->where('e.emple_estado', '=', 1)
                                ->where('e.asistencia_puerta', '=', 1)
                                ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                    'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')
                                ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                                ->where('he.estado', '=', 1)
                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                ->where('de.estado', '=', 1)
                                ->where('de.idDispositivos', '=', $idbiometrico)
                                ->orderBy('he.empleado_emple_id')
                                ->paginate();

                            $horario = insertarPausasH($horario);

                            /* --------------------------------------------------------------------- */

                        } else {
                            /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                            $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                                ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                                ->where('invem.area_id', '=', null)
                                ->where('invem.emple_id', '!=', null)
                                ->get()->first();
                            /* empleados x id */
                            if ($invitado_empleadoIn != null) {
                                /* -------------------------------------------------------------- */

                                $horario = DB::table('horario_empleado as he')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('dispositivo_empleado as de', 'he.empleado_emple_id', '=', 'de.emple_id')
                                    ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')

                                /*   DB::raw('IF(h.horaI> hd.start,CONCAT(DATE(hd.start)," ",h.horaF) , CONCAT('.$fechaSum.'," ",h.horaF)) as horaF')) */

                                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                                    ->where('he.estado', '=', 1)
                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->where('de.estado', '=', 1)
                                    ->where('de.idDispositivos', '=', $idbiometrico)
                                    ->orderBy('he.empleado_emple_id')
                                    ->paginate();

                                $horario = insertarPausasH($horario);

                                /* --------------------------------------------------------------- */

                            } else {
                                /* EMPLEADOS POR AREA */
                                $horario = DB::table('horario_empleado as he')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('dispositivo_empleado as de', 'he.empleado_emple_id', '=', 'de.emple_id')
                                    ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')

                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)

                                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                                    ->where('he.estado', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->where('de.estado', '=', 1)
                                    ->where('de.idDispositivos', '=', $idbiometrico)
                                    ->orderBy('he.empleado_emple_id')
                                    ->paginate();

                                $horario = insertarPausasH($horario);

                            }
                        }

                    } else {

                        $horario = DB::table('horario_empleado as he')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                            ->join('dispositivo_empleado as de', 'he.empleado_emple_id', '=', 'de.emple_id')
                            ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')
                            ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                            ->where('he.estado', '=', 1)
                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('de.estado', '=', 1)
                            ->where('de.idDispositivos', '=', $idbiometrico)
                            ->orderBy('he.empleado_emple_id')
                            ->paginate();

                        $horario = insertarPausasH($horario);

                    }

                    /* AQUI ACABA FILTRACION POR EMPLEADO */
                    /* ----------------------------------------------- */
                }
            } else {
                /* VERIFICAMOS POR PRECAUSIO SI TIENE PERMSO PARA AREA  */
                if ($biometricoPer->dispo_porArea == 1) {
                    /* FILTRO POR AREAAAS */
                    /* --------------------------------------------------- */
                    /* PRIMERO TODO DENTRO DE VALIDACIONES POR USUARIO */
                    if ($usuario_organizacion->rol_id == 3) {

                        /* SI ES INVITADO VER PERMISOS */

                        if ($invitadod->verTodosEmps == 1) {
                            /* CUANDO TIENE TODOS LOS EMPELADOS */
                            $horario = DB::table('horario_empleado as he')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                                ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                                ->where('e.emple_estado', '=', 1)
                                ->where('e.asistencia_puerta', '=', 1)
                                ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                    'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')
                                ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                                ->where('he.estado', '=', 1)
                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                ->where('da.estado', '=', 1)
                                ->where('da.idDispositivos', '=', $idbiometrico)
                                ->orderBy('he.empleado_emple_id')
                                ->paginate();

                            $horario = insertarPausasH($horario);

                        } else {
                            /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                            $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                                ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                                ->where('invem.area_id', '=', null)
                                ->where('invem.emple_id', '!=', null)
                                ->get()->first();
                            /* empleados x id */
                            if ($invitado_empleadoIn != null) {
                                $horario = DB::table('horario_empleado as he')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                                    ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')
                                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                                    ->where('he.estado', '=', 1)
                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('da.estado', '=', 1)
                                    ->where('da.idDispositivos', '=', $idbiometrico)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->orderBy('he.empleado_emple_id')
                                    ->paginate();

                                $horario = insertarPausasH($horario);

                            } else {
                                /* EMPLEADOS POR AREA */
                                $horario = DB::table('horario_empleado as he')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                                    ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')
                                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                                    ->where('he.estado', '=', 1)
                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('da.estado', '=', 1)
                                    ->where('da.idDispositivos', '=', $idbiometrico)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->orderBy('he.empleado_emple_id')
                                    ->paginate();

                                $horario = insertarPausasH($horario);

                            }
                        }

                    } else {
                        /*  dd('entre cuando soy admin'); */
                        /* CUANDO TIENE TODOS LOS EMPELADOS */
                        $horario = DB::table('horario_empleado as he')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                            ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                            ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario')
                            ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)
                            ->where('he.estado', '=', 1)
                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('da.estado', '=', 1)
                            ->where('da.idDispositivos', '=', $idbiometrico)
                            ->orderBy('he.empleado_emple_id')
                            ->paginate();
                        $horario = insertarPausasH($horario);

                    }

                }

            }

        }

        /* --------------------------------------------------------------------------------------------- */

        if ($horario != null) {
            return response()->json($horario);
        } else {
            return response()->json(array('status' => 400, 'title' => 'Empleados no encontrados',
                'detail' => 'No se encontro empleados relacionados con este dispositivo'), 400);
        }
    }

    public function historialHorario(Request $request)
    {
        /* --------DATOS RECIBIDOS-------------- */
        $idUsuarioOrgani = $request->idusuario_organizacion;
        $idbiometrico = $request->idbiometrico;
        /* --------------------------------------------- */

        /* DATOS DE USUARIO ORGANIZACION */
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario', 'uso.rol_id', 'o.organi_id', 'o.organi_razonSocial', 'O.organi_estado')
            ->where('uso.usua_orga_id', '=', $idUsuarioOrgani)
            ->join('users as u', 'uso.user_id', '=', 'u.id')
            ->join('organizacion as o', 'uso.organi_id', '=', 'o.organi_id')
            ->get()->first();
        /* ------------------------------------------- */

        /* DATOS CUADNO PUEDE EXISTIR INVITADO */
        $invitadod = DB::table('invitado')
            ->where('user_Invitado', '=', $usuario_organizacion->idusuario)
            ->where('organi_id', '=', $usuario_organizacion->organi_id)
            ->where('rol_id', '=', 3)
            ->get()->first();
        /* ------------------------------------------- */

        /* OBTENER FECHA ACTUAL Y DE MA??ANA EN FORMATO DATE */
        $fecha = Carbon::now('America/Lima');
        $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
        /* --------------------------------------- */
        /* SI BIOMETRICO ES NULL DEVOLVEMOS LOS EMPLEADOS SIN FILTRAR POR BIOMETIRICO */

        /* --FUNCION PARA INSERTAR PAUSAS----------- */
        function insertarPausas($horario)
        {
            /* OBTENER FECHA ACTUAL Y DE MA??ANA EN FORMATO DATE */
            $fecha = Carbon::now('America/Lima');
            $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
            /* --------------------------------------- */
            foreach ($horario as $tab2) {

                if (Carbon::parse($tab2->horaF)->lt(Carbon::parse($tab2->horaI))) {
                    $despues = new Carbon('tomorrow');
                    $fechaMan = $despues->isoFormat('YYYY-MM-DD');
                    $tab2->horaI = $fechaHoy . " " . $tab2->horaI;
                    $tab2->horaF = $fechaMan . " " . $tab2->horaF;
                } else {
                    $tab2->horaI = $fechaHoy . " " . $tab2->horaI;
                    $tab2->horaF = $fechaHoy . " " . $tab2->horaF;
                }

            }

            /* horaaas de inicio y fin horariio */

            /* INSERTO PAUSAS */
            foreach ($horario as $tab1) {
                $pausas_horario = DB::table('pausas_horario as pauh')
                    ->select('idpausas_horario as idpausa', 'pausH_descripcion as descripcion', 'pausH_Inicio as horaI',
                        'pausH_Fin as horaF', 'pauh.tolerancia_inicio as toleranciaI', 'pauh.tolerancia_fin as toleranciaF',
                        'inactivar as inhabilitar')
                    ->where('pauh.horario_id', '=', $tab1->horario_id)
                    ->distinct('pauh.idpausas_horario')
                    ->get();
                $horaIV = $tab1->horaI;
                $horaFV = $tab1->horaF;

                foreach ($pausas_horario as $tab3) {

                    if (Carbon::parse($tab3->horaF)->lt(Carbon::parse($tab3->horaI))) {
                        $despues = new Carbon('tomorrow');
                        $fechaMan = $despues->isoFormat('YYYY-MM-DD');
                        $tab3->horaI = $fechaHoy . " " . $tab3->horaI;
                        $tab3->horaF = $fechaMan . " " . $tab3->horaF;
                    } else {
                        if (Carbon::parse($tab3->horaI)->lt(Carbon::parse($horaIV))) {
                            $tab3->horaI = $fechaMan . " " . $tab3->horaI;
                        } else {
                            $tab3->horaI = $fechaHoy . " " . $tab3->horaI;
                        }

                        if (Carbon::parse($tab3->horaF)->lt(Carbon::parse($horaIV))) {
                            $tab3->horaF = $fechaMan . " " . $tab3->horaF;
                        } else {
                            $tab3->horaF = $fechaHoy . " " . $tab3->horaF;
                        }
                    }

                }
                $tab1->pausas = $pausas_horario;

            }
            return $horario;
        }
        /* ---------------FIN DE FUNCION-------------------------- */
        if ($idbiometrico == null) {
            /* PRIMERO VER SI ES INVITADO O NO */
            if ($usuario_organizacion->rol_id == 3) {

                /* SI ES INVITADO VER PERMISOS */

                if ($invitadod->verTodosEmps == 1) {
                    /* CUANDO TIENE TODOS LOS EMPELADOS */

                    /*  dd($fechaHoy); */
                    $horario = DB::table('horario_empleado as he')
                        ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                        ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                        ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                        ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                        ->where('e.emple_estado', '=', 1)
                        ->where('e.asistencia_puerta', '=', 1)
                        ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id'
                            , 'h.horario_descripcion', 'h.horaI', 'h.horaF', 'h.horario_tolerancia as toleranciaI',
                            'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario', 'histo.fechaCambio as fechaCambio',
                            'histo.estadohorarioEmp as estado')
                        ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                        ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                        ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                        ->orderBy('he.empleado_emple_id')
                        ->paginate();

                    $horario = insertarPausas($horario);

                } else {
                    /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                    $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                        ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                        ->where('invem.area_id', '=', null)
                        ->where('invem.emple_id', '!=', null)
                        ->get()->first();
                    /* empleados x id */
                    if ($invitado_empleadoIn != null) {

                        /*  dd($fechaHoy); */
                        $horario = DB::table('horario_empleado as he')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                            ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                            ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                            ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                                'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')

                        /*   DB::raw('IF(h.horaI> hd.start,CONCAT(DATE(hd.start)," ",h.horaF) , CONCAT('.$fechaSum.'," ",h.horaF)) as horaF')) */

                            ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                            ->orderBy('he.empleado_emple_id')
                            ->paginate();

                        $horario = insertarPausas($horario);

                    } else {
                        /* EMPLEADOS POR AREA */

                        /*  dd($fechaHoy); */
                        $horario = DB::table('horario_empleado as he')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                            ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                            ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                            ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                            ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                            ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                                'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')

                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)

                            ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                            ->where('invi.estado', '=', 1)
                            ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                            ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                            ->orderBy('he.empleado_emple_id')
                            ->paginate();

                        $horario = insertarPausas($horario);

                    }
                }

            } else {

                /*  dd($fechaHoy); */
                $horario = DB::table('horario_empleado as he')
                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                    ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                    ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                    ->where('e.emple_estado', '=', 1)
                    ->where('e.asistencia_puerta', '=', 1)
                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                        'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')
                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                    ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                    ->orderBy('he.empleado_emple_id')
                    ->paginate();

                $horario = insertarPausas($horario);

            }
        } else {
            /* ----------------------------------------------------------------------------- */
            /* SI BIOMETRICO NO ES NULL ENTONCES LOS EMPLEADOS SE FILTRARA X DISPOSTIVOS */
            /* ---------------------------------------------------------------------------- */
            /* SI EXISTE ID DE BIOMETRICO ENTONCES FILTRAMOS POR BIOMETRICOS */

            /* OBTENEMOS LOS PERMISO QUE TIENE EL BIOMETRICO  */
            $biometricoPer = DB::table('dispositivos')
                ->where('idDispositivos', '=', $idbiometrico)
                ->where('organi_id', '=', $usuario_organizacion->organi_id)
                ->get()->first();

            /* ------------------------------------------------------------ */
            /* VERIFIAMOS SI TIENE PERMISO POR EMPLEADO O POR AREA */

            /* 1. PRIMERO VERIFICAMOS SI TIENE PERMISO POR EMPLEADO */
            if ($biometricoPer->dispo_porEmp == 1) {
                /* COMO TIENE PERMISO PARA EMPLEADO VERIFICAMOS SI TIENE PERMISO PARA TODOS */
                if ($biometricoPer->dispo_todosEmp == 1) {
                    /* SI TIENE PERMISO PARA TODOS ENTONCES AQUI NO FILTRAMO MUCHO */
                    /* AQUI PONDRE VALIDACIONES POR USUARIO */
                    /* PRIMERO VER SI ES INVITADO O NO */
                    if ($usuario_organizacion->rol_id == 3) {

                        /* SI ES INVITADO VER PERMISOS */

                        if ($invitadod->verTodosEmps == 1) {
                            /* CUANDO TIENE TODOS LOS EMPELADOS */

                            /*  dd($fechaHoy); */
                            $horario = DB::table('horario_empleado as he')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                                ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                                ->where('e.emple_estado', '=', 1)
                                ->where('e.asistencia_puerta', '=', 1)
                                ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                    'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                                    'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')
                                ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                                ->orderBy('he.empleado_emple_id')
                                ->paginate();

                            $horario = insertarPausas($horario);

                        } else {
                            /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                            $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                                ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                                ->where('invem.area_id', '=', null)
                                ->where('invem.emple_id', '!=', null)
                                ->get()->first();
                            /* empleados x id */
                            if ($invitado_empleadoIn != null) {

                                /*  dd($fechaHoy); */
                                $horario = DB::table('horario_empleado as he')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                                        'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')

                                /*   DB::raw('IF(h.horaI> hd.start,CONCAT(DATE(hd.start)," ",h.horaF) , CONCAT('.$fechaSum.'," ",h.horaF)) as horaF')) */

                                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->orderBy('he.empleado_emple_id')
                                    ->paginate();
                                $horario = insertarPausas($horario);

                            } else {
                                /* EMPLEADOS POR AREA */
                                /*  dd($fechaHoy); */
                                $horario = DB::table('horario_empleado as he')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                                        'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')

                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)

                                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                                    ->where('invi.estado', '=', 1)
                                    ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->orderBy('he.empleado_emple_id')
                                    ->paginate();
                                $horario = insertarPausas($horario);

                            }
                        }

                    } else {

                        /*  dd($fechaHoy); */
                        $horario = DB::table('horario_empleado as he')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                            ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                            ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                                'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')
                            ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                            ->orderBy('he.empleado_emple_id')
                            ->paginate();
                        $horario = insertarPausas($horario);

                    }
                    /* --------------------------------------- */
                } else {
                    /* AQUI CUANDO SE FILTRA X EMPLEADO */

                    /* AQUI FILTRAMOS CON LA TABLA DISPOSITIVOS EMPLEADO */

                    if ($usuario_organizacion->rol_id == 3) {

                        /* SI ES INVITADO VER PERMISOS */

                        if ($invitadod->verTodosEmps == 1) {
                            /* CUANDO TIENE TODOS LOS EMPELADOS */
                            /* CUANDO TIENE TODOS LOS EMPELADOS */

                            /*  dd($fechaHoy); */
                            $horario = DB::table('horario_empleado as he')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                ->join('dispositivo_empleado as de', 'he.empleado_emple_id', '=', 'de.emple_id')
                                ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                                ->where('e.emple_estado', '=', 1)
                                ->where('e.asistencia_puerta', '=', 1)
                                ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                    'h.horaF', 'h.horario_tolerancia as toleranciaI',
                                    'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')
                                ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                ->where('de.estado', '=', 1)
                                ->where('de.idDispositivos', '=', $idbiometrico)
                                ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                                ->orderBy('he.empleado_emple_id')
                                ->paginate();

                            $horario = insertarPausas($horario);
                            /* --------------------------------------------------------------------- */

                        } else {
                            /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                            $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                                ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                                ->where('invem.area_id', '=', null)
                                ->where('invem.emple_id', '!=', null)
                                ->get()->first();
                            /* empleados x id */
                            if ($invitado_empleadoIn != null) {
                                /* -------------------------------------------------------------- */

                                /*  dd($fechaHoy); */
                                $horario = DB::table('horario_empleado as he')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('dispositivo_empleado as de', 'he.empleado_emple_id', '=', 'de.emple_id')
                                    ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                        'h.horaF', 'h.horario_tolerancia as toleranciaI',
                                        'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')

                                /*   DB::raw('IF(h.horaI> hd.start,CONCAT(DATE(hd.start)," ",h.horaF) , CONCAT('.$fechaSum.'," ",h.horaF)) as horaF')) */

                                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->where('de.estado', '=', 1)
                                    ->where('de.idDispositivos', '=', $idbiometrico)
                                    ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                                    ->orderBy('he.empleado_emple_id')
                                    ->paginate();

                                $horario = insertarPausas($horario);

                                /* --------------------------------------------------------------- */

                            } else {
                                /* EMPLEADOS POR AREA */
                                /*  dd($fechaHoy); */
                                $horario = DB::table('horario_empleado as he')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('dispositivo_empleado as de', 'he.empleado_emple_id', '=', 'de.emple_id')
                                    ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                                        'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')

                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)

                                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->where('de.estado', '=', 1)
                                    ->where('de.idDispositivos', '=', $idbiometrico)
                                    ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                                    ->orderBy('he.empleado_emple_id')
                                    ->paginate();

                                $horario = insertarPausas($horario);
                            }
                        }

                    } else {

                        /*  dd($fechaHoy); */
                        $horario = DB::table('horario_empleado as he')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                            ->join('dispositivo_empleado as de', 'he.empleado_emple_id', '=', 'de.emple_id')
                            ->join('empleado as e', 'de.emple_id', '=', 'e.emple_id')
                            ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                                'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')
                            ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('de.estado', '=', 1)
                            ->where('de.idDispositivos', '=', $idbiometrico)
                            ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                            ->orderBy('he.empleado_emple_id')
                            ->paginate();

                        $horario = insertarPausas($horario);
                    }

                    /* AQUI ACABA FILTRACION POR EMPLEADO */
                    /* ----------------------------------------------- */
                }
            } else {
                /* VERIFICAMOS POR PRECAUSIO SI TIENE PERMSO PARA AREA  */
                if ($biometricoPer->dispo_porArea == 1) {
                    /* FILTRO POR AREAAAS */
                    /* --------------------------------------------------- */
                    /* PRIMERO TODO DENTRO DE VALIDACIONES POR USUARIO */
                    if ($usuario_organizacion->rol_id == 3) {

                        if ($invitadod->verTodosEmps == 1) {
                            /* CUANDO TIENE TODOS LOS EMPELADOS */
                            /*  dd($fechaHoy); */
                            $horario = DB::table('horario_empleado as he')
                                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                                ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                                ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                                ->where('e.emple_estado', '=', 1)
                                ->where('e.asistencia_puerta', '=', 1)
                                ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                    'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                                    'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')
                                ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                                ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                ->where('da.estado', '=', 1)
                                ->where('da.idDispositivos', '=', $idbiometrico)
                                ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                                ->orderBy('he.empleado_emple_id')
                                ->paginate();
                            $horario = insertarPausas($horario);

                        } else {
                            /* CUADNO TIENE EMPLEADOS ASIGNADOS */
                            $invitado_empleadoIn = DB::table('invitado_empleado as invem')
                                ->where('invem.idinvitado', '=', $invitadod->idinvitado)
                                ->where('invem.area_id', '=', null)
                                ->where('invem.emple_id', '!=', null)
                                ->get()->first();
                            /* empleados x id */
                            if ($invitado_empleadoIn != null) {
                                /*  dd($fechaHoy); */
                                $horario = DB::table('horario_empleado as he')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                                    ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                                    ->join('invitado_empleado as inve', 'e.emple_id', '=', 'inve.emple_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                        'h.horaF', 'h.horario_tolerancia as toleranciaI',
                                        'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')
                                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('da.estado', '=', 1)
                                    ->where('da.idDispositivos', '=', $idbiometrico)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                                    ->orderBy('he.empleado_emple_id')
                                    ->paginate();

                                $horario = insertarPausas($horario);

                            } else {
                                /* EMPLEADOS POR AREA */
                                /*  dd($fechaHoy); */
                                $horario = DB::table('horario_empleado as he')
                                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                                    ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                                    ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                                    ->join('invitado_empleado as inve', 'e.emple_area', '=', 'inve.area_id')
                                    ->join('invitado as invi', 'inve.idinvitado', '=', 'invi.idinvitado')
                                    ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                                    ->leftJoin('area as ar', 'e.emple_area', '=', 'ar.area_id')
                                    ->where('e.emple_estado', '=', 1)
                                    ->where('e.asistencia_puerta', '=', 1)
                                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                                        'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')
                                    ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                                    ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                                    ->where('da.estado', '=', 1)
                                    ->where('da.idDispositivos', '=', $idbiometrico)
                                    ->where('invi.estado', '=', 1)
                                    ->where('invi.idinvitado', '=', $invitadod->idinvitado)
                                    ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                                    ->orderBy('he.empleado_emple_id')
                                    ->paginate();

                                $horario = insertarPausas($horario);
                            }
                        }

                    } else {
                        /*  dd('entre cuando soy admin'); */
                        /* CUANDO TIENE TODOS LOS EMPELADOS */
                        /*  dd($fechaHoy); */
                        $horario = DB::table('horario_empleado as he')
                            ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                            ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                            ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                            ->join('dispositivo_area as da', 'e.emple_area', '=', 'da.area_id')
                            ->join('historial_horarioempleado as histo', 'he.horarioEmp_id', '=', 'histo.horarioEmp_id')
                            ->where('e.emple_estado', '=', 1)
                            ->where('e.asistencia_puerta', '=', 1)
                            ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                                'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF',
                                'he.fuera_horario', 'histo.fechaCambio as fechaCambio', 'histo.estadohorarioEmp as estado')
                            ->where(DB::raw('DATE(hd.start)'), '=', $fechaHoy)

                            ->where('e.organi_id', '=', $usuario_organizacion->organi_id)
                            ->where('da.estado', '=', 1)
                            ->where('da.idDispositivos', '=', $idbiometrico)
                            ->whereDate('histo.fechaCambio', '=', $fechaHoy)
                            ->orderBy('he.empleado_emple_id')
                            ->paginate();
                        $horario = insertarPausas($horario);

                    }

                }

            }

        }

        /* --------------------------------------------------------------------------------------------- */

        if ($horario != null) {
            return response()->json($horario);
        } else {
            return response()->json(array('status' => 400, 'title' => 'Empleados no encontrados',
                'detail' => 'No se encontro empleados relacionados con este dispositivo'), 400);
        }
    }

    public function registroHuella(Request $request)
    {

        $arrayDatos = new Collection();

        foreach ($request->all() as $req) {
            $errores = [];
            $validacion = Validator::make($req, [
                'idempleado' => 'required',
                'posicion_huella' => 'required',
                'tipo_registro' => 'required',
                'path' => 'required',
                'iFlag' => 'required',
                'iFaceIndex' => 'required',
                'iLength' => 'required',
                'id_plantilla' => 'required',

            ], [
                'required' => ':atributo es obligatorio',
            ]);
            if ($validacion->fails()) {
                //: ARRAY DE ERRORES
                if (isset($validacion->failed()["idempleado"])) {
                    array_push($errores, array("campo" => "idempleado", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["posicion_huella"])) {
                    array_push($errores, array("campo" => "posicion_huella", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["tipo_registro"])) {
                    array_push($errores, array("campo" => "tipo_registro", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["path"])) {
                    array_push($errores, array("campo" => "path", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["iFlag"])) {
                    array_push($errores, array("campo" => "iFlag", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["iFaceIndex"])) {
                    array_push($errores, array("campo" => "iFaceIndex", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["iLength"])) {
                    array_push($errores, array("campo" => "iLength", "mensaje" => "Es obligatorio"));
                }
                if (isset($validacion->failed()["id_plantilla"])) {
                    array_push($errores, array("campo" => "id_plantilla", "mensaje" => "Es obligatorio"));
                }


                return response()->json(array("errores" => $errores), 400);
            }
            /*  RECIBO PARAMENTROS*/
            $idempleado = $req['idempleado'];
            $posicion_huella = $req['posicion_huella'];
            $tipo_registro = $req['tipo_registro'];
            $path = $req['path'];
            $iFlag = $req['iFlag'];
            $iFaceIndex = $req['iFaceIndex'];
            $iLength = $req['iLength'];
            $id_plantilla = $req['id_plantilla'];
            /* ----------------------------- */
            /* VALIDANDO EMPLEADOIIIII */
            $empleados = DB::table('empleado as e')
                ->join('organizacion as or', 'e.organi_id', '=', 'or.organi_id')
                ->where('e.emple_id', '=', $req['idempleado'])
                ->where('e.emple_estado', '=', 1)
                ->get()->first();

            /* -----------REGISTRO -----------------------------*/
            $plantilla_empleadobio = new plantilla_empleadobio();
            /* -------------------------------------------- */

            /* PRIMERO VALIDEMOS QUE NO SE REPITA */
            $plantilla_empleadobioVali = DB::table('plantilla_empleadobio')
                ->where('idempleado', '=', $req['idempleado'])
                ->where('posicion_huella', '=', $req['posicion_huella'])
                ->where('tipo_registro', '=', $req['tipo_registro'])
                ->get()->first();

            if ($plantilla_empleadobioVali) {

                $plantilla_empleadobioArray = array(
                    'id' => $plantilla_empleadobioVali->id,
                    'idempleado' => $idempleado,
                    'error' => 'Empleado con biometria duplicada',
                    'id_plantilla' =>$id_plantilla,
                    'estado' => false);

                /* ---------------------------- */
            } else {

                /* VERIFICAMOS SI EMPLEADO EXISTE */
                if ($empleados) {
                    $plantilla_empleadobio->idempleado = $idempleado;

                    /* VALIDAMOS QUE POSICION DE HUELLA SEA DE 0 A 9  */
                    if ($posicion_huella < 10 && $posicion_huella >= 0) {
                        $plantilla_empleadobio->posicion_huella = $posicion_huella;

                        /* ----------VALIDANDO TIPO_REGISTRO */
                        $tipo_registroBD = DB::table('tipo_registrobio')
                            ->where('idtipo_registro', '=', $tipo_registro)
                            ->get()->first();

                        /* SI EXISTE */
                        if ($tipo_registroBD) {
                            $plantilla_empleadobio->tipo_registro = $tipo_registro;
                            $plantilla_empleadobio->path = $path;
                            $plantilla_empleadobio->iFlag = $iFlag;
                            $plantilla_empleadobio->iFaceIndex = $iFaceIndex;
                            $plantilla_empleadobio->iLength = $iLength;
                            $plantilla_empleadobio->save();

                            $plantilla_empleadobioArray = array(
                                'id' => $plantilla_empleadobio->id,
                                'idempleado' => $idempleado,
                                'posicion_huella' => $posicion_huella,
                                'tipo_registro' => $tipo_registro,
                                'id_plantilla' =>$id_plantilla,
                                'estado' => true);

                        } else {
                            $plantilla_empleadobioArray = array(
                                'idempleado' => $idempleado,
                                'error' => 'Tipo de registro no encontrado',
                                'id_plantilla' =>$id_plantilla,
                                'estado' => false);
                        }

                    }

                    /* SI POSICION DE HUELLA ES INCORRECTA */
                    else {
                        $plantilla_empleadobioArray = array(
                            'idempleado' => $idempleado,
                            'error' => 'Posicion de huella incorrecta',
                            'id_plantilla' =>$id_plantilla,
                            'estado' => false);
                    }

                }

                /* SI NO EXISTE EMPLEADO */
                else {
                    $plantilla_empleadobioArray = array(
                        'idempleado' => $idempleado,
                        'error' => 'No se encontro empleados con este id',
                        'id_plantilla' =>$id_plantilla,
                        'estado' => false);

                }
            }

            /* INSERTAMO A AARRAY  */
            $arrayDatos->push($plantilla_empleadobioArray);
            /* ---------------------------- */
        }
        if ($arrayDatos != null) {
            return response()->json($arrayDatos);
        } else {
            return response()->json(array('status' => 400, 'title' => 'No se pudo registrar',
                'detail' => 'No se encontro empleados relacionados con este dispositivo'), 400);
        }

    }

    public function importar(Request $request)
    {

        /*OBTENEMOS ARCHIVO FILE Y CREAMOS NUEBO COLLECTION   */
        $contents = new Collection();
        $file = $request->file('file');
        $file1 = File::get($file);
        /* --------------------------------------------- */

        /* obtenemos fila de archivo hacemos explode para espacio*/
        $row = explode("\r\n", $file1);
        /* ----------------------------------------------------- */

        /*---- RECORREMOS FILAS------------- */
        foreach ($row as $filas) {

            /* SI LAS FILAS NO ESTAN VACIAS LA GUARDAMOS */
            if ($filas != "" || $filas != null) {

                /* SEPARAMOS POR COMAS */
                $row2 = explode(",", $filas);
                /* ------------------------- */
                /* CREAMOS ARRAY PARA INSERTAR EN COLLECTION */
                $datos = ["tipoMarcacion" => $row2[0], "fechaMarcacion" => $row2[1],
                    "idEmpleado" => $row2[2],
                    "idDisposi" => $row2[1], "idHoraEmp" => $row2[2]];
                /* ---------------------------------------------------- */

                /* INSERTAMOS DATOS */
                $contents->push($datos);
            }
            /* ---------------------------------------- */

        }
        /* --------------------------------- */

        /* ORDENAMOS ARRAY POR FECHA DE MARCACION */
        $arrayOrdenado = $contents->sortBy('fechaMarcacion');
        $arrayOrdenado->values()->all();
        dd($arrayOrdenado);

    }

    public function importarJS(Request $request)
    {

        /*OBTENEMOS ARCHIVO FILE Y CREAMOS NUEBO COLLECTION   */
        $contents = new Collection();
        $file = $request->file('file');
        $data = file_get_contents($file);
        $datosJ = json_decode($data, true);

        foreach ($datosJ as $req) {

            $datos = ['idDisposi' => $req['idDisposi'], 'idEmpleado' => $req['idEmpleado'],
                'tipoMarcacion' => $req['tipoMarcacion'], 'fechaMarcacion' => $req['fechaMarcacion'],
                'idHoraEmp' => $req['idHoraEmp'],
            ];

            $contents->push($datos);
        }
        /* --------------------------------------------- */

        /* ORDENAMOS ARRAY POR FECHA DE MARCACION */
        $arrayOrdenado = $contents->sortBy('fechaMarcacion');
        $arrayOrdenado->values()->all();
        dd($arrayOrdenado);

    }

    public function descargarExtractor()
    {
        return response()->download(app_path() . "/Extractor/ExtractorRHnube.zip");
    }



    public function marcacionBiometrico3(Request $request)
    {

        //*OBTENER FECHA ACTUAL
        $fechaHoy = Carbon::now('America/Lima');
        $horaActual = $fechaHoy->isoFormat('YYYY-MM-DD HH:mm:ss');

        /****************OBTENEMOS ARCHIVO FILE Y CREAMOS NUEBO COLLECTION***********************   */
        $contents = new Collection();
        $file = $request->file('file');
        $data = file_get_contents($file);

        $datosJ = json_decode($data, true);
        $datosJ = collect($datosJ)->sortBy('fechaMarcacion')->values()->toArray();

        foreach ($datosJ as $req) {

            /* VALIDANDO TIPO DE MARCACION */

            $datos = ['idDisposi' => $req['idDisposi'], 'idEmpleado' => $req['idEmpleado'],
                'fechaMarcacion' => $req['fechaMarcacion'],
                'id' => $req['id'],
            ];

            $contents->push($datos);
        }

        //* ORDENAMOS ARRAY POR FECHA DE MARCACION */
        $arrayOrdenado = $contents->sortBy('fechaMarcacion');
        $arrayOrdenado->values()->all();

        /***************************************************************************  */

        /*------------------- RECORREMOS ARRAY ORDENADO------------------------------------------------------- */

        $arrayDatos = new Collection();

        foreach ($arrayOrdenado as $req) {

            //*FECHA DE MARCACION
            $fecha1V = Carbon::create($req['fechaMarcacion'])->toDateString();
            $fecha1Vdespues = Carbon::create($fecha1V)->addDays(1)->format('Y-m-d');
            $diaAnt=Carbon::create($fecha1V)->subDays(1)->format('Y-m-d');

            /* --------------------------------------------------------------- */

            /*-------------- VERIFICAMOS SI TIENE HORARIO--------------------------- */
            $horarioEmpleado = DB::table('horario_empleado as he')
                ->join('horario as h', 'h.horario_id', '=', 'he.horario_horario_id')
                ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                ->select(
                    'he.horarioEmp_id as idHorarioEmpleado',
                    'h.horaI as horaI',
                    'h.horaF as horaF',
                    'h.horario_tolerancia as toleranciaI',
                    'h.horario_toleranciaF as toleranciaF',
                    'hd.start','h.horario_id'
                )
                ->where('he.empleado_emple_id', '=', $req['idEmpleado'])
                ->whereBetween(DB::raw('DATE(hd.start)'),  [$diaAnt,$fecha1Vdespues])
                ->where('he.estado', '=', 1)
                ->orderBy('h.horaI', 'ASC')
                ->get();

            //* SI NO TIENE HORARIO
            if ($horarioEmpleado->isEmpty()) {
                $conhorario = 0;
            } else {
                //*SI TIENE HORARIO

                foreach ($horarioEmpleado as $horarioEmpleados) {

                    //*verificamos si hora fin de horario pertenece a hoy
                    $fecha = Carbon::create($horarioEmpleados->start);
                    $fechaHorario = $fecha->isoFormat('YYYY-MM-DD');
                    $despues = $fecha->addDays(1);
                    $fechaMan = $despues->isoFormat('YYYY-MM-DD');

                    if (Carbon::parse($horarioEmpleados->horaF)->lt(Carbon::parse($horarioEmpleados->horaI))) {

                        $horarioEmpleados->horaI = Carbon::parse($fechaHorario . " " . $horarioEmpleados->horaI)->subMinutes($horarioEmpleados->toleranciaI);
                        $horarioEmpleados->horaF = Carbon::parse($fechaMan . " " . $horarioEmpleados->horaF)->addMinutes($horarioEmpleados->toleranciaF);
                    } else {
                        $horarioEmpleados->horaI = Carbon::parse($fechaHorario . " " . $horarioEmpleados->horaI)->subMinutes($horarioEmpleados->toleranciaI);
                        $horarioEmpleados->horaF = Carbon::parse($fechaHorario . " " . $horarioEmpleados->horaF)->addMinutes($horarioEmpleados->toleranciaF);
                    }
                }


                //*verificamos si esta dentro de horario

                foreach ($horarioEmpleado as $horarioDentro) {

                    $fechaHorahoy = Carbon::create($req['fechaMarcacion']);
                    if ($fechaHorahoy->gte($horarioDentro->horaI) && $fechaHorahoy->lte($horarioDentro->horaF)) {
                        //*se encontro 1 horario y se detiene foreach
                        $conhorario = $horarioDentro->idHorarioEmpleado;
                        $idhorario = $horarioDentro->horario_id;
                        $inicioH= ($horarioDentro->horaI)->format("H:i:s");
                        break;
                    } else {
                        //*NO SE ENCONTRO HORARIO

                        $conhorario = 0;
                    }

                }


            }
            /* --------------------CALCULAMOS EL TIPO DE MARCACION----------------------- */
            /* ----------------------SI RECIBO SIN HORARIO----------------------------- */
            if ($conhorario == 0) {

                //************ARRAY GENERALES**************************************
                //*marcacion cualquiera d empleado de hoy
                $marcacion_puertaVacio = DB::table('marcacion_puerta as mv')
                    ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha1V)
                    ->whereNull('mv.horarioEmp_id')
                    ->get();

                //*ultima marcacion de emmpleado
                $marcacion_puertaVerif = DB::table('marcacion_puerta as mv')
                    ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha1V)
                    ->whereNull('mv.horarioEmp_id')
                    ->orderby(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), 'ASC')
                    ->get()->last();

                //* SI NO TENGO SALIDA
                $marcacion_puertaVerif2 = DB::table('marcacion_puerta as mv')
                    ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->where('mv.marcaMov_salida', '=', null)
                    ->whereDate('mv.marcaMov_fecha', '=', $fecha1V)
                    ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                    ->whereNull('mv.horarioEmp_id')
                    ->orderby('marcaMov_fecha', 'ASC')
                    ->get()->first();

                /* **********FIN ARRAY GENERALES************** */

                /* VERIFICO SI NO HAY MARCACIONES ANTES */

                if ($marcacion_puertaVacio->isEmpty()) {

                    //* entrada porque es la 1ra marcacion
                    $tipoMarcacion = 1;
                } else {
                    /* YA HAY MARCACIONES PARA ESTE EMPLEADO Y FECHA */
                    /* ------------------------------------------------------ */
                    /* SI HAY MARCACION CON TODOS LOS DATOS */
                    if ($marcacion_puertaVerif->marcaMov_fecha != null && $marcacion_puertaVerif->marcaMov_salida != null) {

                        //* entrada porque mi anterior marcacion ya tiene entrrada y salida
                        $tipoMarcacion = 1;
                    } else {

                        //*si no tengo salida
                        if ($marcacion_puertaVerif2) {

                            //*salida
                            $tipoMarcacion = 0;
                        } else {
                            //*entrada->en casa que por la web se crea marcacion con salida
                            $tipoMarcacion = 1;
                        }
                    }
                }
            }
            /* ------------------------------------------------------------------ */
            else {

                /* ------CON HORARIO-------------- */
                //************ARRAY GENERALES**************************************
                //*marcacion cualquiera d empleado de hoy
                $marcacion_puertaVacio = DB::table('marcacion_puerta as mv')
                    ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                   /*  ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha1V) */
                    ->where('mv.horarioEmp_id', '=', $conhorario)
                    ->get();

                //*ultima marcacion de emmpleado
                $marcacion_puertaVerif = DB::table('marcacion_puerta as mv')
                    ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                   /*  ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha1V) */
                    ->where('mv.horarioEmp_id', '=', $conhorario)
                    ->orderby(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), 'ASC')
                    ->get()->last();

                //* SI NO TENGO SALIDA
                $marcacion_puertaVerif2 = DB::table('marcacion_puerta as mv')
                    ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->where('mv.marcaMov_salida', '=', null)
                    /* ->whereDate('mv.marcaMov_fecha', '=', $fecha1V) */
                    ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                    ->where('mv.horarioEmp_id', '=', $conhorario)
                    ->orderby('marcaMov_fecha', 'ASC')
                    ->get()->first();

                /* **********FIN ARRAY GENERALES************** */

                /* VERIFICO SI NO HAY MARCACIONES ANTES */

                if ($marcacion_puertaVacio->isEmpty()) {

                    //* entrada porque es la 1ra marcacion
                    $tipoMarcacion = 1;
                } else {
                    /* YA HAY MARCACIONES PARA ESTE EMPLEADO Y FECHA */
                    /* ------------------------------------------------------ */
                    /* SI HAY MARCACION CON TODOS LOS DATOS */
                    if ($marcacion_puertaVerif->marcaMov_fecha != null && $marcacion_puertaVerif->marcaMov_salida != null) {

                        //* entrada porque mi anterior marcacion ya tiene entrrada y salida
                        $tipoMarcacion = 1;
                    } else {

                        //*si no tengo salida
                        if ($marcacion_puertaVerif2) {

                            //*salida
                            $tipoMarcacion = 0;
                        } else {
                            //*entrada->en casa que por la web se crea marcacion con salida
                            $tipoMarcacion = 1;
                        }
                    }
                }

                /* ---------------FIN DE FUNCION-------------------------- */
            }

            /* --------------------------------------------------------------- */

            /* PRIMERO VALIDAMOS FECHA */
            if (Carbon::create($req['fechaMarcacion'])->gt(Carbon::create($horaActual))) {
                $respuestaMarcacion = array(
                    'id' => $req['id'],
                    'error' => 'Fecha invalida',
                    'estado' => false);
            } else {
                /* VALIDANDO EMPLEADOIIIII */
                $empleados = DB::table('empleado as e')
                    ->join('organizacion as or', 'e.organi_id', '=', 'or.organi_id')
                    ->where('e.emple_id', '=', $req['idEmpleado'])
                    ->where('e.emple_estado', '=', 1)
                    ->get()->first();

                if ($empleados) {

                    //*VALIDANDO QUE MARCACION NO SE REPITA
                    $marcacion_puertaVerifrepeticion = DB::table('marcacion_puerta as mv')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->where('mv.marcaMov_fecha', '=', $req['fechaMarcacion'])
                    ->orWhere('mv.marcaMov_salida', '=', $req['fechaMarcacion'])
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                        ->get()->first();
                      /*   dd($req['idEmpleado'],$marcacion_puertaVerifrepeticion); */

                    if (!$marcacion_puertaVerifrepeticion) {
                        /*CUADNO ES MARCACION DE ENTRADA */

                        //*OBTENEMOS ULTIMA MARCACION cuando la salida es mayor de lo nuevo q recibo
                        $marcacion_puertaVerifMayor = DB::table('marcacion_puerta as mv')
                            ->where('mv.marcaMov_salida', '>=', $req['fechaMarcacion'])
                            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                            ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null ,mv.marcaMov_salida, mv.marcaMov_fecha)'), '=', $fecha1V)
                            ->where(DB::raw('IF(mv.marcaMov_fecha is null,0,mv.marcaMov_fecha)'), '<=', $req['fechaMarcacion'])
                            ->where( function($query) use($conhorario){
                                if($conhorario!=0){
                                    $query->where('mv.horarioEmp_id', '=', $conhorario);
                                }
                                else{
                                    $query->whereNull('mv.horarioEmp_id');
                                }
                            })
                            ->orderby('marcaMov_fecha', 'ASC')
                            ->get()->first();
                        if ($tipoMarcacion == 1) {
                          /*   dd($marcacion_puertaVerifMayor); */
                            if ($marcacion_puertaVerifMayor) {
                                 //*si tengo marcacion de entrada

                                if($marcacion_puertaVerifMayor->marcaMov_fecha!=null){
                                    //*VALIDANDO FECHA DE NUEVO
                                    $marcacion_puertaVerifrepeticionNu = DB::table('marcacion_puerta as mv')
                                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                    ->where('mv.marcaMov_fecha', '=', $req['fechaMarcacion'])
                                    ->orWhere('mv.marcaMov_salida', '=', $req['fechaMarcacion'])
                                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                    ->get()->first();
                                    if($marcacion_puertaVerifrepeticionNu){
                                        $respuestaMarcacion = array(
                                            'id' => $req['id'],
                                            'error' => 'Fecha de marcacion de empleado ya registrada',
                                            'estado' => true);
                                    } else{
                                            $marcacion_biometrico = new marcacion_puerta();

                                            $marcacion_biometrico->marcaMov_fecha =$marcacion_puertaVerifMayor->marcaMov_salida;
                                            /* -------------------- */

                                            $marcacion_biometrico->marcaMov_emple_id = $marcacion_puertaVerifMayor->marcaMov_emple_id;
                                            $marcacion_biometrico->dispositivoEntrada = $marcacion_puertaVerifMayor->dispositivoSalida;

                                            $marcacion_biometrico->organi_id = $marcacion_puertaVerifMayor->organi_id;

                                            $marcacion_biometrico->horarioEmp_id =$marcacion_puertaVerifMayor->horarioEmp_id;

                                            $marcacion_biometrico->tipoMarcacionB = 1;

                                            $marcacion_biometrico->save();

                                            $marcacion_biometrico2 = marcacion_puerta::find($marcacion_puertaVerifMayor->marcaMov_id);
                                            $marcacion_biometrico2->marcaMov_salida = $req['fechaMarcacion'];
                                            $marcacion_biometrico2->dispositivoSalida = $req['idDisposi'];
                                            $marcacion_biometrico2->save();

                                            $respuestaMarcacion = array(
                                                'id' => $req['id'],
                                                'estado' => true);
                                    }

                                }
                                else{
                                    //*tengo una marcacion donde solo tiene salida y es mayor a nueva marcacion
                                    $marcacion_puertaVerifrepeticionNu = DB::table('marcacion_puerta as mv')
                                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                    ->where('mv.marcaMov_fecha', '=', $req['fechaMarcacion'])
                                    ->orWhere('mv.marcaMov_salida', '=', $req['fechaMarcacion'])
                                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                    ->get()->first();
                                    if($marcacion_puertaVerifrepeticionNu){
                                        $respuestaMarcacion = array(
                                            'id' => $req['id'],
                                            'error' => 'Fecha de marcacion de empleado ya registrada',
                                            'estado' => true);
                                    } else{
                                    $marcacion_biometrico2 = marcacion_puerta::find($marcacion_puertaVerifMayor->marcaMov_id);
                                    $marcacion_biometrico2->marcaMov_fecha=$req['fechaMarcacion'];

                                    $marcacion_biometrico2->dispositivoEntrada = $req['idDisposi'];
                                    $marcacion_biometrico2->save();
                                    $respuestaMarcacion = array(
                                        'id' => $req['id'],
                                        'estado' => true);
                                    }

                                }
                            } else {

                                $marcacion_puertaVerifMayor2 = DB::table('marcacion_puerta as mv')
                                ->where('mv.marcaMov_fecha', '>=', $req['fechaMarcacion'])
                                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null ,mv.marcaMov_salida, mv.marcaMov_fecha)'), '=', $fecha1V)
                                ->where(DB::raw('IF(mv.marcaMov_salida is null,0,mv.marcaMov_salida)'), '>=', $req['fechaMarcacion'])
                                 ->where( function($query) use($conhorario){
                                    if($conhorario!=0){
                                        $query->where('mv.horarioEmp_id', '=', $conhorario);
                                    }
                                    else{
                                        $query->whereNull('mv.horarioEmp_id');
                                    }
                                })
                                ->orderby('marcaMov_fecha', 'ASC')
                                ->get()->first();


                                if($marcacion_puertaVerifMayor2){
                                    $marcacion_puertaVerifrepeticionNu = DB::table('marcacion_puerta as mv')
                                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                    ->where('mv.marcaMov_fecha', '=', $req['fechaMarcacion'])
                                    ->orWhere('mv.marcaMov_salida', '=', $req['fechaMarcacion'])
                                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                    ->get()->first();
                                    if($marcacion_puertaVerifrepeticionNu){
                                        $respuestaMarcacion = array(
                                            'id' => $req['id'],
                                            'error' => 'Fecha de marcacion de empleado ya registrada',
                                            'estado' => true);
                                    } else{
                                       $marcacion_biometrico = new marcacion_puerta();

                                        $marcacion_biometrico->marcaMov_fecha =$marcacion_puertaVerifMayor2->marcaMov_salida;
                                        /* -------------------- */

                                        $marcacion_biometrico->marcaMov_emple_id = $marcacion_puertaVerifMayor2->marcaMov_emple_id;
                                        $marcacion_biometrico->dispositivoEntrada = $marcacion_puertaVerifMayor2->dispositivoSalida;

                                        $marcacion_biometrico->organi_id = $marcacion_puertaVerifMayor2->organi_id;

                                        $marcacion_biometrico->horarioEmp_id =$marcacion_puertaVerifMayor2->horarioEmp_id;

                                        $marcacion_biometrico->tipoMarcacionB = 1;

                                        $marcacion_biometrico->save();

                                        $marcacion_biometrico2 = marcacion_puerta::find($marcacion_puertaVerifMayor2->marcaMov_id);
                                        $marcacion_biometrico2->marcaMov_fecha = $req['fechaMarcacion'];
                                        $marcacion_biometrico2->marcaMov_salida = $marcacion_puertaVerifMayor2->marcaMov_fecha;
                                        $marcacion_biometrico2->dispositivoEntrada = $req['idDisposi'];
                                        $marcacion_biometrico2->dispositivoSalida = $marcacion_puertaVerifMayor2->dispositivoEntrada;
                                        $marcacion_biometrico2->save();

                                        $respuestaMarcacion = array(
                                            'id' => $req['id'],
                                            'estado' => true);
                                        }
                                }
                                else{

                                    //*SI MARCACION DE SALIDA ES MENOR A LO QUE RECIBO Y NO TENGO ENTRADA
                                    $marcacion_puertaVerimenorsalida = DB::table('marcacion_puerta as mv')
                                    ->where('mv.marcaMov_salida', '<=', $req['fechaMarcacion'])
                                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                    ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null ,mv.marcaMov_salida, mv.marcaMov_fecha)'), '=', $fecha1V)
                                    ->where('mv.marcaMov_fecha', '=',null)
                                    ->where( function($query) use($conhorario){
                                        if($conhorario!=0){
                                            $query->where('mv.horarioEmp_id', '=', $conhorario);
                                        }
                                        else{
                                            $query->whereNull('mv.horarioEmp_id');
                                        }
                                    })
                                    ->orderby('marcaMov_salida', 'ASC')
                                    ->get()->first();

                                    if($marcacion_puertaVerimenorsalida){
                                        $marcacion_puertaVerifrepeticionNu = DB::table('marcacion_puerta as mv')
                                        ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                        ->where('mv.marcaMov_fecha', '=', $req['fechaMarcacion'])
                                        ->orWhere('mv.marcaMov_salida', '=', $req['fechaMarcacion'])
                                        ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                        ->get()->first();
                                        if($marcacion_puertaVerifrepeticionNu){
                                            $respuestaMarcacion = array(
                                                'id' => $req['id'],
                                                'error' => 'Fecha de marcacion de empleado ya registrada',
                                                'estado' => true);
                                        } else{
                                        $marcacion_biometrico2 = marcacion_puerta::find($marcacion_puertaVerimenorsalida->marcaMov_id);
                                        $marcacion_biometrico2->marcaMov_fecha = $marcacion_puertaVerimenorsalida->marcaMov_salida;
                                        $marcacion_biometrico2->dispositivoEntrada = $marcacion_puertaVerimenorsalida->dispositivoSalida;
                                        $marcacion_biometrico2->marcaMov_salida = $req['fechaMarcacion'];
                                        $marcacion_biometrico2->dispositivoSalida = $req['idDisposi'];
                                        $marcacion_biometrico2->save();
                                        $respuestaMarcacion = array(
                                            'id' => $req['id'],
                                            'estado' => true);
                                         }


                                    }
                                    else{
                                         //*SI MARCACION DE ENTRADA ES MAYOR A LO QUE RECIBO Y NO TENGO SALIDA
                                        $marcacion_puertaVerimayorEntrada = DB::table('marcacion_puerta as mv')
                                        ->where('mv.marcaMov_fecha', '>=', $req['fechaMarcacion'])
                                        ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                        ->where('mv.marcaMov_salida', '=',null)
                                        ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null ,mv.marcaMov_salida, mv.marcaMov_fecha)'), '=', $fecha1V)
                                        ->where( function($query) use($conhorario){
                                            if($conhorario!=0){
                                                $query->where('mv.horarioEmp_id', '=', $conhorario);
                                            }
                                            else{
                                                $query->whereNull('mv.horarioEmp_id');
                                            }
                                        })
                                        ->orderby('marcaMov_fecha', 'ASC')
                                        ->get()->first();

                                        if($marcacion_puertaVerimayorEntrada){
                                            $marcacion_puertaVerifrepeticionNu = DB::table('marcacion_puerta as mv')
                                            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                            ->where('mv.marcaMov_fecha', '=', $req['fechaMarcacion'])
                                            ->orWhere('mv.marcaMov_salida', '=', $req['fechaMarcacion'])
                                            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                            ->get()->first();
                                            if($marcacion_puertaVerifrepeticionNu){
                                                $respuestaMarcacion = array(
                                                    'id' => $req['id'],
                                                    'error' => 'Fecha de marcacion de empleado ya registrada',
                                                    'estado' => true);
                                            } else{
                                            $marcacion_biometrico2 = marcacion_puerta::find($marcacion_puertaVerimayorEntrada->marcaMov_id);
                                            $marcacion_biometrico2->marcaMov_fecha = $req['fechaMarcacion'];
                                            $marcacion_biometrico2->dispositivoEntrada =$req['idDisposi'];
                                            $marcacion_biometrico2->marcaMov_salida = $marcacion_puertaVerimayorEntrada->marcaMov_fecha;
                                            $marcacion_biometrico2->dispositivoSalida =$marcacion_puertaVerimayorEntrada->dispositivoEntrada;
                                            $marcacion_biometrico2->save();
                                            $respuestaMarcacion = array(
                                                'id' => $req['id'],
                                                'estado' => true);
                                            }
                                        }
                                        else{
                                            $marcacion_puertaVerifrepeticionNu = DB::table('marcacion_puerta as mv')
                                            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                            ->where('mv.marcaMov_fecha', '=', $req['fechaMarcacion'])
                                            ->orWhere('mv.marcaMov_salida', '=', $req['fechaMarcacion'])
                                            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                            ->get()->first();
                                            if($marcacion_puertaVerifrepeticionNu){
                                                $respuestaMarcacion = array(
                                                    'id' => $req['id'],
                                                    'error' => 'Fecha de marcacion de empleado ya registrada',
                                                    'estado' => true);
                                                } else{
                                                    $marcacion_biometrico = new marcacion_puerta();

                                                    $marcacion_biometrico->marcaMov_fecha = $req['fechaMarcacion'];
                                                    /* -------------------- */

                                                    $marcacion_biometrico->marcaMov_emple_id = $req['idEmpleado'];
                                                    $marcacion_biometrico->dispositivoEntrada = $req['idDisposi'];

                                                    $marcacion_biometrico->organi_id = $empleados->organi_id;

                                                    if ($conhorario == 0) {
                                                        $marcacion_biometrico->horarioEmp_id = null;
                                                    } else {
                                                        $marcacion_biometrico->horarioEmp_id = $conhorario;
                                                    }

                                                    $marcacion_biometrico->tipoMarcacionB = 1;

                                                    $marcacion_biometrico->save();

                                                    $respuestaMarcacion = array(
                                                        'id' => $req['id'],
                                                        'estado' => true);
                                                }
                                        }

                                        }
                                    }



                            }

                            /* --------------------------------------------------------------- */
                        }
                        /* CUADNO ES TIPO 0  SON SALIDA DE MARCACION Y FIN DE PAUSA  */
                        else {

                            /* AQUI VALIDAREMOS PARA INSERTAR LA SALIDA */

                            if ($tipoMarcacion == 0) {

                                /* CUADNO ES SALIDA */

                                /* CONVERTIMOS LA FECHA DE MARCACION EN DATE */
                                $fecha1 = Carbon::create($req['fechaMarcacion'])->toDateString();

                                /* VERIFICAMOS  PARA EMPAREJAR  */

                                if ($conhorario == 0) {
                                    $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                                        ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                                        ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                        ->where('mv.marcaMov_salida', '=', null)
                                        ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                                        ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                        ->whereNull('mv.horarioEmp_id')
                                        ->orderby('marcaMov_fecha', 'ASC')
                                        ->get()->last();
                                } else {
                                    $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                                        ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                                        ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                        ->where('mv.marcaMov_salida', '=', null)
                                       /*  ->whereDate('mv.marcaMov_fecha', '=', $fecha1) */
                                        ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                        ->where('mv.horarioEmp_id', '=', $conhorario)
                                        ->orderby('marcaMov_fecha', 'ASC')
                                        ->get()->last();
                                }

                                if ($marcacion_puerta1) {
                                    $marcacion_puertaVerifrepeticionNu = DB::table('marcacion_puerta as mv')
                                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                    ->where('mv.marcaMov_fecha', '=', $req['fechaMarcacion'])
                                    ->orWhere('mv.marcaMov_salida', '=', $req['fechaMarcacion'])
                                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                    ->get()->first();
                                    if($marcacion_puertaVerifrepeticionNu){
                                        $respuestaMarcacion = array(
                                            'id' => $req['id'],
                                            'error' => 'Fecha de marcacion de empleado ya registrada',
                                            'estado' => true);
                                    } else{
                                        $marcacion_biometrico = marcacion_puerta::find($marcacion_puerta1->marcaMov_id);
                                        $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                                        $marcacion_biometrico->dispositivoSalida = $req['idDisposi'];
                                        $marcacion_biometrico->save();

                                        $respuestaMarcacion = array(
                                            'id' => $req['id'],
                                            'estado' => true);
                                        }

                                }
                                else{

                                }

                            }

                        }

                          //*********************VERIFICACION PAUSAS AUTOMATICA***********************/
                    if($conhorario != 0){

                        //*VERIFICO QUE SEA LA PRIMERA VEZ CON ESTE HORARIO, tener en cuenta que ya se marco la 1 vez

                            $marcacion_puertaPr = DB::table('marcacion_puerta as mv')
                            ->leftJoin('dispositivos as dis', 'mv.dispositivoEntrada', '=', 'dis.idDispositivos')
                            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                            ->where('mv.horarioEmp_id', '=', $conhorario)
                            ->orderby('marcaMov_fecha', 'ASC')
                            ->get();

                            //*SOLO SE REALIZA LA PRIMERA VEEZ
                            if($marcacion_puertaPr->count()==1  &&  $marcacion_puertaPr[0]->marcaMov_salida==null){
                                /* BUSCO PAUSAS CON DESCONTAR AUTOM*/
                                $pausas_horario = DB::table('pausas_horario as pauh')
                                ->select('idpausas_horario as idpausa', 'pausH_descripcion as descripcion', 'pausH_Inicio as horaI',
                                    'pausH_Fin as horaF', 'pauh.tolerancia_inicio as toleranciaI', 'pauh.tolerancia_fin as toleranciaF',
                                    'inactivar as inhabilitar','descontar')
                                ->where('pauh.horario_id', '=', $idhorario)
                                ->where('descontar', '=',1)
                                ->distinct('pauh.idpausas_horario')
                                ->get();
                                if($pausas_horario->isNotEmpty()){

                                    //* PONEMOS HORA DE INICIO Y HORA FIN
                                    foreach ($pausas_horario as $pausasDescontar) {
                                            $fechaMarcaRecib = Carbon::create($fecha1V);
                                            $fechaRecibP=$fechaMarcaRecib->isoFormat('YYYY-MM-DD');
                                        if (Carbon::parse($pausasDescontar->horaF)->lt(Carbon::parse($pausasDescontar->horaI))) {


                                            $despues = $fechaMarcaRecib->addDays(1);
                                            $fechaMan = $despues->isoFormat('YYYY-MM-DD');
                                            $pausasDescontar->horaI = $fechaRecibP . " " . $pausasDescontar->horaI;
                                            $pausasDescontar->horaF = $fechaMan . " " . $pausasDescontar->horaF;
                                        } else {
                                            if (Carbon::parse($pausasDescontar->horaI)->lt(Carbon::parse($inicioH))) {
                                                $pausasDescontar->horaI = $fechaMan . " " . $pausasDescontar->horaI;
                                            } else {
                                                $pausasDescontar->horaI = $fechaRecibP . " " . $pausasDescontar->horaI;
                                            }

                                            if (Carbon::parse($pausasDescontar->horaF)->lt(Carbon::parse($inicioH))) {
                                                $pausasDescontar->horaF = $fechaMan . " " . $pausasDescontar->horaF;
                                            } else {
                                                $pausasDescontar->horaF = $fechaRecibP . " " . $pausasDescontar->horaF;
                                            }
                                        }

                                    }

                                    //*INSERTAMOS MARCACION AUTOMATICA
                                    foreach($pausas_horario as $pausasDescontar){

                                        //* ultima marcacion sin salida con el mismo id de horario
                                        $marcacion_puertaPausa = DB::table('marcacion_puerta as mv')
                                        ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                        ->where('mv.horarioEmp_id', '=', $conhorario)
                                        ->where('mv.marcaMov_salida', '=',null)
                                        ->orderby('marcaMov_fecha', 'ASC')
                                        ->get()->last();

                                        if($marcacion_puertaPausa->marcaMov_fecha < $pausasDescontar->horaI){
                                        //*pausa inicio sera la salida de la marcacion
                                        $marcacion_automaticaI = marcacion_puerta::find($marcacion_puertaPausa->marcaMov_id);
                                        $marcacion_automaticaI->marcaMov_salida = $pausasDescontar->horaI;
                                        $marcacion_automaticaI->dispositivoSalida = 81;
                                        $marcacion_automaticaI->save();

                                        } else{
                                            //*se crea marcacion con salida
                                            $marcacion_automaticaF = new marcacion_puerta();
                                            $marcacion_automaticaF->marcaMov_salida = $pausasDescontar->horaI;
                                            $marcacion_automaticaF->marcaMov_emple_id = $req['idEmpleado'];
                                            $marcacion_automaticaF->organi_id = $empleados->organi_id;
                                            $marcacion_automaticaF->horarioEmp_id = $conhorario;
                                            $marcacion_automaticaF->tipoMarcacionB = 1;
                                            $marcacion_automaticaF->dispositivoSalida = 81;
                                            $marcacion_automaticaF->save();
                                        }

                                        //*Pausa salida sera nueva entrada de marcacion
                                        $marcacion_automaticaF = new marcacion_puerta();
                                        $marcacion_automaticaF->marcaMov_fecha = $pausasDescontar->horaF;
                                        $marcacion_automaticaF->marcaMov_emple_id = $req['idEmpleado'];
                                        $marcacion_automaticaF->organi_id = $empleados->organi_id;
                                        $marcacion_automaticaF->horarioEmp_id = $conhorario;
                                        $marcacion_automaticaF->tipoMarcacionB = 1;
                                        $marcacion_automaticaF->dispositivoEntrada =81;
                                        $marcacion_automaticaF->save();

                                    }

                                }

                            }






                        }
                    //**************************FIN VERIFICACION**************** *****************/
                    } else {
                        $respuestaMarcacion = array(
                            'id' => $req['id'],
                            'error' => 'Fecha de marcacion de empleado ya registrada',
                            'estado' => true);
                    }

                } else {
                    $respuestaMarcacion = array(
                        'id' => $req['id'],
                        'error' => 'Empleado no existe',
                        'estado' => false);
                }

            }

            /* INSERTAMO A AARRAY  */
            /* if($respuestaMarcacion){

            } */
            $arrayDatos->push($respuestaMarcacion);
            /* ---------------------------- */

        }

        if ($arrayDatos != null) {

            return response()->json($arrayDatos);
        } else {
            return response()->json(array('status' => 400, 'title' => 'No se pudo registrar marcacion',
                'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 400);
        }
    }



    public function listaHuellas(Request $request){

        $idUsuarioOrgani = $request->idusuario_organizacion;
        $usuario_organizacion = DB::table('usuario_organizacion as uso')
            ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario', 'uso.rol_id', 'o.organi_id', 'o.organi_razonSocial', 'O.organi_estado')
            ->where('uso.usua_orga_id', '=', $idUsuarioOrgani)
            ->join('users as u', 'uso.user_id', '=', 'u.id')
            ->join('organizacion as o', 'uso.organi_id', '=', 'o.organi_id')
            ->get()->first();

        $listaHuellas=DB::table('plantilla_empleadobio as pem')
        ->select('pem.id','pem.idempleado','pem.posicion_huella','pem.tipo_registro',
        'pem.path','pem.iFlag','pem.iFaceIndex','pem.iLength','pem.estado')
        ->leftJoin('empleado as e','pem.idempleado','=','e.emple_id')
        ->where('e.organi_id','=',$usuario_organizacion->organi_id)
        ->get();

        //*poniendo estado
        foreach($listaHuellas as $listaHuella){
            if($listaHuella->estado==1){
                $listaHuella->estado=true;
            } else{
                $listaHuella->estado=false;
            }
        }

        return response()->json($listaHuellas);
    }

    public function borrarHuellas(Request $request){

        $arrayDatosEliminar = new Collection();

        foreach ($request->all() as $key => $req) {
            $errores = [];
            $validacion = Validator::make($req, [
                'id' => 'required'
            ], [
                'required' => ':atributo es obligatorio',
            ]);
            if ($validacion->fails()) {
                //: ARRAY DE ERRORES
                if (isset($validacion->failed()["id"])) {
                    array_push($errores, array("campo" => "id", "mensaje" => "Es obligatorio"));
                }
                return response()->json(array("errores" => $errores), 400);
            }
            /*  RECIBO PARAMENTROS*/
            $idplantilla = $req['id'];
            /* ----------------------------- */

            /*CAMBIANDO ESTADO */
            $plantillaBio=plantilla_empleadobio::find($idplantilla);

            if($plantillaBio){
                $plantillaBio->estado=0;
                $plantillaBio->save();

                $borradoArray = array(
                'id' => $idplantilla,
                'estado' => true);
            } else{
                $borradoArray = array(
                    'id' => $idplantilla,
                    'estado' => false);
            }



            /* INSERTAMO A AARRAY  */
            $arrayDatosEliminar->push($borradoArray);
            /* ---------------------------- */
        }
        if ($arrayDatosEliminar != null) {
            return response()->json($arrayDatosEliminar);
        } else {
            return response()->json(array('status' => 500, 'title' => 'No se pudo eliminar',
                'detail' => 'No se pudo eliminar'), 500);
        }

    }
}
