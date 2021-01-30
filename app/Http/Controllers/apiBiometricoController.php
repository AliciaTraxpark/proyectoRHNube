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
                        ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario', 'uso.rol_id', 'o.organi_id', 'o.organi_razonSocial')
                        ->where('user_id', '=', $tab->id)
                        ->join('users as u', 'uso.user_id', '=', 'u.id')
                        ->join('organizacion as o', 'uso.organi_id', '=', 'o.organi_id')
                        ->get();
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
                                    ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario', 'uso.rol_id', 'o.organi_id', 'o.organi_razonSocial')
                                    ->where('user_id', '=', Auth::user()->id)
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
                                        ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario', 'uso.rol_id', 'o.organi_id', 'o.organi_razonSocial')
                                        ->where('user_id', '=', Auth::user()->id)
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
                            ->select('uso.usua_orga_id as idusuario_organizacion', 'uso.user_id as idusuario', 'uso.rol_id', 'o.organi_id', 'o.organi_razonSocial')
                            ->where('user_id', '=', Auth::user()->id)
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
                    'detail' => 'No se encontró usuario registrado con este correo'), 400);
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
                            ->select('idDispositivos', 'dispo_descripUbicacion as descripcion', 'dispo_movil as ipPuerto',
                                'dispo_codigo as serie', 'version_firmware', 'dispo_todosEmp', 'dispo_porEmp')
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
                                ->select('idDispositivos', 'dispo_descripUbicacion as descripcion', 'dispo_movil as ipPuerto',
                                    'dispo_codigo as serie', 'version_firmware', 'dispo_todosEmp', 'dispo_porEmp')
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
                    ->select('idDispositivos', 'dispo_descripUbicacion as descripcion', 'dispo_movil as ipPuerto',
                        'dispo_codigo as serie', 'version_firmware', 'dispo_todosEmp', 'dispo_porEmp')
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
                    ->select('idDispositivos', 'dispo_descripUbicacion as descripcion', 'dispo_movil as ipPuerto',
                        'dispo_codigo as serie', 'version_firmware')
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
        function agruparIDBiometricos($empleado)
        {
            $idBiometricos = array();
            foreach ($empleado as $tab1) {

                /* VERIFICAMOS DISPOSITIVOS CON TODOS LOS EMPLEADOS */
                $dispositivosBi = DB::table('dispositivos as di')
                    ->where('di.dispo_porEmp', '=', 1)
                    ->where('di.dispo_todosEmp', '=', 1)
                    ->where('di.tipoDispositivo', '=', 3)
                    ->where('di.dispo_estadoActivo', '=', 1)
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
                    $empleado = agruparIDBiometricos($empleado);

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
                        $empleado = agruparIDBiometricos($empleado);

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
                        $empleado = agruparIDBiometricos($empleado);

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
                $empleado = agruparIDBiometricos($empleado);

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
                            $empleado = agruparIDBiometricos($empleado);

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
                                $empleado = agruparIDBiometricos($empleado);

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
                                $empleado = agruparIDBiometricos($empleado);

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
                        $empleado = agruparIDBiometricos($empleado);

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
                            $empleado = agruparIDBiometricos($empleado);
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
                                $empleado = agruparIDBiometricos($empleado);
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
                                $empleado = agruparIDBiometricos($empleado);
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
                        $empleado = agruparIDBiometricos($empleado);
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
                            $empleado = agruparIDBiometricos($empleado);
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
                                $empleado = agruparIDBiometricos($empleado);
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
                                $empleado = agruparIDBiometricos($empleado);
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
                        $empleado = agruparIDBiometricos($empleado);
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
    public function marcacionBiometrico(Request $request)
    {
        $fechaHoy = Carbon::now('America/Lima');
        $horaActual = $fechaHoy->isoFormat('YYYY-MM-DD HH:mm:ss');

        /* --------------ORDENAMOS DE MENOR A MAYOR-------------------------------------------------- */
        /*  $arrayDatos = new Collection();
        foreach ($request->all() as $req) {

        if (empty($req['idHoraEmp'])) {
        $datos = ['idDisposi' => $req['idDisposi'], 'idEmpleado' => $req['idEmpleado'],
        'tipoMarcacion' => $req['tipoMarcacion'], 'fechaMarcacion' => $req['fechaMarcacion'],
        ];
        } else {

        $datos = ['idDisposi' => $req['idDisposi'], 'idEmpleado' => $req['idEmpleado'],
        'tipoMarcacion' => $req['tipoMarcacion'], 'fechaMarcacion' => $req['fechaMarcacion'],
        'idHoraEmp' => $req['idHoraEmp'],
        ];
        }
        $arrayDatos->push($datos);
        }
        $arrayOrdenado = $arrayDatos->sortBy('fechaMarcacion');
        $arrayOrdenado->values()->all(); */
        /* dd($arrayOrdenado); */
        /* ----------------------------------------------------------------------------------------------------*/
        /*OBTENEMOS ARCHIVO FILE Y CREAMOS NUEBO COLLECTION   */
        $contents = new Collection();
        $file = $request->file('file');
        $data = file_get_contents($file);
        /*  $contents->sortBy('fechaMarcacion'); */
        $datosJ = json_decode($data, true);
        /*  dd($datosJ); */
        $datosJ = collect($datosJ)->sortBy('fechaMarcacion')->values()->toArray();

        foreach ($datosJ as $req) {

            /* VALIDANDO TIPO DE MARCACION */

            $datos = ['idDisposi' => $req['idDisposi'], 'idEmpleado' => $req['idEmpleado'],
                'fechaMarcacion' => $req['fechaMarcacion'],
                'idHoraEmp' => $req['idHoraEmp'],
            ];

            $contents->push($datos);
        }
        /* --------------------------------------------- */

        /* ORDENAMOS ARRAY POR FECHA DE MARCACION */
        $arrayOrdenado = $contents->sortBy('fechaMarcacion');
        $arrayOrdenado->values()->all();

        /*---------------------------------------------------------------------------------------------------  */

        /*------------------- RECORREMOS ARRAY ORDENADO------------------------------------------------------- */
        foreach ($arrayOrdenado as $req) {

            /* --------------------------------------------------------------- */
            /* ----------------------SI RECIBO SIN HORARIO----------------------------- */
            if ($req['idHoraEmp'] == 0) {

                $fecha1V = Carbon::create($req['fechaMarcacion'])->toDateString();

                /* VERIFICO SI NO HAY MARCACIONES ANTES */
                $marcacion_puertaVacio = DB::table('marcacion_puerta as mv')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->whereDate('mv.marcaMov_fecha', '=', $fecha1V)
                    ->orWhereDate('mv.marcaMov_salida', '=', $fecha1V)
                    ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                    ->get();
                /*  dd($marcacion_puertaVacio); */
                if ($marcacion_puertaVacio->isEmpty()) {

                    $tipoMarcacion = 1;

                }
                /* ---------------------------------------------------- */
                else {

                    /* YA HAY MARCACIONES PARA ESTE EMPLEADO Y FECHA */
                    /* BUSCAMOS SI EXISTE UNA ULTIMA MARCACION CON TODO LOS DATOS */
                    $marcacion_puertaVerif = DB::table('marcacion_puerta as mv')
                        ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    /* ->where('mv.marcaMov_salida', '!=', null)
                    ->where('mv.marcaMov_fecha', '!=', null) */
                        ->whereDate('mv.marcaMov_fecha', '=', $fecha1V)
                        ->orWhereDate('mv.marcaMov_salida', '=', $fecha1V)
                        ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                        ->orderby('marcaMov_fecha', 'ASC')
                        ->get()->last();

                    /* ------------------------------------------------------ */
                    /* SI HAY MARCACION CON TODOS LOS DATOS */
                    if ($marcacion_puertaVerif) {
                        if ($marcacion_puertaVerif->marcaMov_fecha != null && $marcacion_puertaVerif->marcaMov_salida != null) {

                            $tipoMarcacion = 1;
                        } else {
                            /*--------- SI NO TENGO SALIDA ----------------------------*/
                            $marcacion_puertaVerif2 = DB::table('marcacion_puerta as mv')
                                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                ->where('mv.marcaMov_salida', '=', null)
                                ->whereDate('mv.marcaMov_fecha', '=', $fecha1V)
                                ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                ->orderby('marcaMov_fecha', 'ASC')
                                ->get()->first();
                            /* ------------------------------------------------------ */

                            if ($marcacion_puertaVerif2) {

                                $tipoMarcacion = 0;
                            }
                        }

                    }
                    /*    else{

                $marcacion_puertaVerif2 = DB::table('marcacion_puerta as mv')
                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                ->where('mv.marcaMov_salida', '=', null)
                ->whereDate('mv.marcaMov_fecha', '=', $fecha1V)
                ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                ->orderby('marcaMov_fecha', 'ASC')
                ->get()->first();

                if($marcacion_puertaVerif2){
                $tipoMarcacion=0;
                }

                } */
                }
            }
            /* ------------------------------------------------------------------ */
            else {
                /* TENGO HORARIO */

            }
            /* ---------------------------------- */
            /* --------------------------------------------------------------- */

            /*CUADNO ES MARCACION DE ENTRADA */
            if ($tipoMarcacion == 1 || $tipoMarcacion == 2) {
                $marcacion_biometrico = new marcacion_puerta();

                /* VALIDANDO FECHA  */
                if (Carbon::create($req['fechaMarcacion'])->gt(Carbon::create($horaActual))) {
                    return response()->json(array('status' => 500, 'title' => 'No se pudo validar fecha',
                        'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 500);
                } else {
                    $marcacion_biometrico->marcaMov_fecha = $req['fechaMarcacion'];
                }
                /* -------------------- */

                $marcacion_biometrico->marcaMov_emple_id = $req['idEmpleado'];
                $marcacion_biometrico->dispositivos_idDispositivos = $req['idDisposi'];

                /* VALIDANDO EMPLEADOIIIII */
                $empleados = DB::table('empleado as e')
                    ->join('organizacion as or', 'e.organi_id', '=', 'or.organi_id')
                    ->where('e.emple_id', '=', $req['idEmpleado'])
                    ->get()->first();
                if ($empleados) {
                    $marcacion_biometrico->organi_id = $empleados->organi_id;

                    if (empty($req['idHoraEmp'])) {} else {
                        $marcacion_biometrico->horarioEmp_id = $req['idHoraEmp'];
                    }

                    if ($tipoMarcacion == 1) {
                        $marcacion_biometrico->tipoMarcacionB = 1;
                    } else {
                        $marcacion_biometrico->tipoMarcacionB = 2;
                    }

                    $marcacion_biometrico->save();
                } else {
                    return response()->json(array('status' => 500, 'title' => 'No se pudo encontrar empleado',
                        'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 500);
                }
                /* --------------------------------------------------------------- */
            }
            /* CUADNO ES TIPO 0 O 3 QUE SON SALIDA DE MARCACION Y FIN DE PAUSA  */
            else {

                /* VALIDAMOS QUE LA HORA Y FECHA NO SEA MAYOR QUE LA DEL SERVIDOR */
                if (Carbon::create($req['fechaMarcacion'])->gt(Carbon::create($horaActual))) {
                    return response()->json(array('status' => 500, 'title' => 'No se pudo validar fecha',
                        'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 500);
                } else {
                    /* AQUI VALIDAREMOS PARA INSERTAR LA SALIDA */

                    if ($tipoMarcacion == 0) {

                        /* CUADNO ES SALIDA DE HORARIO */

                        /* CONVERTIMOS LA FECHA DE MARCACION EN DATE */
                        $fecha1 = Carbon::create($req['fechaMarcacion'])->toDateString();

                        /* CONSULTAMOS SI HAY UNA MARCACION DE ANTERIOR QUE ESTE LLENA */
                        $marcacion_puerta00 = DB::table('marcacion_puerta as mv')
                            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                            ->where('mv.marcaMov_salida', '!=', null)
                            ->where('mv.marcaMov_fecha', '!=', null)
                            ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                            ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                            ->where('mv.tipoMarcacionB', '=', 1)
                            ->orderby('marcaMov_fecha', 'ASC')
                            ->get()->last();

                        /* SI EXISTE ENTONCES COMPARRAREMOS */
                        if ($marcacion_puerta00) {

                            if ($marcacion_puerta00->marcaMov_fecha > $req['fechaMarcacion']) {
                                $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                    ->where('mv.marcaMov_salida', '=', null)
                                    ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                                    ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                    ->where('mv.tipoMarcacionB', '=', 1)
                                    ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                    ->orderby('marcaMov_fecha', 'ASC')
                                    ->get()->first();
                            } else {
                                $marcacion_puerta1 = [];
                                $marcacion_puerta1 == null;
                            }

                        } else {
                            $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                ->where('mv.marcaMov_salida', '=', null)
                                ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                                ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                ->where('mv.tipoMarcacionB', '=', 1)
                                ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                ->orderby('marcaMov_fecha', 'ASC')
                                ->get()->last();

                        }

                        /* VERIFICAMOS SI EXISTE PARA EMPAREJAR O PONEMOS UNO NUEVO */
                        if ($marcacion_puerta1 == null) {

                            $marcacion_biometrico = new marcacion_puerta();
                            $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                            $marcacion_biometrico->marcaMov_emple_id = $req['idEmpleado'];
                            $marcacion_biometrico->dispositivos_idDispositivos = $req['idDisposi'];

                            /* VALIDANDO EMPLEADOIIIII */
                            $empleados = DB::table('empleado as e')
                                ->join('organizacion as or', 'e.organi_id', '=', 'or.organi_id')
                                ->where('e.emple_id', '=', $req['idEmpleado'])
                                ->get()->first();
                            if ($empleados) {
                                $marcacion_biometrico->organi_id = $empleados->organi_id;

                                if (empty($req['idHoraEmp'])) {} else {
                                    $marcacion_biometrico->horarioEmp_id = $req['idHoraEmp'];
                                }

                                $marcacion_biometrico->tipoMarcacionB = 1;

                                $marcacion_biometrico->save();
                            } else {
                                return response()->json(array('status' => 500, 'title' => 'No se pudo encontrar empleado',
                                    'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 500);
                            }
                        } else {

                            $marcacion_biometrico = marcacion_puerta::find($marcacion_puerta1->marcaMov_id);
                            $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                            $marcacion_biometrico->save();
                        }

                    } else {
                        /* CONVERTIMOS LA FECHA DE MARCACION EN DATE */
                        $fecha1 = Carbon::create($req['fechaMarcacion'])->toDateString();

                        /* CONSULTAMOS SI HAY UNA MARCACION DE ANTERIOR QUE ESTE LLENA */
                        $marcacion_puerta00 = DB::table('marcacion_puerta as mv')
                            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                            ->where('mv.marcaMov_salida', '!=', null)
                            ->where('mv.marcaMov_fecha', '!=', null)
                            ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                            ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                            ->where('mv.tipoMarcacionB', '=', 2)
                            ->orderby('marcaMov_fecha', 'ASC')
                            ->get()->last();

                        /* SI EXISTE ENTONCES COMPARRAREMOS */
                        if ($marcacion_puerta00) {
                            if ($marcacion_puerta00->marcaMov_fecha > $req['fechaMarcacion']) {
                                $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                    ->where('mv.marcaMov_salida', '=', null)
                                    ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                                    ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                    ->where('mv.tipoMarcacionB', '=', 2)
                                    ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                    ->orderby('marcaMov_fecha', 'ASC')
                                    ->get()->first();
                            } else {
                                $marcacion_puerta1 = [];
                                $marcacion_puerta1 == null;
                            }

                        } else {
                            $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                ->where('mv.marcaMov_salida', '=', null)
                                ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                                ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                ->where('mv.tipoMarcacionB', '=', 2)
                                ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                ->orderby('marcaMov_fecha', 'ASC')
                                ->get()->last();

                        }

                        /* VERIFICAMOS SI EXISTE PARA EMPAREJAR O PONEMOS UNO NUEVO */
                        if ($marcacion_puerta1 == null) {

                            $marcacion_biometrico = new marcacion_puerta();
                            $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                            $marcacion_biometrico->marcaMov_emple_id = $req['idEmpleado'];
                            $marcacion_biometrico->dispositivos_idDispositivos = $req['idDisposi'];

                            /* VALIDANDO EMPLEADOIIIII */
                            $empleados = DB::table('empleado as e')
                                ->join('organizacion as or', 'e.organi_id', '=', 'or.organi_id')
                                ->where('e.emple_id', '=', $req['idEmpleado'])
                                ->get()->first();
                            if ($empleados) {
                                $marcacion_biometrico->organi_id = $empleados->organi_id;

                                if (empty($req['idHoraEmp'])) {} else {
                                    $marcacion_biometrico->horarioEmp_id = $req['idHoraEmp'];
                                }

                                $marcacion_biometrico->tipoMarcacionB = 2;

                                $marcacion_biometrico->save();
                            } else {
                                return response()->json(array('status' => 500, 'title' => 'No se pudo encontrar empleado',
                                    'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 500);
                            }
                        } else {

                            $marcacion_biometrico = marcacion_puerta::find($marcacion_puerta1->marcaMov_id);
                            $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                            $marcacion_biometrico->save();
                        }

                    }
                }
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

        /* OBTENER FECHA ACTUAL Y DE MAÑANA EN FORMATO DATE */
        $fecha = Carbon::now('America/Lima');
        $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
        /* --------------------------------------- */
        /* SI BIOMETRICO ES NULL DEVOLVEMOS LOS EMPLEADOS SIN FILTRAR POR BIOMETIRICO */

        /* --FUNCION PARA INSERTAR PAUSAS----------- */
        function insertarPausasH($horario)
        {
            /* OBTENER FECHA ACTUAL Y DE MAÑANA EN FORMATO DATE */
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

        /* OBTENER FECHA ACTUAL Y DE MAÑANA EN FORMATO DATE */
        $fecha = Carbon::now('America/Lima');
        $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
        /* --------------------------------------- */
        /* SI BIOMETRICO ES NULL DEVOLVEMOS LOS EMPLEADOS SIN FILTRAR POR BIOMETIRICO */

        /* --FUNCION PARA INSERTAR PAUSAS----------- */
        function insertarPausas($horario)
        {
            /* OBTENER FECHA ACTUAL Y DE MAÑANA EN FORMATO DATE */
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

            /*  RECIBO PARAMENTROS*/
            $idempleado = $req['idempleado'];
            $posicion_huella = $req['posicion_huella'];
            $tipo_registro = $req['tipo_registro'];
            $path = $req['path'];
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
                            $plantilla_empleadobio->save();

                            $plantilla_empleadobioArray = array(
                                'id' => $plantilla_empleadobio->id,
                                'idempleado' => $idempleado,
                                'posicion_huella' => $posicion_huella,
                                'tipo_registro' => $tipo_registro,
                                'estado' => true);

                        } else {
                            $plantilla_empleadobioArray = array(
                                'idempleado' => $idempleado,
                                'error' => 'Tipo de registro no encontrado',
                                'estado' => false);
                        }

                    }

                    /* SI POSICION DE HUELLA ES INCORRECTA */
                    else {
                        $plantilla_empleadobioArray = array(
                            'idempleado' => $idempleado,
                            'error' => 'Posicion de huella incorrecta',
                            'estado' => false);
                    }

                }

                /* SI NO EXISTE EMPLEADO */
                else {
                    $plantilla_empleadobioArray = array(
                        'idempleado' => $idempleado,
                        'error' => 'No se encontro empleados con este id',
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

    public function marcacionBiometrico2(Request $request)
    {
        $fechaHoy = Carbon::now('America/Lima');
        $horaActual = $fechaHoy->isoFormat('YYYY-MM-DD HH:mm:ss');

        /* ----------------------------------------------------------------------------------------------------*/
        /*OBTENEMOS ARCHIVO FILE Y CREAMOS NUEBO COLLECTION   */
        $contents = new Collection();
        $file = $request->file('file');
        $data = file_get_contents($file);

        $datosJ = json_decode($data, true);
        $datosJ = collect($datosJ)->sortBy('fechaMarcacion')->values()->toArray();

        foreach ($datosJ as $req) {

            /* VALIDANDO TIPO DE MARCACION */

            $datos = ['idDisposi' => $req['idDisposi'], 'idEmpleado' => $req['idEmpleado'],
                'fechaMarcacion' => $req['fechaMarcacion'],
                'idHoraEmp' => $req['idHoraEmp'], 'id' => $req['id'],
            ];

            $contents->push($datos);
        }
        /* --------------------------------------------- */

        /* ORDENAMOS ARRAY POR FECHA DE MARCACION */
        $arrayOrdenado = $contents->sortBy('fechaMarcacion');
        $arrayOrdenado->values()->all();

        /*---------------------------------------------------------------------------------------------------  */

        /*------------------- RECORREMOS ARRAY ORDENADO------------------------------------------------------- */
        $arrayDatos = new Collection();

        foreach ($arrayOrdenado as $req) {

            /* --------------------------------------------------------------- */
            /* ----------------------SI RECIBO SIN HORARIO----------------------------- */
            if ($req['idHoraEmp'] == 0 || $req['idHoraEmp'] == null) {

                $fecha1V = Carbon::create($req['fechaMarcacion'])->toDateString();

                /* VERIFICO SI NO HAY MARCACIONES ANTES */
                $marcacion_puertaVacio = DB::table('marcacion_puerta as mv')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha1V)
                    ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                    ->whereNull('mv.horarioEmp_id')
                    ->get();
                /*  dd($marcacion_puertaVacio); */
                if ($marcacion_puertaVacio->isEmpty()) {

                    $tipoMarcacion = 1;

                }
                /* ---------------------------------------------------- */
                else {

                    /* YA HAY MARCACIONES PARA ESTE EMPLEADO Y FECHA */
                    /* BUSCAMOS SI EXISTE UNA ULTIMA MARCACION CON TODO LOS DATOS */
                    $marcacion_puertaVerif = DB::table('marcacion_puerta as mv')
                        ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                        ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha1V)
                        ->whereNull('mv.horarioEmp_id')
                        ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                        ->orderby(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), 'ASC')
                        ->get()->last();

                    /* ------------------------------------------------------ */
                    /* SI HAY MARCACION CON TODOS LOS DATOS */
                    if ($marcacion_puertaVerif) {
                        if ($marcacion_puertaVerif->marcaMov_fecha != null && $marcacion_puertaVerif->marcaMov_salida != null) {
                            /*  dd($marcacion_puertaVerif, $req['idEmpleado']); */
                            $tipoMarcacion = 1;
                        } else {
                            /*--------- SI NO TENGO SALIDA ----------------------------*/
                            $marcacion_puertaVerif2 = DB::table('marcacion_puerta as mv')
                                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                ->where('mv.marcaMov_salida', '=', null)
                                ->whereDate('mv.marcaMov_fecha', '=', $fecha1V)
                                ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                ->whereNull('mv.horarioEmp_id')
                                ->orderby('marcaMov_fecha', 'ASC')
                                ->get()->first();
                            /* ------------------------------------------------------ */

                            if ($marcacion_puertaVerif2) {

                                $tipoMarcacion = 0;
                            }
                        }

                    }
                }
            }
            /* ------------------------------------------------------------------ */
            else {
                /* TENGO HORARIO */
                $horario = DB::table('horario_empleado as he')
                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                    ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                    ->where('he.horarioEmp_id', '=', $req['idHoraEmp'])

                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario', 'hd.start')

                    ->orderBy('he.empleado_emple_id')
                    ->get();

                /* --------------------------------------- */
                foreach ($horario as $tab2) {
                    /* OBTENER FECHA ACTUAL Y DE MAÑANA EN FORMATO DATE */
                    $fecha = Carbon::create($tab2->start);
                    $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                    $despues = $fecha->addDays(1);
                    $fechaMan = $despues->isoFormat('YYYY-MM-DD');

                    if (Carbon::parse($tab2->horaF)->lt(Carbon::parse($tab2->horaI))) {

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
                        ->where('inactivar', '=', 0)
                        ->distinct('pauh.idpausas_horario')
                        ->get();
                    $horaIV = $tab1->horaI;
                    $horaFV = $tab1->horaF;

                    $fecha = Carbon::create($tab1->start);
                    $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                    $despues = $fecha->addDays(1);
                    $fechaMan = $despues->isoFormat('YYYY-MM-DD');

                    foreach ($pausas_horario as $tab3) {

                        if (Carbon::parse($tab3->horaF)->lt(Carbon::parse($tab3->horaI))) {

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
                /*  dd($horario); */

                /*----------------------------------------- VALIDDANDO TIPO DE MARCACION ----------------------------------------------------*/
                /* OBTENEMOS HORA INICIAL Y FINAL */
                $horaI = Carbon::create($horario[0]->horaI);
                $horaF = Carbon::create($horario[0]->horaF);
                /*  $toleranciaI= $horario[0]->toleranciaI; */
                $arrayPausas = $horario[0]->pausas;
                /* ------------------------------------------------- */
                /* VERIFICO SI NO HAY MARCACIONES ANTES */
                $fecha2V = Carbon::create($req['fechaMarcacion'])->toDateString();

                $marcacion_puertaVacio2 = DB::table('marcacion_puerta as mv')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha2V)
                    ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                    ->where('mv.horarioEmp_id', '!=', null)
                    ->get();

                /* CUANDO ES POR 1 VEZ EN EL DIA */
                /*  if ($marcacion_puertaVacio2->isEmpty()) { */

                //VERIFICAMOS SI TIENE PAUSAS
                //DIFERENCIAS
                $diferenciaI = Carbon::create($req['fechaMarcacion'])->diffInMinutes($horaI);
                $diferenciaF = Carbon::create($req['fechaMarcacion'])->diffInMinutes($horaF);
                /* ----------------------------------------------------------------- */
                $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                    ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                    ->where('mv.marcaMov_salida', '=', null)
                    ->whereDate('mv.marcaMov_fecha', '=', $fecha2V)
                    ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                    ->where('mv.tipoMarcacionB', '=', 1)
                    ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                    ->where('mv.horarioEmp_id', '!=', null)
                    ->orderby('marcaMov_fecha', 'ASC')
                    ->get()->last();
                /*------------------------------------------------------------------  */
                /* ----------SI NO TIENEE PAUSAS --------------------------*/
                if ($arrayPausas->isEmpty()) {
                    if ($diferenciaI > $diferenciaF) {
                        $tipoMarcacion = 3;
                    } else {

                        if ($marcacion_puerta1) {
                            $tipoMarcacion = 3;
                        } else {
                            $tipoMarcacion = 2;
                        }

                    }
                }
                /* --------FIN SI NO TIENE PAUSAS------------------------------ */
                else {
                    $arrayDiferencias = new Collection();
                    /* ----------------------------------SI TIENE PAUSAS----------------------------------------- */

                    //*PRIMERO CALCULAMOS MENOR DIFERENCIA CON HORA Y ENTRADA GENERAL
                    if ($diferenciaI > $diferenciaF) {
                        $tipoMarcacionH = 3;
                        $horaSeteo = $horaF;
                        $datos0 = ["tipoMarcacion" => $tipoMarcacionH, "diferencia" => $diferenciaF];
                    } else {
                        $tipoMarcacionH = 2;
                        $horaSeteo = $horaI;
                        $datos0 = ["tipoMarcacion" => $tipoMarcacionH, "diferencia" => $diferenciaI];
                    }
                    $arrayDiferencias->push($datos0);

                    //*SEGUNDO CALCULAMOS MENOR DIFERENCIA CON HORA Y ENTRADA DE PAUSAS
                    foreach ($arrayPausas as $arrayPausas2) {

                        /* NO ESTOY TOMANDO EN CUETNA LA TOLERANCIAA */
                        $horaIP = Carbon::create($arrayPausas2->horaI);
                        $horaFP = Carbon::create($arrayPausas2->horaF);
                        $diferenciaPI = Carbon::create($req['fechaMarcacion'])->diffInMinutes($horaIP);
                        $diferenciaPF = Carbon::create($req['fechaMarcacion'])->diffInMinutes($horaFP);

                        if ($diferenciaPI > $diferenciaPF) {
                            $tipoMarcacionP = 2;
                            $horaSeteoP = $horaFP;
                            $datos1 = ["tipoMarcacion" => $tipoMarcacionP, "diferencia" => $diferenciaPF];
                        } else {
                            $tipoMarcacionP = 3;
                            $horaSeteoP = $horaIP;
                            $datos1 = ["tipoMarcacion" => $tipoMarcacionP, "diferencia" => $diferenciaPI];
                        }

                        $arrayDiferencias->push($datos1);
                    }
                    /* ORDENO PARA OBTENER LA MENOR DIFERECNIA */
                    $arrayDiferencias1 = $arrayDiferencias->sortBy('diferencia')->values()->all();
                    $arrayFinal = $arrayDiferencias1[0];
                    /* SI OBTUVIMOS ENTRADA */
                    if ($arrayFinal["tipoMarcacion"] == 2) {
                        /* VERIFICAMOS SI TIENE MARCACION SIN SALIDA */
                        if ($marcacion_puerta1) {
                            $tipoMarcacion = 3;
                        } else {
                            $tipoMarcacion = 2;
                        }

                    } else {
                        $tipoMarcacion = 3;
                    }

                    /*  dd($req['fechaMarcacion']); */

                    /* dd($arrayFinal["diferencia"]); */
                }

                /* ------------------------------------------- */

                /*   } */

                /* ---------------FIN DE FUNCION-------------------------- */
            }
            /* ---------------------------------- */
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
                    ->get()->first();

                if ($empleados) {
                    /*CUADNO ES MARCACION DE ENTRADA SIN HORARIO */
                    if ($tipoMarcacion == 1) {
                        $marcacion_biometrico = new marcacion_puerta();

                        $marcacion_biometrico->marcaMov_fecha = $req['fechaMarcacion'];
                        /* -------------------- */

                        $marcacion_biometrico->marcaMov_emple_id = $req['idEmpleado'];
                        $marcacion_biometrico->dispositivos_idDispositivos = $req['idDisposi'];

                        $marcacion_biometrico->organi_id = $empleados->organi_id;

                        if (empty($req['idHoraEmp'])) {} else {
                            $marcacion_biometrico->horarioEmp_id = $req['idHoraEmp'];
                        }

                        $marcacion_biometrico->tipoMarcacionB = 1;

                        $marcacion_biometrico->save();

                        $respuestaMarcacion = array(
                            'id' => $req['id'],
                            'estado' => true);

                        /* --------------------------------------------------------------- */
                    }
                    /* CUADNO ES TIPO 0  SON SALIDA DE MARCACION Y FIN DE PAUSA  */
                    else {

                        /* AQUI VALIDAREMOS PARA INSERTAR LA SALIDA */

                        if ($tipoMarcacion == 0) {

                            /* CUADNO ES SALIDA DE HORARIO */

                            /* CONVERTIMOS LA FECHA DE MARCACION EN DATE */
                            $fecha1 = Carbon::create($req['fechaMarcacion'])->toDateString();

                            /* VERIFICAMOS  PARA EMPAREJAR  */
                            $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                ->where('mv.marcaMov_salida', '=', null)
                                ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                                ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                ->where('mv.tipoMarcacionB', '=', 1)
                                ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                ->whereNull('mv.horarioEmp_id')
                                ->orderby('marcaMov_fecha', 'ASC')
                                ->get()->last();

                            if ($marcacion_puerta1) {
                                $marcacion_biometrico = marcacion_puerta::find($marcacion_puerta1->marcaMov_id);
                                $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                                $marcacion_biometrico->save();

                                $respuestaMarcacion = array(
                                    'id' => $req['id'],
                                    'estado' => true);

                            }

                        } else {

                            /* CUANDO TEENGA HORARIO */
                            if ($tipoMarcacion == 2 || $tipoMarcacion == 3) {
                                if ($tipoMarcacion == 2) {
                                    /*  entrada */

                                    $marcacion_biometrico = new marcacion_puerta();
                                    $marcacion_biometrico->marcaMov_fecha = $req['fechaMarcacion'];
                                    $marcacion_biometrico->marcaMov_emple_id = $req['idEmpleado'];
                                    $marcacion_biometrico->dispositivos_idDispositivos = $req['idDisposi'];
                                    $marcacion_biometrico->organi_id = $empleados->organi_id;

                                    if (empty($req['idHoraEmp'])) {} else {
                                        $marcacion_biometrico->horarioEmp_id = $req['idHoraEmp'];
                                    }

                                    $marcacion_biometrico->tipoMarcacionB = 1;

                                    $marcacion_biometrico->save();
                                    $respuestaMarcacion = array(
                                        'id' => $req['id'],
                                        'estado' => true);

                                    /* --------------------------------------------------------------- */
                                } else {
                                    /* SALIDA */
                                    /* CUADNO ES SALIDA DE HORARIO */

                                    /* CONVERTIMOS LA FECHA DE MARCACION EN DATE */
                                    $fecha1 = Carbon::create($req['fechaMarcacion'])->toDateString();

                                    /* CONSULTAMOS SI HAY UNA MARCACION DE ANTERIOR QUE ESTE LLENA */
                                    $marcacion_puerta00 = DB::table('marcacion_puerta as mv')
                                        ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                        ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha1)
                                        ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                        ->where('mv.tipoMarcacionB', '=', 1)
                                        ->orderby('marcaMov_fecha', 'ASC')
                                        ->where('mv.horarioEmp_id', '!=', null)
                                        ->get()->last();

                                    /* SI EXISTE ENTONCES COMPARRAREMOS */
                                    if ($marcacion_puerta00) {
                                        if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida != null) {
                                            $marcacion_biometrico = new marcacion_puerta();
                                            $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                                            $marcacion_biometrico->marcaMov_emple_id = $req['idEmpleado'];
                                            $marcacion_biometrico->dispositivos_idDispositivos = $req['idDisposi'];

                                            $marcacion_biometrico->organi_id = $empleados->organi_id;

                                            if (empty($req['idHoraEmp'])) {} else {
                                                $marcacion_biometrico->horarioEmp_id = $req['idHoraEmp'];
                                            }

                                            $marcacion_biometrico->tipoMarcacionB = 1;

                                            $marcacion_biometrico->save();
                                            $respuestaMarcacion = array(
                                                'id' => $req['id'],
                                                'estado' => true);

                                        } else {
                                            $marcacion_puertaVerif2 = DB::table('marcacion_puerta as mv')
                                                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                                ->where('mv.marcaMov_salida', '=', null)
                                                ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                                                ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                                ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                                ->where('mv.horarioEmp_id', '!=', null)
                                                ->orderby('marcaMov_fecha', 'ASC')
                                                ->get()->first();
                                            if ($marcacion_puertaVerif2) {
                                                $marcacion_biometrico = marcacion_puerta::find($marcacion_puertaVerif2->marcaMov_id);
                                                $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                                                $marcacion_biometrico->save();
                                                $respuestaMarcacion = array(
                                                    'id' => $req['id'],
                                                    'estado' => true);

                                            } else {
                                                $marcacion_biometrico = new marcacion_puerta();
                                                $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                                                $marcacion_biometrico->marcaMov_emple_id = $req['idEmpleado'];
                                                $marcacion_biometrico->dispositivos_idDispositivos = $req['idDisposi'];

                                                $marcacion_biometrico->organi_id = $empleados->organi_id;

                                                if (empty($req['idHoraEmp'])) {} else {
                                                    $marcacion_biometrico->horarioEmp_id = $req['idHoraEmp'];
                                                }

                                                $marcacion_biometrico->tipoMarcacionB = 1;

                                                $marcacion_biometrico->save();
                                                $respuestaMarcacion = array(
                                                    'id' => $req['id'],
                                                    'estado' => true);

                                            }
                                        }

                                    } else {
                                        $fecha2V = Carbon::create($req['fechaMarcacion'])->toDateString();

                                        /* VERIFICAMOS SI LA ULTIMA MARCACION SIN SALIDA EXSITE E INSERTAMOS */
                                        $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                                            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                            ->where('mv.marcaMov_salida', '=', null)
                                            ->whereDate('mv.marcaMov_fecha', '=', $fecha2V)
                                            ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                            ->where('mv.tipoMarcacionB', '=', 1)
                                            ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                            ->where('mv.horarioEmp_id', '!=', null)
                                            ->orderby('marcaMov_fecha', 'ASC')
                                            ->get()->last();
                                        if ($marcacion_puerta1) {
                                            $marcacion_biometrico = marcacion_puerta::find($marcacion_puerta1->marcaMov_id);
                                            $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                                            $marcacion_biometrico->save();
                                            $respuestaMarcacion = array(
                                                'id' => $req['id'],
                                                'estado' => true);

                                        }

                                    }

                                }
                            }

                        }

                    }

                } else {
                    $respuestaMarcacion = array(
                        'id' => $req['id'],
                        'error' => 'Empleado no existe',
                        'estado' => false);
                }

            }
            /* INSERTAMO A AARRAY  */
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

    public function marcacionBiometrico3(Request $request)
    {

        /** EXPORTAMOS ARCHIVO JSON
         *  OBTENEMOS ARCHIVO FILE Y CREAMOS NUEVO COLLECTION
         */

        //* OBTENEMOS ARCHIVO
        $contents = new Collection();
        $file = $request->file('file');
        $data = file_get_contents($file);
        $datosJ = json_decode($data, true);

        //* ODENAMOS POR FECHA DE MARCACION
        $datosJ = collect($datosJ)->sortBy('fechaMarcacion')->values()->toArray();

        //* RECORREMOS DATOS DE JSON Y LO GUARDAMOS EN UN COLLECCION
        foreach ($datosJ as $req) {
            $datos = ['idDisposi' => $req['idDisposi'], 'idEmpleado' => $req['idEmpleado'],
                'fechaMarcacion' => $req['fechaMarcacion'],
                'idHoraEmp' => $req['idHoraEmp'], 'id' => $req['id'],
            ];
            $contents->push($datos);
        }

        //* ORDENAMOS ARRAY POR FECHA DE MARCACION
        $arrayOrdenado = $contents->sortBy('fechaMarcacion');
        $arrayOrdenado->values()->all();

        /* -------------------------------------------------------------- */

        /** SETEAMOS VARIABLES QUE UTILIZAREMOS
         * FECHA ACTUAL Y FECHA DE MARCACION
         */

        //* FECHA ACTUAL
        $fechaHoy = Carbon::now('America/Lima');
        $horaActual = $fechaHoy->isoFormat('YYYY-MM-DD HH:mm:ss');

        //* FECHA DE MARCACION EN DATE
        $fecha = Carbon::create($req['fechaMarcacion'])->toDateString();

        //*COLLECTION DE RESPUESTA
        $arrayDatos = new Collection();

        /* ------------------------------------------------------ */

        /**----------------------VERIFICAR ARRAYS------------------------------------------------------ */

        /* VERIFICO SI NO HAY MARCACIONES ANTES */
        $marcacion_puertaVacio = DB::table('marcacion_puerta as mv')
            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
            ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha)
            ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
            ->whereNull('mv.horarioEmp_id')
            ->get();

        /* VERIFICO  ULTIMA MARCACION */
        $marcacion_puertaVerif = DB::table('marcacion_puerta as mv')
            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
            ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha)
            ->whereNull('mv.horarioEmp_id')
            ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
            ->orderby(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), 'ASC')
            ->get()->last();

        /*--------- SI  TENGO SALIDA ----------------------------*/
        $marcacion_puertaVerif2 = DB::table('marcacion_puerta as mv')
            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
            ->where('mv.marcaMov_salida', '=', null)
            ->whereDate('mv.marcaMov_fecha', '=', $fecha)
            ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
            ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
            ->whereNull('mv.horarioEmp_id')
            ->orderby('marcaMov_fecha', 'ASC')
            ->get()->first();
        /* ------------------------------------------------------ */

        //* SI  TENGO SALIDA CON HORARIO
        $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
            ->where('mv.marcaMov_salida', '=', null)
            ->whereDate('mv.marcaMov_fecha', '=', $fecha)
            ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
            ->where('mv.tipoMarcacionB', '=', 1)
            ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
            ->where('mv.horarioEmp_id', '!=', null)
            ->orderby('marcaMov_fecha', 'ASC')
            ->get()->last();
        /* ---------------------------------------------------------------------------- */

        /** RECORREMOS ARRAY COLLECTION ORDENADO
         * OBTENEMOS TIPO DE MARCACION SEGUN TENGA HORARIO O NO
         */
        foreach ($arrayOrdenado as $req) {

            //* SI RECIBO SIN HORARIO
            if ($req['idHoraEmp'] == 0 || $req['idHoraEmp'] == null) {

                //*SI NO TENGO MARCACIONES
                if ($marcacion_puertaVacio->isEmpty()) {
                    $tipoMarcacion = 1;
                } else {
                    /* YA HAY MARCACIONES PARA ESTE EMPLEADO Y FECHA */
                    /* BUSCAMOS SI EXISTE UNA ULTIMA MARCACION CON TODO LOS DATOS */
                    if ($marcacion_puertaVerif) {

                        if ($marcacion_puertaVerif->marcaMov_fecha != null && $marcacion_puertaVerif->marcaMov_salida != null) {

                            //* SI EXISTE ENTONCES SERA TIPO 1 ENTRADA
                            $tipoMarcacion = 1;

                        } else {
                            //* SERA TIPO SALIDA
                            if ($marcacion_puertaVerif2) {
                                $tipoMarcacion = 0;
                            }
                        }

                    }
                }
            }
            /* SI RECIBO CON HORARIO */
            else {

                //*OBTENGO HORARIO
                $horario = DB::table('horario_empleado as he')
                    ->join('horario_dias as hd', 'hd.id', '=', 'he.horario_dias_id')
                    ->join('horario as h', 'he.horario_horario_id', '=', 'h.horario_id')
                    ->join('empleado as e', 'he.empleado_emple_id', '=', 'e.emple_id')
                    ->where('he.horarioEmp_id', '=', $req['idHoraEmp'])
                    ->select('he.empleado_emple_id as idempleado', 'he.horarioEmp_id as idHorarioEmp', 'h.horario_id', 'h.horario_descripcion', 'h.horaI',
                        'h.horaF', 'h.horario_tolerancia as toleranciaI', 'h.horario_toleranciaF as toleranciaF', 'he.fuera_horario', 'hd.start')
                    ->orderBy('he.empleado_emple_id')
                    ->get();

                /** OBTENGO PAUSAS DE HORARIO
                 * E INSERTO EN HORARIO
                 */

                foreach ($horario as $tab2) {
                    /* OBTENER FECHA ACTUAL Y DE MAÑANA EN FORMATO DATE */
                    $fecha = Carbon::create($tab2->start);
                    $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                    $despues = $fecha->addDays(1);
                    $fechaMan = $despues->isoFormat('YYYY-MM-DD');

                    if (Carbon::parse($tab2->horaF)->lt(Carbon::parse($tab2->horaI))) {

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
                        ->where('inactivar', '=', 0)
                        ->distinct('pauh.idpausas_horario')
                        ->get();
                    $horaIV = $tab1->horaI;
                    $horaFV = $tab1->horaF;

                    $fecha = Carbon::create($tab1->start);
                    $fechaHoy = $fecha->isoFormat('YYYY-MM-DD');
                    $despues = $fecha->addDays(1);
                    $fechaMan = $despues->isoFormat('YYYY-MM-DD');

                    foreach ($pausas_horario as $tab3) {

                        if (Carbon::parse($tab3->horaF)->lt(Carbon::parse($tab3->horaI))) {

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
                /* ------------FIN DE HORARIO CON PAUSAAS----------------- */

                /* ------VALIDANDO TIPO DE MARCACION---------------- */
                //* OBTENEMOS HORA INICIAL Y FINAL */
                $horaI = Carbon::create($horario[0]->horaI);
                $horaF = Carbon::create($horario[0]->horaF);
                /*  $toleranciaI= $horario[0]->toleranciaI; */
                $arrayPausas = $horario[0]->pausas;

                //*DIFERENCIAS
                $diferenciaI = Carbon::create($req['fechaMarcacion'])->diffInMinutes($horaI);
                $diferenciaF = Carbon::create($req['fechaMarcacion'])->diffInMinutes($horaF);

                //*SI NO TIENE PAUSAS
                if ($arrayPausas->isEmpty()) {

                    //* SI LA MARCACION ESTA MAS CERCA A SALIDA SERA SALIDA=3
                    if ($diferenciaI > $diferenciaF) {
                        $tipoMarcacion = 3;
                    } else {
                        //* SI LA MARCACION ESTA MAS CERCA A ENTRADA
                        //* SI ANTES TENGO MARCACION SIN SALIDA
                        if ($marcacion_puerta1) {
                            $tipoMarcacion = 3;
                        } else {

                            //* SI NO SERA ENTRADA=2
                            $tipoMarcacion = 2;
                        }

                    }
                }
                //* FIN SI NO TENGO PAUSAS

                else {
                    $arrayDiferencias = new Collection();
                    /* ----------------------------------SI TIENE PAUSAS----------------------------------------- */

                    //*PRIMERO CALCULAMOS MENOR DIFERENCIA CON HORA Y ENTRADA GENERAL
                    if ($diferenciaI > $diferenciaF) {
                        $tipoMarcacionH = 3;
                        $horaSeteo = $horaF;
                        $datos0 = ["tipoMarcacion" => $tipoMarcacionH, "diferencia" => $diferenciaF];
                    } else {
                        $tipoMarcacionH = 2;
                        $horaSeteo = $horaI;
                        $datos0 = ["tipoMarcacion" => $tipoMarcacionH, "diferencia" => $diferenciaI];
                    }
                    $arrayDiferencias->push($datos0);

                    //*SEGUNDO CALCULAMOS MENOR DIFERENCIA CON HORA Y ENTRADA DE PAUSAS
                    foreach ($arrayPausas as $arrayPausas2) {

                        /* NO ESTOY TOMANDO EN CUETNA LA TOLERANCIAA */
                        $horaIP = Carbon::create($arrayPausas2->horaI);
                        $horaFP = Carbon::create($arrayPausas2->horaF);
                        $diferenciaPI = Carbon::create($req['fechaMarcacion'])->diffInMinutes($horaIP);
                        $diferenciaPF = Carbon::create($req['fechaMarcacion'])->diffInMinutes($horaFP);

                        if ($diferenciaPI > $diferenciaPF) {
                            $tipoMarcacionP = 2;
                            $horaSeteoP = $horaFP;
                            $datos1 = ["tipoMarcacion" => $tipoMarcacionP, "diferencia" => $diferenciaPF];
                        } else {
                            $tipoMarcacionP = 3;
                            $horaSeteoP = $horaIP;
                            $datos1 = ["tipoMarcacion" => $tipoMarcacionP, "diferencia" => $diferenciaPI];
                        }

                        $arrayDiferencias->push($datos1);
                    }
                    /* ORDENO PARA OBTENER LA MENOR DIFERECNIA */
                    $arrayDiferencias1 = $arrayDiferencias->sortBy('diferencia')->values()->all();
                    $arrayFinal = $arrayDiferencias1[0];
                    /* SI OBTUVIMOS ENTRADA */
                    if ($arrayFinal["tipoMarcacion"] == 2) {
                        /* VERIFICAMOS SI TIENE MARCACION SIN SALIDA */
                        if ($marcacion_puerta1) {
                            $tipoMarcacion = 3;
                        } else {
                            $tipoMarcacion = 2;
                        }

                    } else {
                        $tipoMarcacion = 3;
                    }

                }
                /* ------------------------------------------------- */
            }
            //*FIN SI TENGO HORARIO ************************

            /**
             * EMPEZAMOS A REGISTRAR LAS MARCACIONES RECIBIDAS
             */
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
                    ->get()->first();

                if ($empleados) {
                    /*CUADNO ES MARCACION DE ENTRADA SIN HORARIO */
                    if ($tipoMarcacion == 1) {
                        $marcacion_biometrico = new marcacion_puerta();

                        $marcacion_biometrico->marcaMov_fecha = $req['fechaMarcacion'];
                        /* -------------------- */

                        $marcacion_biometrico->marcaMov_emple_id = $req['idEmpleado'];
                        $marcacion_biometrico->dispositivos_idDispositivos = $req['idDisposi'];

                        $marcacion_biometrico->organi_id = $empleados->organi_id;

                        if (empty($req['idHoraEmp'])) {} else {
                            $marcacion_biometrico->horarioEmp_id = $req['idHoraEmp'];
                        }

                        $marcacion_biometrico->tipoMarcacionB = 1;

                        $marcacion_biometrico->save();

                        $respuestaMarcacion = array(
                            'id' => $req['id'],
                            'estado' => true);

                        /* --------------------------------------------------------------- */
                    }
                    /* CUADNO ES TIPO 0  SON SALIDA DE MARCACION Y FIN DE PAUSA  */
                    else {

                        /* AQUI VALIDAREMOS PARA INSERTAR LA SALIDA */

                        if ($tipoMarcacion == 0) {

                            /* CUADNO ES SALIDA DE HORARIO */

                            /* CONVERTIMOS LA FECHA DE MARCACION EN DATE */
                            $fecha1 = Carbon::create($req['fechaMarcacion'])->toDateString();

                            /* VERIFICAMOS  PARA EMPAREJAR  */
                            $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                ->where('mv.marcaMov_salida', '=', null)
                                ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                                ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                ->where('mv.tipoMarcacionB', '=', 1)
                                ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                ->whereNull('mv.horarioEmp_id')
                                ->orderby('marcaMov_fecha', 'ASC')
                                ->get()->last();

                            if ($marcacion_puerta1) {
                                $marcacion_biometrico = marcacion_puerta::find($marcacion_puerta1->marcaMov_id);
                                $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                                $marcacion_biometrico->save();

                                $respuestaMarcacion = array(
                                    'id' => $req['id'],
                                    'estado' => true);

                            }

                        } else {

                            /* CUANDO TEENGA HORARIO */
                            if ($tipoMarcacion == 2 || $tipoMarcacion == 3) {
                                if ($tipoMarcacion == 2) {
                                    /*  entrada */

                                    $marcacion_biometrico = new marcacion_puerta();
                                    $marcacion_biometrico->marcaMov_fecha = $req['fechaMarcacion'];
                                    $marcacion_biometrico->marcaMov_emple_id = $req['idEmpleado'];
                                    $marcacion_biometrico->dispositivos_idDispositivos = $req['idDisposi'];
                                    $marcacion_biometrico->organi_id = $empleados->organi_id;

                                    if (empty($req['idHoraEmp'])) {} else {
                                        $marcacion_biometrico->horarioEmp_id = $req['idHoraEmp'];
                                    }

                                    $marcacion_biometrico->tipoMarcacionB = 1;

                                    $marcacion_biometrico->save();
                                    $respuestaMarcacion = array(
                                        'id' => $req['id'],
                                        'estado' => true);

                                    /* --------------------------------------------------------------- */
                                } else {
                                    /* SALIDA */
                                    /* CUADNO ES SALIDA DE HORARIO */

                                    /* CONVERTIMOS LA FECHA DE MARCACION EN DATE */
                                    $fecha1 = Carbon::create($req['fechaMarcacion'])->toDateString();

                                    /* CONSULTAMOS SI HAY UNA MARCACION DE ANTERIOR QUE ESTE LLENA */
                                    $marcacion_puerta00 = DB::table('marcacion_puerta as mv')
                                        ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                        ->whereDate(DB::raw('IF(mv.marcaMov_fecha is null,mv.marcaMov_salida ,mv.marcaMov_fecha)'), '=', $fecha1)
                                        ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                        ->where('mv.tipoMarcacionB', '=', 1)
                                        ->orderby('marcaMov_fecha', 'ASC')
                                        ->where('mv.horarioEmp_id', '!=', null)
                                        ->get()->last();

                                    /* SI EXISTE ENTONCES COMPARRAREMOS */
                                    if ($marcacion_puerta00) {
                                        if ($marcacion_puerta00->marcaMov_fecha != null && $marcacion_puerta00->marcaMov_salida != null) {
                                            $marcacion_biometrico = new marcacion_puerta();
                                            $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                                            $marcacion_biometrico->marcaMov_emple_id = $req['idEmpleado'];
                                            $marcacion_biometrico->dispositivos_idDispositivos = $req['idDisposi'];

                                            $marcacion_biometrico->organi_id = $empleados->organi_id;

                                            if (empty($req['idHoraEmp'])) {} else {
                                                $marcacion_biometrico->horarioEmp_id = $req['idHoraEmp'];
                                            }

                                            $marcacion_biometrico->tipoMarcacionB = 1;

                                            $marcacion_biometrico->save();
                                            $respuestaMarcacion = array(
                                                'id' => $req['id'],
                                                'estado' => true);

                                        } else {
                                            $marcacion_puertaVerif2 = DB::table('marcacion_puerta as mv')
                                                ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                                ->where('mv.marcaMov_salida', '=', null)
                                                ->whereDate('mv.marcaMov_fecha', '=', $fecha1)
                                                ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                                ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                                ->where('mv.horarioEmp_id', '!=', null)
                                                ->orderby('marcaMov_fecha', 'ASC')
                                                ->get()->first();
                                            if ($marcacion_puertaVerif2) {
                                                $marcacion_biometrico = marcacion_puerta::find($marcacion_puertaVerif2->marcaMov_id);
                                                $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                                                $marcacion_biometrico->save();
                                                $respuestaMarcacion = array(
                                                    'id' => $req['id'],
                                                    'estado' => true);

                                            } else {
                                                $marcacion_biometrico = new marcacion_puerta();
                                                $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                                                $marcacion_biometrico->marcaMov_emple_id = $req['idEmpleado'];
                                                $marcacion_biometrico->dispositivos_idDispositivos = $req['idDisposi'];

                                                $marcacion_biometrico->organi_id = $empleados->organi_id;

                                                if (empty($req['idHoraEmp'])) {} else {
                                                    $marcacion_biometrico->horarioEmp_id = $req['idHoraEmp'];
                                                }

                                                $marcacion_biometrico->tipoMarcacionB = 1;

                                                $marcacion_biometrico->save();
                                                $respuestaMarcacion = array(
                                                    'id' => $req['id'],
                                                    'estado' => true);

                                            }
                                        }

                                    } else {
                                        $fecha2V = Carbon::create($req['fechaMarcacion'])->toDateString();

                                        /* VERIFICAMOS SI LA ULTIMA MARCACION SIN SALIDA EXSITE E INSERTAMOS */
                                        $marcacion_puerta1 = DB::table('marcacion_puerta as mv')
                                            ->where('mv.marcaMov_emple_id', '=', $req['idEmpleado'])
                                            ->where('mv.marcaMov_salida', '=', null)
                                            ->whereDate('mv.marcaMov_fecha', '=', $fecha2V)
                                            ->where('mv.marcaMov_fecha', '<=', $req['fechaMarcacion'])
                                            ->where('mv.tipoMarcacionB', '=', 1)
                                            ->where('mv.dispositivos_idDispositivos', '=', $req['idDisposi'])
                                            ->where('mv.horarioEmp_id', '!=', null)
                                            ->orderby('marcaMov_fecha', 'ASC')
                                            ->get()->last();
                                        if ($marcacion_puerta1) {
                                            $marcacion_biometrico = marcacion_puerta::find($marcacion_puerta1->marcaMov_id);
                                            $marcacion_biometrico->marcaMov_salida = $req['fechaMarcacion'];
                                            $marcacion_biometrico->save();
                                            $respuestaMarcacion = array(
                                                'id' => $req['id'],
                                                'estado' => true);

                                        }

                                    }

                                }
                            }

                        }

                    }

                } else {
                    $respuestaMarcacion = array(
                        'id' => $req['id'],
                        'error' => 'Empleado no existe',
                        'estado' => false);
                }

            }
            /* INSERTAMO A AARRAY  */
            $arrayDatos->push($respuestaMarcacion);
            /* ---------------------------- */
        }

        if ($arrayDatos != null) {

            return response()->json($arrayDatos);
        } else {
            return response()->json(array('status' => 400, 'title' => 'No se pudo registrar marcacion',
                'detail' => 'No se pudo registrar marcacion, compruebe que los datos sean validos'), 400);
        }
        /* ------------------------------------------------------------------------- */
    }
}
