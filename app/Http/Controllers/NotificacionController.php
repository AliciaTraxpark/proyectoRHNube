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
        $eventos = DB::table('eventos_usuario as eu')
            ->where('eu.users_id', '=', Auth::user()->id)
            ->get()
            ->first();

        if (!$eventos) {
            $mensaje = [
                "tipo" => 'eventosUsuario',
                "mensaje" => 'Aún no has personalizado tu calendario'
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
                "tipo" => 'empleado',
                "mensaje" => 'Aún no has personalizado tu calendario'
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
                "tipo" => 'empleado',
                "mensaje" => 'Aún no has personalizado tu calendario'
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
                "tipo" => 'empleado',
                "mensaje" => 'Aún no has personalizado tu calendario'
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
                "tipo" => 'empleado',
                "mensaje" => 'Aún no has personalizado tu calendario'
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
                "tipo" => 'empleado',
                "mensaje" => 'Aún no has personalizado tu calendario'
            ];
            $recipient = User::find(Auth::user()->id);
            $recipient->notify(new NuevaNotification($mensaje));
        }
        $departamento = DB::table('empleado as e')
            ->join('ubigeo_peru_departments as d', 'd.id', '=', 'e.emple_departamento')
            ->select('d.name', DB::raw('COUNT(d.name) as total'))
            ->where('e.users_id', '=', Auth::user()->id)
            ->where('e.emple_estado', '=', 1)
            ->groupBy('d.id')
            ->get()->first();

        if (!$departamento) {
            $mensaje = [
                "tipo" => 'empleado',
                "mensaje" => 'Aún no has personalizado tu calendario'
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
                "tipo" => 'empleado',
                "mensaje" => 'Aún no has personalizado tu calendario'
            ];
            $recipient = User::find(Auth::user()->id);
            $recipient->notify(new NuevaNotification($mensaje));
        }
        $respuesta = Auth::user()->notifications;
        return response()->json($respuesta, 200);
    }
}
