<?php

namespace App\Http\Controllers;

use App\Mail\correoAdministrativo;
use App\Mail\correoReunion;
use App\Mail\sugerenciaMail;
use App\Mail\correoPartner;
use App\organizacion;
use App\persona;
use App\User;
use App\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class soportesPorCorreoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except('envioAgendaReunion', 'envioPartner');
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
        $nombres = $request->get('nombre_apellidos');
        $apellidos = $request->get('modal_saveMeet_lastname');
        $telefono = $request->get('telefono');
        $correo = $request->get('correo');
        $empresa = $request->get('empresa');
        $cargo = $request->get('cargo');
        $nTrabajadores = $request->get('colaborador');
        $comment = $request->get('comentario');

        $dateMeet = $request->get('diaReunion');
        $hourMeet = $request->get('horaReunion');
        $agenda = new Agenda();
        $agenda->nombres = $request->get('nombre_apellidos');
        $agenda->telefono = $request->get('telefono');
        $agenda->correo = $request->get('correo');
        $agenda->empresa = $request->get('empresa');
        $agenda->cargo = $request->get('cargo');
        $agenda->colaboradores = $request->get('colaborador');
        $agenda->fecha = $dateMeet." ".$hourMeet;
        $agenda->comentario = $request->get('comentario');
        //dd($dateMeet);
        $agenda->save();
        $horario = $agenda->fecha;

        $envio = Mail::to('miguelpacheco.1622@gmail.com')->queue(new correoReunion($nombres, $apellidos, $telefono, $correo, $empresa, $cargo, $nTrabajadores, $comment, $fecha, $horario));
        return response()->json($horario, 200);
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

    public function envioPartner(Request $request){
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
        $ruc = $request->get('rucP');
        $razonSocial = $request->get('razonSocialP');
        $contacto = $request->get('contactoP');
        $correo = $request->get('correoP');
        $telefono = $request->get('telefonoP');
        $mensaje = $request->get('mensajeP');
        $fecha = $fecha;
        $envio = Mail::to('miguelpacheco.1622@gmail.com')->queue(new correoPartner($ruc, $razonSocial, $contacto, $correo, $telefono, $mensaje, $fecha));
        return response()->json($envio, 200);
    }
}
