<?php

namespace App\Http\Controllers;

use App\Mail\correoAdministrativo;
use App\Mail\correoReunion;
use App\Mail\sugerenciaMail;
use App\organizacion;
use App\persona;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class soportesPorCorreoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except('envioAgendaReunion');
    }

    public function soporte()
    {
        $correoAdministrativo = env('MAIL_FROM_ADDRESS');
        return view('correosA.soporte', ["correo" => $correoAdministrativo]);
    }

    public function envioTicketSoporte(Request $request)
    {
        $valor = $request->get('contenido');
        $asunto = $request->get('asunto');
        $email = env('MAIL_FROM_ADDRESS');
        $idOrganizacion = session('sesionidorg');
        $organizacion = organizacion::find($idOrganizacion);
        $idEmpleado = Auth::user()->id;
        $usuario = User::find($idEmpleado);
        $persona = persona::find($usuario->perso_id);
        Mail::to($email)->queue(new correoAdministrativo($valor, $asunto,$organizacion,$persona,$usuario));
        return response()->json($valor, 200);
    }

    public function envioAgendaReunion(Request $request)
    {   
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $today = Carbon::now();
        $año= $today->year;
        $mes= $meses[$today->month-1];
        $dia= $today->day;
        $hour = $today->hour;
        $minute = $today->minute;
        $second = $today->second;
        $fecha = "".$dia." de ".$mes." del ".$año." ".$hour.":".$minute.":".$second."";
        $email = 'info@rhnube.com.pe';
        //$email = 'miguelpacheco.1622@gmail.com';
        $nombres = $request->get('modal_saveMeet_name');
        $apellidos = $request->get('modal_saveMeet_lastname');
        $telefono = $request->get('modal_saveMeet_movil');
        $correo = $request->get('modal_saveMeet_email');
        $empresa = $request->get('modal_saveMeet_company');
        $cargo = $request->get('modal_saveMeet_job');
        $nTrabajadores = $request->get('modal_saveMeet_nWorkers');
        $envio = Mail::to($email)->queue(new correoReunion($nombres, $apellidos, $telefono, $correo, $empresa, $cargo, $nTrabajadores, $fecha));
        return response()->json("Enviado", 200);
    }

    public function sugerencia()
    {
        $correoAdministrativo = env('MAIL_FROM_ADDRESS');
        return view('correosA.sugerencia', ["correo" => $correoAdministrativo]);
    }

    public function envioSugerencia(Request $request)
    {
        $valor = $request->get('contenido');
        $asunto = $request->get('asunto');
        $email = env('MAIL_FROM_ADDRESS');
        $idOrganizacion = session('sesionidorg');
        $organizacion = organizacion::find($idOrganizacion);
        $idEmpleado = Auth::user()->id;
        $usuario = User::find($idEmpleado);
        $persona = persona::find($usuario->perso_id);
        Mail::to($email)->queue(new sugerenciaMail($valor, $asunto,$organizacion,$persona,$usuario));
        return response()->json($valor, 200);
    }
}
