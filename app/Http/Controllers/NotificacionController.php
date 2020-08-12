<?php

namespace App\Http\Controllers;

use App\Notifications\NuevaNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function notificacionesUsuario()
    {
        $respuestaEU = false;
        $respuestaEA = false;
        $respuestaEN = false;
        $respuestaEC = false;
        $respuestaEL = false;
        $respuestaED = false;
        $respuestaEH = false;
        $respuestaECC = false;
        $notificacion = Auth::user()->notifications;
        foreach ($notificacion as $notif) {
            $aux = $notif->data;
            foreach ($aux as $ax) {
                if ($ax["tipo"] == "eventosUsuario") {
                    $respuestaEU = true;
                }
                if ($ax["tipo"] == "empleadoArea") {
                    $respuestaEA = true;
                }
                if ($ax["tipo"] == "empleadoNivel") {
                    $respuestaEN = true;
                }
                if ($ax["tipo"] == "empleadoContrato") {
                    $respuestaEC = true;
                }
                if ($ax["tipo"] == "empleadoCentro") {
                    $respuestaECC = true;
                }
                if ($ax["tipo"] == "empleadoLocal") {
                    $respuestaEL = true;
                }
                if ($ax["tipo"] == "empleadoDepartamento") {
                    $respuestaED = true;
                }
                if ($ax["tipo"] != "empleadoHorario") {
                    $respuestaEH = true;
                }
            }
        }
        if (sizeof($notificacion) == 0) {
            $eventos = DB::table('eventos_usuario as eu')
                ->where('eu.users_id', '=', Auth::user()->id)
                ->get()
                ->first();

            if (!$eventos) {
                $mensaje = [
                    "tipo" => 'eventosUsuario',
                    "mensaje" => 'Personaliza tu calendario.'
                ];
                $recipient = User::find(Auth::user()->id);
                $recipient->notify(new NuevaNotification($mensaje));
            }
            $area = DB::table('empleado as e')
                ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                ->select('a.area_descripcion', DB::raw('COUNT(a.area_descripcion) as Total'))
                ->where('e.users_id', '=', Auth::user()->id)
                ->where('e.emple_estado', '=', 1)
                ->groupBy('a.area_id')
                ->get()->first();

            if (!$area) {
                $mensaje = [
                    "tipo" => 'empleadoArea',
                    "mensaje" => 'Asigna área a tus empleados.'
                ];
                $recipient = User::find(Auth::user()->id);
                $recipient->notify(new NuevaNotification($mensaje));
            }
            $nivel = DB::table('empleado as e')
                ->join('nivel as n', 'e.emple_nivel', '=', 'n.nivel_id')
                ->select('n.nivel_descripcion', DB::raw('COUNT(n.nivel_descripcion) as Total'))
                ->where('e.users_id', '=', Auth::user()->id)
                ->where('e.emple_estado', '=', 1)
                ->groupBy('n.nivel_id')
                ->get()->first();

            if (!$nivel) {
                $mensaje = [
                    "tipo" => 'empleadoNivel',
                    "mensaje" => 'Asigna nivel a tus empleados.'
                ];
                $recipient = User::find(Auth::user()->id);
                $recipient->notify(new NuevaNotification($mensaje));
            }
            $contrato = DB::table('empleado as e')
                ->join('tipo_contrato as c', 'e.emple_tipoContrato', '=', 'c.contrato_id')
                ->select('c.contrato_descripcion', DB::raw('COUNT(c.contrato_descripcion) as Total'))
                ->where('e.users_id', '=', Auth::user()->id)
                ->where('e.emple_estado', '=', 1)
                ->groupBy('c.contrato_id')
                ->get()->first();

            if (!$contrato) {
                $mensaje = [
                    "tipo" => 'empleadoContrato',
                    "mensaje" => 'Asigna contrato a tus empleados.'
                ];
                $recipient = User::find(Auth::user()->id);
                $recipient->notify(new NuevaNotification($mensaje));
            }
            $centro = DB::table('empleado as e')
                ->join('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                ->select('cc.centroC_descripcion', DB::raw('COUNT(cc.centroC_descripcion) as Total'))
                ->where('e.users_id', '=', Auth::user()->id)
                ->where('e.emple_estado', '=', 1)
                ->groupBy('cc.centroC_id')
                ->get()->first();

            if (!$centro) {
                $mensaje = [
                    "tipo" => 'empleadoCentro',
                    "mensaje" => 'Asigna CC a tus empleados.'
                ];
                $recipient = User::find(Auth::user()->id);
                $recipient->notify(new NuevaNotification($mensaje));
            }
            $local = DB::table('empleado as e')
                ->join('local as l', 'e.emple_local', '=', 'l.local_id')
                ->select('l.local_descripcion', DB::raw('COUNT(l.local_descripcion) as Total'))
                ->where('e.users_id', '=', Auth::user()->id)
                ->where('e.emple_estado', '=', 1)
                ->groupBy('l.local_id')
                ->get()->first();

            if (!$local) {
                $mensaje = [
                    "tipo" => 'empleadoLocal',
                    "mensaje" => 'Asigna local a tus empleados.'
                ];
                $recipient = User::find(Auth::user()->id);
                $recipient->notify(new NuevaNotification($mensaje));
            }
            $horario = DB::table('horario as h')
                ->where('h.user_id', '=', Auth::user()->id)
                ->get()
                ->first();

            if (!$horario) {
                $mensaje = [
                    "tipo" => 'empleadoHorario',
                    "mensaje" => 'Personaliza tus horarios.'
                ];
                $recipient = User::find(Auth::user()->id);
                $recipient->notify(new NuevaNotification($mensaje));
            }
            $respuesta = Auth::user()->notifications;
        } else {
            if ($respuestaEU == false) {
                $eventos = DB::table('eventos_usuario as eu')
                    ->where('eu.users_id', '=', Auth::user()->id)
                    ->get()
                    ->first();

                if (!$eventos) {
                    $mensaje = [
                        "tipo" => 'eventosUsuario',
                        "mensaje" => 'Personaliza tu calendario.'
                    ];
                    $recipient = User::find(Auth::user()->id);
                    $recipient->notify(new NuevaNotification($mensaje));
                }
            }
            if ($respuestaEA == false) {
                $area = DB::table('empleado as e')
                    ->join('area as a', 'e.emple_area', '=', 'a.area_id')
                    ->select('a.area_descripcion', DB::raw('COUNT(a.area_descripcion) as Total'))
                    ->where('e.users_id', '=', Auth::user()->id)
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('a.area_id')
                    ->get()->first();

                if (!$area) {
                    $mensaje = [
                        "tipo" => 'empleadoArea',
                        "mensaje" => 'Asigna área a tus empleados.'
                    ];
                    $recipient = User::find(Auth::user()->id);
                    $recipient->notify(new NuevaNotification($mensaje));
                }
            }
            if ($respuestaEN == false) {
                $nivel = DB::table('empleado as e')
                    ->join('nivel as n', 'e.emple_nivel', '=', 'n.nivel_id')
                    ->select('n.nivel_descripcion', DB::raw('COUNT(n.nivel_descripcion) as Total'))
                    ->where('e.users_id', '=', Auth::user()->id)
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('n.nivel_id')
                    ->get()->first();

                if (!$nivel) {
                    $mensaje = [
                        "tipo" => 'empleadoNivel',
                        "mensaje" => 'Asigna nivel a tus empleados.'
                    ];
                    $recipient = User::find(Auth::user()->id);
                    $recipient->notify(new NuevaNotification($mensaje));
                }
            }
            if ($respuestaEC == false) {
                $contrato = DB::table('empleado as e')
                    ->join('tipo_contrato as c', 'e.emple_tipoContrato', '=', 'c.contrato_id')
                    ->select('c.contrato_descripcion', DB::raw('COUNT(c.contrato_descripcion) as Total'))
                    ->where('e.users_id', '=', Auth::user()->id)
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('c.contrato_id')
                    ->get()->first();

                if (!$contrato) {
                    $mensaje = [
                        "tipo" => 'empleadoContrato',
                        "mensaje" => 'Asigna contrato a tus empleados.'
                    ];
                    $recipient = User::find(Auth::user()->id);
                    $recipient->notify(new NuevaNotification($mensaje));
                }
            }
            if ($respuestaECC == false) {
                $centro = DB::table('empleado as e')
                    ->join('centro_costo as cc', 'e.emple_centCosto', '=', 'cc.centroC_id')
                    ->select('cc.centroC_descripcion', DB::raw('COUNT(cc.centroC_descripcion) as Total'))
                    ->where('e.users_id', '=', Auth::user()->id)
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('cc.centroC_id')
                    ->get()->first();

                if (!$centro) {
                    $mensaje = [
                        "tipo" => 'empleadoCentro',
                        "mensaje" => 'Asigna CC a tus empleados.'
                    ];
                    $recipient = User::find(Auth::user()->id);
                    $recipient->notify(new NuevaNotification($mensaje));
                }
            }
            if ($respuestaEL == false) {
                $local = DB::table('empleado as e')
                    ->join('local as l', 'e.emple_local', '=', 'l.local_id')
                    ->select('l.local_descripcion', DB::raw('COUNT(l.local_descripcion) as Total'))
                    ->where('e.users_id', '=', Auth::user()->id)
                    ->where('e.emple_estado', '=', 1)
                    ->groupBy('l.local_id')
                    ->get()->first();

                if (!$local) {
                    $mensaje = [
                        "tipo" => 'empleadoLocal',
                        "mensaje" => 'Asigna local a tus empleados.'
                    ];
                    $recipient = User::find(Auth::user()->id);
                    $recipient->notify(new NuevaNotification($mensaje));
                }
            }
            if ($respuestaEH == false) {
                $horario = DB::table('horario as h')
                    ->where('h.user_id', '=', Auth::user()->id)
                    ->get()
                    ->first();

                if (!$horario) {
                    $mensaje = [
                        "tipo" => 'empleadoHorario',
                        "mensaje" => 'Personaliza tus horarios.'
                    ];
                    $recipient = User::find(Auth::user()->id);
                    $recipient->notify(new NuevaNotification($mensaje));
                }
            }
            $respuesta = Auth::user()->notifications;
        }
        return response()->json($respuesta, 200);
    }
    public function showNotificaciones()
    {
        $respuesta = Auth::user()->notifications;
        return response()->json($respuesta, 200);
    }
}
